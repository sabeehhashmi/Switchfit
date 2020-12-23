@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{route('trainer.index')}}">Trainers</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
</ol>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">Add Personal Trainer </h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-7">
                <input type="hidden" id="lng" name="lng" value="">
                <input type="hidden" id="lat" name="lat" value="">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="input-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" maxlength="100">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="input-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" maxlength="100">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="input-group">
                            <label>Date of Birth</label>
                            <input type="text" class="form-control" id="date_of_birth" name="date_of_birth">
                            <span class="fa fa-calendar input-calendar"></span>

                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="input-group">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone" placeholder="03227890778">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6 col-sm-6">
                        <div class="input-group">
                            <label>Email</label>
                            <input type="text" class="form-control" name="email" id="email">
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6">
                        <div class="input-group">
                            <label>Gender</label>
                            <select class="form-control custom-select" name="gender" id="gender">
                                <option value="" selected>Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-12">
                        <div class="d-flex">
                            <div class="input-group">
                                <label>Address</label>
                                <input type="text"
                                class="form-control br-form " name="address" id="address"
                                value=""
                                onclick="showGoogleMapModal()"
                                style="border-radius: 5px 0 0 5px !important;"
                                required>
                            </div>
                            <a href="#" class="btn btn-location-icon" onclick="showGoogleMapModal()">
                                <img src="/assets/images/loc-icon.png" alt="icon" class=""> </a>

                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="input-group">
                                <label>Country</label>
                                <input type="text" class="form-control"
                                name="country" id="country" value="">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="input-group">
                                <label>State</label>
                                <input type="text" class="form-control"
                                name="state" id="state" value="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="input-group">
                                <label>City</label>
                                <input type="text" class="form-control "
                                name="city" id="city" value="">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="input-group">
                                <label>Postal code</label>
                                <input type="text" class="form-control  "
                                name="postal_code" id="postal_code" value="">

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-group">
                                <label>About </label>
                                <textarea class="form-control" id="about" name="about" maxlength="1000"></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="input-group">
                                <label>Qualifications</label>
                                <textarea class="form-control" id="qualification_1" name="qualification_1"></textarea>
                            </div>
                        </div>
                    </div>
                    
                    @if(\App\Category::all())
                    <div class="row mt-2">
                        <div class="col-sm-12">
                            <div class="input-group">
                                <label>Categories</label>
                            </div>
                            <div class="padding-5">
                                @foreach(\App\Category::all() as $i=>$category)
                                <div
                                class="checkbox form-check-inline checkbox-primary text-left custom-text">
                                <input type="checkbox" id="a{{$i}}" name="categories[]" class="categories"
                                value="{{$category->id}}"
                                >
                                <label for="i{{$i}}"> {{$category->name}} </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <div class="col-sm-5">

                <div class="profile-img">
                    <div class="img-holder mt-4">
                        <img src="{{old('view_logo','/assets/images/attached-files/img-1.jpg')}}" id="view_logo"
                        name="view_logo" alt="image"/>
                    </div>
                    <div class="croped-image-div">
                        <input id="sample_input" type="hidden" name="test[image]"/>
                    </div>

                    {{--                        <a href="#" class="btn btn-verify"><img src="/assets/images/verify-icon.png"> Verified</a>--}}
                </div>
                <div class="input-group">
                    <label>Availability </label>
                </div>
                <div class="row ">
                    <div class="col-sm-12">

                        <div class="d-flex height-40">
                            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                                <input type="checkbox" id="monday" name="days[]" class="cbx_days" value="monday" data-dayid="1"
                                onchange="daySelect(this)" checked>
                                <label for="monday"> Monday </label>
                            </div>

                            <div class="input-group">
                                <select class="form-control custom-select br-0 " name="monday_start"
                                id="monday_start">
                                {!! getHoursInHTMLDropDownOptions() !!}
                            </select>
                        </div>
                        <div class="input-group">
                            <select class="form-control custom-select bl-0 " name="monday_end"
                            id="monday_end">
                            {!! getHoursInHTMLDropDownOptions('12:00') !!}
                        </select>
                    </div>
                </div>
                @error('monday')
                <span class="invalid-feedback" role="alert" style="display: block;">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="row ">
            <div class="col-sm-12">
                <div class="d-flex height-40">
                    <div class="checkbox checkbox-primary text-left custom-text mt-2">
                        <input type="checkbox" id="tuesday" name="days[]" class="cbx_days" value="tuesday"
                        onchange="daySelect(this)" checked data-dayid="2">
                        <label for="tuesday"> Tuesday </label>
                    </div>
                    <div class="input-group">
                        <select class="form-control custom-select br-0" name="tuesday_start"
                        id="tuesday_start">
                        {!! getHoursInHTMLDropDownOptions() !!}
                    </select>
                </div>
                <div class="input-group">
                    <select class="form-control custom-select bl-0" name="tuesday_end"
                    id="tuesday_end">
                    {!! getHoursInHTMLDropDownOptions('12:00') !!}
                </select>
            </div>
        </div>
        @error('tuesday')
        <span class="invalid-feedback" role="alert" style="display: block;">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
    </div>
