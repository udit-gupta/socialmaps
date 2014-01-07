<?php
    // PHP for getting Facebook content.
    $app_id = "<app_id>";
    $app_secret = "<client_token>";
    $login_page = "http://<example.domain.net>/socialmaps/";
    $url = "http://<example.domain.net>/socialmaps/map.php";

    $code = $_REQUEST["code"];
    if(empty($code)) { echo("<script> top.location.href='" . $login_page . "'</script>");    exit ('Redirecting to the login page...');}

    $token_url = "https://graph.facebook.com/oauth/access_token?client_id="
        . $app_id . "&redirect_uri=" . urlencode($url) . "&client_secret="
        . $app_secret . "&code=" . $code;
    $access_token = file_get_contents($token_url);
    $graph_url = "https://graph.facebook.com/me?" . $access_token;
    $user = json_decode(file_get_contents($graph_url));
    $access_token = explode("=", $access_token);
    $access_token = $access_token[1];
    // Create the user if it doesn't exists and updates it otherwise.
    require('lib/mysql/connect_db.php');
    require_once('lib/mysql/mysql_class.php');
    $ob = new mysql("users");
    $result = $ob->select("*","WHERE fb_id='$user->id'");
    $row = mysql_fetch_array($result);
    if($row)
        $ob->update("access_token,access_token_permanent","'$access_token',1","fb_id='$user->id'");
    else
        $ob->insert("fb_id,access_token,access_token_permanent","'$user->id','$access_token',1");
    // End of user creation/update.
    $wrap = base64_encode($user->id.":".md5($access_token));
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="icon" type="image/png" href="images/favicon.ico" />
        <title>Social Maps</title>
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
            // Global Variables
            var lat = [], lng = [], acc = [];
            var time = '1800-01-01 00:00:00', wrap = '<? echo $wrap ?>';
            var map,mgr,markerCluster,marker_count=0;
            var marker = [],marker_array = [],circle = [], ajax_data=[];
            var markerBounds = new google.maps.LatLngBounds();
            var color = '#3366FF';
            var stroke_op = 0.4;
            var fill_op = 0.1;

            // Rendering the map
            function setupMap()
            {
                var position = new google.maps.LatLng(22,77);
                var options = {zoom: 5, center: position, mapTypeId: google.maps.MapTypeId.ROADMAP};
                map = new google.maps.Map(document.getElementById('map_canvas'),options);
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
                mgr = new MarkerManager(map);
            }
            
            // Plotting the Markers
            function setupMarkers()
            {
                for(var j=marker_count;j<lat.length;j++)
                {
                    var pos = new google.maps.LatLng(lat[j],lng[j]);
                    var marker_options = {position: pos, map:map, draggable:false, animation: google.maps.Animation.DROP};
                    marker[j] = new google.maps.Marker(marker_options);
                    circle_option = {strokeColor:color,strokeOpacity:stroke_op,strokeWeight:2,fillColor:color,fillOpacity:fill_op,map:null,center:pos,radius:parseInt(acc[j])};
                    circle[j] = new google.maps.Circle(circle_option);
                    function make_circle(circle, map) {return function() { if(circle.getMap()!=null) {circle.setMap(null);} else {circle.setMap(map);} }; }
                    google.maps.event.addListener(marker[j], 'click', make_circle(circle[j], map));
                    function toggleBounce() { if (this.getAnimation() != null) {this.setAnimation(null);} else {this.setAnimation(google.maps.Animation.BOUNCE);} }
                    google.maps.event.addListener(marker[j], 'click', toggleBounce);
                    marker_array.push(marker[j]);
                    markerBounds.extend(pos);
                }
                if(marker_count > 2)
                    map.fitBounds(markerBounds);
                markerCluster = new MarkerClusterer(map,marker_array);
                google.maps.event.addListener(mgr, 'loaded', function() { mgr.addMarkers(marker_array); mgr.refresh(); });
                marker_count=j;
            }

            // Plot the new Markers Recieved
            function processAjaxData()
            {
                var temp=[];
                if(ajax_data.length>1)
                {
                    for(var i=0;i<ajax_data.length-1;i++)
                    {
                        temp = ajax_data[i].split(':');
                        lat.push(temp[0]);
                        lng.push(temp[1]);
                        acc.push(temp[2]);
                    }
                    setupMarkers();
                    time = ajax_data[i];
                }
                ajaxFunction();
            }

            // Ajax Call for new markers
            function ajaxFunction()
            {
                $.post("ajax_markers.php",{time:time, wrap:wrap}, function(result) { ajax_data=result.split(','); });
                setTimeout("processAjaxData();",2000);
            }

            // Control Function
            function main()
            {
                setupMap();
                ajaxFunction();
            }
        </script>
    </head>
    <body onload="$(document).ready(main);">
        <div id="user" style="width: 100%; height: 5%">
            <? echo($user->name); ?>
        </div>
        <div id="map_canvas" style="width: 100%; height: 95%"></div>
    </body>
</html>
