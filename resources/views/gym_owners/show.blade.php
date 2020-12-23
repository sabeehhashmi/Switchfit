@extends('layouts.app')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{route('gym_owner.list')}}">Gym Owners</a></li>
        <li class="breadcrumb-item active" aria-current="page">Show</li>
    </ol>
@endsection
@section('content')

    <!-- Start Page content -->
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="page-title-box">
                        <h4 class="page-title">View Gym Owner </h4>
                    </div>
                </div>
            </div>

                <div class="row">
                    <div class="col-sm-9">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="input-group">
                                    <label>Name of business</label>
                                    <input id="name" type="text" name="name"
                                           class="form-control"
                                           placeholder=""
                                           value="{{ old('name',$gymOwner->first_name) }}" readonly autocomplete="email"
                                           autofocus>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="input-group">
                                    <label>Manager Name</label>
                                    <input id="manager_name" type="text" name="manager_name"
                                           class="form-control"
                                           placeholder=" "
                                           value="{{ old('manager_name',$gymOwner->manager_name) }}" readonly
                                           autocomplete="manager_name"
                                           autofocus>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12">
                                <div class="input-group">
                                    <label>Business Address</label>
                                    <input type="text" class="form-control" name="address" id="address"
                                           value="{{old('address',$gymOwner->address)}}" readonly>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="input-group">
                                    <label>Email Address</label>
                                    <input id="email" type="email" name="email"
                                           class="form-control "
                                           placeholder=""
                                           value="{{ old('email',$gymOwner->email) }}" readonly autocomplete="email"
                                           autofocus>

                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="input-group">
                                    <label>Phone Number</label>
                                    <input id="phone" type="text" name="phone"
                                           class="form-control "
                                           placeholder=""
                                           value="{{ old('phone',$gymOwner->phone) }}" readonly autocomplete="phone"
                                           autofocus>

                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="checkbox checkbox-primary text-left custom-text">
                                    <input id="checkbox2" name="terms_conditions"
                                           type="checkbox" {{$gymOwner->terms_conditions==1?'checked':''}}>
                                    <label for="checkbox2">
                                        By creating account i agree with Owner's terms and conditions
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="col-sm-3">
                        <div class="img-holder mt-4">
                            <img src="{{asset($gymOwner->logo) ?? '/assets/images/attached-files/img-1.jpg'}}"
                                 id="view_logo" alt="image"/>
                        </div>
{{--                        <div class="custom-file">--}}
{{--                            <input type="file"--}}
{{--                                   accept="image/*"--}}
{{--                                   name="logo" class="custom-file-input @error('logo') is-invalid @enderror"--}}
{{--                                   id="logo"--}}
{{--                                   onchange="readImageFile(this,'view_logo')">--}}
{{--                            <label class="custom-file-label" for="customFile">Choose Logo</label>--}}
{{--                           --}}
{{--                        </div>--}}
                    </div>
                </div> <!-- end row -->

        </div>
        <!-- container -->
    </div>

@endsection
