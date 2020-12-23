@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{route('gym.list')}}">Gyms</a></li>
    <li class="breadcrumb-item active" aria-current="page">Create</li>
</ol>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">Add Gym </h4>
                </div>
            </div>
        </div>
        <form method="post" action="{{route('gym.store')}}" enctype="multipart/form-data" id="gym_form">
            @csrf

            <input type="hidden" id="image_urls" name="image_urls" value="{{ old('image_urls') }}">
            <input type="hidden" id="lat" name="lat" value="{{ old('lat') }}">
            <input type="hidden" id="lng" name="lng" value="{{ old('lng') }}">

            <div class="row">

                <div class="col-sm-7">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="input-group">
                                <label>Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name') }}" required/>
                                @error('name')
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
                                    value="{{old('address')}}"
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
                                <img src="/assets/images/loc-icon.png" alt="icon" class=""> </a>

                            </div>
                            @error('address')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="input-group">
                                <label>Country</label>
                                <input type="text" class="form-control @error('country') is-invalid @enderror"
                                name="country" id="country" value="{{ old('country') }}" required>
                                @error('country')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="input-group">
                                <label>State</label>
                                <input type="text" class="form-control @error('state') is-invalid @enderror
                                " name="state" id="state" value="{{ old('state') }}" required>
                                @error('state')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6">
                            <div class="input-group">
                                <label>City</label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror"
                                name="city" id="city" value="{{ old('city') }}" required>
                                @error('city')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6">
                            <div class="input-group">
                                <label>Postel code</label>
                                <input type="text" class="form-control  @error('postal_code') is-invalid @enderror"
                                name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                                required>
                                @error('postal_code')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-group">
                                <label>About GYM</label>
                                <textarea class="form-control @error('about') is-invalid @enderror"
                                name="about" maxlength="1000" required>{{ old('about') }}</textarea>
                                @error('about')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    @if(\App\Facility::all())
                    <div class="row">
                        <div class="col-sm-12">
                            <div class="input-group">
                                <label>Facilities</label>
                                @error('facilities')
                                <span class="invalid-feedback" role="alert" style="display: block;">
                                    <strong>{{ $message }}</strong>
                                </span>

                                @enderror
                            </div>
                            <div class="padding-5">
                                @foreach(\App\Facility::all() as $i=>$facility)
                                <div
                                class="checkbox form-check-inline checkbox-primary text-left custom-text">
                                <input type="checkbox" id="f{{$i}}" name="facilities[]"
                                value="{{$facility->id}}"
                                {{(old('facilities') && in_array((string)$facility->id,old('facilities'))) ?'checked':''}}>
                                <label for="f{{$i}}"> {{$facility->name}} </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                @if(\App\Amenity::all())
                <div class="row mt-2">
                    <div class="col-sm-12">
                        <div class="input-group">
                            <label>Amenities</label>
                            @error('amenities')
                            <span class="invalid-feedback" role="alert" style="display: block;">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                        <div class="padding-5">
                            @foreach(\App\Amenity::all() as $i=>$amenity)
                            <div
                            class="checkbox form-check-inline checkbox-primary text-left custom-text">
                            <input type="checkbox" id="a{{$i}}" name="amenities[]"
                            value="{{$amenity->id}}"
                            {{(old('amenities') && in_array((string)$amenity->id,old('amenities'))) ?'checked':''}}>
                            <label for="i{{$i}}"> {{$amenity->name}} </label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>

        <div class="col-sm-5">
            <div class="input-group">
                <label>Opening Hours </label>
            </div>
            <div class="row">
                <div class="col-sm-12">

                    <div class="d-flex height-40">
                        <div class="checkbox checkbox-primary text-left custom-text mt-2">
                            <input type="checkbox" id="monday" name="days[]" value="monday"
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
    <div class="row">
        <div class="col-sm-12">
            <div class="d-flex height-40">
                <div class="checkbox checkbox-primary text-left custom-text mt-2">
                    <input type="checkbox" id="tuesday" name="days[]" value="tuesday"
                    onchange="daySelect(this)" checked>
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

<div class="row">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="wednesday" name="days[]" value="wednesday"
                onchange="daySelect(this)" checked>
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

<div class="row">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="thursday" name="days[]" value="thursday"
                onchange="daySelect(this)" checked>
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

<div class="row">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="friday" name="days[]" value="friday"
                onchange="daySelect(this)" checked>
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

<div class="row">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="saturday" name="days[]" value="saturday"
                onchange="daySelect(this)" checked>
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

<div class="row">
    <div class="col-sm-12">
        <div class="d-flex height-40">
            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                <input type="checkbox" id="sunday" name="days[]" value="sunday"
                onchange="daySelect(this)" checked>
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

<div class="row">
    <div class="col-sm-11">
        {{--                                <div class="input-group mt-2">--}}
            {{--                                    <label>Photo Gym</label>--}}
        {{--                                </div>--}}

        {{--                                <ul class="photo-list" id="gym-photos">--}}
            {{--                                    <li>--}}
                {{--                                        <div class="upload-btn-wrapper">--}}
                    {{--                                            <div class="image-holder">--}}
                        {{--                                                <a href="#"><img id="img_file1" src="/assets/images/upload-img.png"--}}
                            {{--                                                                 alt="Placeholder"--}}
                            {{--                                                    /></a>--}}
                            {{--                                                <a href="#"--}}
                            {{--                                                   onclick="deleteGymImage()"--}}
                            {{--                                                ><i class="fa fa-remove"></i></a>--}}
                        {{--                                            </div>--}}
                        {{--                                            <input type="file" name="file1" id="file1"--}}
                        {{--                                                   data-img-id="img_file1"--}}
                        {{--                                                   onchange="readGymImageFile(this)"/>--}}
                    {{--                                        </div>--}}
                {{--                                    </li>--}}
            {{--                                </ul>--}}
        </div>

    </div>


</div>
</div> <!-- end row -->
</form>

<div class="row" style="margin-top: 10px;">
    <div class="col-sm-8">
        @error('image_urls')
        <span class="invalid-feedback" role="alert" style="display: block;">
            <strong>{{ $message }}</strong>
        </span>
        @enderror
        @include('includes.dropzone_uploader',[
            'pathDir'=>'assets/uploads/gyms/',
            'imagesInputId'=>'#image_urls',
            'dropZoneText'=>'Drop gym photos here to upload',
            'maxFiles'=>6,
            ])
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <button type="button" onclick="checkValidation()"
            class="btn btn-md primay-btn pull-right inline-block">Create Gym
        </button>
    </div>
</div>

</div>
</div>
@endsection

@section('modals')
{{--    <!-- googleMap -->--}}
@include('modals.googleMap',[
    'title'=>'Choose Gym Address',
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
    <script>
        function checkValidation() {
            if (!$('#address').val() || !$('#lat').val() || !$('#lng').val()) {
                $('#lat_lng_vali').show();
                $('#address').addClass('is-invalid');
                return false;
            } else {
                $('#lat_lng_vali').hide();
                $('#address').removeClass('is-invalid')
            }

            $('#gym_form').submit();

        }
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
    </script>
    @endsection

