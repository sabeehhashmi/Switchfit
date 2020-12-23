@extends('layouts.app')

@section('content')

    <div class="login-banner">
        <div class="login-panel">
            <div class="logo-holder text-center">
                <img src="/assets/images/login-logo.png" alt="logo"/>
                <div class="border-bottom"></div>
            </div>
            <h1>Forgot Password</h1>
            <form class="text-center" method="post" action="{{route('admin.update.confirm.password',$token)}}">
                @csrf
                <div class="input-field">
                    <input id="password" type="password"
                           class="form-control @error('password') is-invalid @enderror" name="password"
                           placeholder="New Password"
                           required autocomplete="new-password">
                    @error('password')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>

                <div class="input-field">
                    <input id="confirm_password" type="password"
                           class="form-control @error('confirm_password') is-invalid @enderror"
                           placeholder="Confirm Password"
                           name="confirm_password" required autocomplete="current-password">
                    @error('confirm_password')
                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                    @enderror
                </div>

                <div class="clearfix"></div>
                <button type="submit" class="btn btn-md primay-btn">Confirm Password</button>
            </form>
        </div>
    </div>

@endsection
