<?php
    // PHP for getting Facebook content.
    $app_id = "<app_id>";
    $app_secret = "<client_token>";
    $login_page = "http://<example.domain.net>/socialmaps/";
    $url = "http://<example.domain.net>/socialmaps/directions.php";

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
        <title>Social Maps Realtime Travel</title>
        <style type="text/css">
            html {height: 100%}
            body {height: 100%; margin: 0px; padding: 0px}
            #map_canvas {height: 100%}
        </style>
        <meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
        <meta http-equiv="content-type" content="text/html; charset=UTF-8"/>
        <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
        <script type="text/javascript" src="lib/js/KeyDragZoom.js"></script>
        <script type="text/javascript" src="lib/js/JQuery.js"></script>
        <script type="text/javascript">
            // Global Declarations
            var time = '1800-01-01 00:00:00', wrap = '<? echo $wrap ?>';
            var directionDisplay,map,request;
            var start="",end="",waypts=[];
            var directionsService = new google.maps.DirectionsService();

            function setupMap()
            {
                directionsDisplay = new google.maps.DirectionsRenderer();
                var position = new google.maps.LatLng(22,77);
                var options = {zoom: 5, center: position, mapTypeId: google.maps.MapTypeId.ROADMAP};
                map = new google.maps.Map(document.getElementById('map_canvas'),options);
                map.enableKeyDragZoom({
                                        visualEnabled: true,
                                        vvisualPosition: google.maps.ControlPosition.LEFT,
                                        visualPositionOffset: new google.maps.Size(35, 0),
                                        visualPositionIndex: null,
                                        visualSprite: "images/dragzoom_btn.png",
                                        visualSize: new google.maps.Size(20, 20),
                                        visualTips: {
                                                        off: "Turn on",
                                                        on: "Turn off"
                                                    }
                                     });
                directionsDisplay.setMap(map);
            }

            function renderRoute()
            {
                request = { origin: start, destination: end, waypoints: waypts, travelMode: google.maps.DirectionsTravelMode.WALKING  };
                directionsService.route(request, function(response, status) {
                                                                                if (status == google.maps.DirectionsStatus.OK)
                                                                                {
                                                                                    directionsDisplay.setDirections(response);
                                                                                    var route = response.routes[0];
                                                                                }  // I think this is redundant
                                                                            });
            }

            // Plot the new Markers Recieved
            function processAjaxData()
            {
                var temp=[];
                if(ajax_data.length>1)
                {
                    var i=0;
                    if(start.length == 0) {
                        temp = ajax_data[i].split(':'); start=temp[0]+","+temp[1];
                        i++;
                    }
                    for(i=i;i<ajax_data.length-2;i++) {
                        temp = ajax_data[i].split(':');
                        waypts.push({location:temp[0]+","+temp[1],stopover:true});
                    }
                    temp = ajax_data[i].split(':');
                    end=temp[0]+","+temp[1]; time = ajax_data[i];
                }
                renderRoute();
            }

            // Ajax Call for new markers
            function ajaxFunction()
            {
                $.post("ajax_markers.php",{time:time, wrap:wrap}, function(result) {ajax_data=result.split(','); });
                setTimeout("processAjaxData();",2000);
            }


            // Control Funtion
            function main()
            {
                setupMap();
                ajaxFunction();
            }
        </script>
    </head>
    <body onload="main()">
        <div id="map_canvas" style="float:left;width:100%;height:100%;"></div>
    </body>
</html>
