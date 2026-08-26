@php
    $invoiceName = $invoiceName ?? $data['invoiceName'];
    $products = $products ?? $data['products'];
    $subTotalPrice = $subTotalPrice ?? $data['subTotalPrice'];
    $subTotalVat = $subTotalVat ?? $data['subTotalVat'];
    $subTotalDiscounted = $subTotalDiscounted ?? $data['subTotalDiscounted'];
    $subTotalDiscountedVat = $subTotalDiscountedVat ?? $data['subTotalDiscountedVat'];
    $subTotalAmount = $subTotalAmount ?? $data['subTotalAmount'];
    $totalTotal = $totalTotal ?? $data['totalTotal'];
@endphp

@include('invoice.partials.print-header', ['invoiceName' => $invoiceName])

<div class="row mt-3">
    <div class="col-9">
        @if ($products->isEmpty())
            <p class="text-muted mb-2"><em>No line items for this invoice period.</em></p>
        @endif
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
        <div class="clearfix pt-3">
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
