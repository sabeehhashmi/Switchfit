@extends('layouts.app')
@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{route('gym_owner.list')}}">Gym Owners</a></li>
        <li class="breadcrumb-item active" aria-current="page">Gyms</li>
    </ol>
@endsection
@section('content')
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">
                    <div class="page-title-box">
                        <h4 class="page-title"> {{\App\User::getFullName(getUrlSegment(4))}}</h4>
                    </div>
                </div>
                @if(isGymOwner())
                    <div class="col">
                        <div class="text-right">
                            <a href="{{route('gym.create')}}" class="btn btn-md btn-add-gym mt-2">Add Gym</a>
                        </div>
                    </div>
                @endif
            </div>
            {{--            <div class="row">--}}
            {{--                                <div class="col-sm-3">--}}
            {{--                                    <div class="input-group">--}}
            {{--                                        <select class="form-control custom-select width-40">--}}
            {{--                                            <option>5 per page</option>--}}
            {{--                                            <option>10 per page</option>--}}
            {{--                                        </select>--}}
            {{--                                    </div>--}}
            {{--                                </div>--}}
            @if($gyms->total())
                <div class="col-sm-12">
                    <div class="d-flex justify-content-end">
                        <div class="input-group search-input">
                            <input type="text" id="search_key" class="form-control br-form"
                                   onkeyup="searchGymByGymOwner()" onkeydown="searchGymByGymOwner()"
                                   style="border-radius: 5px 0 0 5px !important;border-right: 0 !important;"
                                   placeholder="Search" name="">
                        </div>
                        <a href="#" class="btn btn-location-icon search-input" onclick="searchGymByGymOwner()"><img
                                src="/assets/images/search-icon.png"
                                alt="icon"> </a>
                    </div>
                </div>
            @endif
        </div>

        <div id="all_rows">
            @if($gyms->total())
                @foreach($gyms as $gym)
                    <div class="gym-panel " id="gym_{{$gym->id}}">
                        <div class="row">
                            <div class="col-sm-2" id="img-{{$gym->id}}">
                                <div class="gym-image-holder">
                                    <input type="hidden">
                                    <img
                                        src="{{$gym->images? asset(json_decode($gym->images)[0]):'/assets/images/default-img.jpg'}}"
                                        alt="img"/>
                                </div>
                            </div>
                            <div class="col-sm-10">
                                <div class="gym-content-panel">
                                    <div class="row">
                                        <div class="col-sm-10">
                                            <div class="d-flex align-self-center" data-id="{{$gym->id}}">
                                                <h3 class="results_gym"
                                                    data-id="{{$gym->id}}">{{Str::limit($gym->name,30,'...')}}</h3>
                                                <div class="t-client-review">
                                                    <i class="fa fa-star {{\App\Review::getReviewByGymId($gym->id)['total_stars'] >= 1 ? 'checked':''}}"
                                                       aria-hidden="true"></i>
                                                    <i class="fa fa-star {{\App\Review::getReviewByGymId($gym->id)['total_stars'] >= 2 ? 'checked':''}}"
                                                       aria-hidden="true"></i>
                                                    <i class="fa fa-star {{\App\Review::getReviewByGymId($gym->id)['total_stars'] >= 3 ? 'checked':''}}"
                                                       aria-hidden="true"></i>
                                                    <i class="fa fa-star {{\App\Review::getReviewByGymId($gym->id)['total_stars'] >= 4 ? 'checked':''}}"
                                                       aria-hidden="true"></i>
                                                    <i class="fa fa-star {{\App\Review::getReviewByGymId($gym->id)['total_stars'] >= 5 ? 'checked':''}}"
                                                       aria-hidden="true"></i>
                                                    <a href="{{route('gym.show.reviews',$gym->id)}}"> <span> {{\App\Review::getReviewByGymId($gym->id)['total_stars']}} ({{\App\Review::getReviewByGymId($gym->id)['total_reviews']}} Reviews)</span></a>

                                                </div>

                                                {{--                                            <ul class="rating-list">--}}
                                                {{--                                                <li><a href="#"><i class="fa fa-star"></i></a></li>--}}
                                                {{--                                                <li><a href="#"><i class="fa fa-star"></i></a></li>--}}
                                                {{--                                                <li><a href="#"><i class="fa fa-star"></i></a></li>--}}
                                                {{--                                                <li><a href="#"><i class="fa fa-star"></i></a></li>--}}
                                                {{--                                                <li><a href="#"><i class="fa fa-star"></i></a></li>--}}
                                                {{--                                                <li><a href="{{route('gym.show.reviews',$gym->id)}}"><span>5.0 (232 Reviews)</span></a></li>--}}
                                                {{--                                            </ul>--}}
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <div class="d-flex action-btn text-right pull-right">
                                                <a href="{{route('gym.show',$gym->id)}}"><i class="fa fa-eye"
                                                                                            aria-hidden="true"></i></a>
                                                <a href="{{route('gym.edit',$gym->id)}}"><i class="fa fa-pencil"
                                                                                            aria-hidden="true"></i></a>
                                                <a href="#"
                                                   onclick="questionNotification('Confirmation','Are You Sure? You want to delete gym?','{{route('gym.delete',$gym->id)}}')"
                                                ><i class="fa fa-trash" aria-hidden="true"></i></a>
                                            </div>
                                        </div>
                                    </div>
                                    <small><i class="fa fa-map-marker" aria-hidden="true"></i> {{$gym->address}}</small>
                                    <ul class="pass-gym-sec">
                                        <li>
                                            <div class="d-flex justify-content-center align-self-center"
                                                 style="cursor: pointer;"
                                                 onclick="window.location.href='{{route('passes.list',$gym->id)}}'">
                                                  <span class="icon-holder">
                                                    <img src="/assets/images/pass.png" alt="icons"/>
                                                  </span>
                                                <span class="icon-content">
                                                    <strong>Passes</strong>
                                                                    <p>{{collect(\App\Pass::getActivePasses($gym->id))->count()}}</p>
                                                </span>
                                            </div>
                                        </li>
                                        <li>
                                            <div class="d-flex justify-content-center align-self-center"
                                                 style="cursor: pointer;"
                                                 onclick="window.location.href='{{route('gym.show.members',$gym->id)}}'">
                                                  <span class="icon-holder">
                                                    <img src="/assets/images/gym-icon.png" alt="icons"/>
                                                  </span>
                                                <span class="icon-content">
                                                    <strong>Members</strong>
                                                             <p>{{collect(\App\Gym::getActiveGymMembers($gym->id))->count()}}</p>
                                                  </span>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                @include('includes.not_found_alert',['message'=>'No Found Any Gym'])
            @endif

        </div>

        <div id="no_match" style="display: none">
            @include('includes.not_found_alert',['message'=>'No matching records found'])
        </div>

        <div class="pull-right" id="pagi">
            {{ $gyms->links('paginations.default') }}
        </div>
        <div class="clearfix"></div>
    </div>

@endsection

@section('script')
    <script>
        function searchGymByGymOwner() {
            setTimeout(function () {
                var key = $('#search_key').val();
                if (key) {
                    $('#pagi').hide();
                } else {
                    $('#pagi').show();
                }

                $.ajax({
                    url: '{{route('gym_owner.gym.search')}}',
                    type: "POST",
                    data: {
                        _token: '{{csrf_token()}}',
                        key: key,
                        gym_owner_id: '{{getUrlSegment(4)}}',
                    },
                    success: function (res) {
                        $('#all_rows').html(res)
                    }
                });
            }, 1000)
        }

    </script>
@endsection
