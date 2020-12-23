@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{route('trainer.index')}}">Trainers</a></li>
    <li class="breadcrumb-item active" aria-current="page">Show</li>
</ol>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-6">
                <div class="page-title-box">
                    <h4 class="page-title">Show Personal Trainer </h4>
                </div>
            </div>
            <div class="col-sm-6 text-right">
             <label
             style="color: #b0bac9;font-size: 25px;text-transform: capitalize;font-family: 'ProximaNova-Regular';position: relative;top: 5px;">
             Status
         </label>
         <input type="checkbox" data-plugin="switchery"
         data-color="#32317d" data-size="small" data-switchery="true"
         style="display: none;"
         onchange="updateStatusOrNot(this,{{$trainer['id']}})"
         {{$trainer['is_disabled'] == 0 ? 'checked':''}}>
     </div>
 </div>
 <div class="row">
    <div class="col-sm-7">
        <input type="hidden" id="lng" name="lng" value="{{$trainer['lat']}}">
        <input type="hidden" id="lat" name="lat" value="{{$trainer['lng']}}">
        <div class="row pointer_none">
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
        <div class="row pointer_none">
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
        <div class="row pointer_none">
            <div class="col-xs-6 col-sm-6">
                <div class="input-group">
                    <label>Email</label>
                    <input type="text" class="form-control" name="email" id="email"
                    value="{{$trainer['email']}}">
                </div>
            </div>
            <div class="col-xs-6 col-sm-6">
                <div class="input-group">
                    <label>Gender</label>
                    <select class="form-control custom-select" name="gender" id="gender">
                        <option value="male" {{$trainer['gender']=='male'?'selected':''}}>Male</option>
                        <option value="female" {{$trainer['gender']=='female'?'selected':''}}>Female
                        </option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row pointer_none">
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
            <div class="row pointer_none">
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
            <div class="row pointer_none">
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

            <div class="row pointer_none">
                <div class="col-sm-12">
                    <div class="input-group">
                        <label>About </label>
                        <textarea class="form-control" id="about" name="about">{{$trainer['about']}}</textarea>
                    </div>
                </div>
            </div>
            <div class="row pointer_none">
                <div class="col-xs-12 col-sm-12">
                    <div class="input-group">
                        <label>Qualifications</label>
                        <textarea class="form-control" id="qualification_1" name="qualification_1">{{$trainer['qualification_1']}}</textarea>
                    </div>
                </div>
            </div>


            @if(\App\Category::all())
            <div class="row mt-2 pointer_none">
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
            <div class="upload-btn-wrapper pointer_none" id="div_avatar">
                <div class="image-holder">
                    <a href="#"><img
                        style=" width: 250px; height: 250px; display: block;margin: auto; object-fit: cover; border-radius: 50%;"
                        src="{{$trainer['avatar'] ? asset($trainer['avatar']) :'/assets/images/upload-img.png'}}"
                        id="img_avatar" alt="Placeholder"></a>
                    </div>
                    <input type="file" name="avatar" id="avatar"
                    onchange="document.getElementById('img_avatar').src = window.URL.createObjectURL(this.files[0])">
                </div>
                <div>
                    <label
                    style="color: #b0bac9;font-size: 25px;text-transform: capitalize;font-family: 'ProximaNova-Regular';position: relative;top: 5px;">
                    Verification
                </label>
                <input type="checkbox" data-plugin="switchery"
                data-color="#32317d" data-size="small" data-switchery="true"
                style="display: none;"
                onchange="updateVerifiedOrNot(this)"
                {{$trainer['is_verified'] ? 'checked':''}}><br>

            </div>
        </div>
        <div class="input-group">
            <label>Availability </label>
        </div>
        <div class="row  pointer_none">
            <div class="col-sm-12">

                <div class="d-flex height-40">
                    <div class="checkbox checkbox-primary text-left custom-text mt-2">
                        <input type="checkbox" id="monday" name="days[]" class="cbx_days" value="monday"
                        onchange="daySelect(this)"
                        {{checkday($trainer['id'],1)==1?'checked':''}}>
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
                    class="form-control custom-select bl-0 {{ checkday($trainer['id'],1)==0 ?'div-disable':''}}"
                    name="monday_end"
                    id="monday_end">
                    {!! getEndHoursDropDownOptions($trainer['id'],1) !!}
                </select>
            </div>
        </div>

    </div>
</div>
<div class="row  pointer_none">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="tuesday" name="days[]" class="cbx_days" value="tuesday"
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
<div class="row  pointer_none">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="wednesday" name="days[]" class="cbx_days"
                value="wednesday"
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
            class="form-control custom-select bl-0 {{ checkday($trainer['id'],3)==0 ?'div-disable':''}}"
            name="wednesday_end"
            id="wednesday_end">
            {!! getEndHoursDropDownOptions($trainer['id'],3) !!}
        </select>
    </div>
</div>
</div>
</div>
<div class="row  pointer_none">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="thursday" name="days[]" class="cbx_days" value="thursday"
                onchange="daySelect(this)"
                {{checkday($trainer['id'],4)==1?'checked':''}}>
                <label for="thursday"> Thursday </label>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select br-0 {{ checkday($trainer['id'],4)==0 ?'div-disable':''}}"
                name="thursday_start"
                id="thursday_start">
                {!! getStartHoursDropDownOptions($trainer['id'],4) !!}
            </select>
        </div>

        <div class="input-group">
            <select
            class="form-control custom-select bl-0 {{ checkday($trainer['id'],4)==0 ?'div-disable':''}}"
            name="thursday_end"
            id="thursday_end">
            {!! getEndHoursDropDownOptions($trainer['id'],4) !!}
        </select>
    </div>
