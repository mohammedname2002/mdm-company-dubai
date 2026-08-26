<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use App\Services\CreditNoteService;
use App\Services\InvoiceService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreditNoteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $creditNoteId = $this->route('id');

        $rules = [
            'credit_note_number' => [
                'required',
                'string',
                'max:100',
                Rule::unique('credit_notes', 'credit_note_number')->ignore($creditNoteId),
            ],
            'note_date' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:500'],
        ];

        if ($this->route()->named('credit-note.store')) {
            $rules['invoice_id'] = ['required', 'exists:invoices,id'];
            $rules['line_items'] = ['required', 'array', 'min:1'];
            $rules['line_items.*.product_id'] = ['required', 'integer', 'exists:products,id'];
            $rules['line_items.*.quantity'] = ['required', 'integer', 'min:0'];
            $rules['line_items.*.free_items'] = ['required', 'integer', 'min:0'];
            $rules['line_items.*.line_note'] = ['nullable', 'string', 'max:500'];
        }

        return $rules;
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            if (! $this->route()->named('credit-note.store')) {
                return;
            }

            $invoiceId = $this->input('invoice_id');
            if (! $invoiceId) {
                return;
            }

            $invoice = Invoice::find($invoiceId);
            if (! $invoice) {
                return;
            }

            $products = app(InvoiceService::class)->show($invoice->id);
            if ($products->isEmpty()) {
                $validator->errors()->add('invoice_id', 'This invoice has no products in its date range.');

                return;
            }

            $allowed = $products->keyBy('id');
            /** @var CreditNoteService $creditNoteService */
            $creditNoteService = app(CreditNoteService::class);
            $rows = $this->input('line_items', []);

            $submittedIds = collect($rows)->pluck('product_id')->map(fn ($id) => (int) $id);
            if ($submittedIds->count() !== $submittedIds->unique()->count()) {
                $validator->errors()->add('line_items', 'Duplicate product lines were submitted.');

                return;
            }

            $submittedIds = $submittedIds->sort()->values();
            $expectedIds = $allowed->keys()->map(fn ($id) => (int) $id)->sort()->values();
            if ($submittedIds->count() !== $expectedIds->count() || $submittedIds->diff($expectedIds)->isNotEmpty()) {
                $validator->errors()->add('line_items', 'Line items must include every product on this invoice exactly once.');

                return;
            }

            $credited = false;

            foreach ($rows as $idx => $row) {
                $pid = (int) ($row['product_id'] ?? 0);

                $product = $allowed->get($pid);
                if (! $product) {
                    $validator->errors()->add("line_items.$idx.product_id", 'Invalid product for this invoice.');

                    continue;
                }

                $qty = (int) ($row['quantity'] ?? 0);
                $free = (int) ($row['free_items'] ?? 0);

                if ($qty < 0 || $free < 0) {
                    $validator->errors()->add("line_items.$idx.quantity", 'Quantities cannot be negative.');

                    continue;
                }

                $ret = $creditNoteService->returnedTotalsForInvoiceProduct((int) $invoiceId, $pid);
                $origPaid = (int) $product->quantity;
                $origFree = (int) ($product->free_items ?? 0);
                $availPaid = max(0, $origPaid - $ret['quantity']);
                $availFree = max(0, $origFree - $ret['free_items']);

                if ($availPaid <= 0 && $availFree <= 0) {
                    if ($qty > 0 || $free > 0) {
                        $validator->errors()->add("line_items.$idx.quantity", 'This line is already fully returned on prior credit notes.');
                    }

                    continue;
                }

                if ($qty > $availPaid) {
                    $validator->errors()->add("line_items.$idx.quantity", "Paid return quantity cannot exceed {$availPaid} (remaining on invoice).");
                }
                if ($free > $availFree) {
                    $validator->errors()->add("line_items.$idx.free_items", "Free return quantity cannot exceed {$availFree} (remaining on invoice).");
                }

                if ($qty === 0 && $free === 0) {
                    continue;
                }

                if ($qty === 0 && $free > 0) {
                    $credited = true;

                    continue;
                }

                if ($qty > 0) {
                    $credited = true;
                }
            }

            if (! $credited) {
                $validator->errors()->add('line_items', 'Credit at least one paid or free unit on at least one line.');
            }
        });
    }
}
