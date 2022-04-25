@extends('layouts.user')

@section('title', $title)

@section('content')
<div class="container">
    <div class="row">
        <customer-due-manage-create :customers="{{ $customers }}" :cashes="{{ $cashes }}" :banks="{{ $banks }}"></customer-due-manage-create>
    </div>
</div>
@endsection
