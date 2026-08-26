@php
    $rowStart = $rowStart ?? 0;
    $showSubtotal = $showSubtotal ?? false;
    $subTotalPrice = $subTotalPrice ?? 0;
    $subTotalVat = $subTotalVat ?? 0;
    $subTotalDiscounted = $subTotalDiscounted ?? 0;
    $subTotalDiscountedVat = $subTotalDiscountedVat ?? 0;
    $subTotalAmount = $subTotalAmount ?? 0;

    $companyDiscount = (float) ($invoiceName->company->discount ?? 0);

    $showDiscountInHeaders = $showDiscountInHeaders ?? (
        $companyDiscount > 0 &&
        collect($products)->contains(fn ($p) => (bool) ($p->apply_company_discount ?? true))
    );
@endphp

<table class="table table-bordered invoice-items-table">
    <thead>
        <tr>
            <th>#</th>
            <th class="aaa">Item</th>
            <th class="aaa">Price</th>
            <th class="aaa">VAT</th>

            @if($showDiscountInHeaders)
                <th class="aaa">Price with Discount ({{ $companyDiscount }}%)</th>
                <th class="aaa">VAT</th>
                <th class="aaa">Price with Discount &amp; VAT</th>
            @else
                <th class="aaa">Price with VAT</th>
            @endif
        </tr>
    </thead>

    <tbody>
        @foreach ($products as $product)
            @php
                $productPrice = $product->price * $product->quantity;

                $unitVat = ($product->price * $product->vat) / 100;
                $productVat = $unitVat * $product->quantity;

                $productPriceWithDiscont = $product->linePriceAfterDiscount($companyDiscount);

                $productWithVat = ($productPriceWithDiscont * $product->vat) / 100;

                $productPriceWithDiscontAndVat = $productPriceWithDiscont + $productWithVat;

                $priceWithVat = $productPrice + $productVat;
            @endphp

            <tr>
                <td>{{ $rowStart + $loop->iteration }}</td>

                <td class="product-name-cell">
                    ({{ $product->quantity }}
                    @if(($product->free_items ?? 0) > 0)
                        +{{ $product->free_items }}
                    @endif)
                    {{ $product->name }}
                </td>

                <td>{{ number_format($productPrice, 2) }}</td>

                <td>{{ number_format($productVat, 2) }}</td>

                @if($showDiscountInHeaders)
                    <td>{{ number_format($productPriceWithDiscont, 2) }}</td>
                    <td>{{ number_format($productWithVat, 2) }}</td>
                    <td>{{ number_format($productPriceWithDiscontAndVat, 2) }}</td>
                @else
                    <td>{{ number_format($priceWithVat, 2) }}</td>
                @endif
            </tr>
        @endforeach

        @if($showSubtotal)
            <tr style="font-weight:bold;background:#f9f9f9;">
                <td colspan="2" style="text-align:right;">
                    {{ $subtotalLabel ?? 'Sub Total' }}
                </td>

                <td>{{ number_format($subTotalPrice, 2) }}</td>

                <td>{{ number_format($subTotalVat, 2) }}</td>

                @if($showDiscountInHeaders)
                    <td>{{ number_format($subTotalDiscounted, 2) }}</td>
                    <td>{{ number_format($subTotalDiscountedVat, 2) }}</td>
                    <td>{{ number_format($subTotalAmount, 2) }}</td>
                @else
                    <td>{{ number_format($subTotalPrice + $subTotalVat, 2) }}</td>
                @endif
            </tr>
        @endif
    </tbody>
</table>
