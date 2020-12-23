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
        <h4 class="page-title">{{\App\User::getFullName(getUrlSegment(4))}}'s Payouts </h4>
      </div>
    </div>

    <div class="col-xl-12">
      <div class="card">
        <div class="card-body">
         @if(!empty($all_bookings['bookings']->first()))
         <form class="dates-conatiner-form">
          {{ csrf_field() }}
          <input type="hidden" name="tariner_id" class="tariner_id" value="{{getUrlSegment(4)}}">
          <div class="row">
            <div class="col-xs-12 col-sm-3">
              <div class="input-group">
                <label>From Date</label>

                <input type="text" class="form-control date_picker from_date"  name="from_date"
                value="">
                <span class="fa fa-calendar input-calendar"></span>
              </div>
            </div>
            <div class="col-xs-12 col-sm-3">
              <div class="input-group">
                <label>To Date</label>

                <input type="text" class="form-control date_picker to_date"  name="to_date"
                value="">
                <span class="fa fa-calendar input-calendar"></span>
              </div>
            </div>
            <div class="col-xs-12 col-sm-4">
              <div class="input-group">
                <div class="reset-btn">
                  <button type="button" class="btn btn-danger">Reset</button>
                </div>
              </div>
            </div>
            <div class="col-xs-12 col-sm-2">
              <div class="input-group">
                <div class="reset-btn">
                  <button type="button" class="btn btn-info btn-download-xlx">Download as xlsx</button>
                </div>
              </div>
            </div>
          </div>
        </form>
        <div id="all_rows">

          <div class="dropdown float-right">
            <h4 class="header-title">Total Earning: <i class="mdi mdi-currency-gbp"></i>{{number_format((float)$all_bookings['total_payment'], 2, '.', '')}}</h4>
          </div>

          <h4 class="header-title mb-3">UpComing Earning: <i class="mdi mdi-currency-gbp"></i>{{number_format((float)$all_bookings['payable'], 2, '.', '')}}</h4>

          <div class="table-responsive">
            <table class="table table-hover table-centered table-nowrap m-0">

              <thead>
                <tr>
                  <th>Date</th>
                  <th>Total Activities</th>
                  <th>Total Payment</th>
                  <th>Total Receiveable</th>
                  <th>Status</th>

                </tr>
              </thead>
              <tbody>
                @if(!empty($all_bookings['bookings']))
                @php
                $bookings = $all_bookings['bookings'];
                @endphp
                @foreach($bookings as $booking)

                <tr>
                  <td>
                    {{date("d M Y", strtotime($booking->booking_date))}}

                  </td>

                  <td style="text-align: center;">
                    {{$booking->total_activites}}
                  </td>

                  <td>
                    <i class="mdi mdi-currency-gbp"></i>{{number_format((float)$booking->total_amount, 2, '.', '')}}
                  </td>


                  <td>
                    <i class="mdi mdi-currency-gbp"></i>{{number_format((float)$booking->total_receiveable, 2, '.', '')}}
                  </td>



                  <td>

                    @if($booking->payout == 1)

                    <strong class="text-success status-shown">Completed</strong>
                    <a href="{{ url('/') }}/get/date/vise/bookings/{{$booking->booking_date}}/{{getUrlSegment(4)}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                    @else

                    <strong class="text-warning status-shown payout-status-pending">
                    Pending</strong>
                    <a href="{{ url('/') }}/get/date/vise/bookings/{{$booking->booking_date}}/{{getUrlSegment(4)}}" ><i class="fa fa-eye" aria-hidden="true"></i></a>
                    @endif
                  </td>
                </tr>

                @endforeach

                @endif



              </tbody>
            </table>
          </div>

          
        </div>
        @else
        @include('includes.not_found_alert',['message'=>'No Payouts Found'])
        @endif
      </div>
    </div>
  </div>

</div>


<!-- container -->
</div>

@endsection
@section('head_script')
<style type="text/css">
  .reset-btn {
    margin-top: 36px;
  }
  td a{
    color: #000;
  }
</style>
<link href="http://code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css" rel="Stylesheet" type="text/css"/>
{{--        <script type="text/javascript" src="http://code.jquery.com/jquery-1.7.2.min.js"></script>--}}
<script type="text/javascript" src="http://code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
<script>
  $(document).ready(function () {
    $(".date_picker").datepicker({
      dateFormat: "dd/mm/yy",

    });

    

  });
  $(document).ready(function () {
    $('.btn-danger').click(function(){
      $('.from_date').val('');
      $('.to_date').val('');
      $.ajax({
        url: '{{url('/')}}'+'/trainer/payout/detail',
        type: "POST",
        data: {
          _token: '{{csrf_token()}}',
          trainer_id:$('.tariner_id').val(),
          from_date:$('.from_date').val(),
          to_date:$('.to_date').val(),

        },
        success: function (res) {
          $('#all_rows').html(res)
        }
      });
    });
    $('.date_picker').change(function (e) {
      formData = $('.dates-conatiner-form').serialize();
      $.ajax({
        url: '{{url('/')}}'+'/trainer/payout/detail',
        type: "POST",
        data: {
          _token: '{{csrf_token()}}',
          trainer_id:$('.tariner_id').val(),
          from_date:$('.from_date').val(),
          to_date:$('.to_date').val(),

        },
        success: function (res) {
          $('#all_rows').html(res)
        }
      });

    });
    $('.btn-download-xlx').click(function(){
      formData = $('.dates-conatiner-form').serialize();
      $.ajax({
        url: '{{url('/')}}'+'/get/payouts/download',
        type: "POST",
        data: {
         _token: '{{csrf_token()}}',
         trainer_id:$('.tariner_id').val(),
         from_date:$('.from_date').val(),
         to_date:$('.to_date').val(),


       },
       success: function (res) {
        window.location.href = res.file;
        console.log(res.file);

      }
    });

    });
  });
</script>
@endsection


