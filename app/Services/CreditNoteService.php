<?php

namespace App\Services;

use App\Models\CreditNote;
use App\Models\CreditNoteItem;
use App\Models\InventoryMovement;
use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CreditNoteService
{
    public function __construct(protected InvoiceService $invoiceService)
    {
    }

    public function index(int $paginate = 10): LengthAwarePaginator
    {
        if ($paginate > 50) {
            $paginate = 50;
        }

        return CreditNote::with(['invoice.company'])
            ->orderByDesc('id')
            ->paginate($paginate);
    }

    public function find(int $id): CreditNote
    {
        return CreditNote::with(['invoice.company', 'items'])->findOrFail($id);
    }

    /** @return array{quantity: int, free_items: int} */
    public function returnedTotalsForInvoiceProduct(int $invoiceId, int $productId): array
    {
        $row = CreditNoteItem::query()
            ->where('product_id', $productId)
            ->whereHas('creditNote', fn ($q) => $q->where('invoice_id', $invoiceId))
            ->selectRaw('COALESCE(SUM(quantity),0) as q, COALESCE(SUM(free_items),0) as f')
            ->first();

        return [
            'quantity' => (int) ($row->q ?? 0),
            'free_items' => (int) ($row->f ?? 0),
        ];
    }

    /**
     * @return Collection<int, Collection<int, CreditNoteItem>>
     */
    public function returnHistoryGroupedByProduct(int $invoiceId): Collection
    {
        return CreditNoteItem::query()
            ->whereHas('creditNote', fn ($q) => $q->where('invoice_id', $invoiceId))
            ->with('creditNote:id,credit_note_number,note_date')
            ->orderBy('id')
            ->get()
            ->groupBy('product_id');
    }

    public function buildInvoicePreview(Invoice $invoice): array
    {
        $invoice->load('company');
        $products = $this->invoiceService->show($invoice->id);
        $discount = (float) ($invoice->company->discount ?? 0);
        $historyByProduct = $this->returnHistoryGroupedByProduct($invoice->id);

        $lines = [];
        foreach ($products as $product) {
            $origPaid = (int) $product->quantity;
            $origFree = (int) ($product->free_items ?? 0);
            $ret = $this->returnedTotalsForInvoiceProduct($invoice->id, $product->id);
            $availPaid = max(0, $origPaid - $ret['quantity']);
            $availFree = max(0, $origFree - $ret['free_items']);

            $defaultsPaid = 0;
            $defaultsFree = 0;
            $lineDiscount = $product->lineDiscountPercent($discount);

            $previewItem = new CreditNoteItem([
                'item_name' => $product->name,
                'quantity' => $defaultsPaid,
                'free_items' => $defaultsFree,
                'unit_price' => $product->price,
                'vat_percent' => $product->vat,
                'company_discount_percent' => $lineDiscount,
            ]);

            $history = ($historyByProduct->get($product->id) ?? collect())
                ->map(fn (CreditNoteItem $i) => [
                    'credit_note_number' => $i->creditNote->credit_note_number,
                    'note_date' => $i->creditNote->note_date->format('Y-m-d'),
                    'quantity' => (int) $i->quantity,
                    'free_items' => (int) ($i->free_items ?? 0),
                    'line_note' => $i->line_note,
                ])
                ->values()
                ->all();

            $lines[] = [
                'product_id' => $product->id,
                'item_name' => $product->name,
                'original_quantity' => $origPaid,
                'original_free_items' => $origFree,
                'previously_returned_quantity' => $ret['quantity'],
                'previously_returned_free' => $ret['free_items'],
                'available_quantity' => $availPaid,
                'available_free_items' => $availFree,
                'quantity' => $defaultsPaid,
                'free_items' => $defaultsFree,
                'max_quantity' => $availPaid,
                'max_free_items' => $availFree,
                'unit_price' => (float) $product->price,
                'vat_percent' => (float) $product->vat,
                'discount_percent' => (float) $lineDiscount,
                'apply_company_discount' => (bool) ($product->apply_company_discount ?? true),
                'price_after_discount' => $previewItem->priceAfterDiscount(),
                'vat' => $previewItem->vatAmount(),
                'total' => $previewItem->lineTotal(),
                'return_history' => $history,
                'fully_returned' => $availPaid <= 0 && $availFree <= 0,
            ];
        }

        $collection = collect($lines);

        $totals = [
            'subtotal_before_discount' => $collection->sum(fn (array $l) => $l['unit_price'] * (int) $l['quantity']),
            'discount_amount' => 0.0,
            'before_vat' => $collection->sum('price_after_discount'),
            'vat' => $collection->sum('vat'),
            'grand' => $collection->sum('total'),
        ];
        $totals['discount_amount'] = max(0, $totals['subtotal_before_discount'] - $totals['before_vat']);

        return [
            'lines' => $lines,
            'company_discount_percent' => $discount,
            'totals' => $totals,
            'company_name' => $invoice->company->name ?? '',
            'invoice_number' => $invoice->invoice_number,
            'trn' => $invoice->company->trn ?? '',
        ];
    }

    public function store(\Illuminate\Http\Request $request): CreditNote
    {
        $invoice = Invoice::with('company')->findOrFail($request->invoice_id);
        $products = $this->invoiceService->show($invoice->id);
        if ($products->isEmpty()) {
            throw ValidationException::withMessages([
                'invoice_id' => 'This invoice has no products in its date range.',
            ]);
        }

        $byId = $products->keyBy('id');
        $discount = (float) ($invoice->company->discount ?? 0);

        return DB::transaction(function () use ($request, $invoice, $byId, $discount) {
            $creditNote = CreditNote::create([
                'invoice_id' => $invoice->id,
                'credit_note_number' => $request->credit_note_number,
                'note_date' => $request->note_date,
                'reason' => $request->reason,
            ]);

            foreach ($request->line_items as $row) {
                $productId = (int) ($row['product_id'] ?? 0);
                $product = $byId->get($productId);
                if (! $product) {
                    throw ValidationException::withMessages([
                        'line_items' => 'One or more lines reference products that are not on this invoice.',
                    ]);
                }

                $qty = (int) ($row['quantity'] ?? 0);
                $free = (int) ($row['free_items'] ?? 0);
                if ($qty <= 0 && $free <= 0) {
                    continue;
                }

                $productModel = Product::lockForUpdate()->find($productId);
                if (! $productModel) {
                    throw ValidationException::withMessages([
                        'line_items' => 'Product could not be loaded for stock update.',
                    ]);
                }

                $ret = $this->returnedTotalsForInvoiceProduct($invoice->id, $productId);
                $origPaid = (int) $product->quantity;
                $origFree = (int) ($product->free_items ?? 0);
                $availPaid = max(0, $origPaid - $ret['quantity']);
                $availFree = max(0, $origFree - $ret['free_items']);

                if ($qty > $availPaid || $free > $availFree) {
                    throw ValidationException::withMessages([
                        'line_items' => 'Return quantities exceed what is still available for this invoice. Refresh the page and try again.',
                    ]);
                }

                $lineNote = isset($row['line_note']) ? Str::limit((string) $row['line_note'], 500, '') : null;
                if ($lineNote === '') {
                    $lineNote = null;
                }

                $item = CreditNoteItem::create([
                    'credit_note_id' => $creditNote->id,
                    'product_id' => $productModel->id,
                    'item_name' => $productModel->name,
                    'quantity' => $qty,
                    'free_items' => $free,
                    'unit_price' => $productModel->price,
                    'vat_percent' => $productModel->vat,
                    'company_discount_percent' => $product->lineDiscountPercent($discount),
                    'line_note' => $lineNote,
                ]);

                InventoryMovement::create([
                    'product_id' => $productModel->id,
                    'credit_note_id' => $creditNote->id,
                    'credit_note_item_id' => $item->id,
                    'movement_type' => 'credit_return',
                    'quantity_paid_delta' => $qty,
                    'quantity_free_delta' => $free,
                    'note' => $lineNote,
                ]);

                $productModel->increment('stock_quantity', $qty + $free);
            }

            $creditNote->load('items');
            if ($creditNote->items->isEmpty()) {
                $creditNote->delete();
                throw ValidationException::withMessages([
                    'line_items' => 'Credit at least one paid or free unit on at least one line.',
                ]);
            }

            return $creditNote->fresh(['invoice.company', 'items']);
        });
    }

    public function update(int $id, \Illuminate\Http\Request $request): CreditNote
    {
        $creditNote = $this->find($id);
        $creditNote->update([
            'credit_note_number' => $request->credit_note_number,
            'note_date' => $request->note_date,
            'reason' => $request->reason,
        ]);

        return $creditNote->fresh(['invoice.company', 'items']);
    }

    public function delete(int $id): CreditNote
    {
        return DB::transaction(function () use ($id) {
            $creditNote = CreditNote::with('items')->findOrFail($id);

            foreach ($creditNote->items as $item) {
                if (! $item->product_id) {
                    continue;
                }
                $product = Product::lockForUpdate()->find($item->product_id);
                if ($product) {
                    $delta = (int) $item->quantity + (int) ($item->free_items ?? 0);
                    $product->stock_quantity = max(0, (int) $product->stock_quantity - $delta);
                    $product->save();
                }
            }

            $creditNote->delete();

            return $creditNote;
        });
    }
}