</div>
<div class="row ">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="wednesday" name="days[]" class="cbx_days"
                value="wednesday"
                onchange="daySelect(this)" checked data-dayid="3">
                <label for="wednesday"> Wednesday </label>
            </div>
            <div class="input-group">
                <select class="form-control custom-select br-0" name="wednesday_start"
                id="wednesday_start">
                {!! getHoursInHTMLDropDownOptions() !!}
            </select>
        </div>
        <div class="input-group">
            <select class="form-control custom-select bl-0" name="wednesday_end"
            id="wednesday_end">
            {!! getHoursInHTMLDropDownOptions('12:00') !!}
        </select>
    </div>
</div>
@error('wednesday')
<span class="invalid-feedback" role="alert" style="display: block;">
    <strong>{{ $message }}</strong>
</span>
@enderror
</div>
</div>
<div class="row ">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="thursday" name="days[]" class="cbx_days" value="thursday"
                onchange="daySelect(this)" checked data-dayid="4">
                <label for="thursday"> Thursday </label>
            </div>
            <div class="input-group">
                <select class="form-control custom-select br-0 " name="thursday_start"
                id="thursday_start">
                {!! getHoursInHTMLDropDownOptions() !!}
            </select>
        </div>
        <div class="input-group">
            <select class="form-control custom-select bl-0 " name="thursday_end"
            id="thursday_end">
            {!! getHoursInHTMLDropDownOptions('12:00') !!}
        </select>
    </div>
</div>
@error('thursday')
<span class="invalid-feedback" role="alert" style="display: block;">
    <strong>{{ $message }}</strong>
</span>
@enderror
</div>
</div>
<div class="row ">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="friday" name="days[]" class="cbx_days" value="friday"
                onchange="daySelect(this)" checked data-dayid="5">
                <label for="friday"> Friday </label>
            </div>
            <div class="input-group">
                <select class="form-control custom-select br-0" name="friday_start"
                id="friday_start">
                {!! getHoursInHTMLDropDownOptions() !!}
            </select>
        </div>
        <div class="input-group">
            <select class="form-control custom-select bl-0" name="friday_end"
            id="friday_end"
            >
            {!! getHoursInHTMLDropDownOptions('12:00') !!}
        </select>
    </div>
</div>
@error('friday')
<span class="invalid-feedback" role="alert" style="display: block;">
    <strong>{{ $message }}</strong>
</span>
@enderror
</div>
</div>
<div class="row ">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="saturday" name="days[]" class="cbx_days" value="saturday"
                onchange="daySelect(this)" checked data-dayid="6">
                <label for="saturday"> Saturday </label>
            </div>
            <div class="input-group">
                <select class="form-control custom-select br-0" name="saturday_start"
                id="saturday_start">
                {!! getHoursInHTMLDropDownOptions() !!}
            </select>
        </div>
        <div class="input-group">
            <select class="form-control custom-select bl-0" name="saturday_end"
            id="saturday_end">
            {!! getHoursInHTMLDropDownOptions('12:00') !!}
        </select>
    </div>
