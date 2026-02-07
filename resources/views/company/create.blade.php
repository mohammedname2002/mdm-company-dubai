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
                            <li class="breadcrumb-item active">Store Company</li>
                        </ol>
                    </div>
                    <h4 class="page-title">Store Company</h4>
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
                        <h4 class="header-title">Store Company</h4>
                        <p class="text-muted font-13">More complex layouts can also be created with the grid system.</p>

                        <form action="{{ route('company.store') }}" method="POST">
                            @csrf
                            <div class="row">

                                <div class="mb-3 col-md-6">
                                    <label for="inputAddress" class="form-label">Company Name</label>
                                    <input type="text" name="name" class="form-control" id="inputAddress"
                                        placeholder="Company Name ">
                                    @error('name')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="inputAddress" class="form-label">TRN For Company </label>
                                    <input type="text" name="trn" class="form-control" id="inputAddress"
                                        placeholder="TRN For Name ">
                                    @error('trn')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="inputAddress" class="form-label">Company Address </label>
                                    <input type="text" name="address" class="form-control" id="inputAddress"
                                        placeholder="Company dress ">
                                    @error('address')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3 col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" id="phone"
                                        placeholder="Phone">
                                    @error('phone')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row">
                                <div class="mb-3 col-md-6">
                                    <label for="discount" class="form-label">Discount %</label>
                                    <input type="number" name="discount" class="form-control" id="discount">
                                    @error('discount')
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
