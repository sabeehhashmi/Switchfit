@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{route('gym.list')}}">Gyms</a></li>
    <li class="breadcrumb-item active" aria-current="page">Edit</li>
</ol>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">Edit Gym </h4>
                </div>
            </div>
        </div>

        <form method="post" action="{{route('gym.update',$gym->id)}}" enctype="multipart/form-data" id="gym_form">
            @csrf

            <input type="hidden" id="image_urls" name="image_urls" value="{{old('image_urls',$gym->images)}}">
            <input type="hidden" id="lat" name="lat" value="{{old('lat',$gym->lat)}}">
            <input type="hidden" id="lng" name="lng" value="{{old('lng',$gym->lng)}}">

            <div class="row">
                <div class="col-sm-8">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12">
                            <div class="input-group">
                                <label>Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                name="name"
                                value="{{ old('name',$gym->name) }}" required>
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
                                    value="{{old('address',$gym->address)}}"
                                    onclick="showGoogleMapModal()"
                                    style="border-radius: 5px 0 0 5px !important;"
                                    required>
                                </div>
                                <a href="#" class="btn btn-location-icon" onclick="showGoogleMapModal()">
                                    <img src="/assets/images/loc-icon.png" alt="icon"> </a>
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
                                    name="country" id="country" value="{{ old('country',$gym->country) }}"
                                    required>
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
                                    " name="state" id="state" value="{{ old('state',$gym->state) }}" required>
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
                                    name="city" id="city" value="{{ old('city',$gym->city) }}" required>
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
                                    name="postal_code" id="postal_code"
                                    value="{{ old('postal_code',$gym->postal_code) }}" required>
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
                                    name="about" maxlength="1000" required>{{ old('about',$gym->about) }}</textarea>
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
                                    {{ inArrayByKeyAndValue($gym->facilities,'facility_id',(int)$facility->id)==1
                                    || (old('facilities') && in_array((string)$facility->id,old('facilities'))) ?'checked':''}}
                                    >
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
                                {{ inArrayByKeyAndValue($gym->amenities,'amenity_id',(int)$amenity->id)
                                || (old('amenities') && in_array((string)$amenity->id,old('amenities')))?'checked':''}}
                                >
                                <label for="i{{$i}}"> {{$amenity->name}} </label>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

            </div>
            <div class="col-sm-4">
                <div class="input-group">
                    <label>Opening Hours</label>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="d-flex height-40">
                            <div class="checkbox checkbox-primary text-left custom-text mt-2">
                                <input type="checkbox" id="monday" name="days[]" value="monday"
                                {{ inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','monday') ?'checked':''}}
                                onchange="daySelect(this)">
                                <label for="monday"> Monday </label>
                            </div>

                            <div class="input-group">
                                <select
                                class="form-control custom-select br-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','monday') ?'div-disable':''}} "
                                name="monday_start"
                                id="monday_start">
                                {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','monday')
                                ?collect(json_decode($gym->time_schedule))->firstWhere('day','monday')->time_start :'') !!}
                            </select>
                        </div>
                        <div class="input-group">
                            <select
                            class="form-control custom-select bl-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','monday') ?'div-disable':''}}"
                            name="monday_end"
                            id="monday_end">
                            {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','monday')
                            ?collect(json_decode($gym->time_schedule))->firstWhere('day','monday')->time_end:'') !!}
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
                        onchange="daySelect(this)"
                        {{ inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','tuesday') ?'checked':''}}>
                        <label for="tuesday"> Tuesday </label>
                    </div>
                    <div class="input-group">
                        <select
                        class="form-control custom-select br-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','tuesday') ?'div-disable':''}}"
                        name="tuesday_start"
                        id="tuesday_start">
                        {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','tuesday')
                        ?collect(json_decode($gym->time_schedule))->firstWhere('day','tuesday')->time_start:'') !!}
                    </select>
                </div>
                <div class="input-group">
                    <select
                    class="form-control custom-select bl-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','tuesday') ?'div-disable':''}}"
                    name="tuesday_end"
                    id="tuesday_end">
                    {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','tuesday')
                    ?collect(json_decode($gym->time_schedule))->firstWhere('day','tuesday')->time_end:'') !!}
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
                onchange="daySelect(this)"
                {{ inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','wednesday') ?'checked':''}}>
                <label for="wednesday"> Wednesday </label>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select br-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','wednesday') ?'div-disable':''}}"
                name="wednesday_start"
                id="wednesday_start">
                {!! getHoursInHTMLDropDownOptions(
                    inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','wednesday')
                    ?collect(json_decode($gym->time_schedule))->firstWhere('day','wednesday')->time_start:'') !!}

                </select>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select bl-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','wednesday') ?'div-disable':''}}"
                name="wednesday_end"
                id="wednesday_end">
                {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','wednesday')
                ?collect(json_decode($gym->time_schedule))->firstWhere('day','wednesday')->time_end:'') !!}

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
                onchange="daySelect(this)"
                {{ inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','thursday') ?'checked':''}}>
                <label for="thursday"> Thursday </label>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select br-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','thursday') ?'div-disable':''}}"
                name="thursday_start"
                id="thursday_start">
                {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','thursday')
                ?collect(json_decode($gym->time_schedule))->firstWhere('day','thursday')->time_start:'') !!}
            </select>
        </div>
        <div class="input-group">
            <select
            class="form-control custom-select bl-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','thursday') ?'div-disable':''}}"
            name="thursday_end"
            id="thursday_end">
            {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','thursday')
            ?collect(json_decode($gym->time_schedule))->firstWhere('day','thursday')->time_end:'') !!}
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
            <div class="checkbox checkbox-primary text-left custom-text mt-2 ">
                <input type="checkbox" id="friday" name="days[]" value="friday"
                onchange="daySelect(this)"
                {{ inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','friday') ?'checked':''}}>
                <label for="friday"> Friday </label>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select br-0  {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','friday') ?'div-disable':''}}"
                name="friday_start"
                id="friday_start">

                {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(
                    json_decode($gym->time_schedule),'day','friday')
                ? collect(json_decode($gym->time_schedule))->firstWhere('day','friday')->time_start
                :'00:00') !!}
            </select>
        </div>
        <div class="input-group">
            <select
            class="form-control custom-select bl-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','friday') ?'div-disable':''}}"
            name="friday_end"
            id="friday_end">
            {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','friday')
            ?collect(json_decode($gym->time_schedule))->firstWhere('day','friday')->time_end:'') !!}
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
                onchange="daySelect(this)"
                {{ inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','saturday') ?'checked':''}}>
                <label for="saturday"> Saturday </label>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select br-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','saturday') ?'div-disable':''}}"
                name="saturday_start"
                id="saturday_start">
                {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','saturday')
                ?collect(json_decode($gym->time_schedule))->firstWhere('day','saturday')->time_start:'') !!}
            </select>
        </div>
        <div class="input-group">
            <select
            class="form-control custom-select bl-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','saturday') ?'div-disable':''}}"
            name="saturday_end"
            id="saturday_end">
            {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','saturday')
            ?collect(json_decode($gym->time_schedule))->firstWhere('day','saturday')->time_end:'') !!}
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
                onchange="daySelect(this)"
                {{ inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','sunday') ?'checked':''}}>
                <label for="sunday"> Sunday </label>
            </div>
            <div class="input-group">
                <select
                class="form-control custom-select br-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','sunday') ?'div-disable':''}}"
                name="sunday_start"
                id="sunday_start">
                {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','sunday')
                ?  collect(json_decode($gym->time_schedule))->firstWhere('day','sunday')->time_start :'') !!}
            </select>
        </div>
        <div class="input-group">
            <select
            class="form-control custom-select bl-0 {{ !inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','sunday') ?'div-disable':''}}"
            name="sunday_end"
            id="sunday_end">
            {!! getHoursInHTMLDropDownOptions(inArrayByKeyAndValue(json_decode($gym->time_schedule),'day','sunday')
            ? collect(json_decode($gym->time_schedule))->firstWhere('day','sunday')->time_end:'') !!}
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
            {{--                                    @php  $counter = 0; @endphp--}}
            {{--                                    @if($gym->images)--}}
            {{--                                        @foreach(json_decode($gym->images) as  $i=>$imageUrl)--}}
            {{--                                            @php $counter++; @endphp--}}
            {{--                                            <li>--}}
                {{--                                                <div class="upload-btn-wrapper">--}}
                    {{--                                                    <div class="image-holder">--}}
                        {{--                                                        <a href="#">--}}
                            {{--                                                            <img id="img_file{{$counter}}"--}}
                            {{--                                                                 src="{{asset($imageUrl)}}"--}}
                            {{--                                                                 alt="Placeholder"--}}
                            {{--                                                                --}}{{--                                                                         onclick="$('#file{{$counter}}').click()"--}}
                            {{--                                                            /></a>--}}
                            {{--                                                        <a href="#"--}}
                            {{--                                                           onclick="deleteGymImage(this)"--}}
                            {{--                                                           data-index="{{$i}}"--}}
                            {{--                                                           data-gym-id="{{$gym->id}}"--}}
                            {{--                                                        ><i class="fa fa-remove"></i></a>--}}
                        {{--                                                    </div>--}}
                        {{--                                                    --}}{{--                                                    <input type="file" name="file{{$counter}}" id="file{{$counter}}"--}}
                        {{--                                                    --}}{{--                                                           onchange="readGymImageFile(this,'img_file{{$counter}}')"/>--}}
                    {{--                                                </div>--}}
                {{--                                            </li>--}}
                {{--                                        @endforeach--}}
                {{--                                    @endif--}}

                {{--                                    @if($counter<6)--}}
                {{--                                        @php $counter++; @endphp--}}
                {{--                                        <li>--}}
                    {{--                                            <div class="upload-btn-wrapper">--}}
                        {{--                                                <div class="image-holder">--}}
                            {{--                                                    <a href="#"><img id="img_file{{$counter}}"--}}
                                {{--                                                                     src="/assets/images/upload-img.png"--}}
                                {{--                                                                     alt="Placeholder"--}}
                                {{--                                                                     onclick="$('#file{{$counter}}').click()"/></a>--}}
                            {{--                                                </div>--}}
                            {{--                                                <input type="file" name="file{{$counter}}" id="file{{$counter}}"--}}
                            {{--                                                       data-img-id="img_file{{$counter}}"--}}
                            {{--                                                       onchange="readGymImageFile(this)"--}}
                            {{--                                                />--}}
                        {{--                                            </div>--}}
                    {{--                                        </li>--}}
                    {{--                                    @endif--}}

                    {{--                                    --}}{{--                                    @for ($i = $counter; $i < 6; $i++)--}}
                    {{--                                    --}}{{--                                        @php $counter++; @endphp--}}
                    {{--                                    --}}{{--                                        <li>--}}
                        {{--                                    --}}{{--                                            <div class="upload-btn-wrapper">--}}
                            {{--                                    --}}{{--                                                <div class="image-holder">--}}
                                {{--                                    --}}{{--                                                    <a href="#"><img id="img_file{{$counter}}"--}}
                                    {{--                                    --}}{{--                                                                     src="/assets/images/upload-img.png"--}}
                                    {{--                                    --}}{{--                                                                     alt="Placeholder"--}}
                                    {{--                                    --}}{{--                                                                     onclick="$('#file{{$counter}}').click()"/></a>--}}
                                {{--                                    --}}{{--                                                </div>--}}
                                {{--                                    --}}{{--                                                <input type="file" name="file{{$counter}}" id="file{{$counter}}"--}}
                                {{--                                    --}}{{--                                                       onchange="readImageFile(this,'img_file{{$counter}}')"/>--}}
                            {{--                                    --}}{{--                                            </div>--}}
                        {{--                                    --}}{{--                                        </li>--}}
                        {{--                                    --}}{{--                                    @endfor--}}
                    {{--                                </ul>--}}
                </div>
            </div>

        </div>
    </div> <!-- end row -->
</form>
<div class="row">
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
            <button type="button" onclick="$('#gym_form').submit()"
            class="btn btn-md primay-btn pull-right inline-block">Update Gym
        </button>
    </div>
</div>
</div>
</div>
@endsection

@section('modals')
{{--    <!-- container -->--}}
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
    <script type="text/javascript">
      $(document).ready(function () {
        $('.custom-select').change(function (e) {

            current_id = this.id.split('_');
            start_val = $('#'+current_id[0]+'_start').val();
            end_val = $('#'+current_id[0]+'_end').val();
            if(Date.parse('01/01/2011 '+ end_val) <= Date.parse('01/01/2011 '+start_val)){
                notification('danger', 'End Time Should be greater than start time');
                
            }
            
        });
    });
</script>
@endsection