</div>
</div>
</div>
<div class="row  pointer_none">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="friday" name="days[]" class="cbx_days" value="friday"
                onchange="daySelect(this)"
                {{checkday($trainer['id'],5)==1?'checked':''}}>
                <label for="friday"> Friday </label>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select br-0 {{ checkday($trainer['id'],5)==0 ?'div-disable':''}}"
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
<div class="row  pointer_none">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="saturday" name="days[]" class="cbx_days" value="saturday"
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
            class="form-control custom-select bl-0  {{  checkday($trainer['id'],6)==0 ?'div-disable':''}}"
            name="saturday_end"
            id="saturday_end">
            {!! getEndHoursDropDownOptions($trainer['id'],6) !!}
        </select>
    </div>
</div>
</div>
</div>
<div class="row  pointer_none">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="sunday" name="days[]" class="cbx_days" value="sunday"
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
                    <a href="{{$trainer['document'] ? asset($trainer['document']) :'/assets/images/upload-img.png'}}" class="html5lightbox" title="Verification  Document" data-fullscreenmode="1"><img src="{{$trainer['document'] ? asset($trainer['document']) :'/assets/images/upload-img.png'}}"></a>
                </div>

            </div>
        </div>
    </div>
    <div class="col-sm-8 pointer_none" style="padding: 0 15px 0 5px;">
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

</div>
<!-- container -->
</div>

@endsection

@section('modals')
@section('style')


<script type="text/javascript">
    var html5lightbox_options = {
        watermark: "http://html5box.com/images/html5boxlogo.png",
        watermarklink: "http://html5box.com"
    };
</script>


    {{--
    @Description Image Viewer Style
    @Author Khuram Qadeer
    --}}
    <style>
        /* The Modal (background) */
        #html5-watermark{
            display: none !important;
        }
        #view_img_modal {
            display: none; /* Hidden by default */
            position: fixed; /* Stay in place */
            z-index: 1; /* Sit on top */
            padding-top: 100px; /* Location of the box */
            left: 0;
            top: 0;
            width: 100%; /* Full width */
            height: 100%; /* Full height */
            overflow: auto; /* Enable scroll if needed */
            background: linear-gradient(to bottom, #3f1120 0%, #330f23 22%, #10092d 71%, #08082f 88%, #08082f 100%);
            /* Fallback color */
        }

        /* Modal Content (image) */
        .modal-content {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
        }

        /* Caption of Modal Image */
        #caption {
            margin: auto;
            display: block;
            width: 80%;
            max-width: 700px;
            text-align: center;
            padding: 10px 0;
            height: 150px;
            font-family: 'ProximaNova-Regular';
            color: white;
        }

        /* Add Animation */
        #view_img_modal .modal-content, #caption {
            -webkit-animation-name: zoom;
            -webkit-animation-duration: 0.6s;
            animation-name: zoom;
            animation-duration: 0.6s;
        }

        @-webkit-keyframes zoom {
            from {
                -webkit-transform: scale(0)
            }
            to {
                -webkit-transform: scale(1)
            }
        }

        @keyframes zoom {
            from {
                transform: scale(0)
            }
            to {
                transform: scale(1)
            }
        }

        /* The Close Button */



        /* 100% Image Width on Smaller Screens */

    </style>
    @endsection
    

    @endsection

    @section('head_script')
    <link href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" rel="Stylesheet" type="text/css"/>
    {{--        <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>--}}
    <script type="text/javascript" src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script type="text/javascript" src="{{ url('/') }}/assets/js/html5lightbox.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"
    integrity="sha512-uURl+ZXMBrF4AwGaWmEetzrd+J5/8NRkWAvJx5sbPSSuOb0bZLqf+tOzniObO00BjHa/dD7gub9oCGMLPQHtQA=="
    crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css"
    integrity="sha512-nNlU0WK2QfKsuEmdcTwkeh+lhGs6uyOxuUs+n+0oXSYDok5qy0EI0lt01ZynHq6+p/tbgpZ7P+yUb+r71wqdXg=="
    crossorigin="anonymous"/>

    <script>
        $(document).ready(function () {
            // $('#img_doc').fancybox();
            $("#date_of_birth").datepicker({
                maxDate: new Date(),
                changeYear: true,
                changeMonth: true
            });
            $("#document_expire_date").datepicker({
                minDate: new Date(),
                changeYear: true,
                changeMonth: true
            });
        });
        function updateStatusOrNot(el,id) {
            Notiflix.Confirm.Show('Confirmation', 'Would you like to change status of this user?', true, false,
                function () {
                    location.href = "/user/enabledisable/"+id;
                }, function () {
                    $(el).trigger('click');
                });
        }

        /**
         * @Description Update verification
         * @param el
         * @Author Khuram Qadeer.
         */
         function updateVerifiedOrNot(el) {
            Notiflix.Confirm.Show('Confirmation', 'Would you verified to {{\App\User::getFullName($trainer['id'])}}?', true, false,
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
                    $(el).trigger('click');
                });
        }
    </script>
    @endsection
