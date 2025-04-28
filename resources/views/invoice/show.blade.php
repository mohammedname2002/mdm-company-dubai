@extends('layouts.master')
@section('title') Invoice @endsection

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
    }

    /* Print-specific styles */
    @media print {
        body {
            font-family: Arial, sans-serif;
            background-color: rgb(0, 0, 0);
        }
        .tax{
            font-weight: 600;
        }

        /* Hide the print button during printing */
        .d-print-none {
            display: none;
        }

        /* Ensure table borders are applied correctly */


        .table-bordered th, .table-bordered td {
            border: 1px solid #000000 !important;
            padding: 8px !important;
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

        tr, td, th {
            page-break-inside: avoid !important;
        }

        .table th, .table td {
            font-size: 10px !important;
            font-style: black;
        }

        /* Ensure footer appears well in print */
        .footer {
            font-size: 12px;
            font-style: black;
            text-align: left;
            padding-top: 10px;
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
                                <img src="{{ asset('assets/images/logo-light.jpg') }}" alt="" height="50">
                            </div>
                        </div>

                        <div class="float-end">
                            <img src="{{ asset('assets/images/oge.png') }}" alt="" height="70">
                        </div>
                    </div>
                    <div style="text-align:center" class="auth-logo">
                    <h2   id="tax" style="font-weight:600 !important">Tax Invoice </h2>                            </div>

                    <div class="row">

                        <div class="col-md-6">

                            <div class="mt-3">
                                <p><b>{{ $invoiceName->company->name }}</b></p>

                                @if ($invoiceName->company->trn)
                                <p><b>TRN: {{ $invoiceName->company->trn }}</b></p>

                                @endif

                                @if($invoiceName->company->address)
                                <p><b>Address: {{ $invoiceName->company->address }}</b></p>

                                @endif

                            </div>
                        </div><!-- end col -->
                        <div class="col-md-4 offset-md-2">
                            <div class="mt-3 float-end">
                                <p><strong>Order Date:</strong> <span>{{ \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $invoiceName->date_of_create)->format('Y-m-d') }}</span></p>
                                <p><strong>Order Status:</strong>
                                    @if($invoiceName->status == 'paid')
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
                                            {{--  <th class="aaa">Price</th>
                                            <th class="aaa">Vat</th>
                                             <th class="aaa">FOC</th>  --}}

                                            @if($invoiceName->company->discount == 0)
                                            <th class="aaa">Price </th>
                                            @else
                                            <th class="aaa">Price with {{  $invoiceName->company->discount}}% Discount</th>
                                            @endif

                                            <th class="aaa">Vat </th>

                                            @if($invoiceName->company->discount == 0)

                                            <th class="aaa">Price with Vat</th>
                                            @else
                                            <th class="aaa">Price with Discount & Vat</th>
                                            @endif

                                        </tr>
                                    </thead>
                                    <tbody>


                                        @foreach ($products as $product)
                                        <tr>

                                            @php
                                            $productPriceWithDiscont = $product->price * $product->quantity - ($product->price * $product->quantity * ($invoiceName->company->discount / 100)) ;
                                            $productPriceWithDiscontAndVat = $productPriceWithDiscont + ($productPriceWithDiscont * $product->vat / 100) ;
                                            $productWithVat = ($productPriceWithDiscont * $product->vat / 100) ;
                                       @endphp
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $product->name }}</td>
                                            {{--  <td>{{ $product->price * $product->quantity }}</td>
                                            <td>{{ $product->getVatAmount() }}</td>
                                                                                        <td>3</td>  --}}

                                            <td>{{$productPriceWithDiscont }}</td>
                                            <td>{{$productWithVat }}</td>

                                            <td>{{ $productPriceWithDiscontAndVat }}</td>
                                        </tr>


                                        @endforeach
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


                        <div class="col-sm-6" >
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
                                    <p><strong>Address:</strong> Office 153-101 King Mohammed Abdulaziz Mohammed bin Faris - Deira - Al Murar</p>
                                    <p><strong>TRN:</strong> 104718581200003</p>
                                    <p><strong>Website:</strong> <a href="https://example.com" target="_blank">MDM.com</a></p>
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
                        <button onclick="downloadPDF()" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-download me-1"></i> Download PDF
                        </button>
                    </div>

                </div> <!-- end card-body -->
            </div> <!-- end card -->
        </div> <!-- end col -->
    </div> <!-- end row -->

</div> <!-- container -->

@endsection

@section('scripts')
<script>
    function downloadPDF() {
        // Using jsPDF library to generate a PDF
        const { jsPDF } = window.jspdf; // Destructure jsPDF from window.jspdf

        // Create a new jsPDF instance
        const doc = new jsPDF();

        // Select the content to print (in this case, the entire container)
        const content = document.querySelector('.container-fluid'); // Select your container or any section you want

        // Use html method to capture the content and add it to the PDF
        doc.html(content, {
            callback: function (doc) {
                // Save the PDF with a custom filename
                doc.save('invoice.pdf'); // Save the file as 'invoice.pdf'
            },
            margin: [10, 10, 10, 10], // Add margins (top, right, bottom, left)
            x: 10, // Start x position for content
            y: 10, // Start y position for content
            width: 180, // Content width (adjust based on your content size)
            windowWidth: 800 // The width of the window (for responsive handling)
        });
    }
</script>

@endsection
