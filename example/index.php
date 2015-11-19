<?php
namespace Example;

use Upgle\Importer\ExcelImporter;
use Upgle\Algorithm\Dijkstra;
use Upgle\Model\SeoulMetro;

require_once(dirname(__FILE__). '/../vendor/autoload.php');
define('EXCEL_PATH', dirname(__FILE__). '/metro.xlsx');
date_default_timezone_set('Europe/London');

$seoulMetro = new SeoulMetro();
$importer = new ExcelImporter(EXCEL_PATH, $seoulMetro);
$importer->import();

$algorithm = new Dijkstra($seoulMetro);
$path = $algorithm->getShortestPath(
    $seoulMetro->getStationIdByName("망월사", "1"),
    $seoulMetro->getStationIdByName("시청", "1")
);

$stations = [];
foreach($seoulMetro->getVertices() as $station) {
    $stations[$station->getId()] = $station->getName();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Simple Polylines</title>
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #nav {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 48px;
            border-bottom: 1px solid #000;
            z-index: 100;
            background: #fff;
        }
        #map {
            margin-left:275px;
            height: 100%;
        }
        #sidebar {
            height:100%;
            position: absolute;
            left: 0;
            top: 0;
            width: 275px;
            background: #fff;
            border-right: 1px solid #aaa;
            z-index: 90;
            font-size: 13px;
            box-sizing: border-box;
        }
        #subway-route {
            margin: 0;
            padding: 60px 0 0;
        }
        #subway-route li {
            position: relative;
            list-style: none;
            padding: 0 0 0 20px;
            margin: 0;
            height: 36px;
            line-height: 36px;
        }
        #subway-route .mark {
            position: relative;
            display: inline-block;
            top: 5px;
            width: 10px;
            height: 10px;
            border-radius: 100%;
            margin-right: 8px;
            background: #fff;
            border: 4px solid #aaa;
            font-size: 12px;
            text-align: center;
            font-weight: bold;
        }
        #subway-route .line1 .mark {
            border-color: #003291;
            color: #003291;
        }

        #subway-route .lineU .mark {
            border-color: #fd8d00;
            color: #fd8d00;
        }

        #subway-route .line1 + .line1:before {
            position: absolute;
            display: block;
            top: -9px;
            left:27px;
            background : #003291;
            width: 4px;
            height: 19px;
            content : "";
        }
    </style>
</head>
<body>
<div id="nav">
</div>
<div id="sidebar">
    <ul id="subway-route">
        <?php foreach ($path as $station) : ?>
        <li class="line<?=$station->getLine()?>"><span class="mark"></span><?=$station->getName()?></li>
        <?php endforeach; ?>
    </ul>
</div>
<div id="map"></div>
<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: {lat: 37.493415, lng: 127.014080}
        });
        var flightPlanCoordinates = [
            {lat: 37.493415, lng: 127.014080},
            {lat: 37.485013, lng: 127.016189}
        ];
        var flightPath = new google.maps.Polyline({
            path: flightPlanCoordinates,
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 6
        });

        flightPath.setMap(map);
    }
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeu1Js2eiRwejCHm4gmhaE8I4Oxg-BFSg&signed_in=true&callback=initMap"></script>
</body>
</html>
