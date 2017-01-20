<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>P.O.C</title>
    <link rel="stylesheet" href="{{ asset('css/map.css') }}">
</head>
<body>
    <div id="floating-panel">
        <div id="temp"></div>
        <div class="form-group">
            <label class="radio-inline"><input type="radio" name="mode" checked="checked" value="DRIVING">Driving</label>
            <label class="radio-inline"><input type="radio" name="mode"  value="WALKING">Walking</label>
            <!--<label class="radio-inline"><input type="radio" name="option">Shortest Time</label>-->
        </div>
        <div id="TextBoxesGroup">
            <div class="form-group has-feedback">
                <input  id="address_0" type="email" class="form-control" placeholder="Source address">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input id="address_1" type="email" class="form-control" placeholder="Des address">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
        </div>
            
        <button id="addButton" type=button class="btn btn-primary">Add destination</button>
        <button id="removeButton" type=button  class="btn btn-info">Remove destination</button>
        <!--<button onclick="DrawAllMarkers(map);" type=button value="View All" class="btn btn-success">View All</button>   -->
        <button onclick="DrawPolyline(map);" type=button value="Draw street" class="btn btn-danger">Find direction</button>
        <button onclick="location.reload();" type=button value="DISTANCE" class="btn btn-warning">Refresh</button>
        <div class="form-group">
            <p id="result"></p>
        </div>
    </div>
    <div id="map"></div>
<script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>
<script src="{{ asset('js/map.js') }}"></script>

<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDUhgBbMvryjcfydyz78cETeIazfLgjFsY&callback=initMap">
</script>
<link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
</body>
</html>