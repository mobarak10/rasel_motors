@extends('layouts.user')

@section('title', $title)

@section('content')
<div class="container">
    <div class="row"> 
        <div class="col-md-12 py-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center"> 
                    <h5 class="m-0">Edit Product</h5>

                    <div class="btn-group" role="group" aria-label="Action area">
                        <a href="{{ route('damageStock.index') }}" class="btn btn-primary" title="All Damages">
                            <i class="fa fa-list" aria-hidden="true"></i>
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    
                    <form action="{{ route('damageStock.update', $product->id) }}" method="post">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="product-name">Product Name:</label>
                                    <input type="text" class="form-control" id="product-name" value="{{ $product->name }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="product-code">Product Code:</label>
                                    <input type="text" class="form-control" id="product-code" value="{{ $product->code }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="product-barcode">Product Barcode:</label>
                                    <input type="text" class="form-control" id="product-barcode" value="{{ $product->barcode }}" readonly>
                                </div>

                                <div class="form-group">
                                    <label for="product-description">Product Description:</label>
                                    {{ $product->description }}
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                @foreach($product->warehouses as $warehouse)
                                    <p class="text-inline">
                                        <h3>{{ $warehouse->title }}</h3>
                                        Current Stock ({{ $warehouse->display_quantity }})
                                    </p>

                                    <div class="input-group">
                                        @foreach($warehouse->product_quantity_in_unit['result'] as $key => $value)
                                            <input type="text" class="form-control" name="quantities[{{ $warehouse->id }}][]" placeholder="{{ $warehouse->product_quantity_in_unit['labels'][$key] }}">
                                        @endforeach

                                    </div>
                                @endforeach

                                 <div class="text-right mt-2">
                                    <button type="reset" class="btn btn-danger">@lang('contents.reset')</button>
                                    <button type="submit" class="btn btn-primary">@lang('contents.save')</button>
                                </div>
                            </div>
                        </div>
                        
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- main-panel ends -->
@endsection
