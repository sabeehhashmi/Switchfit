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
        <h4 class="page-title">{{\App\User::getFullName(getUrlSegment(6))}}'s Payouts </h4>
      </div>
    </div>

    <div class="col-xl-12">
      <div class="card">
        <div class="card-body">
          <h3 class="bg-dark date-container">
           {{date("d M Y", strtotime(getUrlSegment(5)))}}
         </h3>
         <div class="table-responsive">
          <table class="table table-hover table-centered table-nowrap m-0">

            <thead>
              <tr>
                <th>Activity Name</th>
                <th>Client</th>
                <th>Time</th>
                <th>Duration</th>
                <th>Total Amount</th>
                <th>SwitchFit Fee</th>
                <th>Tax</th>
                <th>Total Receiveable</th>

              </tr>
            </thead>
            <tbody>
              @if(!empty($booking->first()))

              @foreach($booking as $s_booking)

              <tr>
                <td>
                  
                    {{($s_booking->activity)?$s_booking->activity->name:'Activity Deleted'}}
                  

                </td>

                <td>
                  <strong>
                    {{($s_booking->buyer)?$s_booking->buyer->first_name:''}}
                  </strong>

                </td>

                <td>
                  {{date('h:i A', strtotime($s_booking->start_time))}}

                </td>

                <td>
                  {{$s_booking->duration}} minutes
                </td>

                <td>
                  <i class="mdi mdi-currency-gbp "></i>{{number_format((float)$s_booking->price, 2, '.', '')}}

                </td>
                <td>
                  <i class="mdi mdi-currency-gbp "></i> -{{number_format((float)$s_booking->fee, 2, '.', '')}}

                </td>
                <td>
                  <i class="mdi mdi-currency-gbp "></i>{{number_format((float)$s_booking->tax, 2, '.', '')}}

                </td>
                <td>
                  


                    <i class="mdi mdi-currency-gbp "></i>{{number_format((float)$s_booking->receiveable, 2, '.', '')}}
                  

                </td>
              </tr>

              @endforeach
              @endif



            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

</div>


<!-- container -->
</div>

<style type="text/css">
  .date-container{
    color: white;
    text-align: center;
    padding: 11px 0px;
  }
</style>

@endsection


