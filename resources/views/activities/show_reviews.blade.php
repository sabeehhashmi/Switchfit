@extends('layouts.app')

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ url('/') }}/trainer">Trainers</a></li>
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
    <style type="text/css">
        .trainer-image-container {

            padding-top: 17px;
        }
        .trainer-image-container img {
           width: 120px;
           height: 120px;
           object-fit: cover;
           border-radius: 5px;

       }
   </style>

   @if(!empty($final_data['trainer_data']->reviews->count() > 0))
   <div class="container-fluid">
    <div class="review-sec">
        <div class="row mb-80">
            @if(!empty($final_data['trainer_data']))
            @php
            $trainer_data = $final_data['trainer_data']
            @endphp
            <div class="col-sm-6">
             <div class="trainer-image-container">
                <img src="{{ url('/') }}/{{$trainer_data->avatar}}">
            </div>
            <h4 class="page-title pb-1">{{$trainer_data->first_name.' '.$trainer_data->first_name}}</h4>
            <div class="t-client-review">
                <i class="fa fa-star {{($trainer_data->average_rating > 0.5)?'checked':''}}"
                 aria-hidden="true"></i>
                 <i class="fa fa-star {{($trainer_data->average_rating > 1.5)?'checked':''}}"
                     aria-hidden="true"></i>
                     <i class="fa fa-star {{($trainer_data->average_rating > 2.5)?'checked':''}}"
                         aria-hidden="true"></i>
                         <i class="fa fa-star {{($trainer_data->average_rating > 3.5)?'checked':''}}"
                             aria-hidden="true"></i>
                             <i class="fa fa-star {{($trainer_data->average_rating > 4.5)?'checked':''}}"
                                 aria-hidden="true"></i>
                                 <span class="ml-2">{{number_format((float)$trainer_data->average_rating, 1, '.', '')}}</span>
                                 <span><a href="#">( {{$trainer_data->total_reviews}} Reviews )</a></span>
                             </div>

                         </div>
                         @endif

                         <div class="col-sm-6">
                            @if(!empty($final_data['trainer_data']))
                            <ul class="rating_list">
                                @php
                                $reviews_options = $final_data['reviews_options'];
                                @endphp
                                @foreach($reviews_options as $option)
                                <li>
                                    <strong>{{ucfirst($option->name)}}</strong>
                                    <div class="t-client-review">
                                        <i class="fa fa-star {{($option->average_rating > 0.5)?'checked':''}}"
                                         aria-hidden="true"></i>
                                         <i class="fa fa-star {{($option->average_rating > 1.5)?'checked':''}}"
                                             aria-hidden="true"></i>
                                             <i class="fa fa-star {{($option->average_rating > 2.5)?'checked':''}}"
                                                 aria-hidden="true"></i>
                                                 <i class="fa fa-star {{($option->average_rating > 3.5)?'checked':''}}"
                                                     aria-hidden="true"></i>
                                                     <i class="fa fa-star {{($option->average_rating > 4.5)?'checked':''}}"
                                                         aria-hidden="true"></i>
                                                         <span>{{number_format((float)$option->average_rating, 1, '.', '')}}</span>
                                                     </div>
                                                 </li>
                                                 @endforeach
                                             </ul>
                                             @endif
                                         </div>
                                     </div>
                                     <div class="row mt-4">
                                        <div class="col-sm-12">
                                            @if(!empty($final_data['reviews_details']))
                                            @php
                                            $reviews_details = $final_data['reviews_details'];
                                            @endphp
                                            @foreach($reviews_details as $review)
                                            <div class="single-testimonial">
                                                <div class="t-client-info mb-30 fix">
                                                    <div class="t-client-img">
                                                        <img
                                                        src="{{url('/')}}/{{$review->user->avatar}}"
                                                        alt="avatar">
                                                    </div>
                                                    <div class="t-client-name fix">
                                                        <h5>{{$review->user->first_name.' '.$review->user->last_name}} <span
                                                            class="date-issue">{{$review->created_at->format('d F, Y')}}</span>
                                                            <span class="d-flex action-btn text-right pull-right">
                                                               @if(isSuperAdmin())
                                                               <a href="#"
                                                               onclick="questionNotification('Confirmation','Are You Sure? You want to delete review?','{{route('gym.delete.review',$review->id)}}')"><i
                                                               class="fa fa-trash" aria-hidden="true"></i></a>
                                                               @endif
                                                           </span></h5>
                                                           <div class="t-client-review">
                                                            <i class="fa fa-star 
                                                            {{($review->stars > 0.5)?'checked':''}}"
                                                            aria-hidden="true"></i>
                                                            <i class="fa fa-star {{($review->stars > 1.5)?'checked':''}}"
                                                             aria-hidden="true"></i>
                                                             <i class="fa fa-star {{($review->stars > 2.5)?'checked':''}}"
                                                                 aria-hidden="true"></i>
                                                                 <i class="fa fa-star {{($review->stars > 3.5)?'checked':''}}"
                                                                     aria-hidden="true"></i>
                                                                     <i class="fa fa-star {{($review->stars > 4.5)?'checked':''}}"
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
