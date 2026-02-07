<?php

namespace App\Services;

use App\Models\Company;


use App\Models\Invoice;
use App\Models\Product;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\LengthAwarePaginator;

class InvoiceService
{
    public static function make()
    {
        return new self();
    }
    public function index($relations = [], $count = [], $params = ['*'], $paginate = 10, $search = null): LengthAwarePaginator
    {
        if ($paginate > 50) {
            $paginate = 50;
        }

        $query = Invoice::with($relations)
            ->select($params)
            ->withCount($count);

        if ($search) {
            $query->where('name', 'LIKE', '%' . $search . '%');
        }

        return $query->paginate($paginate);
    }
    public function find($id, $params = ['*'], $relations = [], $count = [])
    {

        return findByid(Invoice::class, $id, $relations, $params, $count);
    }
    public function create()
    {
        $companies = Company::all();
        return $companies;
    }
public function store($request)
{
    // Determine next invoice number (never below 53)
    if (Cache::has('invoice_number')) {
        $nextInvoiceNumber = (int) Cache::get('invoice_number');
    } else {
        $lastInvoice = Invoice::orderByDesc('id')->first();
        if ($lastInvoice && preg_match('/INV-\d{4}-(\d+)/', $lastInvoice->invoice_number, $matches)) {
            $lastNumber = (int) $matches[1];
            // If DB last number is less than 53 start from 53, otherwise increment
            $nextInvoiceNumber = $lastNumber >= 53 ? $lastNumber + 1 : 53;
        } else {
            $nextInvoiceNumber = 53;
        }
    }

    // safety: ensure we never use a value < 53
    if ($nextInvoiceNumber < 53) {
        $nextInvoiceNumber = 53;
    }

    // Generate the invoice number
    $invoiceNumber = 'INV-' . date('Y') . '-' . str_pad($nextInvoiceNumber, 6, '0', STR_PAD_LEFT);

    $invoice = Invoice::create([
        'company_id' => $request->company_id,
        'from' => $request->from,
        'to' => $request->to,
        'invoice_number' => $invoiceNumber,
        'status' => $request->status,
        'date_of_create' => Carbon::now(),
    ]);

    // Cache next value
    Cache::forever('invoice_number', $nextInvoiceNumber + 1);

    return $invoice;
}
    public function update($id, $request)
    {
        $invoice = $this->find($id, ['*']);
        $invoice->update([
            'company_id' => $request->company_id,
            'from' => $request->from,
            'to' => $request->to,
            'invoice_number' => $invoice->invoice_number, // preserve existing number
            'status' => $request->status,
            'date_of_create' => $invoice->date_of_create, // preserve original creation date
        ]);
        return $invoice;
    }

    public function delete($id)
    {


        $invoice = $this->find($id, ['*']);
        $invoice->delete();
        return $invoice;
    }

    public function show($id)
    {
        $invoice = $this->find($id, ['*']);
        $companyId = $invoice->company->id;

        $invoices = Product::whereBetween('date_of_create', [$invoice->from, $invoice->to])
            ->where('company_id', $companyId) // Add this line to filter by company
            ->with('company') // To include the company relation
            ->get();

        return $invoices;
    }
}
