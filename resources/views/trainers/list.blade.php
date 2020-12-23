@extends('layouts.app')
@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item active" aria-current="page">Trainers</li>
</ol>
@endsection
@section('content')
<style type="text/css">
    .trainer-content-holder strong,.profile-img-holder img{
        cursor: pointer;
    }
</style>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col">
                <div class="page-title-box">
                    <h4 class="page-title">All Trainers </h4>
                </div>
            </div>
            {{-- <div class="col">
                <div class="text-right">
                    <a href="{{route('trainer.create')}}" class="btn btn-md btn-add-gym">Add Personal Trainer</a>
                </div>
            </div> --}}
        </div>
        @if($trainers->first())
        <div class="row">
            <div class="col-sm-3">

            </div>
            <div class="col-sm-9">
                <div class="d-flex justify-content-end">
                    <div class="input-group search-input">
                        <input type="text" class="form-control br-form" id="filter_trainer"
                        style="border-radius: 5px 0 0 5px !important;border-right: 0 !important;"
                        placeholder="Search" value="{{isset ($_GET["searc_param"])?$_GET["searc_param"]:''}}">
                    </div>
                    <a href="#" class="btn btn-location-icon search-input"><img
                        src="/assets/images/search-icon.png" alt="icon"> </a>
                    </div>
                </div>
            </div>
            <div class="row"  id= "all_rows">
                @foreach($users as $trainer)
                <div class="col-sm-4 row_trainers">
                    <div class="card-box trainer-box">
                        <div class="row mlr-10">
                            <div class="col-5">
                                <div class="profile-img-holder">
                                    <img src="{{\App\User::getUserAvatar($trainer->id)}}" alt="img" onclick="window.location.href='{{route('trainer.show',$trainer->id)}}';"/>
                                </div>
                            </div>
                            <div class="col-7 p-0">
                                <div class="trainer-content-holder">
                                    <strong    onclick="window.location.href='{{route('trainer.show',$trainer->id)}}';">{{\App\User::getFullName($trainer->id)}}</strong>
                                    <small><a href="{{ url('/activities') }}/{{$trainer->id}}">Total
                                        Activities: {{collect(\App\TrainerActivity::getByUserId($trainer->id))->count()}}</a>  </small>
                                        @if($trainer->is_verified==1)
                                        <a href="#" style="pointer-events:none;"
                                        class="btn btn-verifed"><span><img
                                            src="/assets/images/verify-icon.png" alt="icon"/></span>Verified</a>
                                            @else
                                            <a href="#" style="pointer-events:none;"
                                            class="btn btn-verifed gold-bg"><span><img
                                                src="/assets/images/unverify-icon.png" alt="icon"/></span>Unverified</a>
                                                @endif


                                                    <div class="t-client-review">
                                                    <i class="fa fa-star {{($trainer->average_rating > 0.5)?'checked':''}} "
                                                       aria-hidden="true"></i>
                                                    <i class="fa fa-star {{($trainer->average_rating > 1.5)?'checked':''}}"
                                                       aria-hidden="true"></i>
                                                    <i class="fa fa-star {{($trainer->average_rating > 2.5)?'checked':''}}"

                                                       aria-hidden="true"></i>
                                                    <i class="fa fa-star {{($trainer->average_rating > 3.5)?'checked':''}}"
                                                       aria-hidden="true"></i>
                                                    <i class="fa fa-star {{($trainer->average_rating > 4.5)?'checked':''}}"
                                                       aria-hidden="true"></i>
                                                    <a href="{{ url('/') }}/trainer/reviews/{{$trainer->id}}"> <span> ({{ $trainer->total_reviews}} Reviews)</span></a>

                                                </div>

                                                <div class="btn-group dot-btn">
                                                    <a href="javascript:void(0);" class="dropdown-toggle"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-ellipsis-v"></i>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <button class="dropdown-item" type="button"
                                                    onclick="window.location.href='{{route('trainer.show',$trainer->id)}}';">
                                                    View
                                                </button>
                                                <button class="dropdown-item" type="button"
                                                onclick="window.location.href='{{route('trainer.edit',$trainer->id)}}'">
                                                Edit
                                            </button>
                                            <button class="dropdown-item" type="button"
                                                onclick="window.location.href='{{ url('/') }}/split-activities/{{$trainer->id}}'">
                                                Acitivity Schedule
                                            </button>
                                            <button class="dropdown-item" type="button"
                                                onclick="window.location.href='{{ url('/') }}/trainer/payout/detail/{{$trainer->id}}'">
                                                Payouts
                                            </button>
                                            <button class="dropdown-item" type="button"
                                            onclick="questionNotification('Confirmation','Are You Sure? You want to delete trainer?','{{route('trainer.delete',$trainer->id)}}')">
                                            Delete
                                        </button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

        </div>

        <div class="row " id="pagi">
            <div class="col-md-8">
            </div>

            <div class="col-md-4 text-right pagination_links">
                {{ $trainers->links() }}
            </div>
        </div>

        @elseif(isset($_GET['searc_param']))
        @include('includes.not_found_alert',['message'=>'"'.$_GET['searc_param'].'" Not Found'])
        @else
        @include('includes.not_found_alert',['message'=>'Not Record Found'])
        @endif
        <div id="no_match" style="display: none">
            @include('includes.not_found_alert',['message'=>'Not Match'])
        </div>
        <!-- end row -->
    </div>
    <!-- container -->
</div>
<style type="text/css">
    .t-client-review i.checked {
    font-size: 15px;

}
.t-client-review a {
    font-size: 15px;

}
</style>

@endsection

@section('script')

<script>
    function searchGym() {
        setTimeout(function () {
            var key = $('#search_key').val();
            if (key) {
                $('#pagi').hide();
            } else {
                $('#pagi').show();
            }
            $.ajax({
                url: '{{url('')}}',
                type: "POST",
                data: {
                    _token: '{{csrf_token()}}',
                    key: key
                },
                success: function (res) {
                    $('#all_rows').html(res)
                }
            });
        }, 1000)
    }
</script>
<script>
        /**
         * @Description Gym List Searching
         * @Author Khuram Qadeer.
         */
         $("#filter_trainer").keyup(function () {


            var key = $(this).val();
            if (key) {
                $('#pagi').hide();
            } else {
                $('#pagi').show();
            }
            $.ajax({
                url: '{{url('/')}}'+'/search/filter',
                type: "POST",
                data: {
                    _token: '{{csrf_token()}}',
                    key: key
                },
                success: function (res) {
                    $('#all_rows').html(res)
                }
            });

                //window.location.href = "/trainer/?searc_param="+$(this).val();



            });
        </script>
        @endsection
