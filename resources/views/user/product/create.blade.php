@extends('layouts.user')

@section('title', $title)

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12 py-3">
                <create-product-component :units="{{ $units }}" :brands="{{ $brands }}" :categories="{{ $categories }}" :warehouses="{{ $warehouses }}"></create-product-component>
            </div>
        </div>
    </div>
@endsection
