@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{route('gym.list')}}">Gym</a></li>
    <li class="breadcrumb-item active" aria-current="page">Members</li>
</ol>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">All Members </h4>
                </div>
            </div>
        </div>
        @if($members)
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <table id="datatable" class="table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Passes</th>
                                <th>Action</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($members as $member)
                            <tr>
                                <td>{{\App\User::getFullName($member->buyer_id)}}</td>
                                <td>{{($member->buyer_user)?$member->buyer_user->phone:''}}</td>
                                <td>{{$member->total_passes}}{{-- \App\Pass::find($member->pass_id)['title'] --}}</td>
                                <td><div class="d-flex">
                                    <a href="{{ url('/') }}/gym/members_detail/{{$member->gym_id}}/{{$member->buyer_id}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                   
                                </div>{{-- {{$member->allow_visits - $member->user_visits}} --}}</td>


                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @else
        @include('includes.not_found_alert',['message'=>'No Found Any Member'])
        @endif
    </div>
    <!-- container -->
</div>

@endsection
