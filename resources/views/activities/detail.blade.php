@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
  <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
  <li class="breadcrumb-item"><a href="{{ url('/') }}/trainer">Trainers</a></li>
  <li class="breadcrumb-item active" aria-current="page">Activity</li>
</ol>
@endsection
@section('content')

<style type="text/css">
  .show-mod{
   pointer-events: none
 }
 .show-mod .btn.btn-md.primay-btn.pull-right.inline-block {
    display: none !important;
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
.img-holder img{
  width: 100%
}
</style>

<div class="content {{($show_only==1)?'show-mod':''}} ">
 <div class="container-fluid">
  <div class="row">
    <div class="col">
      <div class="page-title-box">
        <h4 class="page-title">Manage Activity </h4>
      </div>
    </div>
  </div>
  @if($activity)
  <form class="activity-detail-form" method="POST" enctype="multipart/form-data">
    <div class="row">
      <div class="col-sm-7">
        <div class="row">
          <div class="col-xs-12 col-sm-6">
            <div class="input-group">
              <label>Name</label>
              <input type="text" class="form-control" name="name" value="{{$activity->name}}" maxlength="50">
            </div>
          </div>
          <div class="col-xs-12 col-sm-6">
            <div class="input-group">
              <label>Price</label>
              <input type="text" class="form-control" name="price" value="{{number_format((float)$activity->price, 2, '.', '')}}">
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-xs-12 col-sm-6">
            <div class="input-group">
              <label>Duration</label>
              <select class="form-control custom-select" name="duration">
                @for ($x = 15; $x <= 120; $x+=15) 
                <option value="{{$x}}" {{($activity->duration == $x)?'selected':''}}>
                 {{$x}} minutes
               </option>
               @endfor

             </select>
           </div>
         </div>
         <div class="col-xs-12 col-sm-6">
          <div class="input-group">
            <label>Type</label>
            <select class="form-control custom-select location-select" name="type">
              <option value="location" {{($activity->type == 'location')?'selected':''}}>
                On Location
              </option>
              <option value="online" {{($activity->type == 'online')?'selected':''}}>Online
              </option>
            </select>
          </div>
        </div>
      </div>

      <div class="row online_information">
        <div class="col-xs-12 col-sm-12">
          <div class="d-flex">
            <div class="input-group">
              <label>Online Activity Information</label>
              <input type="text" class="form-control" name="online_information" value="{{$activity->online_information}}">
            </div>
          </div>
        </div>
      </div>
              <div class="row location-area">
                <div class="col-xs-12 col-sm-12">
                  <div class="d-flex">
                    <div class="input-group">
                      <label>Address</label>
                      <input type="text" class="form-control br-form" style="border-radius: 5px 0 0 5px !important;border-right: 0 !important;" onclick="showGoogleMapModal()" id="address" name="address" value="{{$activity->address}}">
                      <input type="hidden" id="lng" name="lng" value="{{$activity->lng}}">
                      <input type="hidden" id="lat" name="lat" value="{{$activity->lat}}">
                      <input type="hidden" name="activity_id" value="{{$activity->id}}">
                      {{ csrf_field() }}
                    </div>
                    <a href="#" onclick="showGoogleMapModal()" class="btn btn-location-icon"><img src="{{ url('/') }}/assets/images/loc-icon.png" alt="icon"> </a>
                  </div>
                </div>
              </div>


              <div class="row">
                <div class="col-sm-12">
                  <div class="input-group">
                    <label>About Activity</label>
                    <textarea class="form-control" name="about">{{$activity->about}}</textarea>
                  </div>
                </div>
              </div>

            </div>
            <div class="col-sm-5">
              <div class="profile-img">
                    <div class="img-holder">
                        <img src="{{($activity->image)?url('/').'/'.$activity->image:'/assets/images/upload-img.png' }}" id="view_logo"
                        name="view_logo" alt="image"/>
                    </div>
                    <div class="croped-image-div">
                        <input id="sample_input" type="hidden" name="image"/>
                    </div>

                    {{--                        <a href="#" class="btn btn-verify"><img src="/assets/images/verify-icon.png"> Verified</a>--}}
                </div>
              


              </div>
              <div class="row">
                <div class="col-sm-12">
                  <button type="btn" class="btn btn-md primay-btn pull-right inline-block">Save Activity</button>
                </div>
              </div>
            </div> <!-- end row -->
          </form>
          @else
          @include('includes.not_found_alert',['message'=>'Activity Deleted'])
          @endif
        </div>
        <!-- container -->
      </div>
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

      <script type="text/javascript">

        $(document).ready(function () {
          var location_val = $('.location-select').val();
          if(location_val == 'online'){
            $('.location-area').hide();
            $('.online_information').show();
          }
          else{
            $('.location-area').show();
            $('.online_information').hide();
          }
          $('.location-select').change(function (e) {



            current_val = $(this).val();
            if(current_val == 'online'){
              $('.location-area').hide();
              $('.online_information').show();
            }
            else{
              $('.location-area').show();
              $('.online_information').hide();
            }


          });


          /*Add the following code if you want the name of the file appear on select*/
          $(".custom-file-input").on("change", function() {
            var fileName = $(this).val().split("\\").pop();
            $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
          });

          $(".activity-detail-form").on('submit',(function(e) {
           e.preventDefault();

           var formData = new FormData(this);

           $.ajax({
            url: "/save/activity",
            type: "POST",
            processData: false,
            contentType: false,
            cache: false,
            data: formData,
            enctype: 'multipart/form-data',
            success: function (res) {
              window.location.href = '/activities/'+{{($activity)?$activity->user_id:''}};
            },
            error: function (res) {
              $('.btn-action').removeClass('div-disable');
              notification('danger', res.responseJSON.message);
            }
          });


           console.log(formData);
           return false
         }));

        });


      </script>

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


