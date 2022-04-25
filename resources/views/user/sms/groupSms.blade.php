@extends('layouts.user')
@section('title', $title)

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="header">
                    <h4 class="main-title">Group SMS Send</h4>
                    <p><small>Can send <strong>Group SMS</strong> from here.</small></p>
                    <p><small>About {{ isset($customers) ? count($customers) : 0 }} results found.</small></p>
                </div>
                <div class="mt-2">
                    <p class="mb-2 text-muted"> 1. If don't want send SMS Please Uncheck Customer.</p>
                    <p class="mb-2 text-muted"> 2. Type Message  and then click Send button to Send SMS.</p>
                    {{-- <div class="mt-4">
                        <strong>
                            <span>Total SMS: 500 </span> &nbsp; &nbsp;
                            <span>Send SMS: 100</span> &nbsp; &nbsp;
                            <span>Remaining SMS: 400</span>
                        </strong>
                    </div> --}}
                </div>

            </div>

        </div>

        <div class="row mt-3">
            <div class="col-12">
                <div class="d-block">
                    <form action="{{ route('sms.groupSms') }}" method="POST">
                        @csrf
                        <div class="mb-3 table-responsive">
                            <table class="table custom-table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col" class="p-0">
                                            <label for="check-all" class="p-2 d-block">
                                                <input type="checkbox" class="me-2" id="check-all">
                                                <span>SL </span>
                                            </label>
                                        </th>
                                        <th scope="col">Customer Name</th>
                                        <th scope="col">Mobile No</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @forelse($customers as $index=> $customer)
                                        <tr>
                                            <th scope="row" class="p-0">
                                                <label class="p-2 d-block">
                                                    <input type="checkbox" name="mobiles[]" value="{{ $customer->phone }}" class="me-2">
                                                        {{ $index + $customers->firstItem() }}.
                                                </label>
                                            </th>
                                            <td>{{ $customer->name }}</td>
                                            <td>{{ $customer->phone}}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <th colspan="7">No customer here </th>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                            <!-- data table end -->
                            <!-- paginate -->
                        <div class="print-none">
                            <nav aria-label="Page navigation example" class="d-flex justify-content-end">
                                {{ (isset($customers)) ? optional($customers)->links() : '' }}
                            </nav>
                        </div>
                            <!-- pagination end -->

                        <div class="mb-3 row">
                          <!-- Write Message Start-->
                            <div class="col-12">
                                <label for="message" class="mt-1 form-label required">Message</label>
                                    <textarea name="message" class="form-control" id="message" rows="4"
                                        placeholder="Type message here.." required>{{ old('message')}}</textarea>

                                        <!-- error -->
                                        @error('message')
                                            <small class="text-danger">{{ $message }}</small>
                                        @enderror
                            </div>
                                <!-- Write Message End-->

                                <!-- SMS & Character count start -->
                            <div class="col-md-12 mt-3">
                                <p class="text-muted">
                                    <span>
                                        <strong>Total Characters</strong>
                                            <input type="text" id="total_characters" name="total_characters" value="21" readonly>
                                    </span>
                                    &nbsp;
                                    <span>
                                        <strong>Total Messages</strong>
                                            <input  type="text" id="total_messages" value="1" name="total_messages" readonly>
                                    </span>
                                </p>
                            </div>
                            <!-- SMS & Character count end -->
                        </div>

                        <div class="mb-3 row">
                            <div class="col-2">
                                <label class="mt-1 form-label">&nbsp;</label>
                            </div>

                            <div class="col-12">
                                <button type="reset" class="btn btn-danger me-2">
                                    <i class="bi bi-dash-square"></i>
                                    <span class="p-1">Reset</span>
                                </button>

                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-envelope"></i>
                                    <span class="p-1">Send</span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>

    </div>
@endsection

    @push('script')
        <!-- checked all program script -->
        <script>
            // select master & child checkboxes
            let masterCheckbox = document.getElementById("check-all"),
                childCheckbox = document.querySelectorAll('[name="mobiles[]"]');
            // add 'change' event into master checkbox
            masterCheckbox.addEventListener("change", function() {
                // add/remove attribute into child checkbox conditionally
                for (var i = 0; i < childCheckbox.length; i++) {
                    if(this.checked) {
                        childCheckbox[i].checked = true; // add attribute
                    } else {
                        childCheckbox[i].checked = false; // add attribute
                    }
                }
            });
        </script>
        <!-- checked all program script end -->

        <!-- SMS & Character count js start -->
        <script src="{{ asset('/public/js/sms/sms.js') }}"></script>
        <!-- SMS & Character count js end -->
    @endpush


