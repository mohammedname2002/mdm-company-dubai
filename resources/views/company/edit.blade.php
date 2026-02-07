@extends('layouts.master')
@section('title')
    Edit Company
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
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                            <li class="breadcrumb-item active">Edit Company</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Company</h4>
                </div>
            </div>
        </div>
        <!-- end page title -->

        @if (session()->has('success'))
            <div class="alert alert-primary alert-dismissible fade show" role="alert">
                <i class="mdi mdi-bullseye-arrow me-2"></i>
                {{ session()->get('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Form row -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title">Edit Company</h4>
                        <p class="text-muted font-13">Edit the details of the selected company.</p>

                        <form action="{{ route('company.update', $company->id) }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="inputAddress" class="form-label">Company Name</label>
                                    <input type="text" name="company_name" class="form-control" id="inputAddress"
                                        value="{{ old('company_name', $company->name) }}" placeholder="Company Name">
                                    @if ($errors->has('company_name'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('company_name') }}
                                        </div>
                                    @endif
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" id="phone"
                                        value="{{ old('phone', $company->phone ?? '') }}" placeholder="Phone">
                                    @if ($errors->has('phone'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('phone') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="inputAddress" class="form-label">TRN For Comapny </label>
                                    <input type="text" name="trn" class="form-control" id="inputAddress"
                                        value="{{ old('trn', $company->trn) }}" placeholder="Trn Name">
                                    @if ($errors->has('trn'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('trn') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="inputAddress" class="form-label">Company Address</label>
                                    <input type="text" name="address" class="form-control" id="inputAddress"
                                        value="{{ old('address', $company->address) }}" placeholder="address Name">
                                    @if ($errors->has('address'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('address') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="discount" class="form-label">Discount %</label>
                                    <input type="number" name="discount" class="form-control" id="discount"
                                        value="{{ old('discount', $company->discount) }}">
                                    @if ($errors->has('discount'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('discount') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary waves-effect waves-light">Update</button>

                        </form>

                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div>
        <!-- end row -->
    </div>
@endsection

@section('scripts')
@endsection
