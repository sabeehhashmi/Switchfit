

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
				
				<div class="gym-box red-bg" onclick="window.location.href='/user/list/normal_users'">
					<strong>App Users</strong>
					<div class="row">
						<div class="col col-8">
							<h3>{{$users->count()}}</h3>
							<small>Total Joined</small>
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
				<div class="gym-box lightred-bg" onclick="window.location.href='/trainer'">
					<strong>Trainers</strong>
					<div class="row">
						<div class="col col-8">
							<h3>{{$trainers->count()}}</h3>
							<small>Total Trainers</small>
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
				<div class="gym-box org-bg" onclick="window.location.href='/trainer'">
					<strong>Activities</strong>
					<div class="row">
						<div class="col col-8">
							<h3>{{$activities->count()}}</h3>
							<small>Total Activities</small>
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
							<h3>Gym Owners</h3>
						</div>
						<div class="col-2">
							<a href="/gym_owner/list" class="all-gym-owners">View All</a>
						</div>
					</div>
					<table id="datatable" class="table" data-ordering='false' data-paging='false' data-paging='false' data-searching='false' data-datatable_info='false' >
						<thead>
							<tr>

								<th>Business Name</th>
								<th>Manager Name</th>
								<th>Email</th>
								<th>Phone</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							@if(!empty($gymOwners->first()))
							@php
							$counter = 0;
							@endphp
							@foreach($gymOwners as $owner)
							@php
							if($counter > 5):
								break;
							endif;
							@endphp
							<tr>
								{{--                                        <td>{{$owner->id}}</td>--}}
								<td>{{$owner->first_name}}</td>
								<td>{{$owner->manager_name}}</td>
								<td>{{$owner->email}}</td>
								<td>{{$owner->phone}}</td>
								<td>
									<div class="d-block">
										<a href="{{route('gym_owner.show',$owner->id)}}"><i class="fa fa-eye" aria-hidden="true"></i></a>
										<a href="{{route('gym_owner.edit',$owner->id)}}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
										<a href="{{route('gym_owner.delete',$owner->id)}}"><i class="fa fa-trash" aria-hidden="true"></i></a>
									</div>
									
								</td>



							</tr>
							@php $counter++; @endphp
							@endforeach
							@endif

						</tbody>
					</table>
				</div>
			</div>
		</div> <!-- end row -->
		<div class="maps-sec">
			<div class="row">
				<div class="col-sm-4">
					<strong>Gyms</strong>
					<div id="map" class="map-container" ></div>
					
				</div>
				<div class="col-sm-4">
					<strong>Our Trainers</strong>
					<div id="map2" class="map-container" ></div>
					
					
					
				</div>
				<div class="col-sm-4">
					<strong>New Trainers</strong>
					@if($trainers->count()>0)
					@php
					$trainer_counter = 1;
					@endphp
					@foreach($trainers as $trainer)
					@php
					if($trainer_counter > 3){
						break;
					}
					@endphp
					<div class="card-box trainer-box trainer-dashboard">
						<div class="row mlr-10">
							<div class="col-4">
								<div class="profile-img-holder">
									<img src="{{asset($trainer->avatar)}}" alt="img">
								</div>
							</div>
							<div class="col-8 p-0">
								<div class="trainer-content-holder">
									<strong>{{$trainer->first_name.' '.$trainer->last_name}}</strong>
									<small>{{$trainer->city.' '.$trainer->country}}</small>
									<p> Joined on {{date("m/d/Y", strtotime($trainer->created_at))}}</p>
									<div class="btn-group dot-btn">
										<a href="javascript:void(0);" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
											<i class="fa fa-ellipsis-v"></i>
										</a>
										<div class="dropdown-menu dropdown-menu-right" x-placement="bottom-end">
											<button class="dropdown-item" type="button"
											onclick="window.location.href='{{route('trainer.show',$trainer->id)}}';">
											View
										</button>
										<button class="dropdown-item" type="button"
										onclick="window.location.href='{{route('trainer.edit',$trainer->id)}}'">
										Edit
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
		@php
		$trainer_counter++;
		@endphp
		@endforeach
		@endif
	</div>
</div>
</div>
</div>
</div>
