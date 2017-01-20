var map;
var markers = [];

function setMapOnAll(map) {
        for (var i = 0; i < markers.length; i++) {
          markers[i].setMap(map);
        }
      }
      
function clearMarkers() {
        setMapOnAll(null);
      }

var rad = function (x) {
    return x * Math.PI / 180;
};

function initMap() {
    var directionsService = new google.maps.DirectionsService;
    var directionsDisplay = new google.maps.DirectionsRenderer;
    map = new google.maps.Map(document.getElementById('map'), {
        zoom: 13,
        center: { lat: 41.085396, lng: -73.530767 }
        
    });
}

var counter = 2;

$(document).ready(function(){

    counter = 2;

    $("#addButton").click(function () {
        if(counter>8){
            alert("Only 10 textboxes allow");
            return false;
        }
        var newTextBoxDiv = $(document.createElement('div'))
            .attr("id", 'TextBoxDiv' + counter).attr("class", 'form-group has-feedback');
        newTextBoxDiv.after().html('<input id="address_'+counter+'" type="email" class="form-control" placeholder="Des address">\
                    <span class="glyphicon glyphicon-envelope form-control-feedback"></span>');
        newTextBoxDiv.appendTo("#TextBoxesGroup");
        counter++;
     });

     $("#removeButton").click(function () {
        if(counter==2){
            alert("No more textbox to remove");
            return false;
        }
        counter--;

        $("#TextBoxDiv" + counter).remove();
     });

     $("#getButtonValue").click(function () {

        var msg = '';
        for(i=1; i<counter; i++){
        msg += "\n Textbox #" + i + " : " + $('#textbox' + i).val();
        }
            alert(msg);
        });
  });

function DrawPolyline(map) {
    markers = [];
    for(i=1; i<counter; i++){ 
        j=i-1
        source = $('#address_' + j).val();
        des = $('#address_' + i).val();  
        DrawPolylineTwoMarkers(map,source,des)
    }
}

