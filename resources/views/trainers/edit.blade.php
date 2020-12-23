@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{route('trainer.index')}}">Trainers</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
</ol>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">Edit Personal Trainer </h4>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-7">
                <input type="hidden" id="lng" name="lng" value="{{$trainer['lng']}}">
                <input type="hidden" id="lat" name="lat" value="{{$trainer['lat']}}">
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="input-group">
                            <label>First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name"
                            value="{{$trainer['first_name']}}">
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="input-group">
                            <label>Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name"
                            value="{{$trainer['last_name']}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6">
                        <div class="input-group">
                            <label>Date of Birth</label>
                            @php
                            $date_of_birth = ($trainer['date_of_birth'])?date("d/m/Y", strtotime($trainer['date_of_birth'])):'';
                            @endphp
                            <input type="text" class="form-control" id="date_of_birth" name="date_of_birth"
                            value="{{$date_of_birth}}">
                            <span class="fa fa-calendar input-calendar"></span>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6">
                        <div class="input-group">
                            <label>Phone Number</label>
                            <input type="text" class="form-control" id="phone" name="phone"
                            value="{{$trainer['phone']}}">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-6 col-sm-6">
                        <div class="input-group">
                            <label>Email</label>
                            <input type="text" class="form-control" name="email" id="email"
                            value="{{$trainer['email']}}" disabled>
                        </div>
                    </div>
                    <div class="col-xs-6 col-sm-6">
                        <div class="input-group">
                            <label>Gender</label>
                            <select class="form-control custom-select" name="gender" id="gender">
                                <option value="" {{!$trainer['gender'] ?'selected':''}}>Select Gender</option>
                                <option value="male" {{$trainer['gender']=='male'?'selected':''}}>Male</option>
                                <option value="female" {{$trainer['gender']=='female'?'selected':''}}>Female
                                </option>
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
                                value="{{$trainer['address']}}"
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
                                name="country" id="country" value="{{$trainer['country']}}">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="input-group">
                                <label>State</label>
                                <input type="text" class="form-control"
                                name="state" id="state" value="{{$trainer['state']}}">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="input-group">
                                <label>City</label>
                                <input type="text" class="form-control "
                                name="city" id="city" value="{{$trainer['city']}}">
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="input-group">
                                <label>Postal code</label>
                                <input type="text" class="form-control  "
                                name="postal_code" id="postal_code" value="{{$trainer['postal_code']}}">

                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-group">
                                <label>About </label>
                                <textarea class="form-control" id="about" name="about"
                                maxlength="1000">{{$trainer['about']}}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="input-group">
                                <label>Qualifications</label>
                                <textarea class="form-control" id="qualification_1" name="qualification_1">{{$trainer['qualification_1']}}</textarea>
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
                                {{ inArrayByKeyAndValue($trainer['categories'],'id',(int)$category->id)==1 ?'checked':''}}
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
                    <div class="img-holder">
                        <img src="{{$trainer['avatar'] ? asset($trainer['avatar']) :'/assets/images/upload-img.png'}}"                         name="view_logo" alt="image" id="view_logo"/>
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
                                <input type="checkbox" id="monday" name="days[]" class="cbx_days" value="monday"
                                onchange="daySelect(this)" {{checkday($trainer['id'],1)==1?'checked':''}}
                                data-dayid="1">
                                <label for="monday"> Monday </label>
                            </div>

                            <div class="input-group">
                                <select
                                class="form-control custom-select br-0 {{ checkday($trainer['id'],1)==0 ?'div-disable':''}}"
                                name="monday_start"
                                id="monday_start">
                                {!! getStartHoursDropDownOptions($trainer['id'],1) !!}
                            </select>
                        </div>

                        <div class="input-group">
                            <select
                            class="form-control custom-select bl-0 {{ checkday($trainer['id'],1)==0?'div-disable':''}}"
                            name="monday_end"
                            id="monday_end">
                            {!! getEndHoursDropDownOptions($trainer['id'],1) !!}
                        </select>
                    </div>
                </div>

            </div>
        </div>

        <div class="row ">
            <div class="col-sm-12">
                <div class="d-flex height-40">
                    <div class="checkbox checkbox-primary text-left custom-text mt-2">
                        <input type="checkbox" id="tuesday" name="days[]" class="cbx_days" value="tuesday" data-dayid="2"
                        onchange="daySelect(this)"
                        {{checkday($trainer['id'],2)==1?'checked':''}}>
                        <label for="tuesday"> Tuesday </label>
                    </div>
                    <div class="input-group">
                        <select
                        class="form-control custom-select br-0 {{ checkday($trainer['id'],2)==0 ?'div-disable':''}}"
                        name="tuesday_start"
                        id="tuesday_start">
                        {!! getStartHoursDropDownOptions($trainer['id'],2) !!}
                    </select>
                </div>

                <div class="input-group">
                    <select
                    class="form-control custom-select bl-0 {{ checkday($trainer['id'],2)==0 ?'div-disable':''}}"
                    name="tuesday_end"
                    id="tuesday_end">
                    {!! getEndHoursDropDownOptions($trainer['id'],2) !!}
                </select>
            </div>
        </div>

    </div>
