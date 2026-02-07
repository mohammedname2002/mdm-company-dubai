@extends('layouts.master')
@section('title')
    Invoice
@endsection

@section('css')
    <style>
        /* Ensure table borders appear in print */
        .table-bordered>:not(caption)>*>* {
            border-width: 1px !important;
        }

        /* Style table header for print */
        .aaa {
            background-color: #1f628e !important;
            color: white !important;
            font-weight: normal !important;
            font-size: 9px !important;
        }

        /* Print-specific styles */
        @media print {
            .aaa {
                font-size: 9px !important;
            }

            body {
                font-family: Arial, sans-serif;
                background-color: rgb(0, 0, 0);
            }

            .tax {
                font-weight: 600;
            }

            /* Hide the print button during printing */
            .d-print-none {
                display: none;
            }

            /* Ensure table borders are applied correctly */


            .table-bordered th,
            .table-bordered td {
                border: 1px solid #000000 !important;
                padding: 8px !important;
            }

            .table th,
            .table-bordered th {
                font-size: 8px !important;
            }

            /* Table header style for print */
            .aaa {
                background-color: #1f628e !important;
                color: rgb(0, 0, 0) !important;
            }

            /* Prevent the table from breaking across pages */
            table {
                page-break-inside: avoid;
                font-style: black;

            }

            tr,
            td,
            th {
                page-break-inside: avoid !important;
            }

            .table th,
            .table td {
                font-size: 8px !important;
                font-style: black;
            }

            /* Ensure footer appears well in print */
            .footer {
                font-size: 12px;
                font-style: black;
                text-align: left;
                padding-top: 10px;
            }

            .product-name-cell {
                font-size: 8px !important;
                width: 287px !important;

            }

        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Invoice</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <!-- Logo & title -->
                        <div class="clearfix">
                            <div class="float-start">
                                <div class="auth-logo">
                                    <img src="{{ asset('assets/images/mdm.png') }}" alt="" height="70">
                                </div>
                            </div>

                            {{--  <div class="float-end">
                                <img src="{{ asset('assets/images/oge.png') }}" alt="" height="70">
                            </div>  --}}
                        </div>
                        <div style="text-align:center" class="auth-logo">
                            <h2 id="tax" style="font-weight:600 !important">Tax Invoice </h2>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mt-3">
                                    <p><b>{{ $invoiceName->company->name }}</b></p>

                                    @if ($invoiceName->company->trn)
                                        <p><b>TRN: {{ $invoiceName->company->trn }}</b></p>
                                    @endif

                                    @if ($invoiceName->company->address)
                                        <p><b>Address: {{ $invoiceName->company->address }}</b></p>
                                    @endif
                                    @if ($invoiceName->company->phone)
                                        <p><b>Phone: {{ $invoiceName->company->phone }}</b></p>
                                    @endif

                                </div>
                            </div><!-- end col -->
                            <div class="col-md-4 offset-md-2">
                                <div class="mt-3 float-end">
                                    <p><strong>Order Date:</strong>
                                        <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $invoiceName->date_of_create)->format('Y-m-d') }}</span>
                                    </p>
                                    <p><strong>Order Status:</strong>
                                        @if ($invoiceName->status == 'paid')
                                            <span class="badge bg-success">{{ $invoiceName->status }}</span>
                                        @else
                                            <span class="badge bg-danger">{{ $invoiceName->status }}</span>
                                        @endif
                                    </p>
                                    <p><strong>Order No.:</strong> <span>{{ $invoiceName->invoice_number }}</span></p>
                                </div>
                            </div><!-- end col -->
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-9">
                            <div class="table table-bordered border-black mb-0">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th class="aaa">Item</th>
                                            <th class="aaa">Price</th> <!-- New: Price before discount -->
                                            <th class="aaa">VAT</th> <!-- New: VAT before discount -->
                                            @if ($invoiceName->company->discount == 0)
                                                <th class="aaa">Price </th>
                                            @else
                                                <th class="aaa">Price with Discount
                                                    ({{ $invoiceName->company->discount }}%)</th>
                                            @endif
                                            <th class="aaa">Vat </th>
                                            @if ($invoiceName->company->discount == 0)
                                                <th class="aaa">Price with Vat</th>
                                            @else
                                                <th class="aaa">Price with Discount
                                                    ({{ $invoiceName->company->discount }}%) &amp; Vat</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $subTotalPrice = 0;
                                            $subTotalVat = 0;
                                            $subTotalDiscounted = 0;
                                            $subTotalDiscountedVat = 0;
                                            $subTotalAmount = 0;
                                        @endphp
                                        @foreach ($products as $product)
                                            @php
                                                $productPriceWithDiscont =
                                                    $product->price * $product->quantity -
                                                    $product->price *
                                                        $product->quantity *
                                                        ($invoiceName->company->discount / 100);
                                                $productPriceWithDiscontAndVat =
                                                    $productPriceWithDiscont +
                                                    ($productPriceWithDiscont * $product->vat) / 100;
                                                $productWithVat = ($productPriceWithDiscont * $product->vat) / 100;
                                                $unitVat = ($product->price * $product->vat) / 100;
                                                $unitPriceAfterDiscount =
                                                    $product->price -
                                                    $product->price * ($invoiceName->company->discount / 100);
                                                $unitVatAfterDiscount = ($unitPriceAfterDiscount * $product->vat) / 100;

                                                // Subtotals
                                                $subTotalPrice += $product->price * $product->quantity;
                                                $subTotalVat += $unitVat * $product->quantity;
                                                $subTotalDiscounted += $productPriceWithDiscont;
                                                $subTotalDiscountedVat += $productWithVat;
                                                $subTotalAmount += $productPriceWithDiscontAndVat;
                                            @endphp
                                            <tr>
                                                <td>{{ $loop->index + 1 }}</td>
                                                <td class="product-name-cell">
                                                    ({{ $product->quantity }}@if (($product->free_items ?? 0) > 0)
                                                        +{{ $product->free_items }}
                                                    @endif)
                                                    {{ $product->name }}</td>
                                                <td>{{ number_format($product->price * $product->quantity, 2) }}</td>
                                                <td>{{ number_format($unitVat * $product->quantity, 2) }}</td>
                                                @if ($invoiceName->company->discount == 0)
                                                    <td>{{ number_format($product->price * $product->quantity, 2) }}</td>
                                                @else
                                                    <td>{{ number_format($productPriceWithDiscont, 2) }}</td>
                                                @endif
                                                <td>{{ number_format($productWithVat, 2) }}</td>
                                                <td>{{ number_format($productPriceWithDiscontAndVat, 2) }}</td>
                                            </tr>
                                        @endforeach
                                        <!-- Sub Total Row -->
                                        <tr style="font-weight: bold; background: #f9f9f9;">
                                            <td colspan="2" style="text-align:right;">Sub Total</td>
                                            <td>{{ number_format($subTotalPrice, 2) }}</td>
                                            <td>{{ number_format($subTotalVat, 2) }}</td>
                                            <td>{{ number_format($subTotalDiscounted, 2) }}</td>
                                            <td>{{ number_format($subTotalDiscountedVat, 2) }}</td>
                                            <td>{{ number_format($subTotalAmount, 2) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-6">
                            <div class="clearfix pt-5">
                                <h4 class="text-muted">Notes:</h4>
                                <small class="text-muted"></small>
                            </div>
                        </div>


                        <div class="col-sm-6">
                            <div class="float-end" style="margin-right: 200px">
                                <h2 style="font-size: 13px;"><b>Total:</b> <span>{{ $totalTotal }}</span></h2>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Box -->
                    <div class="row mt-4">
                        <div class="col-12 d-flex justify-content-end">
                            <div class="border p-3 bg-light" style="border-radius: 8px; width: 300px;">
                                <div class="text-end">
                                    <p><strong>Address:</strong> Office 153-101 King Mohammed Abdulaziz Mohammed bin Faris -
                                        Deira - Al Murar</p>
                                    <p><strong>TRN:</strong> 104718581200003</p>
                                    <p><strong>Website:</strong> <a href="https://example.com" target="_blank">MDM.com</a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Print Button -->
                    <div class="mt-4 mb-1">
                        <div class="text-end d-print-none">
                            <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-printer me-1"></i> Print
                            </a>
                        </div>
                    </div>

                    <!-- Download PDF Button -->
                    <div class="text-end d-print-none">
                        <form action="{{ route('download', $invoiceName->id) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-download me-1"></i> Download PDF
                            </button>
                        </form>
                    </div>

                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div> <!-- end row -->

    </div> <!-- container -->
@endsection

@section('scripts')
@endsection
