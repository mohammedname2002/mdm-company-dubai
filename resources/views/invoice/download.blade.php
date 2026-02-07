<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice</title>
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
</head>

<body>
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
                                    <img src="{{ asset('assets/images/logo-light.jpg') }}" alt=""
                                        height="50">
                                </div>
                            </div>
                            <div class="float-end">
                                <img src="{{ asset('assets/images/oge.png') }}" alt="" height="70">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mt-3">
                                    <p><b>{{ $invoiceName->company->name }}</b></p>
                                    <p><b>TRN: {{ $invoiceName->company->trn }}</b></p>
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

                        <div class="row mt-3">
                            <div class="col-9">
                                <div class="table table-bordered border-black mb-0">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th class="aaa">Item</th>
                                                <th class="aaa">Price</th>
                                                <th class="aaa">Vat</th>
                                                <th class="aaa">Price with Discount</th>
                                                <th class="aaa">Price with Discount & Vat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($products as $product)
                                                <tr>
                                                    <td>{{ $loop->index + 1 }}</td>
                                                    <td>({{ $product->quantity }}@if (($product->free_items ?? 0) > 0)
                                                            +{{ $product->free_items }}
                                                        @endif)
                                                        {{ $product->name }}</td>
                                                    <td>{{ $product->price * $product->quantity }}</td>
                                                    <td>{{ $product->getVatAmount() }}</td>
                                                    <td>{{ $product->price * $product->quantity - $product->price * $product->quantity * ($invoiceName->company->discount / 100) }}
                                                    </td>
                                                    <td>{{ $product->price * $product->quantity - $product->price * $product->quantity * ($invoiceName->company->discount / 100) + $product->getVatAmount() }}
                                                    </td>
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
                                    <h6 class="text-muted">Notes:</h6>
                                    <small class="text-muted"></small>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="float-end">
                                    <p><b>Total:</b> <span>{{ $totalWithVatAndDiscount }}</span></p>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Box -->
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-end">
                                <div class="border p-3 bg-light" style="border-radius: 8px; width: 300px;">
                                    <div class="text-end">
                                        <p><strong>Address:</strong> Office 153-101 King Mohammed Abdulaziz Mohammed bin
                                            Faris - Deira - Al Murar</p>
                                        <p><strong>TRN:</strong> 104718581200003</p>
                                        <p><strong>Website:</strong> <a href="https://example.com"
                                                target="_blank">MDM.com</a></p>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div> <!-- end card-body -->
                </div> <!-- end card -->
            </div> <!-- end col -->
        </div> <!-- end row -->

    </div> <!-- container -->



</body>

</html>
