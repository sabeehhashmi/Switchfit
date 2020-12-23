@extends('layouts.app')

@section('content')

    <div class="login-banner">
        <div class="login-panel">
            <div class="logo-holder text-center">
                <img src="assets/images/login-logo.png" alt="logo"/>
                <div class="border-bottom"></div>
            </div>
            <h1>Log in</h1>
            <form method="POST" action="{{ route('admin.login') }}" class="text-center">
                @csrf
                <div class="input-field">
                    <input id="email" type="email" name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           placeholder="Email"
                           value="{{ old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                    @enderror
                </div>
                <div class="input-field">
                    <input id="password" type="password"
                           class="form-control @error('password') is-invalid @enderror" name="password"
                           placeholder="Password"
                           value="{{ old('password') }}"
                           required>
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                    @enderror
                </div>
                <div class="row">
                    <div class="col">
                        <div class="checkbox checkbox-primary text-left">
                            <input id="checkbox2" name="remember" type="checkbox" checked>
                            <label for="checkbox2">
                                Remember Me
                            </label>
                        </div>
                    </div>
                    <div class="col">
                        <a href="{{ route('admin.reset.password.page') }}" class="text-right"> Forgot Password?</a>
                    </div>
                </div>
                <div class="clearfix"></div>
                <button type="submit" class="btn btn-md primay-btn">Login</button>
            </form>
        </div>
    </div>


@endsection
