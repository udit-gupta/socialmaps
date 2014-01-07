<!DOCTYPE html>
<html>
<head>
<title>Social Maps</title>
<meta name="viewport" content="initial-scale=1.0, user-scalable=no" />
<style type="text/css">
  html { height: 100% }
  body { height: 100%; margin: 0px; padding: 0px }
  #map_canvas { height: 100% }
  h1 {
  font-size: 18px;
  color: White;
  font-weight: bold;
  }
</style>
<script src="http://maps.google.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script type="text/javascript">
<?php
	//error_reporting(0);
	require('lib/mysql/connect_db.php');
	require_once('lib/mysql/mysql_class.php');
	$loc = new mysql("location");
	$result = $loc->select("uid,time,latitude,longitude,accuracy","where uid=1");
	$flag=false;
	if($row = mysql_fetch_array($result))
	{
		echo "  var lat=".$row['latitude'].",lng=".$row['longitude'].", acc=".$row['accuracy'].";";
		$flag=true;
	}
	else
		echo "var lat=22,lng=77;";
?>		
  function initialize()
  {
    var latlng = new google.maps.LatLng(lat, lng);
    var myOptions = {
      zoom: 14,
      center: latlng,
      mapTypeId: google.maps.MapTypeId.HYBRID
    };
    var map = new google.maps.Map(document.getElementById("map_canvas"),myOptions);

    function toggleBounce()
    {
      if (this.getAnimation() != null)
      {
        this.setAnimation(null);
      }
      else
      {
        this.setAnimation(google.maps.Animation.BOUNCE);
      }
    }
    

<?php
	if($flag)
	{
		$result = $loc->select("DISTINCT latitude,longitude,accuracy","where uid=1");
		$color='#FFFFFF';
		$opacity=0.25;
		$i=0;
		while($row = mysql_fetch_array($result))
		{
			$lat_pos = array();	$lng_pos = array();	$acc_pos = array();
			$lat_pos[$i]=$row['latitude']; $lng_pos[$i]=$row['longitude'];  $acc_pos[$i]=$row['accuracy'];
			echo "    // Marker".$i."\n";
			echo "    position".$i." = new google.maps.LatLng(".$lat_pos[$i].", ".$lng_pos[$i].");\n";
			echo "    marker".$i." = new google.maps.Marker({\n";
			echo "    map:map,\n";
			echo "    draggable:false,\n";
			echo "    animation: google.maps.Animation.DROP,\n";
			echo "    position: position".$i."\n";
			echo "    });\n";
			echo "    google.maps.event.addListener(marker".$i.", 'click', toggleBounce);\n";
			echo "		 var contentString".$i."='Lat=".$lat_pos[$i]." & Long=".$lng_pos[$i]."';\n";
			echo "    var infowindow".$i." = new google.maps.InfoWindow({    content: contentString".$i."  });\n";
			echo"     google.maps.event.addListener(marker".$i.", 'click', function() {  infowindow".$i.".open(map,marker".$i."); });\n";
			echo "    // Circle".$i."\n";
			echo "    function circle".$i."()\n";
			echo "    {\n";
			echo "      var p".$i." = {};\n";
			echo "      p".$i."['".$i."'] = {\n";
			echo "      center: new google.maps.LatLng(".$lat_pos[$i].", ".$lng_pos[$i]."),\n";
			echo "      radius: ".$acc_pos[$i]."\n";
			echo "      };\n";
			echo "      for (var j in p".$i.") {\n";
			echo "        var posOptions".$i." = {\n";
			echo "        strokeColor: '".$color."',\n";
			echo "        strokeOpacity: 0.5,\n";
			echo "        strokeWeight: 2,\n";
			echo "        fillColor: '".$color."',\n";
			echo "        fillOpacity: ".$opacity.",\n";
			echo "        map: map,\n";
			echo "        center: p".$i."[j].center,\n";
			echo "        radius: p".$i."[j].radius\n";
			echo "        };\n";
			echo "        posCircle".$i." = new google.maps.Circle(posOptions".$i.");\n";
			echo "      }\n";
			echo "    }\n";
			echo "    google.maps.event.addListener(marker".$i.", 'click', circle".$i.");\n";
			$i++;
		}
	}
	$i=$i-1;
	echo "    // Bounce Last Position\n";
        echo "    marker".$i.".setAnimation(null);\n";
        echo "    marker".$i.".setAnimation(google.maps.Animation.BOUNCE);\n";
        echo "    circle".$i."();\n";
?>
  }
</script>
</head>
<body onload="initialize()">
<div id="map_canvas" style="width:100%; height:100%"></div>
</body>
</html>
