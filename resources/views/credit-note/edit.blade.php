@extends('layouts.master')
@section('title')
@endsection
@section('css')
@endsection
@section('title_page')
    الرئيسية
@endsection
@section('title_page2')
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Edit Credit Note</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <p class="text-muted">Original invoice and line items are fixed. You can change the credit note number, date, and reason only.</p>

                        <form action="{{ route('credit-note.update', $creditNote->id) }}" method="POST">
                            @csrf

                            <div class="row mb-3">
                                <div class="col-lg-6">
                                    <label class="form-label">Original invoice</label>
                                    <input type="text" class="form-control" value="{{ $creditNote->invoice->invoice_number ?? '' }}" disabled>
                                </div>
                                <div class="col-lg-6">
                                    <label class="form-label">Company</label>
                                    <input type="text" class="form-control" value="{{ $creditNote->invoice->company->name ?? '' }}" disabled>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">Credit note number</label>
                                    <input type="text" name="credit_note_number" class="form-control" value="{{ old('credit_note_number', $creditNote->credit_note_number) }}" required>
                                    @error('credit_note_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">Credit note date</label>
                                    <input type="date" name="note_date" class="form-control" value="{{ old('note_date', $creditNote->note_date->format('Y-m-d')) }}" required>
                                    @error('note_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="col-lg-4 mb-3">
                                    <label class="form-label">Reason</label>
                                    <input type="text" name="reason" class="form-control" value="{{ old('reason', $creditNote->reason) }}">
                                    @error('reason')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-sm">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-end">Qty</th>
                                            <th class="text-end">Unit</th>
                                            <th class="text-end">After disc.</th>
                                            <th class="text-end">VAT</th>
                                            <th class="text-end">Total</th>
                                            <th>Note</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($creditNote->items as $item)
                                            <tr>
                                                <td>{{ $item->item_name }}</td>
                                                <td class="text-end">
                                                    {{ (int) $item->quantity }}@if (($item->free_items ?? 0) > 0)
                                                        / {{ (int) $item->free_items }} f @endif
                                                </td>
                                                <td class="text-end">{{ number_format((float) $item->unit_price, 2) }}</td>
                                                <td class="text-end">-{{ number_format($item->priceAfterDiscount(), 2) }}</td>
                                                <td class="text-end">-{{ number_format($item->vatAmount(), 2) }}</td>
                                                <td class="text-end">-{{ number_format($item->lineTotal(), 2) }}</td>
                                                <td>{{ $item->line_note ?: '—' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>
                            <a href="{{ route('credit-note.index') }}" class="btn btn-light">Back</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
