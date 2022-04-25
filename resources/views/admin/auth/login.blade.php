@extends('layouts.auth')

@section('content')
    <!-- login section start -->
    <section class="login-section">
        <div class="cover">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6">
                        <div class="company-description pr-lg-5">
                            <div>
                                <img src="{{ asset('public/images/rasel_motors.png') }}" alt="">
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="form-content pl-lg-4">
                            <div>
                                <h2>{{ __('contents.adminLogin') }}</h2>

                                @include('layouts.partials.alert')

                                <form method="POST" action="{{ route('admin.login') }}">
                                    @csrf

                                    <div class="form-group">
                                        <input type="email" id="email" class="form-control{{ $errors->has('email') ? ' is-invalid' : '' }}" placeholder="Email address" name="email" value="{{ old('email') }}" required autofocus>

                                        @if ($errors->has('email'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('email') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="form-group">
                                        <input type="password" id="password" class="form-control{{ $errors->has('password') ? ' is-invalid' : '' }}" name="password" placeholder="Password" required>

                                        @if ($errors->has('password'))
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $errors->first('password') }}</strong>
                                            </span>
                                        @endif
                                    </div>

                                    <div class="custom-control custom-checkbox">
                                        <input class="custom-control-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="remember">{{ __('contents.rememberMe') }}</label>
                                    </div>

                                    <div class="form-group action-area">
                                        <button type="submit" class="btn">{{ __('contents.login') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- login section end -->
@endsection
