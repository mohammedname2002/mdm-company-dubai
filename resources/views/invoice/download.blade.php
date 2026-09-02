<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        /* ---------------------------------------------------------------
           PDF stylesheet = the invoice preview page as the browser prints
           it (resources/views/invoice/show.blade.php + its @media print
           rules + the Hyper theme defaults). mPDF has no Bootstrap, so the
           classes the shared partials use are re-implemented here with the
           exact values the preview resolves to.
        --------------------------------------------------------------- */
        @page {
            margin: 12mm 15mm 12mm 15mm;
        }

        body {
            font-family: Arial, sans-serif;   /* @media print in show.blade.php */
            font-size: 14px;                  /* --ct-body-font-size: 0.875rem */
            color: #4982b3;                   /* --ct-body-color */
        }

        p {
            margin: 0 0 16px 0;               /* bootstrap p margin-bottom: 1rem */
        }

        h2,
        h4 {
            margin: 0 0 24px 0;
            font-weight: 500;
            line-height: 1.1;
            color: #343a40;                   /* --ct-heading-color */
        }

        h2 {
            font-size: 26px;                  /* h2 at A4 print width */
        }

        h4 {
            font-size: 18px;                  /* 1.125rem */
        }

        a {
            color: #4982b3;
            text-decoration: none;
        }

        /* --- grid / utility classes --- */
        .row,
        .clearfix {
            clear: both;
            width: 100%;
        }

        .float-start {
            float: left;
        }

        .float-end {
            float: right;
        }

        .text-end {
            text-align: right;
        }

        .text-muted {
            color: #98a6ad;
        }

        .col-md-6,
        .col-sm-6 {
            width: 50%;
            float: left;
        }

        .col-md-4 {
            width: 33.33%;
            float: right;
        }

        .col-9 {
            width: 75%;
            float: left;
        }

        .col-12 {
            width: 100%;
        }

        .mt-3 {
            margin-top: 16px;
        }

        .mt-4 {
            margin-top: 24px;
        }

        .pt-5 {
            padding-top: 48px;
        }

        .page-title {
            font-size: 20px;                  /* .page-title-box .page-title */
            margin: 0 0 12px 0;
            color: #343a40;
        }

        /* Badge: the browser does not print background colours, so on the
           printed preview only the label text remains. */
        .badge {
            font-size: 10px;
            font-weight: 500;
            color: #343a40;
        }

        /* --- invoice items table --- */
        table.table {
            width: 100%;
            border-collapse: collapse;
            color: #4982b3;
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000000;        /* @media print */
            padding: 8px;                     /* @media print */
        }

        .table th,
        .table td {
            font-size: 8px;                   /* @media print */
        }

        th {
            font-weight: 500;
        }

        .aaa {
            color: #000000;                   /* @media print .aaa */
            font-weight: normal;
            font-size: 8px;
        }

        .product-name-cell {
            font-size: 8px;
            width: 287px;                     /* @media print */
        }

        /* Sub Total row: its inline grey background is not printed either. */
        .invoice-items-table tbody tr {
            background: none !important;
        }

        /* --- footer box --- */
        .invoice-footer-row {
            clear: both;
        }

        .invoice-footer-box {
            float: right;
            width: 300px;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 16px;
        }
    </style>
</head>
<body>
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

        $logoSrc = public_path('assets/images/mdm.png');
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">Invoice</h4>
            </div>
        </div>
    </div>

    @include('invoice.partials.print-header', ['logoSrc' => $logoSrc])

    <div class="row mt-3">
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

    @include('invoice.partials.print-footer')
</body>

</html>
