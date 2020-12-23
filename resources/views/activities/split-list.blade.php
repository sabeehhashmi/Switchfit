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
        <h4 class="page-title">{{$all_bookings['user']->first_name.' '.$all_bookings['user']->last_name}}'s Activities Schedule </h4>
      </div>
    </div>

    <div class="col-xl-12">
      <div class="card">
        <div class="card-body">
          <h4 class="header-title mb-4"></h4>

          <ul class="nav nav-pills navtab-bg nav-justified pull-in ">
            <li class="nav-item">
              <a href="#home1" data-toggle="tab" aria-expanded="false" class="nav-link active">
                <i class="fe-monitor"></i><span class="d-none d-sm-inline-block ml-2">Upcoming ({{count($all_bookings['active_bookings'])}})</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#profile1" data-toggle="tab" aria-expanded="true" class="nav-link">
                <i class="fe-user"></i> <span class="d-none d-sm-inline-block ml-2">Pending ({{count($all_bookings['pending_bookings'])}})</span>
              </a>
            </li>
            <li class="nav-item">
              <a href="#messages1" data-toggle="tab" aria-expanded="false" class="nav-link">
                <i class="fe-mail"></i> <span class="d-none d-sm-inline-block ml-2">Past ({{count($all_bookings['past_bookings'])}})</span>
              </a>
            </li>
            
          </ul>
          <div class="tab-content">
            <div class="tab-pane active" id="home1">
              @if(!empty($all_bookings) &&  !empty($all_bookings['active_bookings']))
              @php
              $active_bookings = $all_bookings['active_bookings'];
              @endphp
              @foreach( $active_bookings as $active_booking)

              <div class="row split-activity">
                <div class="col-sm-2">
                  <div class="gym-image-holder">
                    <img src="{{url('/')}}/{{($active_booking->activity)?$active_booking->activity->image:''}}" alt="Activity Image">
                  </div>
                </div>
                <div class="col-sm-10">
                  <div class="gym-content-panel">
                    <div class="row">
                      <div class="col-sm-9">
                        <div class="d-flex align-self-center">
                          <h3>{{($active_booking->activity)?$active_booking->activity->name:''}}</h3>

                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="d-flex action-btn text-right pull-right">
                          <a href="{{url('/')}}/get/activity/{{$active_booking->activity_id}}/1"><i class="fa fa-eye" aria-hidden="true"></i></a>
                          
                        </div>

                      </div>


                    </div>
                    <small>
                      <i class="fa fa-map-marker" aria-hidden="true"></i> {{($active_booking->activity->type == 'online')?'Online':$active_booking->activity->address}} <br>
                      <i class="fa fa-user" aria-hidden="true"></i> {{($active_booking->buyer)?$active_booking->buyer->first_name.' '.$active_booking->buyer->last_name:''}} <br>
                      <i class="fa fa-calendar" aria-hidden="true"></i>{{date("d M Y", strtotime($active_booking->booking_date))}}
                      <br>
                      <i class="fa fa-clock-o" aria-hidden="true"></i>
                      {{date('h:i A', strtotime($active_booking->start_time))}}
                      <br>
                      <i class="fa fa-money" aria-hidden="true"></i>
                      £{{number_format((float)$active_booking->price, 2, '.', '')}}
                      
                      
                    </small> 



                  </div>
                </div>
              </div>
              @endforeach
              @else
              @include('includes.not_found_alert',['message'=>'No Upcoming Activities Found'])
              @endif
            </div>
            <div class="tab-pane show" id="profile1">
              @if(!empty($all_bookings) &&  !empty($all_bookings['pending_bookings']))
              @php
              $pending_bookings = $all_bookings['pending_bookings'];
              @endphp
              @foreach( $pending_bookings as $active_booking)

              <div class="row split-activity">
                <div class="col-sm-2">
                  <div class="gym-image-holder">
                    <img src="{{url('/')}}/{{($active_booking->activity)?$active_booking->activity->image:''}}" alt="Activity Image">
                  </div>
                </div>
                <div class="col-sm-10">
                  <div class="gym-content-panel">
                    <div class="row">
                      <div class="col-sm-9">
                        <div class="d-flex align-self-center">
                          <h3>{{($active_booking->activity)?$active_booking->activity->name:''}}</h3>

                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="d-flex action-btn text-right pull-right">
                          <a href="{{url('/')}}/get/activity/{{$active_booking->activity_id}}/1"><i class="fa fa-eye" aria-hidden="true"></i></a>

                        </div>
                      </div>


                    </div>
                    <small>
                      <i class="fa fa-map-marker" aria-hidden="true"></i> {{($active_booking->activity->type == 'online')?'Online':$active_booking->activity->address}} <br>
                      <i class="fa fa-user" aria-hidden="true"></i> {{($active_booking->buyer)?$active_booking->buyer->first_name.' '.$active_booking->buyer->last_name:''}} <br>
                      <i class="fa fa-calendar" aria-hidden="true"></i>{{date("d M Y", strtotime($active_booking->booking_date))}}
                      <br>
                      <i class="fa fa-clock-o" aria-hidden="true"></i>
                      {{date('h:i A', strtotime($active_booking->start_time))}}
                      <br>
                      <i class="fa fa-money" aria-hidden="true"></i>
                      £{{number_format((float)$active_booking->price, 2, '.', '')}}
                      
                      
                    </small> 



                  </div>
                </div>
              </div>
              @endforeach
              @else
              @include('includes.not_found_alert',['message'=>'No Pending Activities Found'])
              @endif
            </div>
            <div class="tab-pane" id="messages1">

              @if(!empty($all_bookings) &&  !empty($all_bookings['past_bookings']))
              @php
              $past_bookings = $all_bookings['past_bookings'];
              @endphp
              @foreach( $past_bookings as $active_booking)

              <div class="row split-activity">
                <div class="col-sm-2">
                  <div class="gym-image-holder">
                    <img src="{{url('/')}}/{{($active_booking->activity)?$active_booking->activity->image:''}}" alt="Activity Image">
                  </div>
                </div>
                <div class="col-sm-10">
                  <div class="gym-content-panel">
                    <div class="row">
                      <div class="col-sm-9">
                        <div class="d-flex align-self-center">
                          <h3>{{($active_booking->activity)?$active_booking->activity->name:''}}</h3>

                        </div>
                      </div>
                      <div class="col-sm-3">
                        <div class="d-flex action-btn text-right pull-right">
                          @if($active_booking->accepted == 2)

                          <h5 class="danger-text text-danger">
                            Rejected
                          </h5>
                          @endif
                          <a href="{{url('/')}}/get/activity/{{$active_booking->activity_id}}/1"><i class="fa fa-eye" aria-hidden="true"></i></a>


                        </div>
                      </div>


                    </div>
                    <small>
                      <i class="fa fa-map-marker" aria-hidden="true"></i>
                      @php
                      $type = ($active_booking->activity)?$active_booking->activity->type:'';
                      if($type){
                        $type = ($type == 'online')?'Online':$active_booking->activity->address;

                      }
                      @endphp
                       {{$type}} <br>
                      <i class="fa fa-user" aria-hidden="true"></i> {{($active_booking->buyer)?$active_booking->buyer->first_name.' '.$active_booking->buyer->last_name:''}} <br>
                      <i class="fa fa-calendar" aria-hidden="true"></i>{{date("d M Y", strtotime($active_booking->booking_date))}}
                      <br>
                      <i class="fa fa-clock-o" aria-hidden="true"></i>
                      {{date('h:i A', strtotime($active_booking->start_time))}}
                      <br>
                      <i class="fa fa-money" aria-hidden="true"></i>
                      £{{number_format((float)$active_booking->price, 2, '.', '')}}
                      
                      
                    </small> 



                  </div>
                </div>
              </div>
              @endforeach
              @else
              @include('includes.not_found_alert',['message'=>'No Past Activities Found'])
              @endif
            </div>

          </div>
        </div>
      </div>
    </div>

  </div>


  <!-- container -->
</div>
<style type="text/css">
  .row.split-activity {
    padding: 3px;
    border: 1px solid #f6f4f7;
    border-radius: 8px;
    margin: 19px 0px;
    background: #f6f4f7;
}
  .nav-pills .nav-link.active {
    background: #3c15a3;
  }
  .navtab-bg {

    margin: 0px 100px;
  }
  .gym-image-holder {
    padding: 13px 0px;
  }
  .danger-text {
    margin: 5px 0px;
  }
  .gym-image-holder img {
   
    height: 165px;
    
}
</style>

@endsection


