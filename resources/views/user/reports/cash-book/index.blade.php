@extends('layouts.user')

@section('title', $title)

@push('style')
    <link href="{{ asset('public/css/stock.css') }}" rel="stylesheet">
@endpush

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h1 class="text-center d-none d-print-block">Shop: {{ config('print.print_details.name') }}</h1>
                <p style="margin-bottom: 0 !important;" class="text-center d-none d-print-block">Phone: {{ config('print.print_details.mobile') }}</p>
                <p class="text-center d-none d-print-block">Address: {{ config('print.print_details.address') }}</p>

                <div class="card current-stock">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        @if(request()->search)
                            <div>
                                <h5 class="m-0"><span>Cash Book Report {{ request()->date }} </span></h5>
                            </div>
                        @endif

                        <div class="text-right">
                            <span class="d-none d-print-block">Print Date: {{ date('d-m-Y') }}, {{ date('H:i:s A') }}</span>
                        </div>
                        <div class="action-area print-none" role="group" aria-label="Action area">
                            <a href="{{ route('cashBook.index') }}" class="btn btn-primary" title="Refresh">
                                <i class="fa fa-refresh" aria-hidden="true"></i>
                            </a>
                            <a href="#" onclick="window.print();" title="Print" class="btn btn-warning">
                                <i aria-hidden="true" class="fa fa-print"></i>
                            </a>
                        </div>
                    </div>

                    <!-- search form start -->
                    <div class="card-body print-none">
                        <form action="{{ route('cashBook.index') }}" method="GET" class="row">
                            <input type="hidden" name="search" value="1">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-10">
                                        <label for="business">Date</label>
                                        <input type="date" class="form-control" name="date" value="{{ (request()->search) ? request()->date : date('Y-m-d') }}" placeholder="Enter date for search">
                                    </div>
                                    <div class="col-md-2" style="padding-top: 30px">
                                        <button type="submit" class="btn btn-primary" title="search">
                                            <i class="fa fa-search"></i> Search
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- search form end -->
                    <div class="card-body p-2">
                        <!-- search form start -->
                        <div class="form-row col-md-12 mx-0">

                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="ml-3">Income Details</h4>
                                    <h4 class="mr-3">Amount</h4>
                                </div>
                                <table class="table table-striped table-sm">
                                    <tr>
                                        <th style="font-size: larger">Opening Balance: </th>
                                        <th class="text-right">{{ number_format($closing_balance->amount ?? 0, 2) }}</th>
                                    </tr>

                                    <tr>
                                        <th style="font-size: larger">Sales: </th>
                                        <th></th>
                                    </tr>
                                    @php
                                        $total_sale = 0;
                                        $total_hire_sale = 0;
                                        $total_installment_collection = 0;
                                        $total_income_transaction = 0;
                                        $total_due_receive = 0;
                                    @endphp
                                    @foreach($sales as $sale)
                                        <tr>
                                            <td>{{ $sale->customer->name ?? '' }}</td>
                                            <td class="text-right">{{ number_format($sale->paid - $sale->change, 2) }}</td>
                                        </tr>
                                        @php
                                            $total_sale += $sale->paid - $sale->change;
                                        @endphp
                                    @endforeach

                                    <tr>
                                        <th style="font-size: larger">Transaction: </th>
                                        <th></th>
                                    </tr>

                                    @foreach($transaction_form_bank as $transaction)
                                        <tr>
                                            <td>Cash Balance({{ $transaction->code }})</td>
                                            <td class="text-right">{{ number_format($transaction->amount, 2) }}</td>
                                        </tr>
                                        @php
                                            $total_income_transaction += $transaction->amount;
                                        @endphp
                                    @endforeach

                                    <tr>
                                        <th>Total Purchase Return:</th>
                                        <td class="text-right">{{ number_format($total_purchase_return,2) }}</td>
                                    </tr>

                                    <tr>
                                        <th style="font-size: larger">Due Receive: </th>
                                        <th></th>
                                    </tr>
                                    @foreach($due_receive as $receive)
                                        <tr>
                                            <td>Party Name: ({{ $receive->customer->name ?? '' }})</td>
                                            <td class="text-right">{{ number_format($receive->amount, 2) }}</td>
                                        </tr>
                                        @php
                                            $total_due_receive += $receive->amount;
                                        @endphp
                                    @endforeach

                                    <tr>
                                        <td>Grand Total</td>
                                        <td class="text-right">
                                            @php
                                                $total_income = $total_sale
                                                                + $total_hire_sale
                                                                + $total_installment_collection
                                                                + $total_purchase_return
                                                                + $total_income_transaction
                                                                + $total_due_receive;
                                            @endphp
                                            {{ number_format($total_income, 2) }}
                                        </td>
                                    </tr>
                                </table>
                            </div>

                            <div class="col-sm-6">
                                <div class="d-flex align-items-center justify-content-between">
                                    <h4 class="ml-3">Expense Details</h4>
                                    <h4 class="mr-3">Amount</h4>
                                </div>
                                <table class="table table-bordered table-sm">
                                    @php
                                        $total_salary = 0;
                                        $total_expense = 0;
                                        // $total_due_paid = 0;
                                        $total_purchase_paid = 0;
                                        $total_expanse_transaction = 0;
                                    @endphp
                                    <tr>
                                        <th style="font-size: larger">Employee Salary:</th>
                                        <th></th>
                                    </tr>
                                    @foreach($salaries as $salary)
                                        <tr>
                                            <td>{{ $salary->user->name ?? '' }}</td>
                                            <td class="text-right">{{ number_format(($salary->increment_total - $salary->decrement_total), 2) }}</td>
                                        </tr>
                                        @php
                                            $total_salary += ($salary->increment_total - $salary->decrement_total);
                                        @endphp
                                    @endforeach
                                    <tr>
                                        <th style="font-size: larger">Expenses:</th>
                                        <th></th>
                                    </tr>
                                    @foreach($expenses as $expense)
                                        <tr>
                                            <td>{{ $expense->glAccountHead->name }}</td>
                                            <td class="text-right">{{ number_format(($expense->amount), 2) }}</td>
                                        </tr>
                                        @php
                                            $total_expense += $expense->amount
                                        @endphp
                                    @endforeach

                                    {{-- @foreach($due_paid as $paid)
                                        @if($paid->amount != 0)
                                            <tr>
                                                <td>{{ $paid->party->name ?? '' }}(Due Paid)</td>
                                                <td class="text-right">{{ number_format(($paid->amount), 2) }}</td>
                                            </tr>
                                        @endif
                                        @php
                                            $total_due_paid += $paid->amount;
                                        @endphp
                                    @endforeach --}}

                                    @foreach($purchases as $purchase)
                                        @if($purchase->paid != 0)
                                            <tr>
                                                <td>{{ $purchase->party->name ?? '' }}(Purchase)</td>
                                                <td class="text-right">{{ number_format(($purchase->paid - $purchase->purchase_cost), 2) }}</td>
                                            </tr>
                                        @endif
                                        @php
                                            $total_purchase_paid += $purchase->paid;
                                        @endphp
                                    @endforeach

                                    <tr>
                                        <th style="font-size: larger">Purchase Cost:</th>
                                        <th class="text-right">{{ number_format($purchases->sum('purchase_cost'), 2) }}</th>
                                    </tr>

                                    <tr>
                                        <th>Total Sale Return: </th>
                                        <td class="text-right">{{ number_format($total_sale_return,2) }}</td>
                                    </tr>

                                    <tr>
                                        <th style="font-size: larger">Transaction:</th>
                                        <th></th>
                                    </tr>
                                    @foreach($transaction_form_cash as $transaction)
                                        @if($transaction->amount != 0)
                                            <tr>
                                                <td>Bank Balance({{ $transaction->code }})</td>
                                                <td class="text-right">{{ number_format(($transaction->amount), 2) }}</td>
                                            </tr>
                                        @endif
                                        @php
                                            $total_expanse_transaction += $transaction->amount;
                                        @endphp
                                    @endforeach

                                    <tr>
                                        <td>Grand Total</td>
                                        @php
                                            $total_expense = $total_salary
                                                            + $total_sale_return
                                                            + $total_expense
                                                            + $total_expanse_transaction
                                                            + $total_purchase_paid;
                                        @endphp
                                        <td class="text-right">
                                            {{ number_format($total_expense, 2)
                                            }}
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-12">
                                <table class="table table-bordered table-sm">
                                    <tr>
                                        <td class="text-right">Cash in hand:</td>
                                        @if(request()->search)
                                            <td class="text-right">
                                                {{ number_format($total_income - $total_expense, 2) }}
                                            </td>
                                        @else
                                            <td class="text-right">
                                                {{ $cashes->sum('amount') }}
                                            </td>
                                        @endif
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-12 print-none">
                                <div class="text-right">

                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#newCashModal" title="Create new cash">
                                        <span class="d-block">Cash Close</span>
                                    </button>
                                </div>
                            </div>

                            <!-- New cash modal start -->
                            <div class="modal fade" id="newCashModal" tabindex="-1" role="dialog" aria-labelledby="insertModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <form action="{{ route('cashBook.storeBalance') }}" method="post">
                                            @csrf

                                            <div class="modal-header">
                                                <h5 class="modal-title" id="insertModalLabel">Closing Balance</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>

                                            <div class="modal-body">
                                                <div class="form-group required">
                                                    <label for="date">Date</label>
                                                    <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" id="date" required>
                                                </div>

                                                <div class="form-group required">
                                                    <input type="text" name="amount" readonly class="form-control" value="{{ $cashes->sum('amount') }}" id="amount" placeholder="0.00" step="any" required>
                                                </div>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('contents.close')</button>
                                                <button type="submit" class="btn btn-primary">@lang('contents.save')</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <!-- New cash modal end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
