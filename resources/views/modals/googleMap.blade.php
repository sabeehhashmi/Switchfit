@section('style')
    <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDIyqy08mOTMQa76nMv5AlQCHI_NxBaFEk&callback=initAutocomplete&libraries=places&v=weekly"
        defer
    ></script>

    <style>
        /* Always set the map height explicitly to define the size of the div
         * element that contains the map. */
        #google_map #map {
            height: 100%;
        }

        #google_map #description {
            font-family: 'ProximaNova-Regular';
            font-size: 15px;
            font-weight: 300;
        }

        #google_map #infowindow-content .title {
            font-weight: bold;
        }

        #google_map #infowindow-content {
            display: none;
        }

        #google_map #map #infowindow-content {
            display: inline;
        }

        .pac-controls label {
            font-family: 'ProximaNova-Regular';
            font-size: 13px;
            font-weight: 300;
        }

        #pac-input {
            background-color: #fff;
            font-family: 'ProximaNova-Regular';
            font-size: 15px;
            font-weight: 300;
            margin-left: 12px;
            padding: 0 11px 0 13px;
            text-overflow: ellipsis;
            width: 400px;
        }

        #pac-input:focus {
            border-color: #4d90fe;
        }

    </style>

@endsection

<div id="google_map" class="modal fade " tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
     style="display: none;" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                <h4 class="modal-title" id="myLargeModalLabel">{{$title}}</h4>
            </div>
            <div class="modal-body">

                <input type="hidden" id="g_address" value="">
                <input type="hidden" id="g_lat" value="">
                <input type="hidden" id="g_lng" value="">
                <input type="hidden" id="g_postal_code" value="">
                <input type="hidden" id="g_country" value="">
                <input type="hidden" id="g_state" value="">
                <input type="hidden" id="g_city" value="">

                <div id="map" style="width: 100%;height: 300px;"></div>
                <input
                    id="pac-input"
                    class="controls form-control "
                    style="margin-top: 10px;padding: 10px;"
                    type="text"
                    placeholder="Search"
                />
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-md primay-btn pull-right inline-block" data-dismiss="modal"
                        onclick="setGoogleData()">Done
                </button>
                {{--                <button type="button" class="btn btn-primary">Save changes</button>--}}
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div>

@section('script')
<style type="text/css">
    .pac-container{
        z-index: 9999;
    }
