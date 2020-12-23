@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Settings</li>
</ol>
@endsection
@section('content')
<script src="https://cdn.ckeditor.com/4.15.1/standard/ckeditor.js"></script>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">Sattings</h4>
                </div>
            </div>
        </div>
        <form method="POST" action="{{ url('/') }}/user/admin/update/settings" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="id" value="{{$user->id}}">

            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="input-group">
                                <label>Current Password</label>
                                <input id="name" type="password" name="old_password"
                                class="form-control @error('password') is-invalid @enderror"
                                >
                                @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12">
                            <div class="input-group">
                                <label>New Password</label>
                                <input id="name" type="password" name="password"
                                class="form-control @error('new_password') is-invalid @enderror"
                                 >
                                @error('new_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-12">
                            <div class="input-group">
                                <label>Confirm Password</label>
                                <input id="name" type="password" name="password_confirmation"
                                class="form-control @error('confirm_password') is-invalid @enderror"
                                 >
                                @error('confirm_password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>

                    </div>
                    <div class="content">



                        <div class="row">
                            <div class="col-12">
                                <textarea name="terms_and_conditions">{{$page->content}}</textarea>
                                <script>
                                    CKEDITOR.replace( 'terms_and_conditions' );
                                </script>
                            </div>

                            <div class="row">
                                <div class="col-sm-12">
                                    <button type="submit" class="btn btn-md primay-btn inline-block">Update</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
        @include('modals.googleMap',[
         'title'=>'Choose Business Address',
         'addressId'=>'#address',
         'latId'=>'#lat',
         'lngId'=>'#lng'
         ])
         @endsection
