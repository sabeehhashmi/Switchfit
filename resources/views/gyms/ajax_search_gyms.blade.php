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
                                    <a href="{{ url('/') }}/gym_owner/list/payout/gyms/{{$gym->id}}"
                                                   
                                                ><i class="fa fa-money" aria-hidden="true"></i></a>
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
    @include('includes.not_found_alert',['message'=>'No gym found'])
@endif

{{--<div class="pull-right">--}}
{{--    {{ $gyms->links('paginations.default') }}--}}
{{--</div>--}}
