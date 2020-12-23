@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
 <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
 <li class="breadcrumb-item"><a href="{{route('trainer.index')}}">Trainers</a></li>
 <li class="breadcrumb-item active" aria-current="page">Activities</li>
</ol>
@endsection
@section('content')
<div class="content">
 <div class="container-fluid">
  <div class="row">
    <div class="col">
      <div class="page-title-box">
        <h4 class="page-title">{{($activities->first())?$activities->first()->user->first_name.' '.$activities->first()->user->last_name.'\'s':\App\User::getFullName(getUrlSegment(2)).' has no'}} Activities </h4>
      </div>
    </div>

    <div class="col-sm-12">
      <div class="d-flex justify-content-end">
        <div class="input-group search-input">
          <input type="text" id="search_key" onkeyup="searchactivity()" onkeydown="searchactivity()"
          class="form-control br-form"
          style="border-radius: 5px 0 0 5px !important;border-right: 0 !important;"
          placeholder="Search" name="">
        </div>
        <a href="#" class="btn btn-location-icon search-input" onclick="searchactivity()"><img
          src="/assets/images/search-icon.png"
          alt="icon"> </a>
        </div>
      </div>

    </div>
    <div id="all_rows"> 
      @if(!empty($activities->first()))
      @foreach($activities as $activity)
      <div class="gym-panel">
        <div class="row">
          <div class="col-sm-2">
            <div class="gym-image-holder">
              <img src="{{ url('/') .'/'.$activity->image}}" alt="Activity Image" />
            </div>
          </div>
          <div class="col-sm-10">
            <div class="gym-content-panel">
              <div class="row">
                <div class="col-sm-9">
                  <div class="d-flex align-self-center">
                    <h3>{{$activity->name}}</h3>

                  </div>
                </div>
                <div class="col-sm-3">
                  <div class="d-flex action-btn text-right pull-right">
                    <a href="{{ url('/').'/get/activity/'.$activity->id.'/1' }}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                    <a href="{{ url('/').'/get/activity/'.$activity->id }}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                    <a href="{{ url('/').'/delete/activity/'.$activity->id }}"><i class="fa fa-trash" aria-hidden="true"></i></a>
                  </div>
                </div>

              </div>
              <small>
                <i class="fa fa-map-marker" aria-hidden="true"></i> {{($activity->type == 'online')?'Online':$activity->address}} <br>
                <i class="fa fa-money" aria-hidden="true"></i>
                £{{number_format((float)$activity->price, 2, '.', '')}}<br>
                <i class="fa fa-clock-o" aria-hidden="true"></i>
               {{$activity->duration}} Minutes
              </small> 
              


             </div>
           </div>
         </div>
       </div>
       @endforeach
        @else
        @include('includes.not_found_alert',['message'=>'No Activity Found'])
        
       @endif
     </div>
     <div class="clearfix"></div>
   </div>
   <div class="pull-right" id="pagi">
    {{ $activities->links('paginations.default') }}
  </div>
  <!-- container -->
</div>
<style type="text/css">
  .gym-content-panel small{
    padding-right: 8px;
  }
  .gym-content-panel small .fa {
    color: #3c15a3;
  }
  .gym-panel{
    padding:8px; 
  }
</style>

<script type="text/javascript">
  function searchactivity() {
    setTimeout(function () {
      var key = $('#search_key').val();
      if (key) {
        $('#pagi').hide();
      } else {
        $('#pagi').show();
      }
      $.ajax({
        url: '/search/activities/'+'{{ ($activities->first())?$activities->first()->user_id:0}}',
        type: "POST",
        data: {
          _token: '{{csrf_token()}}',
          key: key,
          page:'{{isset($_GET['page'])?$_GET['page']:1}}'
        },
        success: function (res) {
          $('#all_rows').html(res)
        }
      });
    }, 1000)
  }
</script>

@endsection


