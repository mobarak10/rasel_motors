@extends('layouts.user')

@section('title', $title)

@section('content')
<div class="container">
    <div class="row">
        <supplier-due-manage-create :suppliers="{{ $suppliers }}" :cashes="{{ $cashes }}" :banks="{{ $banks }}"></supplier-due-manage-create>
    </div>
</div>
@endsection
