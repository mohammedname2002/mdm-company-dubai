@extends('layouts.master')
@section('title')
    Edit Invoice
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
                            <li class="breadcrumb-item active">Edit Product</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Edit Product</h4>
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
                        <h4 class="header-title">Edit Product</h4>
                        <p class="text-muted font-13">Edit the fields and submit to update the Product.</p>

                        <form action="{{ route('product.update', $product->id) }}" method="POST">
                            @csrf

                            <div class="row">
                                <div class="col-lg-6">
                                    <label for="company_id" class="form-label">Company Name</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="company_id" name="company_id">
                                            @foreach ($companies as $company)
                                                <option value="{{ $company->id }}"
                                                    {{ $product->company_id == $company->id ? 'selected' : '' }}>
                                                    {{ $company->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('company_id'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('company_id') }}
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <label for="product_name" class="form-label">Product Name</label>
                                    <input type="text" name="product_name" class="form-control" id="product_name"
                                        placeholder="Product Name" value="{{ $product->name }}">
                                    @if ($errors->has('product_name'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('product_name') }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label for="price" class="form-label">Price</label>
                                    <input type="number" name="price" class="form-control" id="price"
                                        value="{{ $product->price }}">
                                    @if ($errors->has('price'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('price') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3 col-md-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" name="quantity" class="form-control" id="quantity"
                                        value="{{ old('quantity', $product->quantity) }}" min="0">
                                    @if ($errors->has('quantity'))
                                        <div class="invalid-feedback d-block">{{ $errors->first('quantity') }}</div>
                                    @endif
                                </div>

                                <div class="mb-3 col-md-3">
                                    <label for="free_items" class="form-label">Free Items</label>
                                    <input type="number" name="free_items" class="form-control" id="free_items"
                                        value="{{ old('free_items', $product->free_items ?? 0) }}" min="0">
                                    @if ($errors->has('free_items'))
                                        <div class="invalid-feedback d-block">{{ $errors->first('free_items') }}</div>
                                    @endif
                                </div>

                                <div class="mb-3 col-md-3">
                                    <label for="vat" class="form-label">VAT %</label>
                                    <input type="number" name="vat" class="form-control" id="vat"
                                        value="{{ $product->vat }}">
                                    @if ($errors->has('vat'))
                                        <div class="invalid-feedback">
                                            {{ $errors->first('vat') }}
                                        </div>
                                    @endif
                                </div>

                                <div class="mb-3 col-md-3">
                                    <label for="date_of_create" class="form-label">Date of Creation</label>
                                    <div class="col-sm-10">
                                        <input class="form-control" type="date" id="date" name="date_of_create"
                                            value="{{ old('date_of_create', date('Y-m-d', strtotime($product->date_of_create))) }}">
                                        @if ($errors->has('data_of_create'))
                                            <div class="invalid-feedback">
                                                {{ $errors->first('data_of_create') }}
                                            </div>
                                        @endif
                                    </div>
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