</div>
<div class="row ">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="wednesday" name="days[]" class="cbx_days"
                value="wednesday" data-dayid="3"
                onchange="daySelect(this)"
                {{checkday($trainer['id'],3)==1?'checked':''}}>
                <label for="wednesday"> Wednesday </label>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select br-0 {{ checkday($trainer['id'],3)==0 ?'div-disable':''}}"
                name="wednesday_start"
                id="wednesday_start">
                {!! getStartHoursDropDownOptions($trainer['id'],3) !!}
            </select>
        </div>

        <div class="input-group">
            <select
            class="form-control custom-select bl-0 {{  checkday($trainer['id'],3)==0?'div-disable':''}}"
            name="wednesday_end"
            id="wednesday_end">
            {!! getEndHoursDropDownOptions($trainer['id'],3) !!}
        </select>
    </div>
</div>
</div>
</div>
<div class="row ">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="thursday" name="days[]" class="cbx_days" value="thursday" data-dayid="4"
                onchange="daySelect(this)"
                {{checkday($trainer['id'],4)==1?'checked':''}}>
                <label for="thursday"> Thursday </label>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select br-0 {{  checkday($trainer['id'],4)==0 ?'div-disable':''}}"
                name="thursday_start"
                id="thursday_start">
                {!! getStartHoursDropDownOptions($trainer['id'],4) !!}
            </select>
        </div>

        <div class="input-group">
            <select
            class="form-control custom-select bl-0 {{  checkday($trainer['id'],4)==0 ?'div-disable':''}}"
            name="thursday_end"
            id="thursday_end">
            {!! getEndHoursDropDownOptions($trainer['id'],4) !!}
        </select>
    </div>
</div>
</div>
</div>
<div class="row ">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="friday" data-dayid="5" name="days[]" class="cbx_days" value="friday"
                onchange="daySelect(this)" 
                {{checkday($trainer['id'],5)==1?'checked':''}}>
                <label for="friday"> Friday </label>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select br-0 {{  checkday($trainer['id'],5)==0?'div-disable':''}}"
                name="friday_start"
                id="friday_start">
                {!! getStartHoursDropDownOptions($trainer['id'],5) !!}
            </select>
        </div>

        <div class="input-group">
            <select
            class="form-control custom-select bl-0 {{ checkday($trainer['id'],5)==0 ?'div-disable':''}}"
            name="friday_end"
            id="friday_end">
            {!! getEndHoursDropDownOptions($trainer['id'],5) !!}
        </select>
    </div>
</div>

</div>
</div>
<div class="row ">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="saturday" name="days[]" class="cbx_days" value="saturday" data-dayid="6"
                onchange="daySelect(this)"
                {{checkday($trainer['id'],6)==1?'checked':''}}>
                <label for="saturday"> Saturday </label>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select br-0 {{ checkday($trainer['id'],6)==0 ?'div-disable':''}}"
                name="saturday_start"
                id="saturday_start">
                {!! getStartHoursDropDownOptions($trainer['id'],6) !!}
            </select>
        </div>

        <div class="input-group">
            <select
            class="form-control custom-select bl-0 {{  checkday($trainer['id'],6)==0 ?'div-disable':''}}"
            name="saturday_end"
            id="saturday_end">
            {!! getEndHoursDropDownOptions($trainer['id'],6) !!}
        </select>
    </div>
</div>
</div>
</div>
<div class="row ">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="sunday" name="days[]" class="cbx_days" value="sunday" data-dayid="7"
                onchange="daySelect(this)"
                {{checkday($trainer['id'],7)==1?'checked':''}}>
                <label for="sunday"> Sunday </label>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select br-0 {{ checkday($trainer['id'],7)==0 ?'div-disable':''}}"
                name="sunday_start"
                id="sunday_start">
                {!! getStartHoursDropDownOptions($trainer['id'],7) !!}
            </select>
        </div>

        <div class="input-group">
            <select
            class="form-control custom-select bl-0 {{  checkday($trainer['id'],7)==0 ?'div-disable':''}}"
            name="sunday_end"
            id="sunday_end">
            {!! getEndHoursDropDownOptions($trainer['id'],7) !!}
        </select>
    </div>
