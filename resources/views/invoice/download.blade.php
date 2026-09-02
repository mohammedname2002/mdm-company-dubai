<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice</title>
    <style>
        /* ---------------------------------------------------------------
           This stylesheet reproduces, inside mPDF, exactly what Chrome
           prints from the invoice preview page (invoice/show.blade.php).
           Every value below was measured from a real printout of the
           preview (MDM-112.pdf): fonts, sizes, colours, column widths,
           row heights and paddings.

           Page geometry of that printout: US Letter, ~10mm page margins,
           content starts 24px further in (the card offset), giving a
           693px content box with the items table 512px wide.
        --------------------------------------------------------------- */
        body {
            font-family: roboto, sans-serif;   /* theme --ct-body-font-family */
            font-size: 14px;                   /* 0.875rem */
            line-height: 1.5;
            color: #212529;                    /* body text as printed */
        }

        p {
            margin: 0 0 16px 0;                /* 1rem */
        }

        h2#tax {
            margin-top: 9px;
            margin-bottom: 18px;
        }

        h2 {
            font-size: 26.55px;                /* calc(1.3125rem + 0.75vw) at print width */
            margin: 0 0 24px 0;
            line-height: 1.1;
            font-weight: 700;
            color: #343a40;
        }

        h4 {
            font-size: 18px;                   /* 1.125rem */
            margin: 0 0 24px 0;
            line-height: 1.1;
            font-weight: 500;
            color: #343a40;
        }

        a {
            color: #3f3689;                    /* link colour as printed */
            text-decoration: none;
        }

        /* --- layout (Bootstrap equivalents, mPDF has no Bootstrap) --- */
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
            color: #4e5559;                    /* muted text as printed */
        }

        /* The print viewport is 741px wide, i.e. below the md breakpoint,
           so col-md-* stack full width; col-sm-* stay side by side. */
        .col-md-6,
        .col-md-4 {
            width: 100%;
        }

        .col-md-4 .float-end {
            float: right;
            width: 180px;                      /* shrink-wrapped order block */
            margin-bottom: 21px;
        }

        .col-md-4 .mt-3 {
            margin-top: 24px;
        }

        /* Total block: float-end + margin-right:200px in the preview */
        .col-sm-6 .float-end {
            float: right;
            width: 130px;
            margin-top: 19px;
            margin-right: 200px;
            text-align: right;
        }

        .col-sm-6 {
            width: 50%;
            float: left;
        }

        .col-9 {
            width: 516px;                      /* yields the measured 512px table */
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

        /* Measured from the printout: 90px between the table and Notes. */
        .pt-5 {
            padding-top: 90px;
        }

        /* Chrome prints the badge without its background fill */
        .badge {
            font-size: 10.5px;
            font-weight: 500;
            color: #ababab;
        }

        /* --- items table --- */
        table.table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 0 16px 0;                /* bootstrap .table */
        }

        .table-bordered th,
        .table-bordered td {
            border: 1px solid #000000;
            padding: 8px;
            line-height: 1.5;
        }

        .table th,
        .table td {
            font-size: 8px;
        }

        .table thead th {
            vertical-align: bottom;
            font-weight: 500;
            text-align: left;
        }

        .table tbody td {
            vertical-align: top;
        }

        .aaa {
            color: #000000;                    /* @media print .aaa */
            font-weight: normal;
        }

        /* Column widths measured from the printed invoice (512px table) */
        .c-no {
            width: 4.1%;                       /* 21px of 512px */
        }

        .c-item {
            width: 50%;                        /* 256px */
        }

        .c-price {
            width: 10.35%;                     /* 53px */
        }

        .c-vat {
            width: 8.2%;                       /* 42px */
        }

        .c-pricedisc {
            width: 9.57%;                      /* 49px */
        }

        .c-vatdisc {
            width: 8.2%;                       /* 42px */
        }

        .c-total {
            width: 9.57%;                      /* 49px */
        }

        /* Chrome does not print background fills, so the blue header and
           the grey Sub Total row come out white on the printed page. */
        .invoice-items-table thead tr,
        .invoice-items-table tbody tr {
            background: none !important;
        }

        /* --- footer box --- */
        .invoice-footer-row {
            clear: both;
        }

        /* keeps the footer box at the printed distance from the table */
        .col-sm-6 .clearfix h4 {
            margin-bottom: 31px;
        }

        .invoice-footer-box {
            float: right;
            width: 262px;                      /* matches the printed text width */
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

    @include('invoice.partials.print-footer', ['footerBoxWidth' => '255px'])
</body>

</html>
