@extends('layouts.master')
@section('title')
@endsection
@section('css')
@endsection
@section('title_page')
    الرئيسية
@endsection
@section('title_page2')
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <h4 class="page-title">Create Credit Note</h4>
                </div>
            </div>
        </div>

        @if (session()->has('success'))
            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Tax Credit Note</h4>
                        <p class="text-muted font-13">Select an invoice to load its lines. Enter <strong>paid</strong> and <strong>free</strong> quantities to return on each line (leave both at 0 to skip a product). Previous credit notes reduce what you can still return; history appears under each item. Totals update as you type. Amounts print as a credit (negative on the PDF). Returned units increase product <strong>stock</strong> and are logged.</p>

                        @error('line_items')
                            <div class="alert alert-danger">{{ $message }}</div>
                        @enderror

                        <form action="{{ route('credit-note.store') }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-lg-6 mb-3">
                                    <label class="form-label">Original invoice</label>
                                    <select name="invoice_id" id="invoice_id" class="form-control" required>
                                        <option value="">— Select invoice —</option>
                                        @foreach ($invoices as $inv)
                                            <option value="{{ $inv->id }}" {{ (string) old('invoice_id') === (string) $inv->id ? 'selected' : '' }}>
                                                {{ $inv->invoice_number }}
                                                @if ($inv->company)
                                                    — {{ $inv->company->name }}
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('invoice_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Credit note number</label>
                                    <input type="text" name="credit_note_number" class="form-control" value="{{ old('credit_note_number') }}" placeholder="e.g. CN-003" required>
                                    @error('credit_note_number')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-lg-3 mb-3">
                                    <label class="form-label">Credit note date</label>
                                    <input type="date" name="note_date" class="form-control" value="{{ old('note_date', date('Y-m-d')) }}" required>
                                    @error('note_date')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <div class="col-12 mb-3">
                                    <label class="form-label">Reason (optional)</label>
                                    <input type="text" name="reason" class="form-control" value="{{ old('reason') }}" placeholder="e.g. Full goods return">
                                    @error('reason')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div id="preview-wrap" class="mb-3"></div>

                            <button type="submit" class="btn btn-primary waves-effect waves-light">Save credit note</button>
                            <a href="{{ route('credit-note.index') }}" class="btn btn-light">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const select = document.getElementById('invoice_id');
            const wrap = document.getElementById('preview-wrap');
            const previewUrl = @json(url('/credit-note/preview'));
            const oldLineItems = @json(old('line_items', []));

            function fmt(n) {
                const x = Number(n);
                const s = Math.abs(x).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                return '-' + s;
            }

            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }

            function lineAmounts(qty, unitPrice, vatPct, discPct) {
                const q = Math.max(0, Number(qty) || 0);
                const gross = Number(unitPrice) * q;
                const pad = gross - gross * (Number(discPct) / 100);
                const vat = pad * (Number(vatPct) / 100);
                return { gross: gross, price_after_discount: pad, vat: vat, total: pad + vat };
            }

            function recalc(tableRoot) {
                let sumGross = 0, sumPad = 0, sumVat = 0, sumTot = 0;
                tableRoot.querySelectorAll('tr.cn-line').forEach(function (tr) {
                    const qtyInp = tr.querySelector('.cn-qty-paid');
                    const q = qtyInp && !qtyInp.disabled ? qtyInp.value : 0;
                    const unit = Number(tr.getAttribute('data-unit-price'));
                    const vatp = Number(tr.getAttribute('data-vat-percent'));
                    const lineDisc = Number(tr.getAttribute('data-discount-percent') || 0);
                    const am = lineAmounts(q, unit, vatp, lineDisc);
                    sumGross += am.gross;
                    sumPad += am.price_after_discount;
                    sumVat += am.vat;
                    sumTot += am.total;
                    const cPad = tr.querySelector('.cn-cell-pad');
                    const cVat = tr.querySelector('.cn-cell-vat');
                    const cTot = tr.querySelector('.cn-cell-tot');
                    if (cPad) cPad.textContent = fmt(am.price_after_discount);
                    if (cVat) cVat.textContent = fmt(am.vat);
                    if (cTot) cTot.textContent = fmt(am.total);
                });
                const sumDisc = Math.max(0, sumGross - sumPad);
                const root = tableRoot.closest('.cn-preview-scope');
                const fSub = root ? root.querySelector('.cn-foot-sub') : null;
                const fDisc = root ? root.querySelector('.cn-foot-disc') : null;
                const fPad = tableRoot.querySelector('.cn-foot-pad');
                const fVat = tableRoot.querySelector('.cn-foot-vat');
                const fTot = tableRoot.querySelector('.cn-foot-tot');
                if (fSub) fSub.textContent = fmt(sumGross);
                if (fDisc) fDisc.textContent = fmt(sumDisc);
                if (fPad) fPad.textContent = fmt(sumPad);
                if (fVat) fVat.textContent = fmt(sumVat);
                if (fTot) fTot.textContent = fmt(sumTot);
            }

            function applyOldValues(tableRoot) {
                if (!oldLineItems || !oldLineItems.length) {
                    recalc(tableRoot);
                    return;
                }
                const map = {};
                oldLineItems.forEach(function (r) {
                    map[String(r.product_id)] = r;
                });
                tableRoot.querySelectorAll('tr.cn-line').forEach(function (tr) {
                    const hid = tr.querySelector('input[name$="[product_id]"]');
                    if (!hid) return;
                    const o = map[hid.value];
                    if (!o) return;
                    const qp = tr.querySelector('.cn-qty-paid');
                    const qf = tr.querySelector('.cn-qty-free');
                    const ln = tr.querySelector('.cn-line-note');
                    if (qp && !qp.disabled && o.quantity != null) qp.value = o.quantity;
                    if (qf && !qf.disabled && o.free_items != null) qf.value = o.free_items;
                    if (ln && o.line_note != null) ln.value = o.line_note;
                });
                recalc(tableRoot);
            }

            function historyHtml(h) {
                if (!h || !h.length) {
                    return '';
                }
                let lis = '';
                h.forEach(function (r) {
                    const note = r.line_note ? ' — ' + escapeHtml(String(r.line_note)) : '';
                    lis += '<li><span class="text-muted">' + escapeHtml(r.note_date) + '</span> · ' +
                        escapeHtml(r.credit_note_number) + ': paid ' + r.quantity + ', free ' + r.free_items + note + '</li>';
                });
                return '<details class="small text-muted mt-1"><summary>Return history (' + h.length + ')</summary><ul class="mb-0 ps-3">' + lis + '</ul></details>';
            }

            function render(data) {
                if (!data.lines || data.lines.length === 0) {
                    wrap.innerHTML = '<div class="alert alert-warning mb-0">No products found for this invoice (check company and invoice date range).</div>';
                    return;
                }

                const disc = Number(data.company_discount_percent || 0);
                let rows = '';
                data.lines.forEach(function (line, idx) {
                    const lineDisc = Number(line.discount_percent != null ? line.discount_percent : disc);
                    const mxq = line.max_quantity;
                    const mxf = line.max_free_items;
                    const qv = line.quantity;
                    const fv = line.free_items;
                    const full = line.fully_returned;
                    const origText = line.original_quantity + ' paid' + (line.original_free_items ? ' / ' + line.original_free_items + ' free' : '');
                    const retText = line.previously_returned_quantity + ' paid' + (line.previously_returned_free ? ' / ' + line.previously_returned_free + ' free' : '');
                    const availText = line.available_quantity + ' paid' + (line.available_free_items ? ' / ' + line.available_free_items + ' free' : '');
                    const roClass = full ? ' table-secondary' : '';

                    let freeCell = '';
                    if (full) {
                        freeCell = '<input type="hidden" name="line_items[' + idx + '][free_items]" value="0">';
                    } else if (mxf > 0) {
                        freeCell = '<input type="number" class="form-control form-control-sm cn-qty-free" name="line_items[' + idx + '][free_items]" min="0" max="' + mxf + '" value="' + fv + '" style="max-width:5rem">';
                    } else {
                        freeCell = '<span class="text-muted">0</span><input type="hidden" name="line_items[' + idx + '][free_items]" value="0">';
                    }

                    const paidInput = full
                        ? '<span class="text-muted">0</span><input type="hidden" name="line_items[' + idx + '][quantity]" value="0">'
                        : '<input type="number" class="form-control form-control-sm cn-qty-paid" name="line_items[' + idx + '][quantity]" min="0" max="' + mxq + '" value="' + qv + '" style="max-width:5rem" title="Paid units to return (max ' + mxq + ')">';

                    const unitCell = '<span class="text-muted">' + Number(line.unit_price).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + '</span>';

                    const noteCell = full
                        ? '<span class="text-muted">—</span><input type="hidden" name="line_items[' + idx + '][line_note]" value="">'
                        : '<input type="text" class="form-control form-control-sm cn-line-note" name="line_items[' + idx + '][line_note]" maxlength="500" placeholder="Optional">';

                    rows +=
                        '<tr class="cn-line' + roClass + '" data-unit-price="' + escapeHtml(String(line.unit_price)) + '" data-vat-percent="' + escapeHtml(String(line.vat_percent)) + '" data-discount-percent="' + escapeHtml(String(lineDisc)) + '">' +
                        '<td style="min-width:14rem">' +
                            '<input type="hidden" name="line_items[' + idx + '][product_id]" value="' + line.product_id + '">' +
                            '<div class="fw-semibold">' + escapeHtml(line.item_name) + (full ? ' <span class="badge bg-secondary">Fully returned</span>' : '') + '</div>' +
                            historyHtml(line.return_history || []) +
                        '</td>' +
                        '<td class="small">' + escapeHtml(origText) + '</td>' +
                        '<td class="small">' + escapeHtml(retText) + '</td>' +
                        '<td class="small">' + escapeHtml(availText) + '</td>' +
                        '<td style="width:1%">' + paidInput + '</td>' +
                        '<td style="width:1%">' + freeCell + '</td>' +
                        '<td class="text-end">' + unitCell + '</td>' +
                        '<td class="text-end cn-cell-pad"></td>' +
                        '<td class="text-end cn-cell-vat"></td>' +
                        '<td class="text-end cn-cell-tot"></td>' +
                        '<td>' + noteCell + '</td>' +
                        '</tr>';
                });

                wrap.innerHTML =
                    '<div class="cn-preview-scope">' +
                    '<div class="mb-2"><strong>' + escapeHtml(data.company_name || '') + '</strong><br>' +
                    '<span class="text-muted">Invoice:</span> ' + escapeHtml(data.invoice_number || '') +
                    (data.trn ? (' &nbsp;|&nbsp; <span class="text-muted">TRN:</span> ' + escapeHtml(data.trn)) : '') +
                    '</div>' +
                    '<div class="table-responsive"><table class="table table-bordered table-sm mb-2 cn-preview-table">' +
                    '<thead class="table-secondary"><tr>' +
                    '<th>Product</th><th>Original qty</th><th>Previously returned</th><th>Available</th>' +
                    '<th class="text-center">Return (paid)</th><th class="text-center">Return (free)</th>' +
                    '<th class="text-end">Unit price</th><th class="text-end">After discount</th><th class="text-end">VAT</th><th class="text-end">Total</th>' +
                    '<th>Line note</th></tr></thead>' +
                    '<tbody>' + rows + '</tbody>' +
                    '<tfoot>' +
                    '<tr class="small"><td colspan="7" class="text-end">Subtotal (excl. discount)</td><td class="text-end cn-foot-sub"></td><td colspan="3"></td></tr>' +
                    '<tr class="small"><td colspan="7" class="text-end">Company discount</td><td class="text-end cn-foot-disc"></td><td colspan="3"></td></tr>' +
                    '<tr class="fw-bold"><td colspan="7" class="text-end">After discount (before VAT)</td><td class="text-end cn-foot-pad"></td><td class="text-end cn-foot-vat"></td><td class="text-end cn-foot-tot"></td><td></td></tr>' +
                    '</tfoot>' +
                    '</table></div>' +
                    '<p class="text-muted small mb-0">Amounts above are credits (negative on the printed note). VAT is calculated on discounted line amounts.</p>' +
                    '</div>';

                const tbl = wrap.querySelector('.cn-preview-table');
                tbl.addEventListener('input', function () {
                    recalc(tbl);
                });
                applyOldValues(tbl);
            }

            select.addEventListener('change', function () {
                const id = this.value;
                if (!id) {
                    wrap.innerHTML = '';
                    return;
                }
                wrap.innerHTML = '<div class="text-muted">Loading…</div>';
                fetch(previewUrl + '/' + id, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                }).then(function (r) {
                    return r.json();
                }).then(render).catch(function () {
                    wrap.innerHTML = '<div class="alert alert-danger mb-0">Could not load invoice lines.</div>';
                });
            });

            @if (old('invoice_id'))
                select.dispatchEvent(new Event('change'));
            @endif
        })();
    </script>
@endsection
