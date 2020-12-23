@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{route('gym.list')}}">Gyms</a></li>
    <li class="breadcrumb-item active" aria-current="page">Passes</li>
</ol>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">{{\App\Gym::find($members_details[0]->gym_id)['name']}} </h4>
                </div>
            </div>
            <div class="col">
            </div>
        </div>

        @if($members_details)
        @foreach($members_details as $members_detail)
        <div class="gym-panel pass-panel">
            <div class="row">
                <div class="col-sm-2">
                    <div class="gym-image-holder">
                        <img src="{{ url('/') }}/assets/images/pass-img.png" style="object-fit: contain;" alt="img"/>
                    </div>
                </div>
                <div class="col-sm-10">
                    <div class="gym-content-panel">
                        <div class="row">
                            <div class="col-sm-10">
                                <div class="d-flex align-self-center">
                                    <h3>{{ \App\Pass::find($members_detail->pass_id)['title']}}</h3>
                                </div>
                            </div>
                            <div class="col-sm-2">
                                <div class="d-flex action-btn text-right pull-right">
                                    @php
                                    $date_now = date("Y-m-d");    
                                    @endphp
                                    @if($date_now < $members_detail->last_valid_date)
                                    
                                    <h4 class="text-success">
                                        Active
                                    </h4>
                                    @else
                                    
                                    <h4 class="text-danger">
                                        Expired
                                    </h4>
                                    @endif
                                    
                                </div>
                            </div>
                        </div>
                        <small>
                            Remaining Visits: {{$members_detail->allow_visits - $members_detail->user_visits}}<br>
                            Exiry Date: {{date('d M Y', strtotime($members_detail->last_valid_date))}}
                        </small> 



                    </div>
                </div>
            </div>
        </div>
        @endforeach
        @endif

        <div class="clearfix"></div>
    </div>
    <!-- container -->
</div>


@endsection


