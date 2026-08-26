@extends('layouts.master')
@section('title')
    Print Invoices — {{ $company?->name ?? 'All companies' }}
@endsection

@section('css')
    <style>
        .table-bordered>:not(caption)>*>* {
            border-width: 1px !important;
        }

        .aaa {
            background-color: #1f628e !important;
            color: white !important;
            font-weight: normal !important;
            font-size: 9px !important;
        }

        .print-invoice-block {
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px dashed #dee2e6;
        }

        @media print {
            .aaa {
                font-size: 9px !important;
            }

            body {
                font-family: Arial, sans-serif;
            }

            .d-print-none {
                display: none !important;
            }

            .print-invoice-block {
                page-break-after: always;
                margin-bottom: 0;
                padding-bottom: 0;
                border-bottom: none;
            }

            .print-invoice-block:last-child {
                page-break-after: auto;
            }

            .table-bordered th,
            .table-bordered td {
                border: 1px solid #000000 !important;
                padding: 8px !important;
            }

            .table th,
            .table td {
                font-size: 8px !important;
            }

            .product-name-cell {
                font-size: 8px !important;
                width: 287px !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid d-print-none">
        <div class="row mb-3">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="page-title mb-0">
                        Print all invoices — {{ $company?->name ?? 'All companies' }}
                        @if (!empty($filterMonth) || !empty($filterYear))
                            <span class="text-muted fs-6">
                                (
                                @if (!empty($filterMonth))
                                    {{ \Carbon\Carbon::create()->month((int) $filterMonth)->format('F') }}
                                @endif
                                @if (!empty($filterYear))
                                    {{ $filterYear }}
                                @endif
                                )
                            </span>
                        @endif
                        <span class="badge bg-primary ms-2">{{ count($invoices) }} invoice(s)</span>
                    </h4>
                    <div>
                        <a href="{{ route('invoice.index') }}" class="btn btn-secondary waves-effect waves-light">
                            <i class="mdi mdi-arrow-left me-1"></i> Back
                        </a>
                        <button type="button" onclick="window.print()" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-printer me-1"></i> Print all
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        @foreach ($invoices as $data)
            <div class="print-invoice-block">
                @include('invoice.partials.print-single', ['data' => $data])
            </div>
        @endforeach
    </div>
@endsection
