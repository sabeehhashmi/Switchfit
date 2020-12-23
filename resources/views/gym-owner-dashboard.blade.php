<style type="text/css">
	.gym-box small{
		font-size: 16px;
	}
	.gym-box h3{
		font-size: 29px;
	}
	.payout-status-pending {
		width: 55%;
		float: left;
	}
	.map-container {
		width: 700px;
	}
	.recent-review-container {
    overflow-y: auto;
    max-height: 346px;
    overflow-x: hidden;
    float: left;
}
.trainer-content-holder {
    padding: 0 9px;
    
}
.t-client-review a {
    font-size: 14px;

}
.t-client-review i {
    font-size: 14px;
    
}
.t-client-review i.checked {
    font-size: 14px;
    color: #2a2d36;
}
.trainer-dashboard .trainer-content-holder small{
	margin: 0 7px;
}
</style>


<div class="content">
	<div class="container-fluid">
		<div class="row mt_40">
			<div class="col-sm-3">
				<div class="gym-box" onclick="window.location.href='/gym/list'">
					<strong>Gyms</strong>
					<div class="row">
						<div class="col col-8">
							<h3>{{$gyms->count()}}</h3>
							<small>Registered</small>
						</div>
						<div class="col col-4">
							<div class="icon-holder">
								<img src="{{ url('/') }}/assets/images/icon-2.png" alt="text" />
								
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-3">
				
				<div class="gym-box red-bg" onclick="window.location.href='/gym_owner/list/gyms/payout/{{$id}}/?coming=1'">
					<strong>Coming</strong>
					<div class="row">
						<div class="col col-8">
							<h3><i class="mdi mdi-currency-gbp"></i>{{number_format((float)$upcoming, 2, '.', '')}}</h3>
							<small>Total Upcoming</small>
						</div>
						<div class="col col-4">
							<div class="icon-holder">
								<img src="{{ url('/') }}/assets/images/icon-3.png" alt="text" />
							</div>
						</div>
					</div>
				</div>
			</div>  
			<div class="col-sm-3">
				<div class="gym-box lightred-bg" onclick="window.location.href='/gym_owner/list/gyms/payout/{{$id}}'">
					<strong>Total</strong>
					<div class="row">
						<div class="col col-8">
							<h3><i class="mdi mdi-currency-gbp"></i>{{number_format((float)$total_price, 2, '.', '')}}</h3>
							<small>Total Earning</small>
						</div>
						<div class="col col-4">
							<div class="icon-holder">
								<img src="{{ url('/') }}/assets/images/icon-4.png" alt="text" />
							</div>
						</div>
					</div>
				</div>
			</div> 
			<div class="col-sm-3">
				<div class="gym-box org-bg" onclick="window.location.href='/gym/members/owner/all'">
					<strong>Members</strong>
					<div class="row">
						<div class="col col-8">
							<h3>{{$members}}</h3>
							<small>Total Members</small>
						</div>
						<div class="col col-4">
							<div class="icon-holder">
								<img src="{{ url('/') }}/assets/images/icon-1.png" alt="text" />
								
							</div>
						</div>
					</div>
				</div>
			</div> 
		</div>
		<div class="row">
			<div class="col-12">
				<div class="card-box table-responsive">
					<div class="row">
						<div class="col-10 gym-owners-heading">
							<h3>All Payouts</h3>
						</div>
						<div class="col-2">
							<a href="/gym_owner/list/gyms/payout/{{$id}}" class="all-gym-owners">View All</a>
						</div>
					</div>
					<table class="table table-hover table-centered table-nowrap m-0">

						<thead>
							<tr>
								<th>Date</th>
								<th>Gym Name</th>
								<th>Total Pass</th>
								<th>Total Payment</th>
								<th>Total Receiveable</th>
								<th>Status</th>

							</tr>
						</thead>
						<tbody>
							@if(!empty($bookings->first()))

							@php
							$booking_counter = 1;
							@endphp

							@foreach($bookings as $booking)
							@php
							if($booking_counter > 4){
								break;
							}
							@endphp
							<tr>
								<td>
									{{date("d M Y", strtotime($booking->book_date))}}

								</td>

								<td style="text-align: center;">
									{{($booking->gym)?$booking->gym->name:''}}
								</td>
								<td style="text-align: center;">
									{{$booking->total_passes}}
								</td>

								<td>
									<i class="mdi mdi-currency-gbp"></i>{{number_format((float)$booking->sub_total + $booking->switch_fit_fee, 2, '.', '')}}
								</td>


								<td>
									<i class="mdi mdi-currency-gbp"></i>{{number_format((float)$booking->sub_total, 2, '.', '')}}
								</td>



								<td>

									@if($booking->payout_status == 'pending')

									<strong class="text-warning status-shown payout-status-pending">
									Pending</strong>
									<a href="{{ url('/') }}/gym_owner/date/vise/booking/{{$booking->gym_id}}/{{$booking->book_date}}" ><i class="fa fa-eye" aria-hidden="true"></i></a>
									@else
									<strong class="text-success status-shown">Completed</strong>
									<a href="{{ url('/') }}/gym_owner/date/vise/booking/{{$booking->gym_id}}/{{$booking->book_date}}"><i class="fa fa-eye" aria-hidden="true"></i></a>

									@endif
								</td>
							</tr>
							@php
							$booking_counter++;
							@endphp

							@endforeach
							@endif



						</tbody>
					</table>
					
				</div>
			</div>
		</div> 
		<div class="maps-sec">
			<div class="row">
				<div class="col-sm-8">
					<strong>Gyms</strong>
					<div id="map" class="map-container" ></div>
					
				</div>
				<div class="col-sm-4">
					<strong>Recent Reviews</strong>
					<div class="recent-review-container"> 
					@if($reviews->count()>0)
					@foreach($reviews as $review)
					<div class="card-box trainer-box trainer-dashboard">
						<div class="row mlr-10">
							<div class="col-4">
								<div class="profile-img-holder">
									<img src="{{($review->user)?$review->user->avatar:'assets/images/profile-img.png'}}" alt="img">
								</div>
							</div>
							<div class="col-8 p-0">
								<div class="trainer-content-holder">
									<strong> {{($review->user)?' '.$review->user->first_name.' '.$review->user->last_name:'Some User'}}</strong>
									<small>
										{{($review->gym)?$review->gym->name:''}}
									</small>
									<div class="t-client-review">
                                                    <i class="fa fa-star {{($review->stars > 0.5)?'checked':''}} "
                                                       aria-hidden="true"></i>
                                                    <i class="fa fa-star {{($review->stars > 1.5)?'checked':''}}"
                                                       aria-hidden="true"></i>
                                                    <i class="fa fa-star {{($review->stars > 2.5)?'checked':''}}"

                                                       aria-hidden="true"></i>
                                                    <i class="fa fa-star {{($review->stars > 3.5)?'checked':''}}" 
                                                       aria-hidden="true"></i>
                                                    <i class="fa fa-star {{($review->stars > 4.5)?'checked':''}}" 
                                                       aria-hidden="true"></i>
                                                    <a href="{{ url('/') }}/gym/show/reviews/{{$review->gym_id}}"> <span> ({{number_format((float)$review->stars, 1, '.', '')}})</span></a>

                                                </div>

								</div>
							</div>
						</div>
					</div>
					@endforeach
					@endif
				</div>

				</div>
			</div>
		</div>
	</div>
