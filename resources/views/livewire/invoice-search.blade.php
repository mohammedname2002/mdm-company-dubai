<div>
    <div class="row g-2 mb-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Search</label>
            <input type="text" wire:model.debounce.300ms="search" placeholder="Invoice #, status, company..."
                class="form-control" />
        </div>
        <div class="col-md-3">
            <label class="form-label">Company</label>
            <select wire:model="companyId" class="form-select">
                <option value="">All companies</option>
                @foreach ($companies as $company)
                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Month</label>
            <select wire:model="month" class="form-select">
                <option value="">All months</option>
                @foreach (range(1, 12) as $m)
                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Year</label>
            <select wire:model="year" class="form-select">
                <option value="">All years</option>
                @foreach ($years as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" wire:click="clearFilters" class="btn btn-light w-100">
                Clear filters
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-centered table-nowrap mb-0">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Invoice Number</th>
                    <th>Company</th>
                    <th>Period</th>
                    <th>Date of Invoice</th>
                    <th>Payment Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($invoices as $invoice)
                    <tr>
                        <td>{{ $invoices->firstItem() + $loop->index }}</td>
                        <td>{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->company?->name ?? '—' }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($invoice->from)->format('Y-m-d') }}
                            —
                            {{ \Carbon\Carbon::parse($invoice->to)->format('Y-m-d') }}
                        </td>
                        <td>{{ \Carbon\Carbon::parse($invoice->date_of_create)->format('Y-m-d') }}</td>
                        <td>
                            <h5 class="mb-0">
                                <span
                                    class="badge bg-soft-{{ $invoice->status == 'paid' ? 'success text-success' : 'danger text-danger' }}">
                                    <i class="mdi mdi-bitcoin"></i> {{ $invoice->status }}
                                </span>
                            </h5>
                        </td>
                        <td style="display: inline-block">
                            <div class="button-list" style="display: flex;">
                                <form action="{{ route('invoice.show', $invoice->id) }}" method="get">
                                    @csrf
                                    <button type="submit" class="btn btn-success waves-effect waves-light">Preview <i
                                            class="mdi mdi-eye me-1"></i></button>
                                </form>
                                <form action="{{ route('invoice.edit', $invoice->id) }}" method="get">
                                    @csrf
                                    <button type="submit" class="btn btn-success waves-effect waves-light">edit <i
                                            class="mdi mdi-eye me-1"></i></button>
                                </form>
                                <form action="{{ route('invoice.delete', $invoice->id) }}"
                                    id="deleteform{{ $invoice->id }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="JSconfirm(event, {{ $invoice->id }})"
                                        class="btn btn-danger waves-effect waves-light">Delete <i
                                            class="mdi mdi-close"></i></button>
                                </form>
                                <form action="{{ route('download', $invoice->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary waves-effect waves-light">Download <i
                                            class="mdi mdi-printer me-1"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">No invoices found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        {{ $invoices->links() }}
    </div>
</div>
