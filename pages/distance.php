<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Distance and Travel Time Calculator</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var originInput = document.getElementById('origin');
            var destinationInput = document.getElementById('destination');
            var distanceOutput = document.getElementById('distance');
            var travelTimeOutput = document.getElementById('travel-time');

            $('#btnGo').on('click', function() {
                var origin = originInput.value;
                var destination = destinationInput.value;

                if (origin && destination) {
                    var geocodeUrl = 'https://nominatim.openstreetmap.org/search?format=json&q=';
                    var originUrl = geocodeUrl + encodeURIComponent(origin) + ', Bangladesh&limit=1';
                    var destinationUrl = geocodeUrl + encodeURIComponent(destination) + ', Bangladesh&limit=1';

                    $.when(
                        $.getJSON(originUrl),
                        $.getJSON(destinationUrl)
                    ).done(function(originData, destinationData) {
                        var originLatLng = originData[0].length > 0 ? originData[0][0] : null;
                        var destinationLatLng = destinationData[0].length > 0 ? destinationData[0][0] : null;

                        if (originLatLng && destinationLatLng) {
                            var originLat = parseFloat(originLatLng.lat);
                            var originLng = parseFloat(originLatLng.lon);
                            var destinationLat = parseFloat(destinationLatLng.lat);
                            var destinationLng = parseFloat(destinationLatLng.lon);

                            var distance = calculateDistance(originLat, originLng, destinationLat, destinationLng);
                            distanceOutput.innerHTML = distance.toFixed(2) + ' km';

                            calculateTravelTime(originLat, originLng, destinationLat, destinationLng);
                        } else {
                            distanceOutput.innerHTML = 'Error: Unable to geocode the locations.';
                        }
                    }).fail(function() {
                        distanceOutput.innerHTML = 'Error: Failed to fetch geocoding data.';
                    });
                }
            });

            function calculateDistance(lat1, lon1, lat2, lon2) {
                var R = 6371; // Radius of the earth in km
                var dLat = deg2rad(lat2 - lat1);
                var dLon = deg2rad(lon2 - lon1);
                var a =
                    Math.sin(dLat / 2) * Math.sin(dLat / 2) +
                    Math.cos(deg2rad(lat1)) * Math.cos(deg2rad(lat2)) *
                    Math.sin(dLon / 2) * Math.sin(dLon / 2);
                var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                var distance = R * c; // Distance in km
                return distance;
            }

            function calculateTravelTime(lat1, lon1, lat2, lon2) {
                var directionsUrl = 'https://router.project-osrm.org/route/v1/bus/';
                var waypoints = lon1 + ',' + lat1 + ';' + lon2 + ',' + lat2;

                $.getJSON(directionsUrl + waypoints, function(data) {
                    if (data && data.routes && data.routes.length > 0) {
                        var travelTimeInSeconds = data.routes[0].duration;
                        var travelTimeInHours = travelTimeInSeconds / 3600;
                        travelTimeOutput.innerHTML = travelTimeInHours.toFixed(2) + ' hours';
                    } else {
                        travelTimeOutput.innerHTML = 'Error: Unable to calculate travel time.';
                    }
                }).fail(function() {
                    travelTimeOutput.innerHTML = 'Error: Failed to fetch travel time data.';
                });
            }

            function deg2rad(deg) {
                return deg * (Math.PI / 180);
            }
        });
    </script>
</head>
<body>
    <h1>Distance and Travel Time Calculator</h1>
    <input type="text" id="origin" placeholder="Origin">
    <input type="text" id="destination" placeholder="Destination">
    <button id="btnGo">Calculate Distance and Travel Time</button>
    <div id="distance"></div>
    <div id="travel-time"></div>
</body>
</html>
