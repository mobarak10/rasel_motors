@extends('layouts.user')
@section('title', $title)
@section('content')
    <!-- purchase create -->
    <purchase-create-component
        :warehouses="{{ $warehouses }}"
        :products="{{ $products }}"
        :cashes="{{ $cashes }}"
        :parties="{{ $parties }}"
        :bank-accounts="{{ $bank_accounts }}">
    </purchase-create-component>
@endsection
