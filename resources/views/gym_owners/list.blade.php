@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Gym Owners</li>
</ol>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">All Gym Owners </h4>
                </div>
            </div>
            <div class="col">
                <div class="text-right">
                    <a href="{{route('gym_owner.create')}}" class="btn btn-md btn-add-gym">Add Gym Owner</a>
                </div>
            </div>
        </div>

        @if($gymOwners)
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <table id="datatable" class="table" data-ordering='false'>
                        <thead>
                            <tr>
                                {{--                                    <th>ID</th>--}}
                                <th>Business Name</th>
                                <th>Manager Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($gymOwners as $owner)
                            <tr>
                                {{--                                        <td>{{$owner->id}}</td>--}}
                                <td>{{$owner->first_name}}</td>
                                <td>{{$owner->manager_name}}</td>
                                <td>{{$owner->email}}</td>
                                <td>{{$owner->phone}}</td>
                                <td>
                                    <div class="btn-group dot-btn">
                                      <a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                         <i class="fa fa-ellipsis-v"></i>
                                     </a>
                                     <div class="dropdown-menu dropdown-menu-right">

                                        <button class="dropdown-item" type="button" onclick="window.location.href='{{route('gym_owner.show',$owner->id)}}'">
                                          View
                                      </button>
                                      <button class="dropdown-item" type="button" onclick="window.location.href='{{route('gym_owner.edit',$owner->id)}}'">
                                          Edit
                                      </button>
                                      <button class="dropdown-item" type="button" onclick="window.location.href='{{route('gym_owner.delete',$owner->id)}}'">
                                          Delete
                                      </button>
                                      <button class="dropdown-item" type="button" onclick="window.location.href='{{route('gym_owner.list.gyms',$owner->id)}}'"  >
                                          Gyms
                                      </button>
                                      <button class="dropdown-item" type="button" onclick="window.location.href='{{url('/')}}/gym_owner/list/gyms/payout/{{$owner->id}}'">
                                          Payouts
                                      </button>
                                  </div>
                              </div>
                          </td>



                      </tr>
                      @endforeach
                  </tbody>
              </table>
          </div>
      </div>
  </div>
  @else
  @include('includes.not_found_alert',['message'=>'No Found Any Gym Owner'])
  @endif
</div>
<style type="text/css">
    .btn-group.dot-btn {
        position: relative;
    }
</style>
<!-- container -->
</div>

@endsection
