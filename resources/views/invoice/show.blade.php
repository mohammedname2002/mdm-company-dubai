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

            /* 3-page print layout */
            body.multi-page-print table {
                page-break-inside: auto;
            }

            body.multi-page-print tr,
            body.multi-page-print td,
            body.multi-page-print th {
                page-break-inside: auto !important;
            }

            body.multi-page-print .invoice-normal-print {
                display: none !important;
            }

            body.multi-page-print .invoice-multi-print {
                display: block !important;
            }

            body.multi-page-print .print-invoice-page {
                page-break-after: always;
            }

            body.multi-page-print .print-invoice-page:last-child {
                page-break-after: auto;
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
    <style>
        .invoice-multi-print {
            display: none;
        }
    </style>
@endsection

@section('content')
    @php
        $subTotalPrice = 0;
        $subTotalVat = 0;
        $subTotalDiscounted = 0;
        $subTotalDiscountedVat = 0;
        $subTotalAmount = 0;
        foreach ($products as $product) {
            $companyDisc = (float) ($invoiceName->company->discount ?? 0);
            $productPriceWithDiscont = $product->linePriceAfterDiscount($companyDisc);
            $productPriceWithDiscontAndVat =
                $productPriceWithDiscont + ($productPriceWithDiscont * $product->vat) / 100;
            $unitVat = ($product->price * $product->vat) / 100;
            $productWithVat = ($productPriceWithDiscont * $product->vat) / 100;

            $subTotalPrice += $product->price * $product->quantity;
            $subTotalVat += $unitVat * $product->quantity;
            $subTotalDiscounted += $productPriceWithDiscont;
            $subTotalDiscountedVat += $productWithVat;
            $subTotalAmount += $productPriceWithDiscontAndVat;
        }

        $printPage1Products = $products->take(2);
        $printPage2Products = $products->slice(2, 1);
        $printPage3Products = $products->slice(3);
        $rowStarts = [0, 2, 3];

        $calcPageTotals = function ($pageProducts) use ($invoiceName) {
            $companyDisc = (float) ($invoiceName->company->discount ?? 0);
            $subTotalPrice = 0;
            $subTotalVat = 0;
            $subTotalDiscounted = 0;
            $subTotalDiscountedVat = 0;
            $subTotalAmount = 0;
            foreach ($pageProducts as $product) {
                $productPriceWithDiscont = $product->linePriceAfterDiscount($companyDisc);
                $productPriceWithDiscontAndVat =
                    $productPriceWithDiscont + ($productPriceWithDiscont * $product->vat) / 100;
                $unitVat = ($product->price * $product->vat) / 100;
                $productWithVat = ($productPriceWithDiscont * $product->vat) / 100;

                $subTotalPrice += $product->price * $product->quantity;
                $subTotalVat += $unitVat * $product->quantity;
                $subTotalDiscounted += $productPriceWithDiscont;
                $subTotalDiscountedVat += $productWithVat;
                $subTotalAmount += $productPriceWithDiscontAndVat;
            }

            return compact(
                'subTotalPrice',
                'subTotalVat',
                'subTotalDiscounted',
                'subTotalDiscountedVat',
                'subTotalAmount'
            );
        };

        $page1Totals = $calcPageTotals($printPage1Products);
        $page2Totals = $calcPageTotals($printPage2Products);
        $page3Totals = $calcPageTotals($printPage3Products);
    @endphp
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
                    <div class="invoice-normal-print">
                        @include('invoice.partials.print-header')
                    </div>

                    <div class="row mt-3 invoice-normal-print">
                        <div class="col-9">
                            <div class="table table-bordered border-black mb-0">
                                @include('invoice.partials.items-table', [
                                    'products' => $products,
                                    'invoiceName' => $invoiceName,
                                    'showSubtotal' => true,
                                    'subTotalPrice' => $subTotalPrice,
                                    'subTotalVat' => $subTotalVat,
                                    'subTotalDiscounted' => $subTotalDiscounted,
                                    'subTotalDiscountedVat' => $subTotalDiscountedVat,
                                    'subTotalAmount' => $subTotalAmount,
                                ])
                            </div>
                        </div>
                    </div>

                    {{-- 3-page print: page 1 = 2 products, page 2 = 1 product, page 3 = last products --}}
                    <div class="invoice-multi-print">
                        {{-- Page 1: header + first 2 products + page total + footer --}}
                        <div class="print-invoice-page">
                            @include('invoice.partials.print-header')
                            <div class="row mt-3">
                                <div class="col-9">
                                    <div class="table table-bordered border-black mb-0">
                                        @include('invoice.partials.items-table', array_merge([
                                            'products' => $printPage1Products,
                                            'invoiceName' => $invoiceName,
                                            'rowStart' => $rowStarts[0],
                                            'showSubtotal' => true,
                                            'subtotalLabel' => 'Total',
                                        ], $page1Totals))
                                    </div>
                                </div>
                            </div>
                            @include('invoice.partials.print-footer')
                        </div>

                        {{-- Page 2: header + 3rd product + page total + footer --}}
                        <div class="print-invoice-page">
                            @include('invoice.partials.print-header')
                            <div class="row mt-3">
                                <div class="col-9">
                                    <div class="table table-bordered border-black mb-0">
                                        @include('invoice.partials.items-table', array_merge([
                                            'products' => $printPage2Products,
                                            'invoiceName' => $invoiceName,
                                            'rowStart' => $rowStarts[1],
                                            'showSubtotal' => true,
                                            'subtotalLabel' => 'Total',
                                        ], $page2Totals))
                                    </div>
                                </div>
                            </div>
                            @include('invoice.partials.print-footer')
                        </div>

                        {{-- Page 3: last products + page total + footer --}}
                        <div class="print-invoice-page">
                            @include('invoice.partials.print-header')
                            <div class="row mt-3">
                                <div class="col-9">
                                    <div class="table table-bordered border-black mb-0">
                                        @include('invoice.partials.items-table', array_merge([
                                            'products' => $printPage3Products,
                                            'invoiceName' => $invoiceName,
                                            'rowStart' => $rowStarts[2],
                                            'showSubtotal' => true,
                                            'subtotalLabel' => 'Total',
                                        ], $page3Totals))
                                    </div>
                                </div>
                            </div>
                            @include('invoice.partials.print-footer')
                        </div>
                    </div>

                    <div class="row invoice-normal-print">
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
                    <div class="invoice-normal-print">
                        @include('invoice.partials.print-footer')
                    </div>

                    <!-- Print Button -->
                    <div class="mt-4 mb-1">
                        <div class="text-end d-print-none">
                            <a href="javascript:window.print()" class="btn btn-primary waves-effect waves-light">
                                <i class="mdi mdi-printer me-1"></i> Print
                            </a>
                            <button type="button" id="btn-print-3page"
                                class="btn btn-outline-primary waves-effect waves-light ms-1"
                                title="Page 1: 2 products · Page 2: 1 product · Page 3: last products (each page has its own total)">
                                <i class="mdi mdi-printer-settings me-1"></i> Print (3-page layout)
                            </button>
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

    <script>
        (function () {
            var btn = document.getElementById('btn-print-3page');
            if (!btn) return;

            btn.addEventListener('click', function () {
                document.body.classList.add('multi-page-print');
                setTimeout(function () {
                    window.print();
                }, 150);
            });

            window.addEventListener('afterprint', function () {
                document.body.classList.remove('multi-page-print');
            });
        })();
    </script>
@endsection
