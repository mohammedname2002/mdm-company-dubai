@extends('layouts.master')
@section('title')
    Credit Note
@endsection
@section('css')
    <style>
        .cn-meta-table td,
        .cn-meta-table th {
            border: 1px solid #000 !important;
            padding: 8px;
        }

        .cn-items thead th {
            background-color: #555 !important;
            color: #fff !important;
            border: 1px solid #333 !important;
        }

        .cn-items td {
            border: 1px solid #000 !important;
        }

        @media print {
            .d-print-none {
                display: none !important;
            }

            .cn-items thead th {
                background-color: #555 !important;
                color: #fff !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
@endsection
@section('content')
    @php
        $invoice = $creditNote->invoice;
        $company = $invoice->company ?? null;
        $issuerTrn = config('mdm.issuer_trn');
        $subtotalExclDiscount = $creditNote->items->sum(fn ($i) => (float) $i->unit_price * (int) $i->quantity);
        $totalBeforeVat = $creditNote->items->sum(fn ($i) => $i->priceAfterDiscount());
        $discountAmount = max(0, $subtotalExclDiscount - $totalBeforeVat);
        $totalVat = $creditNote->items->sum(fn ($i) => $i->vatAmount());
        $grandTotal = $creditNote->items->sum(fn ($i) => $i->lineTotal());
        $vatPercents = $creditNote->items->pluck('vat_percent')->unique();
        $vatLabel = $vatPercents->count() === 1 ? 'VAT (' . rtrim(rtrim(number_format((float) $vatPercents->first(), 2), '0'), '.') . '%)' : 'VAT';
    @endphp

    <div class="container-fluid">
        <div class="row d-print-none">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Credit Note</h4>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center mb-4 g-2">
                            <div class="col-auto">
                                <img src="{{ asset(config('mdm.logo_path')) }}" alt="MDM" width="52" style="width: 52px; max-width: 52px; max-height: 50px; height: auto;">
                            </div>
                            <div class="col text-center">
                                <div class="fw-bold" style="font-size: 1.35rem;">Credit Note ({{ $company->name ?? '' }})</div>
                            </div>
                            <div class="col-auto" style="min-width: 3.75rem;" aria-hidden="true"></div>
                        </div>

                        <table class="table cn-meta-table mb-4" style="max-width: 520px;">
                            <tbody>
                                <tr>
                                    <td><strong>Credit Note No</strong></td>
                                    <td>{{ $creditNote->credit_note_number }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Date</strong></td>
                                    <td>{{ $creditNote->note_date->format('Y-m-d') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Original Invoice</strong></td>
                                    <td>{{ $invoice->invoice_number ?? '' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>TRN</strong></td>
                                    <td>{{ $issuerTrn }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Reason</strong></td>
                                    <td>{{ $creditNote->reason ?: '—' }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="table-responsive">
                            <table class="table cn-items mb-4">
                                <thead>
                                    <tr>
                                        <th>Item</th>
                                        <th class="text-end">Qty (paid / free)</th>
                                        <th class="text-end">Unit price</th>
                                        <th class="text-end">Price After Discount</th>
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
                                                {{ (int) $item->quantity }}
                                                @if (($item->free_items ?? 0) > 0)
                                                    / {{ (int) $item->free_items }} free
                                                @endif
                                            </td>
                                            <td class="text-end">{{ number_format((float) $item->unit_price, 2) }}</td>
                                            <td class="text-end">-{{ number_format($item->priceAfterDiscount(), 2) }}</td>
                                            <td class="text-end">-{{ number_format($item->vatAmount(), 2) }}</td>
                                            <td class="text-end">-{{ number_format($item->lineTotal(), 2) }}</td>
                                            <td>{{ $item->line_note ? $item->line_note : '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Subtotal (excl. discount):</strong> -{{ number_format($subtotalExclDiscount, 2) }} AED</p>
                                <p><strong>Company discount:</strong> -{{ number_format($discountAmount, 2) }} AED</p>
                                <p><strong>After discount (before VAT):</strong> -{{ number_format($totalBeforeVat, 2) }} AED</p>
                                <p><strong>{{ $vatLabel }}:</strong> -{{ number_format($totalVat, 2) }} AED</p>
                                <p class="mb-0 fw-bold"><strong>Total Amount:</strong> -{{ number_format($grandTotal, 2) }} AED</p>
                            </div>
                        </div>

                        <div class="mt-4 mb-1 d-print-none">
                            <div class="text-end">
                                <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light">
                                    <i class="mdi mdi-printer me-1"></i> Print
                                </a>
                                <form action="{{ route('credit-note.download', $creditNote->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                                        <i class="mdi mdi-download me-1"></i> Download PDF
                                    </button>
                                </form>
                                <a href="{{ route('credit-note.index') }}" class="btn btn-light">Back</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
@endsection
