<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>easemylr</title>
    <script src="https://code.jquery.com/jquery-3.4.1.js"></script>
    <style type="text/css">
    #map {
        height: 700px;
    }
    </style>
</head>

<body>
    <input type="hidden" id="lat" value="{{$get_last_cordinate->latitude}}">
    <input type="hidden" id="lang" value="{{$get_last_cordinate->longitude}}">
    <input type="hidden" id="consigne_pin" value="{{$get_consigne_pin}}">
    <div class="container mt-5">

        <div id="map"></div>
    </div>

    <script type="text/javascript">
    // function initMap() {

    //   const myLatLng = { lat: 22.2734719, lng: 70.7512559 };
    //   const map = new google.maps.Map(document.getElementById("map"), {
    //     zoom: 5,
    //     center: myLatLng,
    //   });

    //   new google.maps.Marker({
    //     position: myLatLng,
    //     map,
    //     title: "Hello Rajkot!",
    //   });
    // }
    function initMap(response) {
        var lat = $('#lat').val();
        var lang = $('#lang').val();
        var consignee_pin = $('#consigne_pin').val();
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 7,
            center: 'Changigarh',
        });
        var directionsDisplay = new google.maps.DirectionsRenderer({
            'draggable': false
        });
        var directionsService = new google.maps.DirectionsService();
        var travel_mode = 'DRIVING';
        var origin = new google.maps.LatLng(lat, lang);
        var destination = consignee_pin;
        directionsService.route({
            "origin": origin,
            "destination": destination,
            "travelMode": travel_mode,
            "avoidTolls": true,
        }, function(response, status) {

            if (status === 'OK') {
                directionsDisplay.setMap(map);
                directionsDisplay.setDirections(response);
                console.log(response);
            } else {
                directionsDisplay.setMap(null);
                directionsDisplay.setDirections(null);
                // alert('Unknown route found with error code 0, contact your manager');
            }
        });
    }

    window.onload = initMap;
    </script>

    <script defer
        src="https://maps.googleapis.com/maps/api/js?libraries=places&language=en&key=AIzaSyCEzojx1_dyy0ACDIF5zP5dt7hk4RggtOg"
        type="text/javascript"></script>
</body>

</html>