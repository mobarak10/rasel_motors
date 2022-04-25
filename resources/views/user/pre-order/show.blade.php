@extends('layouts.user')
@section('title', $title)
@push('style')
    <link href="{{ asset('public/css/invoice.css') }}" rel="stylesheet">
    <link href="{{ asset('public/fonts/font.css') }}" rel="stylesheet">
@endpush
@section('content')
    <div class="container">
        <!-- Print btn -->
        <div class="print pb-3">
            <div class="btn-group">
                <button class="btn" onclick="window.print()">
                    <i class="fa fa-print"></i>
                </button>

                <a class="btn btn-success" href="{{ route('pos.index') }}" title="Back to POS.">
                    <i class="fa fa-chevron-left" aria-hidden="true"></i>
                    &nbsp; Back
                </a>
            </div>
        </div>
        <!-- End of the Print btn -->

        <div class="row">
            <!-- Invoice -->
            <div class="invoice main-invoice">
                <!-- Invoice header -->
                <div class="order-invoice-header">
                    <div class="col-12 text-center">
                        <h4 class="text-center">Shop: {{ config('print.print_details.name') }}</h4>
                        <p style="margin-bottom: 0 !important;" class="text-center">Phone: {{ config('print.print_details.mobile') }}</p>
                        <p class="text-center">Address: {{ config('print.print_details.address') }}</p>
                    </div>
                </div>
                <!-- End of the invoice header -->

                <!-- Client details -->
                <div class="client-details">
                    <div class="row">
                        <div class="col-4">
                            <div class="single">
                                <div class="title">Billed to:</div>
                                <span>{{ $pre_order->customer->name ?? '' }}</span>
                                <span>{{ $pre_order->customer->phone ?? '' }}</span>
                                <span>{{ $pre_order->customer->address ?? '' }}</span>
                            </div>
                        </div>

                        <div class="col-4 pl-4">
                            <div class="single">
                                <div class="title">Invoice number:</div>
                                <span>{{ $pre_order->order_no }}</span>
                            </div>

                            <div class="single">
                                <div class="title">Date of issue:</div>
                                <span>{{ $pre_order->date->format('d F, Y') }}</span>
                            </div>
                        </div>

                        <div class="col-4">
                            <div class="single text-right">
                                <div class="title">Invoice total:</div>
                                <div class="total">BDT
                                    <span>{{ number_format($pre_order->pre_order_grand_total, 2) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End of the client details -->

                <!-- Description -->
                <div class="description">
                    <div class="row">
                        <div class="col-6">
                            <h5 class="text-center">Sale Details</h5>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th class="text-right">Unit Price</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Line total</th>
                                    </tr>
                                </thead>

                                <tbody>
                                @foreach($pre_order->preOrderDetails as $details)
                                    <tr>
                                        <td>
                                            <span class="text-wrap">{{ $details->product->name }}</span>
                                            {{--<p>your product details will go here</p>--}}
                                        </td>
                                        <td class="text-right">
                                            <span>{{ number_format(($details->sale_price), 2) }}</span>
                                        </td>

                                        <td class="text-right text-wrap">
                                            <span>{{ \App\Helpers\Converter::convert($details->quantity, $details->product->unit_code, 'd')['display'] }}</span>
                                        </td>
                                        <td class="text-right">
                                            <span>{{ number_format($details->line_total, 2) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="col-6">
                            <h5 class="text-center">Delivery Details</h5>
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Date</th>
                                        <th class="text-right">Quantity</th>
                                    </tr>
                                </thead>

                                <tbody>
                                @foreach($pre_order->preOrderDeliveryDetails as $details)
                                    <tr>
                                        <td>
                                            <span class="text-wrap">{{ $details->product->name }}</span>
                                            {{--<p>your product details will go here</p>--}}
                                        </td>
                                        <td>
                                            <span>{{ $details->date->format('d F Y') }}</span>
                                        </td>
                                        <td class="text-right">
                                            <span>
                                                {{ \App\Helpers\Converter::convert($details->delivery_quantity, $details->product->unit_code, 'd')['display'] }}
                                            </span>
                                        </td>

                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- End of the description -->

                <!-- Terms and total -->
                <div class="terms-and-total">
                    <div class="row align-items-center">
                        <div class="col-12">
                            <!-- Total -->
                            <div class="total text-right">
                                <div class="single">
                                    Subtotal <span>{{ number_format($pre_order->subtotal, 2) }}</span>
                                </div>

                                <div class="single">
                                    Discount <span>{{ number_format($pre_order->discount, 2) }}</span>
                                </div>

                                @if($pre_order->labour_cost > 0)
                                    <div class="single">
                                        Labour Cost <span>{{ number_format($pre_order->labour_cost, 2) }}</span>
                                    </div>
                                @endif

                                @if($pre_order->transport_cost > 0)
                                    <div class="single">
                                        Transport Cost <span>{{ number_format($pre_order->transport_cost, 2) }}</span>
                                    </div>
                                @endif

                                <div class="single">
                                    Grand Total <span>{{ number_format($pre_order->grand_total, 2) }}</span>
                                </div>

                                <div class="single">
                                    Paid <span>{{ number_format($pre_order->paid, 2) }}</span>
                                </div>

                                @if($pre_order->due)
                                    <div class="single">
                                        Due <span>{{ number_format($pre_order->due, 2) }}</span>
                                    </div>
                                @endif
                            </div>
                            <!-- End of the total -->

                        </div>
                        <div class="col-md-12 text-center mt-4 print-none">
                            Note: {{ $pre_order->comment }}
                        </div>
                    </div>
                </div>
                <!-- End of the terms and total -->
                <!-- Description -->

                <!-- Footer -->
                <div class="footer">
                    <div class="row d-flex align-items-center">
                        <div class="col-6">
                            <div class="customer-signature">
                                Customer signature
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="signature">
                                Authorized signature
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 text-center">
                        Thank you for your business
                    </div>
                </div>
                <!-- End of the footer -->

            </div>
            <!-- End of the invoice -->

        </div>

    </div>
@endsection

