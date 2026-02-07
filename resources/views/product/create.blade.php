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

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box">
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Forms</a></li>
                            <li class="breadcrumb-item active">Store Product </li>
                        </ol>
                    </div>
                    <h4 class="page-title">Store Product</h4>
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
                        <h4 class="header-title">Store Product </h4>
                        <p class="text-muted font-13">More complex layouts can also be created with the grid system.</p>

                        <form action="{{ route('product.store') }}" method="POST">
                            @csrf


                            <div class="row">
                                <div class="col-lg-6">
                                    <label for="example-text-input" class="form-label">Company Name</label>
                                    <div class="col-sm-10">
                                        <select class="form-control" id="type" name="company_id" required>
                                            <option value=" ">Select The Company</option>

                                            @foreach ($companies as $company)
                                                <option value=" {{ $company->id }} ">{{ $company->name }}</option>
                                            @endforeach

                                            @if ($errors->has('company->id'))
                                                <div class="company->id">
                                                    {{ $errors->first('company->id') }}
                                                </div>
                                            @endif
                                        </select>
                                        @error('company_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <label for="inputAddress" class="form-label">Product Name</label>
                                    <input type="text" name="product_name"
                                        class="form-control{{ $errors->has('product_name') ? 'is-invalid' : '' }}"
                                        value="{{ old('product_name') }}" id="inputAddress" placeholder="Product  Name ">
                                    @error('product_name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>


                            </div>
                            <div class="row">
                                <div class="mb-3 col-md-3">
                                    <label for="discount" class="form-label">Price</label>
                                    <input type="number" name="price"
                                        class="form-control {{ $errors->has('discount') ? 'is-invalid' : '' }}"
                                        value="{{ old('discount') }}" id="discount">
                                    @error('price')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>
                                <div class="mb-3 col-md-3">
                                    <label for="quantity" class="form-label">Quantity</label>
                                    <input type="number" name="quantity"
                                        class="form-control {{ $errors->has('quantity') ? 'is-invalid' : '' }}"
                                        value="{{ old('quantity') }}" id="quantity" min="0">
                                    @error('quantity')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror

                                </div>
                                <div class="mb-3 col-md-3">
                                    <label for="free_items" class="form-label">Free Items</label>
                                    <input type="number" name="free_items"
                                        class="form-control {{ $errors->has('free_items') ? 'is-invalid' : '' }}"
                                        value="{{ old('free_items', 0) }}" id="free_items" min="0" placeholder="0">
                                    @error('free_items')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label for="discount" class="form-label">Vat %</label>
                                    <input type="number" name="vat" class="form-control" id="discount">
                                    @error('vat')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-3">
                                    <label for="date_of_create" class="form-label">Date Of Create</label>
                                    <input type="date" name="date_of_create" class="form-control" id="date_of_create">
                                    @error('date_of_create')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            </div>

                            <button type="submit" class="btn btn-primary waves-effect waves-light">Save</button>

                        </form>

                    </div> <!-- end card-body -->
                </div> <!-- end card-->
            </div> <!-- end col -->
        </div>
        <!-- end row -->


        <!-- RADIOS -->



    </div>
@endsection
@section('scripts')
    لوحة التحكم
@endsection
