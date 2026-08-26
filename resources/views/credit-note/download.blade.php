<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tax Credit Note {{ $creditNote->credit_note_number }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #000; }
        table.meta { border-collapse: collapse; margin-bottom: 16px; width: 100%; max-width: 480px; }
        table.meta td { border: 1px solid #000; padding: 6px 8px; }
        table.items { border-collapse: collapse; width: 100%; margin-bottom: 16px; }
        table.items th {
            background-color: #555555;
            color: #fff;
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        table.items th.num { text-align: right; }
        table.items td { border: 1px solid #000; padding: 6px 8px; }
        td.num { text-align: right; }
        .totals p { margin: 4px 0; }
        .totals strong { font-weight: bold; }
        table.doc-head { border-collapse: collapse; width: 100%; margin-bottom: 16px; }
        table.doc-head td { border: none; vertical-align: middle; padding: 0; }
        table.doc-head .doc-head-logo { width: 15%; white-space: nowrap; padding-right: 8px; vertical-align: middle; }
        table.doc-head .doc-head-logo img { display: block; margin: 0; }
        table.doc-head .doc-head-spacer { width: 15%; }
        table.doc-head .doc-head-center { text-align: center; vertical-align: middle; width: 70%; }
        table.doc-head .doc-head-title { font-size: 18px; font-weight: bold; line-height: 1.3; }
    </style>
</head>
<body>
    @php
        $invoice = $creditNote->invoice;
        $company = $invoice->company ?? null;
        $issuerTrn = config('mdm.issuer_trn');
        $logoFile = public_path(config('mdm.logo_path'));
        $mdmLogoUri = is_file($logoFile)
            ? 'file:///' . str_replace(DIRECTORY_SEPARATOR, '/', $logoFile)
            : '';
        $subtotalExclDiscount = $creditNote->items->sum(fn ($i) => (float) $i->unit_price * (int) $i->quantity);
        $totalBeforeVat = $creditNote->items->sum(fn ($i) => $i->priceAfterDiscount());
        $discountAmount = max(0, $subtotalExclDiscount - $totalBeforeVat);
        $totalVat = $creditNote->items->sum(fn ($i) => $i->vatAmount());
        $grandTotal = $creditNote->items->sum(fn ($i) => $i->lineTotal());
        $vatPercents = $creditNote->items->pluck('vat_percent')->unique();
        $vatLabel = $vatPercents->count() === 1 ? 'VAT (' . rtrim(rtrim(number_format((float) $vatPercents->first(), 2), '0'), '.') . '%)' : 'VAT';
    @endphp

    <table class="doc-head">
        <tr>
            <td class="doc-head-logo">
                @if ($mdmLogoUri !== '')
                    <img src="{{ $mdmLogoUri }}" alt="MDM" width="52" style="width: 52px; max-width: 52px; height: auto; max-height: 50px;">
                @endif
            </td>
            <td class="doc-head-center">
                <div class="doc-head-title"><strong>Credit Note ({{ $company->name ?? '' }})</strong></div>
            </td>
            <td class="doc-head-spacer">&nbsp;</td>
        </tr>
    </table>

    <table class="meta">
        <tr><td><strong>Credit Note No</strong></td><td>{{ $creditNote->credit_note_number }}</td></tr>
        <tr><td><strong>Date</strong></td><td>{{ $creditNote->note_date->format('Y-m-d') }}</td></tr>
        <tr><td><strong>Original Invoice</strong></td><td>{{ $invoice->invoice_number ?? '' }}</td></tr>
        <tr><td><strong>TRN</strong></td><td>{{ $issuerTrn }}</td></tr>
        <tr><td><strong>Reason</strong></td><td>{{ $creditNote->reason ?: '—' }}</td></tr>
    </table>

    <table class="items">
        <thead>
            <tr>
                <th>Item</th>
                <th class="num">Qty</th>
                <th class="num">Unit</th>
                <th class="num">Price After Discount</th>
                <th class="num">VAT</th>
                <th class="num">Total</th>
                <th>Note</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($creditNote->items as $item)
                <tr>
                    <td>{{ $item->item_name }}</td>
                    <td class="num">
                        {{ (int) $item->quantity }}
                        @if (($item->free_items ?? 0) > 0)
                            /{{ (int) $item->free_items }} free
                        @endif
                    </td>
                    <td class="num">{{ number_format((float) $item->unit_price, 2) }}</td>
                    <td class="num">-{{ number_format($item->priceAfterDiscount(), 2) }}</td>
                    <td class="num">-{{ number_format($item->vatAmount(), 2) }}</td>
                    <td class="num">-{{ number_format($item->lineTotal(), 2) }}</td>
                    <td>{{ $item->line_note ?: '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <p><strong>Subtotal (excl. discount):</strong> -{{ number_format($subtotalExclDiscount, 2) }} AED</p>
        <p><strong>Company discount:</strong> -{{ number_format($discountAmount, 2) }} AED</p>
        <p><strong>After discount (before VAT):</strong> -{{ number_format($totalBeforeVat, 2) }} AED</p>
        <p><strong>{{ $vatLabel }}:</strong> -{{ number_format($totalVat, 2) }} AED</p>
        <p style="font-weight: bold;"><strong>Total Amount:</strong> -{{ number_format($grandTotal, 2) }} AED</p>
    </div>
</body>
</html>