</div>
@error('saturday')
<span class="invalid-feedback" role="alert" style="display: block;">
    <strong>{{ $message }}</strong>
</span>
@enderror
</div>
</div>
<div class="row ">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="sunday" name="days[]" class="cbx_days" value="sunday"
                onchange="daySelect(this)" checked data-dayid="7">
                <label for="sunday"> Sunday </label>
            </div>
            <div class="input-group">
                <select class="form-control custom-select br-0" name="sunday_start"
                id="sunday_start">
                {!! getHoursInHTMLDropDownOptions() !!}
            </select>
        </div>
        <div class="input-group">
            <select class="form-control custom-select bl-0" name="sunday_end"
            id="sunday_end">
            {!! getHoursInHTMLDropDownOptions('12:00') !!}
        </select>
    </div>
</div>
@error('sunday')
<span class="invalid-feedback" role="alert" style="display: block;">
    <strong>{{ $message }}</strong>
</span>
@enderror
</div>
</div>

<div class="row mt-4">
    <div class="col-sm-4">
        <div class="input-group mt-2">
            <label>Verification</label>
        </div>
        <div class="photo-list verification-img">
            <div class="upload-btn-wrapper" id="div_document">
                <div class="image-holder">
                    <a href="#"><img
                        src="/assets/images/upload-img.png" id="img_doc"
                        alt="Placeholder"></a>
                    </div>
                    <input id="sample_input_doc" type="hidden" name="test[image]"/>
                </div>
            </div>
        </div>
        <div class="col-sm-8" style="padding: 0 15px 0 5px;">
            <div class="input-group mt-2">
                <label>Doc Type</label>
                <select class="form-control custom-select" name="document_type" id="document_type">
                    <option value="passport">Passport</option>
                    <option value="cnic">CNIC</option>
                    <option value="visa">Visa</option>
                </select>
            </div>
            <div class="input-group mt-2">
                <label>Expiry Date</label>
                <input type="text" class="form-control" name="document_expire_date"
                id="document_expire_date">
            </div>
        </div>
    </div>
</div>
</div> <!-- end row -->
<div class="row">
    <div class="col-sm-10">
        <button type="button" onclick="createTrainer()"
        class="btn btn-md primay-btn pull-right inline-block btn-action">Create
    </button>
    
</div>
<div class="col-sm-2 loader-parent ">  <div class="loader"></div> </div>
</div>
</div>
<!-- container -->
</div>
@endsection

