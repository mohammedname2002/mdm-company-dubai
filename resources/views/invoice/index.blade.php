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
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title mb-3">Print all invoices by company</h5>
                    <form class="row g-2 align-items-end" id="print-by-company-form">
                        <div class="col-md-4">
                            <label for="company_id" class="form-label">Company</label>
                            <select name="company" id="company_id" class="form-select">
                                <option value="all" selected>All companies</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">{{ $company->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="filter_month" class="form-label">Month</label>
                            <select name="month" id="filter_month" class="form-select">
                                <option value="">— All months —</option>
                                @foreach (range(1, 12) as $m)
                                    <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="filter_year" class="form-label">Year</label>
                            <select name="year" id="filter_year" class="form-select">
                                <option value="">— All years —</option>
                                @foreach (range(now()->year, 2020) as $y)
                                    <option value="{{ $y }}" {{ $y == now()->year ? 'selected' : '' }}>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-auto">
                            <button type="button" class="btn btn-primary waves-effect waves-light"
                                onclick="goPrintAllInvoices()">
                                <i class="mdi mdi-printer me-1"></i> Preview &amp; print all
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function goPrintAllInvoices() {
            var companyId = document.getElementById('company_id').value || 'all';

            var month = document.getElementById('filter_month').value;
            var year = document.getElementById('filter_year').value;
            var url = @json(route('invoice.print-by-company', ['company' => '__ID__'])).replace('__ID__', companyId);

            var params = new URLSearchParams();
            if (month) {
                params.set('month', month);
            }
            if (year) {
                params.set('year', year);
            }

            var query = params.toString();
            if (query) {
                url += '?' + query;
            }

            window.location.href = url;
        }
    </script>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @livewire('invoice-search')
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
@endsection