</div>
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
                        src="{{$trainer['document'] ? asset($trainer['document']) :'/assets/images/upload-img.png'}}" id="img_doc"
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
                        <option
                        value="passport" {{strtolower($trainer['gender'])=='passport'?'selected':''}}>
                        Passport
                    </option>
                    <option value="cnic" {{strtolower($trainer['gender'])=='cnic'?'selected':''}}>CNIC
                    </option>
                    <option value="visa" {{strtolower($trainer['gender'])=='visa'?'selected':''}}>Visa
                    </option>
                </select>
            </div>
            <div class="input-group mt-2">
                <label>Expiry Date</label>
                @php
                $document_expire_date = ($trainer['document_expire_date'])?date("d/m/Y", strtotime($trainer['document_expire_date'])):'';
                @endphp
                <input type="text" class="form-control" name="document_expire_date"
                id="document_expire_date" value="{{$document_expire_date}}">
                <span class="fa fa-calendar input-calendar"></span>
            </div>
        </div>
    </div>
</div>
</div> <!-- end row -->
<div class="row">
    <div class="col-sm-10">
        <button type="button" onclick="updateTrainer()"
        class="btn btn-md primay-btn pull-right inline-block btn-action">Update
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
    .verification-img .upload-btn-wrapper img.cropped-image{
        height: auto !important;
    }
    .verification-img .custom-file-label::after,.verification-img .custom-file-input:lang(en) ~ .custom-file-label::after{
        content:'';
        padding: 0px;

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


<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script> 
<script src="{{ url('/') }}/assets/components/imgareaselect/scripts/jquery.imgareaselect.js"></script> 
<script src="{{ url('/') }}/assets/build/jquery.awesome-cropper.js"></script>
<link href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" rel="Stylesheet" type="text/css"/>
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script type="text/javascript" src="{{ url('/') }}/assets/js/html5lightbox.js"></script>
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
<script>
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
 $(document).ready(function () {
    $("#date_of_birth").datepicker({
        defaultDate: new Date(2002, 1 - 1, 1),
       changeYear: true,
       dateFormat: "dd/mm/yy",
       yearRange: "-60:+0",
       changeMonth: true
   });
    $("#document_expire_date").datepicker({
        minDate: new Date(),
        changeYear: true,
        dateFormat: "dd/mm/yy",
        changeMonth: true
    });
});

        /**
         * @Description Update Trainer Profile
         * @Author Khuram Qadeer.
         */
         function updateTrainer() {
            $('.loader-parent').show();
            var availability = [];
            $('.cbx_days ').each(function (i, el) {
                var day = $(el).attr('id');
                var available = 0;
                var day_id = $(el).data('dayid');
                if ($(el).is(':checked')) {
                    var available = 1;
                }
                var time_start = $('#' + day + '_start').val();
                var time_end = $('#' + day + '_end').val();

                availability.push({
                    day,
                    time_start,
                    time_end,
                    available,
                    day_id
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
            formData.append('document', $('#sample_input_doc').val());
            formData.append('avatar', $('#sample_input').val());
            formData.append('availability', JSON.stringify(availability));
            formData.append('categories', JSON.stringify(categories));

            $.ajax({
                url: "{{ route('update.trainer',$trainer['id']) }}",
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

        /**
         * @Description update Verified Status of trainer
         * @param el
         * @Author Khuram Qadeer.
         */
         function updateVerifiedOrNot(el) {
            Notiflix.Confirm.Show('Confirmation','Would you verified to {{\App\User::getFullName($trainer['id'])}}?', true, false,
                function () {
                    var id = '{{$trainer['id']}}';
                    var status = $(el).is(':checked') ? 1 : 0;

                    $.ajax({
                        url: '{{route('update.trainer.verified')}}',
                        type: "POST",
                        data: {
                            _token: '{{csrf_token()}}',
                            status: status,
                            id: id
                        },
                        success: function (res) {
                            notification('success','User Verification Status Has Been Updated.')
                        }
                    });
                }, function () {
                    $('.switchery').trigger('click');
                    //$(el).next('span').attr("style", 'pointer-events: none;');
                });
        }
    </script>
    @endsection
