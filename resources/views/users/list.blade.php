@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Users</li>
</ol>
@endsection
@section('content')
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">App Users </h4>
                </div>
            </div>
            <div class="col">
                <div class="text-right">
                    {{--                        <a href="{{route('user.create')}}" class="btn btn-md btn-add-gym">Add Gym Owner</a>--}}
                </div>
            </div>
        </div>

        @if($users)
        <div class="row">
            <div class="col-12">
                <div class="card-box table-responsive">
                    <table id="datatable_users" class="table" data-ordering='false'>
                        <thead>
                            <tr>
                                <th>First Name</th>
                                <th>Last Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Status</th>

                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{$user->first_name}}</td>
                                <td>{{$user->last_name}}</td>
                                <td>{{$user->email}}</td>
                                <td>{{$user->phone}}</td>
                                <td>
                                    <input type="checkbox" data-plugin="switchery"
                                    data-color="#32317d" data-size="small" data-switchery="true"
                                    style="display: none;"
                                    onchange="updateVerifiedOrNot(this,{{$user->id}})"
                                    {{$user->is_disabled==1 ? '':'checked'}}>
                                    
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
    <!-- container -->
</div>
<style type="text/css">
    .enabledisablebtn {
        border-radius: 5px;
        padding: 4px 8px;
        font-size: 13px;
    }
</style>
<script>
     /**
         * @Description Update verification
         * @param el
         * @Author Sabeeh Hashmi.
         */
         function updateVerifiedOrNot(el,id) {
            Notiflix.Confirm.Show('Confirmation', 'Would you like to change status of this user?', true, false,
                function () {
                    location.href = "/user/enabledisable/"+id;
                }, function () {
                    $(el).trigger('click');
                });
        }

        /**
         * @Description append custom select in datatable for user type filter
         * @Author Khuram Qadeer.
         */
        /*$(function () {
            setTimeout(function () {
                 $('#datatable_users_wrapper .row:first div:first').removeClass('col-md-6').addClass('col-md-3');
                $('#datatable_users_wrapper .row:first div:first').html("<div class='dataTables_length' id='datatable_length2'><label> Type <select name='type' onchange='showUserList(this)' aria-controls='datatable' class='form-control form-control-sm' style='width:135px;'><option value='all'>All</option><option value='trainers' {{getUrlSegment(3)=='trainers'?'selected':''}}>Trainers</option><option value='normal_users' {{getUrlSegment(3)=='normal_users'?'selected':''}}>Normal Users</option></select> </label></div>");
            }, 150)
        });*/

        /**
         * @Description filter by user type
         * @param el
         * @Author Khuram Qadeer.
         */
         function showUserList(el) {
            var type = $(el).children("option:selected").val();
            window.location.href = '/user/list/' + type;
        }
    </script>
    @endsection
