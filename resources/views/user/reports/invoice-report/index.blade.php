@extends('layouts.user')

@section('title', $title)

@section('content')
<div class="container">
	<div class="row">
		<div class="col-md-12 py-3">
            <h1 class="text-center d-none d-print-block">Shop: {{ config('print.print_details.name') }}</h1>
            <p style="margin-bottom: 0 !important;" class="text-center d-none d-print-block">Phone: {{ config('print.print_details.mobile') }}</p>
            <p class="text-center d-none d-print-block">Address: {{ config('print.print_details.address') }}</p>

			<div class="card">
				<div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="m-0">Invoice Report </h5>
                    <span class="d-none d-print-block">Print Date: {{ date('d-m-Y') }}, {{ date('H:i:s A') }}</span>
					<div class="btn-group print-none" role="group" aria-level="Action area">
                        <a href="{{ route('invoiceReport') }}" class="btn btn-info" title="Refesh list.">
                            <i class="fa fa-refresh" aria-hidden="true"></i>
                        </a>
                        <a href="#" onclick="window.print();" title="Print" class="ml-1 btn btn-warning">
                            <i aria-hidden="true" class="fa fa-print"></i>
                        </a>
					</div>
				</div>

                <div class="card-body p-0 print-none">
                    <div class="card card-body">
                        <form action="{{ route('invoiceReport') }}" method="GET">
                            <input type="hidden" name="search" value="1">
                            <div class="form-row col-md-12">
                                <div class="form-group col-md-5 required">
                                    <label for="from-date">From date</label>
                                    <input type="date" class="form-control" name="from_date" value="{{ date(request()->from_date) ?? '' }}" placeholder="From date" id="from-date" required>
                                </div>

                                <div class="form-group col-md-5">
                                    <label for="to-date">To date</label>
                                    <input type="date" class="form-control" name="to_date" value="{{ date(request()->to_date) ?? '' }}" placeholder="To date" id="to-date">
                                </div>

                                <div class="form-group col-md-2" style="margin-top: 30px">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-search"></i> &nbsp;
                                        Search
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                @if(request()->search)
                    {{-- report header --}}
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="m-0">{{ date('j F, Y', strtotime(request()->from_date)) }} To {{ date('j F, Y', strtotime(request()->to_date)) }} Dates Invoice Reports</h5>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th class="text-center">Sl</th>
                                    <th>INV No</th>
                                    <th class="text-right">Cost Value</th>
                                    <th class="text-right">Total</th>
                                    <th class="text-right">Vat</th>
                                    <th class="text-right">Discount</th>
                                    <th class="text-right">Special Discount</th>
                                    <th class="text-right">Paid</th>
                                    <th class="text-right">Payment Type</th>
                                </tr>
                            </thead>

                            {{-- <tbody> --}}
                                @php
                                    $total_purchase_price = 0;
                                    $total_sale_price = 0;
                                    $total_vat = 0;
                                    $total_discount = 0;
                                @endphp
                                @forelse($sales as $sale)
                                    <tr>
                                        @php
                                            $total_purchase_price += $sale->saleDetails->sum('purchase_price');
                                            $total_sale_price += $sale->saleDetails->sum('sale_price');
                                            $total_vat += $sale->saleDetails->sum('vat');
                                            $total_discount += $sale->saleDetails->sum('discount');
                                        @endphp
                                        <td class="text-center">{{ $loop->iteration }}.</td>
                                        <td>{{ $sale->invoice_no }}</td>
                                        <td class="text-right">{{ number_format($sale->saleDetails->sum('purchase_price'), 2) }}</td>
                                        <td class="text-right">{{ number_format($sale->saleDetails->sum('sale_price'), 2) }}</td>
                                        <td class="text-right">{{ number_format($sale->saleDetails->sum('vat'), 2) }}</td>
                                        <td class="text-right">{{ number_format($sale->saleDetails->sum('discount'), 2) }}</td>
                                        <td class="text-right">{{ number_format($sale->discount, 2) }}</td>
                                        <td class="text-right">{{ number_format(($sale->tendered - $sale->change) ,2) }}</td>
                                        <td class="text-right">{{ ucfirst($sale->payment_type) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center">No invoice report.</td>
                                    </tr>
                                @endforelse

                                <tr>
                                    <hr>
                                    <th colspan="2" class="text-right">Total </th>
                                    <th class="text-right">{{ number_format($total_purchase_price, 2) }}</th>
                                    <th class="text-right">{{ number_format($total_sale_price, 2) }}</th>
                                    <th class="text-right">{{ number_format($total_vat, 2) }}</th>
                                    <th class="text-right">{{ number_format($total_discount, 2) }}</th>
                                    <th class="text-right">{{ number_format($sales->sum('discount'), 2) }}</th>
                                    <th class="text-right">{{ number_format(($sales->sum('tendered') - $sales->sum('change')), 2) }}</th>
                                </tr>
                            </tbody>
                        </table>

                        <div class="row">
                            <div class="col-3"></div>
                            <div class="col-6">
                                <div class="card m-4 text-center">
                                    <div class="card-body">
                                        <table class="table table-sm">
                                            <tr>
                                                <td class="text-right font-weight-bold">Total Sale Amount:</td>
                                                <td class="text-right">{{ number_format($total_sale_price, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-right font-weight-bold">Total Cost Amount:</td>
                                                <td class="text-right">{{ number_format($total_purchase_price, 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-right font-weight-bold">Profit Amount:</td>
                                                <td class="text-right">{{ number_format(($total_sale_price - $total_purchase_price), 2) }}</td>
                                            </tr>
                                            <tr>
                                                <td class="text-right font-weight-bold">Profit Percent(%):</td>
                                                <td class="text-right">{{ number_format((($total_sale_price - $total_purchase_price) * 100) / ($total_purchase_price), 2) }}</td>
                                            </tr>
                                        </table>
                                  </div>
                            </div>
                            <div class="col-3"></div>
                        </div>
                    </div>
                @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
