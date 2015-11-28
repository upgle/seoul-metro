<?php
namespace Example;

use Upgle\Importer\ExcelImporter;
use Upgle\Algorithm\Dijkstra;
use Upgle\Model\SeoulMetro;
use Upgle\Support\GoogleMap;
use Upgle\Support\Path;

/**
 * Composer Autoload
 */
require_once(dirname(__FILE__). '/../vendor/autoload.php');
define('EXCEL_PATH', dirname(__FILE__). '/metro.xlsx');
date_default_timezone_set('Europe/London');

/**
 * HTTP Request
 */
$startId = (isset($_GET["start"]) && is_numeric($_GET["start"])) ? $_GET["start"] : NULL;
$goalId = (isset($_GET["goal"]) && is_numeric($_GET["goal"])) ? $_GET["goal"] : NULL;
$searchTarget = (isset($_GET["target"])) ? $_GET["target"] : NULL;

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
 * 최소 시간 or 최소 환승
 */
if($searchTarget == "minTransfer") {
    $seoulMetro->setTransferWeightHeavy();
}

/**
 * Benchmark Dijkstra Algorithm
 */
$bench = new \Ubench();
$bench->start();
$algorithm = new Dijkstra($seoulMetro);
$path = [];
if($startId && $goalId) {
    $path = $algorithm->getShortestPath(
        $startId,
        $goalId
    );
}
$bench->end();

/**
 * Make Path Information
 */
$pathInfo = new Path($seoulMetro, $path);

/**
 * Google Map Helper
 */
$googleMap = new GoogleMap($path);
$googleMapCenter = $googleMap->getCenter();

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
    <title>SEOUL Metro</title>
    <script src="js/jquery-1.11.3.min.js"></script>
    <script src="js/typeahead.bundle.min.js"></script>
    <script src="js/metro.js"></script>
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:600' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="//cdn.jsdelivr.net/xeicon/1.0.4/xeicon.min.css">
    <link rel="stylesheet" href="css/default.css">
</head>
<body>
<div id="nav">
    <h1><i class="xi-subway"></i> SEOUL Metro</h1>
    <ul class="statistics">
        <li>알고리즘 연산 시간 : <strong><?=$bench->getTime()?></strong></li>
        <li>메모리 피크 : <strong><?=$bench->getMemoryPeak()?></strong></li>
    </ul>
</div>
<div class="sidebar">
    <form action="/" id="form-search">
        <input type="hidden" name="target" value="" />
        <input type="hidden" name="goal" class="goal" value="<?=$goalId?>" />
        <input type="hidden" name="start" class="start" value="<?=$startId?>"/>
        <input class="start_typeahead" type="text" placeholder="출발 역" value="<?=$seoulMetro->getStationNameById($startId)?>">
        <input class="goal_typeahead" type="text" placeholder="도착 역" value="<?=$seoulMetro->getStationNameById($goalId)?>">
        <button type="submit" class="btn-search" value="빠른길 찾기"><i class="xi-magnifier"></i> 빠른길 찾기</button>
        <ul class="searching-option">
            <li class="minTime <?php if($searchTarget!="minTransfer"):?> active<?php endif; ?>">최소 시간</li>
            <li class="minTransfer <?php if($searchTarget=="minTransfer"):?> active<?php endif; ?>">최소 환승</li>
        </ul>
    </form>
    <div class="subway-information">
        <?php if(count($path) > 0) : ?>
        <div class="subway-information-summary">
            <ul>
                <li>소요시간 <span class="data"><?=$pathInfo->getMinutes()?>분</span></li>
                <li>정차역 <span class="data"><?=$pathInfo->getStationCount()?>개</span></li>
                <li>환승 <span class="data"><?=$pathInfo->getTransferCount()?>회</span></li>
            </ul>
        </div>
        <ul class="subway-route subway-information-route">
            <?php foreach ($path as $station) : ?>
                <li class="line line<?=$station->getLine()?>"><span class="mark"></span><?=$station->getName()?> (<?=$station->getLine()?>호선)</li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
    </div>
</div>
<div id="map"></div>
<script>
    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: {lat: <?=$googleMapCenter["latitude"]?>, lng: <?=$googleMapCenter["longitude"]?>}
        });
        <?php foreach ($path as $station) : ?>
        addMarker({lat: <?=$station->getLatitude()?>, lng: <?=$station->getLongitude()?>}, map);
        <?php endforeach; ?>

        var subwayPlanCoordinates = [
            <?php foreach ($path as $key => $station) : ?>
            {lat: <?=$station->getLatitude()?>, lng:  <?=$station->getLongitude()?>},
            <?php endforeach; ?>
        ];
        var subwayPath = new google.maps.Polyline({
            path: subwayPlanCoordinates,
            geodesic: true,
            strokeColor: '#003291',
            strokeOpacity: 1.0,
            strokeWeight: 6
        });
        subwayPath.setMap(map);
    }
    function addMarker(location, map) {
        var marker = new google.maps.Marker({
            position: location,
            map: map
        });
    }
    var statesData = <?=json_encode($stations)?>;
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeu1Js2eiRwejCHm4gmhaE8I4Oxg-BFSg&signed_in=true&callback=initMap"></script>
</body>
</html>
