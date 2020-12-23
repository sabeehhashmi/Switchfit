@extends('layouts.app')

@section('content')
    <div class="login-banner">
        <div class="login-panel">
            <div class="logo-holder text-center">
                <img src="/assets/images/login-logo.png" alt="logo"/>
                <div class="border-bottom"></div>
            </div>
            <h1>Forgot Password</h1>
            <form class="text-center" method="post" action="{{route('admin.reset.password')}}">
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
                <div class="clearfix"></div>
                <button type="submit" class="btn btn-md primay-btn">Reset password</button>
            </form>
        </div>
    </div>
@endsection