@section('modals')
{{--    <!-- googleMap -->--}}
@include('modals.googleMap',[
    'title'=>'Choose Trainer Address',
    'addressId'=>'#address',
    'latId'=>'#lat',
    'lngId'=>'#lng',
    'postalCodeId'=>'#postal_code',
    'countryId'=>'#country',
    'stateId'=>'#state',
    'cityId'=>'#city',
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
    .croped-image-div, .img-holder {
        width: 295px;
        margin-left: 31px;
        margin-top: 40px;
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
    .verification-img .upload-btn-wrapper img.cropped-image{
        height: auto !important;
    }
    .verification-img .custom-file-label::after,.verification-img .custom-file-input:lang(en) ~ .custom-file-label::after{
        content:'';
        padding: 0px;

    }
</style>
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script> 
<script src="{{ url('/') }}/assets/components/imgareaselect/scripts/jquery.imgareaselect.js"></script> 
<script src="{{ url('/') }}/assets/build/jquery.awesome-cropper.js"></script>
<script>
    $(document).ready(function () {

        $('#sample_input').awesomeCropper(
            { width: 150, height: 150, debug: true }
            );
        $('#sample_input_doc').awesomeCropper(
            { width: 150, height: 150, debug: true }
            );
        $(document).on('click', '.yes', function() {
            $(this).parent().parent().parent().parent().parent().parent().parent().parent().find('#view_logo').hide();
            $(this).parent().parent().parent().parent().parent().parent().parent().find('.image-holder').hide();
        });

    });
</script>
<link href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" rel="Stylesheet" type="text/css"/>
{{--        <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>--}}
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>

<script>
    $(document).ready(function () {
        $("#date_of_birth").datepicker({
            defaultDate: new Date(2002, 1 - 1, 1),
            maxDate: new Date(),
            yearRange: "-60:+0",
            dateFormat: "dd/mm/yy",
            changeYear: true,
            changeMonth: true
        });
        $("#document_expire_date").datepicker({
            minDate: new Date(),
            changeYear: true,
            dateFormat: "dd/mm/yy",
            changeMonth: true
        });
    });
    $(document).ready(function () {
        $('.custom-select').change(function (e) {

            current_id = this.id.split('_');
            start_val = $('#'+current_id[0]+'_start').val();
            end_val = $('#'+current_id[0]+'_end').val();
            if(Date.parse('01/01/2011 '+ end_val) < Date.parse('01/01/2011 '+start_val)){
                notification('danger', 'End Time Should be greater than start time');
                
            }
            
        });
    });

        /**
         * @Description Save Trainer
         * @Author Khuram Qadeer.
         */
         function createTrainer() {
            var availability = [];
            $('.cbx_days ').each(function (i, el) {
                var day = $(el).attr('id');
                var day_id = $(el).data('dayid');
                var time_start = $('#' + day + '_start').val();
                var time_end = $('#' + day + '_end').val();
                var available = 0;
                if ($(el).is(':checked')) {
                    var available = 1;

                }
                availability.push({
                    day,
                    time_start,
                    time_end,
                    day_id,
                    available
                })
            });

            var categories = [];
            $('.categories ').each(function (i, el) {
                if ($(el).is(':checked')) {
                    var id = $(el).val();
                    categories.push({
                        id: parseInt(id)
                    })
                }
            });

            $('.btn-action').addClass('div-disable');
            var formData = new FormData();
            formData.append('_token', '{{csrf_token()}}');
            formData.append('first_name', $('#first_name').val());
            formData.append('last_name', $('#last_name').val());
            formData.append('email', $('#email').val());
            formData.append('date_of_birth', $('#date_of_birth').val());
            formData.append('gender', $('#gender').val());
            formData.append('phone', $('#phone').val());
            formData.append('address', $('#address').val());
            formData.append('lat', $('#lat').val());
            formData.append('lng', $('#lng').val());
            formData.append('country', $('#country').val());
            formData.append('state', $('#state').val());
            formData.append('city', $('#city').val());
            formData.append('postal_code', $('#postal_code').val());
            formData.append('about', $('#about').val());
            formData.append('qualification_1', $('#qualification_1').val());
            formData.append('document_type', $('#document_type option:selected').val());
            formData.append('document_expire_date', $('#document_expire_date').val());
            formData.append('document', $('#div_document input[type=file]')[0].files[0]);
            formData.append('document', $('#sample_input_doc').val());
            formData.append('avatar', $('#sample_input').val());
            formData.append('availability', JSON.stringify(availability));
            formData.append('categories', JSON.stringify(categories));
            $('.loader-parent').show();
            $.ajax({
                url: "{{ route('trainer.store') }}",
                type: "POST",
                processData: false,
                contentType: false,
                cache: false,
                data: formData,
                enctype: 'multipart/form-data',
                success: function (res) {
                   $('.loader-parent').hide();
                   window.location.href = '{{route('trainer.index')}}';
               },
               error: function (res) {
                   $('.loader-parent').hide();
                   $('.btn-action').removeClass('div-disable');
                   notification('danger', res.responseJSON.message);
               }
           });
        }
    </script>

    @endsection
