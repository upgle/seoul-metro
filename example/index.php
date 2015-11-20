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
        #nav .statistics {
            float: right;
            padding: 0 5px 0 0;
            margin: 0;
            font-size: 12px;
            line-height: 50px;
        }
        #nav .statistics li {
            display: inline-block;
            list-style: none;
            padding-right: 15px;
        }
        #map {
            margin-left:280px;
            height: 100%;
        }
        #sidebar {
            height:100%;
            position: absolute;
            left: 0;
            top: 0;
            width: 280px;
            background: #fff;
            border-right: 1px solid #aaa;
            z-index: 90;
            font-size: 13px;
            padding-top: 195px;
            box-sizing: border-box;
        }
        #subway-route {
            margin: 0;
            padding: 13px 0 0;
            height: 100%;
            overflow: auto;
            box-sizing: border-box;
        }
        #subway-route li {
            position: relative;
            list-style: none;
            padding: 0 0 0 22px;
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

        #subway-route .line + .line:before {
            position: absolute;
            display: block;
            background : #ccc;
            top: -9px;
            left:29px;
            width: 4px;
            height: 19px;
            content : "";
        }


        #subway-route .line1 .mark {
            border-color: #003291;
            color: #003291;
        }
        #subway-route .line1 + .line1:before {
            background : #003291;
        }

        #subway-route .line2 .mark {
            border-color: #37b42d;
            color: #37b42d;
        }
        #subway-route .line2 + .line2:before {
            background : #37b42d;
        }

        #subway-route .lineU .mark {
            border-color: #fd8d00;
            color: #fd8d00;
        }

        #form-search {
            position: absolute;
            top: 49px;
            background: #fafafa;
            padding: 16px 17px;
            border-bottom: 1px solid #eee;
        }
        #form-search input[type=text] {
            border: 1px solid #ddd;
            width: 100%;
            height: 31px;
            margin-bottom: 5px;
            padding: 0 10px;
            box-sizing: border-box;
        }
        #form-search .btn-search {
            height: 35px;
            margin-top: 5px;
            background: #ffffff;
            border: 1px solid #bbb;
            cursor: pointer;
            width: 100%;
            font-size: 12px;
        }

        .twitter-typeahead {
            width: 100%;
        }
        .typeahead {
            background-color: #fff;
        }

        .typeahead:focus {
            border: 2px solid #0097cf;
        }

        .tt-query {
            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            -moz-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
            box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
        }

        .tt-hint {
            color: #999
        }

        .tt-menu {
            width: 245px;
            margin: 12px 0;
            padding: 8px 0;
            background-color: #fff;
            border: 1px solid #ccc;
            -webkit-border-radius: 2px;
            -moz-border-radius: 2px;
            border-radius: 2px;
            -webkit-box-shadow: 0 5px 5px rgba(0,0,0,.1);
            -moz-box-shadow: 0 5px 5px rgba(0,0,0,.1);
            box-shadow: 0 5px 5px rgba(0,0,0,.1);
        }
        .tt-suggestion {
            padding: 3px 20px;
            font-size: 14px;
            line-height: 24px;
        }
        .tt-suggestion:hover {
            cursor: pointer;
            color: #fff;
            background-color: #0097cf;
        }
        .tt-suggestion.tt-cursor {
            color: #fff;
            background-color: #0097cf;

        }
        .tt-suggestion p {
            margin: 0;
        }


    </style>
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
        <input class="btn-search" type="submit" value="빠른길 찾기" />
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
