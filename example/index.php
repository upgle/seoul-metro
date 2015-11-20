<?php
namespace Example;

use Upgle\Importer\ExcelImporter;
use Upgle\Algorithm\Dijkstra;
use Upgle\Model\SeoulMetro;

/**
 * Composer Autoload
 */
require_once(dirname(__FILE__). '/../vendor/autoload.php');
define('EXCEL_PATH', dirname(__FILE__). '/metro.xlsx');
date_default_timezone_set('Europe/London');

/**
 * Initilize SeoulMetro Graph
 */
$seoulMetro = new SeoulMetro();

/**
 * Import Data From Excel
 */
$importer = new ExcelImporter(EXCEL_PATH, $seoulMetro);
$importer->import();

/**
 * Benchmark Dijkstra Algorithm
 */
$bench = new \Ubench();
$bench->start();
$algorithm = new Dijkstra($seoulMetro);
$path = [];
if(isset($_GET["start"]) && isset($_GET["goal"]) && is_numeric($_GET["start"]) && is_numeric($_GET["goal"])) {
    $path = $algorithm->getShortestPath(
        $_GET["start"],
        $_GET["goal"]
    );
}
$bench->end();

/**
 * get Stations list
 */
$stations = [];
foreach($seoulMetro->getVertices() as $station) {
    /* @var \Upgle\Model\Station $station */
    $stations[] = [
        "id" => $station->getId(),
        "name" => $station->getName(),
        "line" => $station->getLine()
    ];
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="initial-scale=1.0, user-scalable=no">
    <meta charset="utf-8">
    <title>Simple Polylines</title>
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="js/typeahead.bundle.min.js"></script>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/xeicon/1.0.4/xeicon.min.css">
    <link rel="stylesheet" href="css/default.css">
</head>
<body>
<div id="nav">
    <ul class="statistics">
        <li>알고리즘 연산 시간 : <strong><?=$bench->getTime()?></strong></li>
        <li>메모리 피크 : <strong><?=$bench->getMemoryPeak()?></strong></li>
    </ul>
</div>
<div id="sidebar">
    <form action="/" id="form-search">
        <input name="goal" class="goal" type="hidden" />
        <input name="start" class="start" type="hidden" />
        <input class="start_typeahead" type="text" placeholder="출발 역">
        <input class="goal_typeahead" type="text" placeholder="도착 역">
        <button type="submit" class="btn-search" value="빠른길 찾기"><i class="xi-magnifier"></i> 빠른길 찾기</button>
    </form>
    <ul id="subway-route">
        <?php foreach ($path as $station) : ?>
        <li class="line line<?=$station->getLine()?>"><span class="mark"></span><?=$station->getName()?></li>
        <?php endforeach; ?>
    </ul>
</div>
<div id="map"></div>
<script>
    function initMap() {

        var flightPlanCoordinates = [
            <?php foreach ($path as $key => $station) : ?>
            {lat: <?=$station->getLatitude()?>, lng:  <?=$station->getLongitude()?>},
            <?php endforeach; ?>
        ];

        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: {lat: 37.493415, lng: 127.014080}
        });

        <?php foreach ($path as $station) : ?>
            addMarker({lat: <?=$station->getLatitude()?>, lng: <?=$station->getLongitude()?>}, map);
        <?php endforeach; ?>

        var flightPath = new google.maps.Polyline({
            path: flightPlanCoordinates,
            geodesic: true,
            strokeColor: '#003291',
            strokeOpacity: 1.0,
            strokeWeight: 6
        });
        flightPath.setMap(map);
    }

    // Adds a marker to the map.
    function addMarker(location, map) {
        // Add the marker at the clicked location, and add the next-available label
        // from the array of alphabetical characters.
        var marker = new google.maps.Marker({
            position: location,
            map: map,
        });
    }

    var states = <?=json_encode($stations)?>;
    states = new Bloodhound({
        datumTokenizer: function(d) {
            return Bloodhound.tokenizers.whitespace(d.name);
        },
        queryTokenizer: Bloodhound.tokenizers.whitespace,
        identify: function(obj) { return obj.id; },
        // `states` is an array of state names defined in "The Basics"
        local: states
    });
    $('.start_typeahead').typeahead({ hint: true, highlight: true, minLength: 1 },
        { name: 'states', displayKey: "name", source: states
        }).on('typeahead:selected', function(event, data){
            $('.start').val(data.id);
        });
    $('.goal_typeahead').typeahead({ hint: true, highlight: true, minLength: 1 },
        { name: 'states', displayKey: "name", source: states
        }).on('typeahead:selected', function(event, data){
            $('.goal').val(data.id);
        });
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeu1Js2eiRwejCHm4gmhaE8I4Oxg-BFSg&signed_in=true&callback=initMap"></script>
</body>
</html>
