<div class="clearfix">
    <div class="float-start">
        <div class="auth-logo">
            <img src="{{ $logoSrc ?? asset('assets/images/mdm.png') }}" alt="" height="70">
        </div>
    </div>
</div>
<div style="text-align:center" class="auth-logo">
    <h2 id="tax" style="font-weight:700 !important">Tax Invoice </h2>
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
    </div>
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
    </div>
</div>
