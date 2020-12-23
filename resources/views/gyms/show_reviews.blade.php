@extends('layouts.app')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{route('gym.list')}}">Gyms</a></li>
    <li class="breadcrumb-item active" aria-current="page">Reviews</li>
</ol>
@endsection

@section('content')
<style type="text/css">
    .t-client-img img {
        height: 80px;
        object-fit: cover;
        border-radius: 50%;
        width: 80px;
    }
</style>
<div class="content">
    @if($reviews['reviews'])
    <div class="container-fluid">
        <div class="review-sec">
            <div class="row mb-80">
                <div class="col-sm-6">
                    <h4 class="page-title pb-1">{{$reviews['gym_name']}}</h4>
                    <div class="t-client-review">
                        <i class="fa fa-star {{$reviews['total_stars'] >= 1 ? 'checked':''}}"
                         aria-hidden="true"></i>
                         <i class="fa fa-star {{$reviews['total_stars'] >= 2 ? 'checked':''}}"
                             aria-hidden="true"></i>
                             <i class="fa fa-star {{$reviews['total_stars'] >= 3 ? 'checked':''}}"
                                 aria-hidden="true"></i>
                                 <i class="fa fa-star {{$reviews['total_stars'] >= 4 ? 'checked':''}}"
                                     aria-hidden="true"></i>
                                     <i class="fa fa-star {{$reviews['total_stars'] >= 5 ? 'checked':''}}"
                                         aria-hidden="true"></i>
                                         <span class="ml-2">{{number_format((float)$reviews['total_stars'], 1, '.', '')}}</span>
                                         <span><a href="#">( {{$reviews['total_reviews']}} Reviews )</a></span>
                                     </div>
                                 </div>
                                 <div class="col-sm-6">
                                    @if($reviews['options_stars'])
                                    <ul class="rating_list">
                                        @foreach($reviews['options_stars'] as $option)
                                        <li>
                                            <strong>{{ucfirst($option['name'])}}</strong>
                                            <div class="t-client-review">
                                                <i class="fa fa-star {{$option['stars'] >= 1 ? 'checked':''}}"
                                                 aria-hidden="true"></i>
                                                 <i class="fa fa-star {{$option['stars'] >= 2 ? 'checked':''}}"
                                                     aria-hidden="true"></i>
                                                     <i class="fa fa-star {{$option['stars'] >= 3 ? 'checked':''}}"
                                                         aria-hidden="true"></i>
                                                         <i class="fa fa-star {{$option['stars'] >= 4 ? 'checked':''}}"
                                                             aria-hidden="true"></i>
                                                             <i class="fa fa-star {{$option['stars'] >= 5 ? 'checked':''}}"
                                                                 aria-hidden="true"></i>
                                                                 <span>{{number_format((float)$option['stars'], 1, '.', '')}}</span>
                                                             </div>
                                                         </li>
                                                         @endforeach
                                                     </ul>
                                                     @endif
                                                 </div>
                                             </div>
                                             <div class="row mt-4">
                                                <div class="col-sm-12">
                                                    @if($reviews['reviews'])
                                                    @foreach($reviews['reviews'] as $review)
                                                    <div class="single-testimonial">
                                                        <div class="t-client-info mb-30 fix">
                                                            <div class="t-client-img">
                                                                <img
                                                                src="{{asset($review->given_by_user_avatar)}}"
                                                                alt="avatar">
                                                            </div>
                                                            <div class="t-client-name fix">
                                                                <h5>{{$review->given_by_user_name}} <span
                                                                    class="date-issue">{{$review->created_at->format('d F, Y')}}</span>
                                                                    <span class="d-flex action-btn text-right pull-right">
                                                                       @if(isSuperAdmin())
                                                                       <a href="#"
                                                                       onclick="questionNotification('Confirmation','Are You Sure? You want to delete review?','{{route('gym.delete.review',$review->id)}}')"><i
                                                                       class="fa fa-trash" aria-hidden="true"></i></a>
                                                                       @endif
                                                                   </span></h5>
                                                                   <div class="t-client-review">
                                                                    <i class="fa fa-star {{$review->stars >= 1 ? 'checked':''}}"
                                                                     aria-hidden="true"></i>
                                                                     <i class="fa fa-star {{$review->stars >= 2 ? 'checked':''}}"
                                                                         aria-hidden="true"></i>
                                                                         <i class="fa fa-star {{$review->stars >= 3 ? 'checked':''}}"
                                                                             aria-hidden="true"></i>
                                                                             <i class="fa fa-star {{$review->stars >= 4 ? 'checked':''}}"
                                                                                 aria-hidden="true"></i>
                                                                                 <i class="fa fa-star {{$review->stars >= 5 ? 'checked':''}}"
                                                                                     aria-hidden="true"></i>
                                                                                 </div>
                                                                             </div>
                                                                         </div>
                                                                         <div class="testimonial-content">
                                                                            <p>{{$review->description}}</p>
                                                                        </div>
                                                                    </div>
                                                                    @endforeach
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- container -->
                                                    @else
                                                    @include('includes.not_found_alert',['message'=>'No Found Any Review'])
                                                    @endif
                                                </div>

                                                @endsection
