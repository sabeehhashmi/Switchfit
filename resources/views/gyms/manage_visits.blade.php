@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Manage Visits</li>
</ol>
@endsection
@section('content')
<style type="text/css">
    .profile-visiter .img-holder ul li {
        
        padding: 10px 0px;
    }
</style>

<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">Manage Visit's </h4>
                </div>
            </div>
            <div class="col col-4">
                <div class="text-right">
                    <div class="d-flex">
                        <div class="input-group">
                            <input type="text" name="pass_token" id="pass_token" placeholder="Search Pass ID"
                            class="form-control br-form required @error('pass_token') is-invalid @enderror"
                            style="border-radius: 5px 0 0 5px !important;border-right: 0 !important;"
                            value="{{getUrlSegment(4) ? getUrlSegment(4):'' }}"
                            required>
                            @error('pass_token')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>

                        <a href="#" onclick="window.location.href='/manage/visits/list/'+$('#pass_token').val()"
                        class="btn btn-location-icon m-0 " id="btn_token_search"><i class="fa fa-search"
                        aria-hidden="true"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @if(isset($pass))
    @if($pass)
    <div class="visit-sec">
        <div class="row">
            <div class="col-sm-4">
                <div class="profile-visiter">
                    <div class="img-holder">
                        <img src="{{\App\User::getUserAvatar($pass->buyer_id)}}" alt="img">
                        <strong>{{\App\User::getFullName($pass->buyer_id)}}</strong>
                        <p>{{\App\User::find($pass->buyer_id)['phone']}}</p>
                        @if($pass->is_used == 0 && $pass->is_expire == 0)
                        <ul>
                            <li>
                                <a href="{{route('manage_visits.list')}}"
                                class="btn btn-md primay-btn btn-cancel red-bg">Cancel</a>
                            </li>
                            <li>
                                <a href="#" class="btn btn-md primay-btn btn-cancel"
                                onclick="questionNotification('Confirmation','Are You Sure? You want to add one more visit?','{{route('manage_visits.add.visit',$pass->pass_token)}}')"
                                >Confirm Entry</a>
                            </li>
                        </ul>
                        @elseif($pass->is_used==1)
                        <p style=" font-family: 'ProximaNova-Bold';font-weight: 400;font-size: 15px;color: #d4182e !important;">
                        YOU HAVE NOT ANY VISIT</p>
                        @elseif($pass->is_expire==1)
                        <b style="font-family: 'ProximaNova-Bold';font-weight: 400;font-size: 15px;color: #d4182e !important;">
                            YOUR PASS HAS BEEN EXPIRED
                        </b>
                        @endif

                    </div>
                    <div class="row counter_panel">
                        <div class="col-4">
                            <div class="count-sec text-center">
                                @if($pass->allow_visits ==0)
                                <strong style=" font-size: 25px !important;">{{$pass->qty}}</strong>
                                @else
                                <strong>{{$pass->qty}}</strong>
                                @endif
                                <p>Quantity</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="count-sec text-center">
                                @if($pass->allow_visits ==0)
                                <strong style=" font-size: 25px !important;">Unlimited</strong>
                                @else
                                <strong>{{$pass->allow_visits}}</strong>
                                @endif
                                <p>Total Visit</p>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="count-sec text-center">
                                @if($pass->allow_visits ==0)
                                <strong style=" font-size: 25px !important;">Unlimited</strong>
                                @else
                                <strong>{{(int)$pass->allow_visits - (int)$pass->user_visits}}</strong>
                                @endif
                                <p>Remaining</p>
                            </div>
                        </div>
                    </div>
                    <div class="visiter-details-sec">
                        <ul>
                            <li>
                                <span class="pas-name">Pass Name</span>
                                <span
                                class="pas-idenity">{{\App\Pass::find($pass->pass_id)['title']}}</span>
                            </li>
                            <li>
                                <span class="pas-name">Pass ID</span>
                                <span class="pas-idenity">{{strtoupper($pass->pass_token)}}</span>
                            </li>
                            <li>
                                <span class="pas-name">Buy Date</span>
                                <span class="pas-idenity">{{$pass->created_at->format('d M Y')}}</span>
                            </li>
                            <li>
                                <span class="pas-name">Expiry Date</span>
                                <span
                                class="pas-idenity">{{date('d M Y', strtotime($pass->last_valid_date))}}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                @if(collect($pass->visits_detail)->count() > 0)
                <div class="profile-visiter">
                    <div class="table-responsive">
                        <table class="table table-visit no-footer manage_visit_tbl">
                            <thead>
                                <tr>
                                    <th>Number of Visit</th>
                                    <th>Date</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pass->visits_detail as $visit)
                                <tr>
                                    <td>{{$visit->current_visit_no}}</td>
                                    <td>{{$visit->created_at->format('d M Y')}}</td>
                                    <td>{{strtoupper(date("g:i a", strtotime("$visit->time")))}}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                @include('includes.not_found_alert',['message'=>'Visit History Not Found'])
                @endif
            </div>
        </div>
    </div>
    @else
    @include('includes.not_found_alert',['message'=>$msg])
    @endif
    @else
    @include('includes.not_found_alert',['message'=>$msg])
    @endif
</div>
<!-- container -->
</div>

@endsection

@section('script')
<script>
    $("#pass_token").keyup(function (event) {
        if (event.keyCode == 13) {
            $("#btn_token_search").click();
        }
    });
</script>
@endsection