</style>
    <script >

        // Google code
        (function (exports) {
            "use strict";

            // This example adds a search box to a map, using the Google Place Autocomplete
            // feature. People can enter geographical searches. The search box will return a
            // pick list containing a mix of places and predicted search terms.
            // This example requires the Places library. Include the libraries=places
            // parameter when you first load the API. For example:
            // <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places">
            function initAutocomplete() {
                var prev_address = $('{{$addressId}}').val() ? $('{{$addressId}}').val() : 'Lahore';
                var prev_lat = parseFloat($('{{$latId}}').val()) > 0 ? parseFloat($('{{$latId}}').val()) : 31.5204;
                var prev_lng = parseFloat($('{{$lngId}}').val()) > 0 ? parseFloat($('{{$lngId}}').val()) : 74.3587;

                var map = new google.maps.Map(document.getElementById("map"), {
                    center: {
                        lat: prev_lat,
                        lng: prev_lng,
                    },
                    zoom: 13,
                    mapTypeId: "roadmap",
                    mapTypeControl: false,
                    draggable: true
                });

                var marker = new google.maps.Marker({
                    map: map,
                    title: prev_address,
                    position: {
                        lat: prev_lat,
                        lng: prev_lng
                    },
                    animation: google.maps.Animation.DROP,
                    draggable: true
                });
                $('#pac-input').val(prev_address);
                {{--$('{{$addressId}}').val(prev_address);--}}
                {{--$('{{$latId}}').val(prev_lat);--}}
                {{--$('{{$lngId}}').val(prev_lng);--}}
                new google.maps.event.addListener(marker, 'dragend', function (event) {
                    // console.log(event);
                    var geocoder = new google.maps.Geocoder;
                    geocoder.geocode({'latLng': event.latLng}, function (results, status) {
                        var address = results[0].formatted_address;
                        var lat = event.latLng.lat();
                        var lng = event.latLng.lng();
                        $('#pac-input').val(address);
                        // console.log(address, lat, lng);

                        {{--$('{{$addressId}}').val(address);--}}
                        {{--$('{{$latId}}').val(lat);--}}
                        {{--$('{{$lngId}}').val(lng);--}}
                        $('#g_address').val(address);
                        $('#g_lat').val(lat);
                        $('#g_lng').val(lng);

                        getCountryDataAndSet(results[0].address_components);
                    })
                });
                // Previous End


                var input = document.getElementById("pac-input");
                var searchBox = new google.maps.places.SearchBox(input);
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input); // Bias the SearchBox results towards current map's viewport.

                map.addListener("bounds_changed", function () {
                    searchBox.setBounds(map.getBounds());
                });
                var markers = []; // Listen for the event fired when the user selects a prediction and retrieve
                // more details for that place.

                searchBox.addListener("places_changed", function () {
                    var places = searchBox.getPlaces();

                    if (places.length == 0) {
                        return;
                    } // Clear out the old markers.

                    markers.forEach(function (marker) {
                        marker.setMap(null);
                    });
                    marker.setMap(null);
                    markers = []; // For each place, get the icon, name and location.

                    var bounds = new google.maps.LatLngBounds();
                    places.forEach(function (place) {
                        if (!place.geometry) {
                            console.log("Returned place contains no geometry");
                            return;
                        }

// console.log( place);
                        getCountryDataAndSet(place.address_components);
                        // console.log(place ,place.geometry.location.lat(),place.geometry.location.lng());
                        $('#pac-input').val(place.formatted_address);
                        {{--$('{{$addressId}}').val(place.formatted_address);--}}
                        {{--$('{{$latId}}').val(place.geometry.location.lat());--}}
                        {{--$('{{$lngId}}').val(place.geometry.location.lng());--}}
                        $('#g_address').val(place.formatted_address);
                        $('#g_lat').val(place.geometry.location.lat());
                        $('#g_lng').val(place.geometry.location.lng());

                        // console.log(place.name, place.geometry.location);
                        marker = new google.maps.Marker({
                            map: map,
                            title: place.name,
                            position: place.geometry.location,
                            draggable: true
                        });

                        new google.maps.event.addListener(marker, 'dragend', function (event) {
                            // console.log(event);
                            var geocoder = new google.maps.Geocoder;
                            geocoder.geocode({'latLng': event.latLng}, function (results, status) {
                                var address = results[0].formatted_address;
                                // console.log(results );
                                var lat = event.latLng.lat();
                                var lng = event.latLng.lng();
                                $('#pac-input').val(address);
                                // console.log(address, lat, lng);
                                {{--$('{{$addressId}}').val(address);--}}
                                {{--$('{{$latId}}').val(lat);--}}
                                {{--$('{{$lngId}}').val(lng);--}}

                                $('#g_address').val(address);
                                $('#g_lat').val(lat);
                                $('#g_lng').val(lng);

                                getCountryDataAndSet(results[0].address_components)

                            })
                        });
                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                    });
                    map.fitBounds(bounds);
                });
            }

            exports.initAutocomplete = initAutocomplete;
        })((this.window = this.window || {}));


        /**
         * @Description Get Country Data and set into inputs
         * @param address_components
         * @Author Khuram Qadeer.
         */
        function getCountryDataAndSet(address_components) {
            var postalCode = "";
            var streetLevelAddress = "";
            var country = "";
            var state = "";
            var city = "";
            if (address_components && address_components.length > 0) {
                for (let i = 0; i < address_components.length; i++) {
                    if (address_components[i].types && address_components[i].types.length > 0) {
                        for (let z = 0; z < address_components[i].types.length; z++) {
                            if (address_components[i].types[z] == "postal_code") {
                                postalCode = address_components[i].long_name
                            }
                            if (address_components[i].types[z] == "street_number") {
                                streetLevelAddress = true
                            }
                            if (address_components[i].types[z] == "point_of_interest") {
                                streetLevelAddress = true
                            }
                            if (address_components[i].types[z] == "neighborhood") {
                                streetLevelAddress = true
                            }
                            if (address_components[i].types[z] == "route") {
                                streetLevelAddress = true
                            }
                            if (address_components[i].types[z] == 'administrative_area_level_1') {
                                state = address_components[i].long_name;
                            }
                            if (address_components[i].types[z] == 'locality') {
                                city = address_components[i].long_name;
                            }
                            if (address_components[i].types[z] == 'country') {
                                country = address_components[i].long_name;
                            }
                        }
                    }
                }
            }

            $('#g_postal_code').val(postalCode);
            $('#g_country').val(country);
            $('#g_state').val(state);
            $('#g_city').val(city);
        }

        /**
         * @Description set Google Data into our inputs
         * @Author Khuram Qadeer.
         */
        function setGoogleData() {
            var g_address = $('#g_address').val();
            var g_lat = $('#g_lat').val();
            var g_lng = $('#g_lng').val();
            var g_postal_code = $('#g_postal_code').val();
            var g_country = $('#g_country').val();
            var g_state = $('#g_state').val();
            var g_city = $('#g_city').val();


            $('{{$addressId}}').val(g_address);
            $('{{$latId}}').val(g_lat);
            $('{{$lngId}}').val(g_lng);

            @if(isset($postalCodeId))
            $('{{$postalCodeId}}').val(g_postal_code);
            @endif
            @if(isset($countryId))
            $('{{$countryId}}').val(g_country);
            @endif
            @if(isset($stateId))
            $('{{$stateId}}').val(g_state);
            @endif
            @if(isset($cityId))
            $('{{$cityId}}').val(g_city);
            @endif
        }
    </script>
@endsection
