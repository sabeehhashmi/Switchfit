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
        <h4 class="page-title">{{\App\User::getFullName((!empty($bookings->first()))?$bookings->first()->gym_owner_id:0)}}'s Payouts </h4>
      </div>
    </div>

    <div class="col-xl-12">
      <div class="card">
        <div class="card-body">
          <h3 class="bg-dark date-container">
           {{date("d M Y", strtotime(getUrlSegment(6)))}}
         </h3>
         <div class="table-responsive">
          <table class="table table-hover table-centered table-nowrap m-0">

            <thead>
              <tr>
                <th>Gym</th>
                <th>Client</th>
                <th>Pass</th>
                <th>Total Amount</th>
                <th>SwitchFit Fee</th>
                <th>Tax</th>
                <th>Total Receiveable</th>

              </tr>
            </thead>
            <tbody>
              @if(!empty($bookings->first()))

              @foreach($bookings as $s_booking)

              <tr>
                <td>

                  {{$s_booking->gym->name}}
                  

                </td>

                <td>

                  {{$s_booking->byer->first_name}}
                  
                </td>

                <td>
                  {{$s_booking->pass->title}}

                </td>

                <td>
                  <i class="mdi mdi-currency-gbp "></i>{{number_format((float)$s_booking->sub_total + $s_booking->switch_fit_fee, 2, '.', '')}}
                </td>

                <td>
                  @php

                  $total_amount = $s_booking->sub_total + $s_booking->switch_fit_fee;
                  $percentage = ($s_booking->switch_fit_fee/$total_amount)*100;

                  @endphp
                  <i class="mdi mdi-currency-gbp "></i>{{$percentage}}%

                </td>
                
                <td>
                  <i class="mdi mdi-currency-gbp "></i>{{number_format((float)0, 2, '.', '')}}

                </td>
                <td>



                  <i class="mdi mdi-currency-gbp "></i>{{number_format((float)$s_booking->sub_total, 2, '.', '')}}
                  

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


