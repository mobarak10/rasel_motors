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
            <div class="invoice-two main-invoice">

                <div class="invoice-header">
                    <img class="title" src="{{ asset('public/images/rasel.svg') }}"/>
                    <p class="content">সকল প্রকার চায়না ও জাপানি ইঞ্জিনের যন্ত্রাংশ, মবিল, টিউবওয়েল, সাব মার্সেবল পাম্প, সেনেটারী সামগ্রী, পানির লাইন সামগ্রী, হার্ডওয়্যার সামগ্রী, টাইলস্ এবং রং সুলভ মূল্যে পাওয়া যায়। </p>
                    <p class="address">আলহাজ্ব শরিফুল ইসলাম মার্কেট, ধামরাই রোড, আগ্রাদ্বিগুন, নওগাঁ।</p>
                </div>

                <div class="details">
                    <div>
                        <div class="d-flex justify-content-between top">
                           <div>
                               <p class="font-weight-bold"><span class="font-italic">ক্রেতা: </span>{{ $sale->customer->name }}</p>
                           </div>
                           <div>
                               <p class="font-weight-bold"><span class="font-italic">মেমো নং:</span> {{ $sale->invoice_no }}</p>
                               <p>তারিখ: {{ $sale->date->format('d F Y') }}</p>
                           </div>
                        </div>
                    </div>
                    <table>
                        <thead>
                            <tr>
                            <th scope="col">SL</th>
                            <th scope="col" class="text-center">পণ্যের নাম</th>
                            <th scope="col" class="text-center">QTY</th>
                            <th scope="col" class="text-center">U/M</th>
                            <th scope="col" class="text-center font-italic">দর</th>
                            <th scope="col" class="text-center">Per</th>
                            <th scope="col" class="text-center">দাম</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $total_items = 0;
                            @endphp
                            @foreach($sale->saleDetails as $details)
                                @php
                                    $total_items += $details->quantity;
                                @endphp
                                <tr>
                                    <th scope="row">{{ $loop->iteration }}</th>
                                    <td>{{ $details->product->name }}</td>
                                    <td class="text-center">{{ $details->quantity }}</td>
                                    <td class="text-center">{{ $details->product->unit->name }}</td>
                                    <td class="text-center">{{ number_format($details->sale_price, 2) }}</td>
                                    <td class="text-center">{{ $details->product->unit->name }}</td>
                                    <td class="text-center">{{ number_format($details->line_total) }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <th scope="row"></th>
                                <td colspan="5" class="text-right font-weight-bold">Subtotal</td>
                                <td class="text-center font-weight-bold">{{ $sale->subtotal }}</td>
                            </tr>
                            <tr>
                                <th scope="row"></th>
                                <td>Total</td>
                                <td class="text-center">{{ $total_items }}</td>
                                <td></td>
                                <td class="text-center"></td>
                                <td></td>
                                <td class="text-center">{{ number_format($sale->grand_total, 2) }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
            <!-- End of the invoice -->
        </div>

    </div>
@endsection

