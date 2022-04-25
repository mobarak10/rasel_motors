@extends('layouts.user')
@section('title', $title)
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="m-0">Supplier Due Management</h5>
                        <div class="btn-group" role="group" aria-label="Action area">
                            <a href="{{ route('supplierDueManage.create') }}" class="btn btn-primary" title="">
                                <i class="fa fa-plus"></i>
                            </a>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <table class="table table-sm table-hover">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Party Name</th>
                                    <th>Payment Type</th>
                                    <th>Date</th>
                                    <th class="text-right">Amount</th>
                                    {{-- <th class="text-right">Action</th> --}}
                                </tr>
                            </thead>

                            <tbody>
                            @forelse($manage_dues as $due)
                                <tr>
                                    <td>{{ $loop->iteration }}.</td>
                                    <td>{{ $due->party->name ?? '' }}.</td>
                                    <td>{{ $due->amount > 0 ? 'Received' : 'Paid' }}</td>
                                    <td>{{ $due->date->format('d F Y') }}</td>
                                    <td class="text-right">{{ number_format(abs($due->amount), 2) }}</td>
                                    {{-- <td class="text-right">
                                        <a href="#" class="btn btn-primary" title="Update Due.">
                                            <i class="fa fa-pencil" aria-hidden="true"></i>
                                        </a>
                                    </td> --}}
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">No supplier due manage available</td>
                                </tr>
                            @endforelse
                            </tbody>
                        </table>
                        <!-- paginate -->
                        <div class="float-right mx-2">
                            {{ $manage_dues->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
