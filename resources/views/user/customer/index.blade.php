@extends('layouts.user')

@section('title', 'Customer')

@section('content')
    <div class="container">
        {{-- <div class="row">
            <div class="col-md-12">
                <div class="alert {{ ($total_balance < 0) ? 'alert-danger' : 'alert-success' }}">
                    <strong>@lang('contents.total_cash_bdt') {{ $total_balance }}</strong>
                </div>
            </div>
        </div> --}}

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="d-none mt-2 text-center d-print-block">
                        <h5 class="mb-0 center" style="font-size: 25px"> <strong>{{ config('print.print_details.name') }}</strong> </h5>
                        <p class="mb-0 font-12">{{ config('print.print_details.address') }}</p>
                        <span class="mb-0 font-12">{{ config('print.print_details.mobile') }}</span>
                        <p class="mb-0" style="font-size: 15px">{{ Carbon\Carbon::now()->format('j F, Y h:i:s a') }}</p>
                    </div>

                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">@lang('contents.customer_records')</h5>
                        <span class="d-none d-print-block">{{ date('Y-m-d') }}</span>
                        <div class="action-area print-none">
                            <a href="#" onclick="window.print();" title="Print" class="btn btn-sm btn-warning"><i aria-hidden="true" class="fa fa-print"></i>
                            </a>

                            <a href="{{ route('customer.index') }}" class="btn btn-sm btn-success" title="Refresh.">
                                <i class="fa fa-refresh"></i>
                            </a>

                            <button class="btn btn-sm btn-info" type="button" data-toggle="collapse" data-target="#customer-search">
                                <i class="fa fa-search"></i>
                            </button>

                            <a href="{{ route('customer.create') }}" class="btn btn-sm btn-primary" title="Create new customer">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="collapse col-md-12 print-none" id="customer-search">
                            <form action="{{ route('customer.index') }}" method="GET">
                                <input type="hidden" name="search" value="1">

                                <div class="row">
                                    <div class="form-group col-md-5">
                                        <label for="name">Name</label>
                                        <input type="text" class="form-control" name="condition[name]" placeholder="enter name" id="name">
                                    </div>

                                    <div class="form-group col-md-5">
                                        <label for="phone">Phone</label>
                                        <input type="text" class="form-control" name="condition[phone]" placeholder="enter phone number" id="phone">
                                    </div>
                                    <div class="form-group col-md-2">
                                        <label for="">&nbsp;</label>
                                        <button type="submit" class="btn btn-sm btn-primary">
                                            <i class="fa fa-search"></i> &nbsp;
                                            Search
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('contents.customer_name')</th>
                                    <th>@lang('contents.phone')</th>
                                    <th>@lang('contents.type')</th>
                                    <th class="text-right">Credit Limit</th>
                                    <th class="text-right">@lang('contents.balance')</th>
                                    <th class="text-right print-none">@lang('contents.action')</th>
                                </tr>
                            </thead>

                            <tbody>
                            @forelse($customers as $customer)
                                <tr>
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>{{ $customer->name }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ config('coderill.customer_type')[$customer->type] ?? '' }}</td>
                                    <td class="text-right">{{ number_format($customer->credit_limit, 2) }}</td>
                                    <td class="text-right">{{ number_format($customer->balance, 2) }}</td>
                                    <td class="text-right print-none">
                                        <a href="{{ route('customer.show', $customer->id) }}" class="btn btn-sm btn-primary" title="Customer details.">
                                            <i class="fa fa-eye" aria-hidden="true"></i>
                                        </a>

                                        <a href="{{ route('customer.edit', $customer->id) }}" class="btn btn-sm btn-success" title="Change customer information.">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </a>

                                        <a href="{{ route('customer.index') }}" class="btn btn-sm btn-danger" title="Trash" onClick="if(confirm('Are you sure, You want to move trashed this record?')){event.preventDefault();document.getElementById('delete-form-{{ $customer->id }}').submit();} else {event.preventDefault();}">
                                            <i class="fa fa-times" aria-hidden="true"></i>
                                        </a>

                                        <form action="{{ route('customer.destroy', $customer->id) }}" method="post" id="delete-form-{{ $customer->id }}" style="display: none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">No customer available</td>
                                </tr>
                            @endforelse
                            {{-- <tr>
                                <td colspan="3" class="text-right">@lang('contents.total')</td>
                                <td class="text-right">{{ $total_balance }}</td>
                                <td class="print-none"></td>
                            </tr> --}}
                            </tbody>
                        </table>

                        <!-- paginate -->
                        <div class="float-right mx-2">
                            {{ $customers->links() }}
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
