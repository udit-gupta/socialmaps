<?php
// PHP to get latitudes and longitudes in a array.
//error_reporting(0);
require('lib/mysql/connect_db.php');
require_once('lib/mysql/mysql_class.php');
$loc = new mysql("location");
$result = $loc->select("DISTINCT latitude,longitude,accuracy", "where uid=1");
$i = 0;
$lat_arr = array();
$lng_arr = array();
$acc_arr = array();
while ($row = mysql_fetch_array($result))
{
    $lat_arr[$i] = $row['latitude'];
    $lng_arr[$i] = $row['longitude'];
    $acc_arr[$i] = $row['accuracy'];
    $i++;
}
$result = $loc->select("max(time)", "where uid=1");
$row = mysql_fetch_array($result);
if ($row)
{
    $last_time = $row['max(time)'];
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Human Mapping</title>
        <style type="text/css">
            html {height: 100%}
            body {height: 100%; margin: 0px; padding: 0px}
            #map_canvas {height: 100%}
        </style>
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
        <script type="text/javascript" src="lib/js/MarkerManager.js"></script>
        <script type="text/javascript" src="lib/js/MarkerClusterer.js"></script>
        <script type="text/javascript" src="lib/js/KeyDragZoom.js"></script>
        <script type="text/javascript" src="lib/js/JQuery.js"></script>
        <script type="text/javascript" >
            // Markers - Latitudes and Longitudes.
            var lat = new Array(<? for ($j = 0; $j < $i - 1; $j++) { echo $lat_arr[$j] . ", ";} echo $lat_arr[$j]; ?>);
            var lng = new Array(<? for ($j = 0; $j < $i - 1; $j++) { echo $lng_arr[$j] . ", ";} echo $lng_arr[$j]; ?>);
            var acc = new Array(<? for ($j = 0; $j < $i - 1; $j++) { echo $acc_arr[$j] . ", ";} echo $acc_arr[$j]; ?>);
            var time = <? echo "'".$last_time."'"; ?>;
            function setupMap(lat,lng,acc)
            {
                var position = new google.maps.LatLng(22,77);
                var options = {zoom: 5, center: position, mapTypeId: google.maps.MapTypeId.ROADMAP};
                var map = new google.maps.Map(document.getElementById('map_canvas'),options);
                map.enableKeyDragZoom({
                                        visualEnabled: true,
                                        visualPosition: google.maps.ControlPosition.LEFT,
                                        visualPositionOffset: new google.maps.Size(35, 0),
                                        visualPositionIndex: null,
                                        visualSprite: "images/dragzoom_btn.png",
                                        visualSize: new google.maps.Size(20, 20),
                                        visualTips: {
                                                        off: "Turn on",
                                                        on: "Turn off"
                                                    }
                                     });
                var mgr = new MarkerManager(map);
                var marker = [];
                var circle = [];
                var marker_array = [];
                var markerBounds = new google.maps.LatLngBounds();
                var color = '#3366FF';
                var stroke_op = 0.4;
                var fill_op = 0.1;
                for(var j=0;j<lat.length;j++)
                {
                    var pos = new google.maps.LatLng(lat[j],lng[j]);
                    var marker_options = {position: pos, map:map, draggable:false, animation: google.maps.Animation.DROP};
                    marker[j] = new google.maps.Marker(marker_options);
                    circle_option = {strokeColor:color,strokeOpacity:stroke_op,strokeWeight:2,fillColor:color,fillOpacity:fill_op,map:null,center:pos,radius:acc[j]};
                    circle[j] = new google.maps.Circle(circle_option);
                    function make_circle(circle, map) {return function() { if(circle.getMap()!=null) {circle.setMap(null);} else {circle.setMap(map);} }; }
                    google.maps.event.addListener(marker[j], 'click', make_circle(circle[j], map));
                    google.maps.event.addListener(marker[j], 'click', toggleBounce);
                    marker_array.push(marker[j]);
                    markerBounds.extend(pos);
                }
                map.fitBounds(markerBounds);
                google.maps.event.addListener(mgr, 'loaded', function() { mgr.addMarkers(marker_array,8); mgr.refresh(); });
                var markerCluster = new MarkerClusterer(map, marker_array);
                marker[j-1].setAnimation(google.maps.Animation.BOUNCE); // Bounce Last Marker
            }
            function toggleBounce()
            {
                if (this.getAnimation() != null) {this.setAnimation(null);}
                else {this.setAnimation(google.maps.Animation.BOUNCE);}
            }
            function ajaxFunction()
            {
                $.post("ajax_markers.php",{time:time},function(result) { $("div#test").html(result); });

            }
            function main()
            {
                setupMap(lat,lng,acc);
                setTimeout("ajaxFunction()", 3000);
            }
        </script>
    </head>
    <body onload="$(document).ready(main);">
        <div id="map_canvas" style="width: 100%; height: 100%"></div>
        <div id="test"></div>
    </body>
</html>