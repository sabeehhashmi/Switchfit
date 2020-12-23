@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{route('gym_owner.list')}}">Gym Owners</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
</ol>
@endsection
@section('content')

<!-- Start Page content -->
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">Edit Gym Owner </h4>
                </div>
            </div>
        </div>
        <form id="post-form" method="post" action="javascript:void(0)">

            <input type="hidden" name="id" id="gym_id" value="{{$gymOwner->id}}">
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
                                value="{{ old('name',$gymOwner->first_name) }}" required>
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
                                    <span class="invalid-feedback" role="alert" id="lat_lng_vali"
                                    style="display: none">
                                    <strong>Please, Select Location From Map</strong>
                                </span>
                                @error('address')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                                @error('lat')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
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
                                value="{{ old('email',$gymOwner->email) }}" required autocomplete="email"
                                autofocus>
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
                                maxlength="15"
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
                            <div class="checkbox checkbox-primary text-left custom-text">
                                <input id="checkbox2" name="terms_conditions" class="terms_conditions" 
                                type="checkbox" {{$gymOwner->terms_conditions==1?'checked':''}} required>
                                <label for="checkbox2">
                                    By creating account i agree with Owner's <a
                                    href="http://wantechsolutions.com/terms-condition.html"
                                    target="_blank">terms and conditions</a>
                                </label>
                                @error('terms_conditions')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-3">
                            <button type="button" onclick="updateGymOwner()"
                            class="btn btn-md primay-btn inline-block btn-action">Update
                        </button>
                    </div>
                    <div class="col-sm-3 loader-parent ">  <div class="loader"></div> </div>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="img-holder mt-4">
                    <img
                    src="{{$gymOwner->logo? asset($gymOwner->logo) : '/assets/images/attached-files/img-1.jpg'}}"
                    id="view_logo" alt="image"/>
                </div>
                <div class="croped-image-div">
                    <input id="sample_input" type="hidden" name="test[image]"/>
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


 @section('head_script')
 <style type="text/css">
    .loader-parent {
        padding: 18px;
        display: none;
    }
    .loader {
      border: 6px solid #d0cdcd; /* Light grey */
      border-top: 6px solid #3c15a3; /* Blue */
      border-radius: 50%;
      width: 40px;
      height: 40px;
      animation: spin 2s linear infinite;
  }

  @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
  }
  .modal-body .col-md-3 {
    display: none;
}
.image-field-haver {
    position: relative;
}
.progress-bar,
.progress {
    opacity: 0;
}
.cropped-image {
    width: 100%;
    margin: 0 0 30px;
}
.modal-body .cropped-image {
    width: auto;
}

.modal-dialog {
    max-width: 900px;
    margin: 1.75rem auto;
}
.modal-dialog {
    max-width: 900px;
    margin: 0.20rem auto;
}
.img-holder img{
  width: 100%
}
</style>
<script>

        /**
         * @Description Update Gym Owner
         * @Author Khuram Qadeer.
         */
         function updateGymOwner() {

            $('.btn-action').addClass('div-disable');
            var formData = new FormData();
            formData.append('_token', '{{csrf_token()}}');
            formData.append('id', $('#gym_id').val());
            formData.append('name', $('#name').val());
            formData.append('manager_name', $('#manager_name').val());
            formData.append('address', $('#address').val());
            formData.append('lat', $('#lat').val());
            formData.append('lng', $('#lng').val());
            formData.append('email', $('#email').val());
            formData.append('phone', $('#phone').val());
            formData.append('terms_conditions', $('.terms_conditions').is(':checked'));
            formData.append('logo', $('#sample_input').val());
            
            $('.loader-parent').show();
            $.ajax({
                url: "{{ route('gym_owner.update') }}",
                type: "POST",
                processData: false,
                contentType: false,
                cache: false,
                data: formData,
                enctype: 'multipart/form-data',
                success: function (response) {
                     $('.loader-parent').hide();
                    window.location.href = '{{route('gym_owner.list')}}';
                },
                error: function (res) {
                     $('.loader-parent').hide();
                    $('.btn-action').removeClass('div-disable');
                    notification('danger', res.responseJSON.message);
                }
            });
        }
    </script>
    <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script> 
<script src="{{ url('/') }}/assets/components/imgareaselect/scripts/jquery.imgareaselect.js"></script> 
<script src="{{ url('/') }}/assets/build/jquery.awesome-cropper.js"></script>
<script>
    $(document).ready(function () {

        $('#sample_input').awesomeCropper(
        { width: 150, height: 150, debug: true }
        );
        $(document).on('click', '.yes', function() {
            $('.img-holder').hide();
        });

    });
    </script>
    @endsection
