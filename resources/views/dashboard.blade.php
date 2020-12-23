@extends('layouts.app')

@section('style')
<style type="text/css">
	.gym-box {
		display: block;
		background: linear-gradient(to left, #280659 0%, #40257e 100%);
		border-radius: 10px;
		padding: 15px;
		color: #fff;
		margin: 0 0 30px;
		cursor: pointer;
	}
	.gym-box strong {
		margin: 0 0 30px;
		display: block;
		width: 100%;
		overflow: hidden;
		font-family: ProximaNovaA-Light;
		font-size: 23px;
	}
	.gym-box h3 {
		font-size: 35px;
		display: block;
		margin: 0 0 5px;
		font-family: 'ProximaNova-Regular';
	}
	.gym-box small {
		margin: 0 0 0;
		display: block;
		width: 100%;
		overflow: hidden;
		font-family: ProximaNovaA-Light;
		font-size: 18px;
	}
	.gym-box .icon-holder img {
		display: block;
		max-width: 55px;
		height: auto;
	}
	.red-bg{
		background: linear-gradient(to left, #ac2d67 0%, #691057 100%);
	}
	.lightred-bg{
		background: linear-gradient(to left, #f14853 0%, #af2d68 100%);
	}
	.org-bg{
		background: linear-gradient(to left, #e48e55 0%, #be2e48 100%);
	}
	.mt_40{
		margin-top:40px;
	}
	.row.mt_40 {
		margin-right: -10px;
		margin-left: -10px;
	}
	.row.mt_40  .col-sm-3{
		padding-right: 10px;
		padding-left: 10px;
	}
	.table.dataTable td .d-block a .fa.fa-pencil {
		color: #3c15a3;
		margin:0 5px;
	}
	.table.dataTable td .d-block a .fa.fa-trash {
		color: #e83d4e;
		margin:0 5px;
	}
	.maps-sec strong {
		color: #9c9c9c;
		font-size: 20px;
		margin: 0 0 10px;
		display: block;
	}
	.trainer-dashboard .profile-img-holder img {
		width: 80px;
		height: 80px;
		display: block;
		margin: 0;
		object-fit: cover;
		border-radius: 50%;
	}
	.card-box.trainer-box.trainer-dashboard {
		margin: 0 0 10px;
	}
	.trainer-dashboard strong {
		color: #3c15a3;
		font-size: 20px;
		margin: 0 0 5px;
		display: block;
	}
	.trainer-dashboard .trainer-content-holder small {
		margin: 0 0 5px;
	}
	.trainer-dashboard .trainer-content-holder p {
		color: #dc172f;
	}
	.fa.fa-eye {
		color: #4a4a4d;
	}
	.dataTables_info {
		display: none;
	}
	.map-container{
		width: 350px;
		height: 334px;
	}
	.all-gym-owners{
		color: #3c15a3;
		padding-left: 55px;

	}
	.gym-owners-heading h3 {
		color: #3c15a3;
		padding-left: 20px;
	}
</style>
@endsection
@section('head_script')
<script async defer
src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIyqy08mOTMQa76nMv5AlQCHI_NxBaFEk">
</script>

@endsection
@section('content')
@if(isSuperAdmin())
@include('admin-dashboard',compact('users','trainers','gyms','activities','gymOwners'))
@else
@include('gym-owner-dashboard',compact('gyms','upcoming','members','bookings','total_price','reviews'))
@endif
@endsection
@section('script')

@php
$gym_locations = [];
$counter = 1;
$default_lat_lng = [51.5074, -0.118092];
if(!empty($gyms->first())){

	
	

	foreach($gyms as $gym){
		if(!isSuperAdmin() && $counter == 1){
			$default_lat_lng= [$gym->lat, $gym->lng];
		}
		
		$gym_locations[] = [str_replace( array( '\'', '"', 
			',' , ';', '<', '>' ), ' ',$gym->name),$gym->lat,$gym->lng,$gym->id];
		$counter++;
	}
}
$gym_locations= json_encode($gym_locations);
if(isSuperAdmin()){
	$trainer_locations = [];
	$counter = 1;
	if($trainers->count()>0){
		foreach($trainers as $trainer){
			$trainer_locations[] = [str_replace( array( '\'', '"', 
				',' , ';', '<', '>' ), ' ',$trainer->first_name.' '.$trainer->last_name),$trainer->lat,$trainer->lng,$trainer->id];
			$counter++;
		}
	}

	$trainer_locations= json_encode($trainer_locations);

/*print_r($gym_locations) ;
exit;*/
}
$default_lat_lng= json_encode($default_lat_lng);
@endphp
<script type="text/javascript">
	$(document).ready(function(){
		var locations = {!! $gym_locations !!};	
		var dlocations = {!! $default_lat_lng !!};	

		var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 12,
			center: new google.maps.LatLng(dlocations[0],dlocations[1]),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});

		var infowindow = new google.maps.InfoWindow();

		var marker, i;

		for (i = 0; i < locations.length; i++) {  
			marker = new google.maps.Marker({
				position: new google.maps.LatLng(locations[i][1], locations[i][2]),
				map: map,
				icon: '{{url('/')}}/assets/images/gymmap.png',

			});

			google.maps.event.addListener(marker, 'click', (function(marker, i) {

				return function() {
					//location.href = "/gym_owner/show/"+locations[i][3];
					infowindow.setContent('<a class="googl-redirct-link" href="/gym/show/'+locations[i][3]+'">'+locations[i][0]+'</a>');
					infowindow.open(map, marker);
				}
			})(marker, i));
		}
	});
</script>
@if(isSuperAdmin())
<script type="text/javascript">
	$(document).ready(function(){

		var locations = {!! $trainer_locations !!};
		

		var map = new google.maps.Map(document.getElementById('map2'), {
			zoom: 12,
			center: new google.maps.LatLng(51.5074, -0.118092),
			mapTypeId: google.maps.MapTypeId.ROADMAP,

		});

		var infowindow = new google.maps.InfoWindow();

		var marker, i;

		for (i = 0; i < locations.length; i++) {  
			marker = new google.maps.Marker({
				position: new google.maps.LatLng(locations[i][1], locations[i][2]),
				map: map,
				icon: '{{url('/')}}/assets/images/trainermap.png'

			});			

			google.maps.event.addListener(marker, 'click', (function(marker, i) {
				return function() {
					//location.href = "/trainer/"+locations[i][3];
					infowindow.setContent('<a class="googl-redirct-link" href="/trainer/'+locations[i][3]+'">'+locations[i][0]+'</a>');
					//infowindow.setContent(locations[i][0]);
					infowindow.open(map, marker);
				}
			})(marker, i));
		}
	});
</script>
@endif

@endsection
