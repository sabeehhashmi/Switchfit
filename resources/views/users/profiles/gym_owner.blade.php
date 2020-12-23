@extends('layouts.app')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item active" aria-current="page">My Account</li>
    </ol>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="page-title-box">
                        <h4 class="page-title">My Account</h4>
                    </div>
                </div>
            </div>
            <form method="POST" action="{{ route('user.gym_owner.update.profile') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="id" value="{{$gymOwner->id}}">
                <input type="hidden" id="lat" name="lat" value="{{$gymOwner->lat}}">
                <input type="hidden" id="lng" name="lng" value="{{$gymOwner->lng}}">

                <div class="row">
                    <div class="col-sm-9">
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="input-group">
                                    <label>Name of business</label>
                                    <input id="name" type="text" name="name"
                                           class="form-control @error('name') is-invalid @enderror"
                                           placeholder=""
                                           value="{{ old('name',$gymOwner->first_name) }}" required >
                                    @error('name')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="input-group">
                                    <label>Manager Name</label>
                                    <input id="manager_name" type="text" name="manager_name"
                                           class="form-control @error('manager_name') is-invalid @enderror"
                                           placeholder=" "
                                           value="{{ old('manager_name',$gymOwner->manager_name) }}" required
                                           autocomplete="manager_name"
                                           autofocus>
                                    @error('manager_name')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12">
                                <div class="d-flex">
                                    <div class="input-group">
                                        <label>Address</label>
                                        <input type="text"
                                               class="form-control br-form  @error('address') is-invalid @enderror
                                               @error('lat') is-invalid @enderror
                                               @error('lng') is-invalid @enderror" name="address" id="address"
                                               value="{{old('address',$gymOwner->address)}}"
                                               onclick="showGoogleMapModal()"
                                               style="border-radius: 5px 0 0 5px !important;"
                                               required>
                                    </div>
                                    <a href="#" class="btn btn-location-icon" onclick="showGoogleMapModal()">
                                        <img src="/assets/images/loc-icon.png" alt="icon"> </a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-6">
                                <div class="input-group">
                                    <label>Email Address</label>
                                    <input id="email" type="email" name="email"
                                           class="form-control @error('email') is-invalid @enderror"
                                           placeholder=""
                                           value="{{ old('email',$gymOwner->email) }}"
                                           readonly>
                                    @error('email')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-6">
                                <div class="input-group">
                                    <label>Phone Number</label>
                                    <input id="phone" type="text" name="phone"
                                           class="form-control @error('phone') is-invalid @enderror"
                                           placeholder=""
                                           value="{{ old('phone',$gymOwner->phone) }}" required autocomplete="phone"
                                           maxlength="11"
                                           autofocus>
                                    @error('phone')
                                    <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-sm-12">
                                <button type="submit" class="btn btn-md primay-btn inline-block">Update</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <div class="img-holder mt-4">
                            <img
                                src="{{$gymOwner->logo? asset($gymOwner->logo) : '/assets/images/attached-files/img-1.jpg'}}"
                                id="view_logo" alt="image"/>
                        </div>
                        <div class="custom-file">
                            <input type="file"
                                   accept="image/*"
                                   name="logo" class="custom-file-input @error('logo') is-invalid @enderror"
                                   id="logo"
                                   onchange="readImageFile(this,'view_logo')" >
                            <label class="custom-file-label" for="customFile">Choose Logo</label>
                            @error('logo')
                            <span class="invalid-feedback" role="alert">
                                                            <strong>{{ $message }}</strong>
                                                        </span>
                            @enderror
                        </div>
                    </div>
                </div> <!-- end row -->
            </form>
        </div>
        <!-- container -->
    </div>
    @include('modals.googleMap',[
         'title'=>'Choose Business Address',
         'addressId'=>'#address',
         'latId'=>'#lat',
         'lngId'=>'#lng'
         ])
@endsection
