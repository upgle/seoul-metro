<?php
namespace Example;

use Upgle\Importer\ExcelImporter;
use Upgle\Algorithm\Dijkstra;
use Upgle\Model\SeoulMetro;
use Upgle\Support\GoogleMap;
use Upgle\Support\Path;
use Upgle\Model\Station;

/**
 * Composer Autoload
 */
require_once(dirname(__FILE__). '/../vendor/autoload.php');
define('EXCEL_PATH', dirname(__FILE__). '/metro.xlsx');
define('CACHE_FILE', 'metro.cache');
define('USE_CACHE', true);
date_default_timezone_set('Asia/Seoul');

/**
 * HTTP Request
 */
$startId = isset($_GET["start"]) ? $_GET["start"] : NULL;
$goalId = isset($_GET["goal"]) ? $_GET["goal"] : NULL;
$searchTarget = (isset($_GET["target"])) ? $_GET["target"] : NULL;

/**
 * Excel Parser 퍼포먼스 문제가 있어 Cache 처리
 * 엑셀 파일을 수정하는 경우 `metro.cache` 파일을 삭제하면 됩니다
 */
if(USE_CACHE && file_exists(CACHE_FILE)) {
    $seoulMetro = unserialize(file_get_contents(CACHE_FILE));
} else {

    /**
     * Initialize SeoulMetro Graph
     * SeoulMetro 클래스 초기화
     */
    $seoulMetro = new SeoulMetro();

    /**
     * Import Data From Excel
     * 엑셀 파일로부터 노선 정보를 읽어들임
     */
    $importer = new ExcelImporter(EXCEL_PATH, $seoulMetro);
    $importer->import();
    file_put_contents(CACHE_FILE, serialize($seoulMetro));
}

/**
 * 최소 정거장 or 최소 시간 or 최소 환승 설정
 */
switch($searchTarget) {
    //최소 환승의 경우 환승 가중치를 최대로 높임
    case "minTransfer" :
        $seoulMetro->setTransferWeightHeavy();
        break;
    case "minTime" :
        break;
    case "minStation" :
    default :
    $seoulMetro->setAllWeightSame(1);
        break;
}

/**
 * Dijkstra Algorithm
 * 다익스트라 알고리즘으로 최단경로를 계산
 * (+벤치마킹)
 */
$path = [];
$bench = new \Ubench();

//벤치마킹 시작
$bench->start();

$algorithm = new Dijkstra($seoulMetro);
if($startId && $goalId) {
    //시작 역이 환승역인 경우 연결된 환승역의 가중치를 0으로 세팅
    if($seoulMetro->getVertexById($startId)->isTransferStation()) {
        $seoulMetro->setSpecificTransferWeight($startId, 0);
    }
    //종료 역이 환승역인 경우 연결된 환승역의 가중치를 0으로 세팅
    if($seoulMetro->getVertexById($goalId)->isTransferStation()) {
        $seoulMetro->setSpecificTransferWeight($goalId, 0);
    }
    //최단 경로를 구함
    $path = $algorithm->getShortestPath(
        $startId,
        $goalId
    );
}

//벤치마킹 종료
$bench->end();

/**
 * Make Path Information
 * 소요시간, 정차역, 환승 및 노선 색상 정보를 가공
 */
$pathInfo = new Path($seoulMetro, $path);

/**
 * Google Map Helper
 * 구글 맵에 필요한 정보를 가공
 */
$googleMap = new GoogleMap($path);
$googleMapCenter = $googleMap->getCenter();

/**
 * get Stations list
 * TypeAhead 에서 사용하는 노선 정보
 */
$stations = $seoulMetro->getStationsToArray();
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
        <input class="start_typeahead" type="text" placeholder="출발지를 입력하세요" value="<?=$seoulMetro->getStationNameById($startId)?>">
        <input class="goal_typeahead" type="text" placeholder="도착지를 입력하세요" value="<?=$seoulMetro->getStationNameById($goalId)?>">
        <button type="submit" class="btn-search" value="빠른길 찾기"><i class="xi-magnifier"></i> 빠른길 찾기</button>
        <ul class="searching-option">
            <li class="minStation <?php if(!$searchTarget || $searchTarget=="minStation"):?> active<?php endif; ?>">최소 정거장</li>
            <li class="minTime <?php if($searchTarget=="minTime"):?> active<?php endif; ?>">최소 시간</li>
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
            <?php $prevStation = null ?>
            <?php foreach ($pathInfo->getPath() as $station) : ?>

                <?php
                if($prevStation)
                    $isTransfer = $seoulMetro->isTransferPair($prevStation->getId(), $station->getId());
                ?>
                <li class="line line<?=$station->getLine()?> <?php if($isTransfer) : ?> transfer<?php endif; ?>">
                    <span class="mark"></span><?=$station->getName()?> (<?=Station::$LINE[$station->getLine()]?>)
                </li>
            <?php $prevStation = $station; ?>
            <?php endforeach; ?>
        </ul>
        <?php else : ?>
        <div class="subway-information-nodata">
            <i class="subway-information-nodata-icon xi-pin-circle"></i>
            <p class="subway-information-nodata-text">먼저 검색해주시기 바랍니다.</p>
        </div>


        <?php endif; ?>
    </div>
</div>
<div id="map"></div>
<script type="text/javascript">

    var statesData = <?=json_encode($stations)?>;

    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            zoom: 12,
            center: {lat: <?=$googleMapCenter["latitude"]?>, lng: <?=$googleMapCenter["longitude"]?>}
        });

        /**
         * 지하철 정류장 표시
         */
        <?php foreach ($path as $station) : ?>
        addMarker({lat: <?=$station->getLatitude()?>, lng: <?=$station->getLongitude()?>}, map);
        <?php endforeach; ?>

        /**
         * 지하철 경로 표시
         */
        var subwayPlanCoordinates;
        var subwayPath;
        <?php
        $pathColor = null;
        foreach ($pathInfo->getColorPath() as $index => $stations) :
        ?>
        subwayPlanCoordinates = [
            <?php foreach ($stations as $key => $station) : ?>
            <?php if($key == 0) $pathColor = Path::$COLOR[$station->getLine()]; ?>
            {lat: <?=$station->getLatitude()?>, lng:  <?=$station->getLongitude()?>},
            <?php endforeach; ?>
        ];
        subwayPath = new google.maps.Polyline({
            path: subwayPlanCoordinates,
            geodesic: true,
            strokeColor: "<?=$pathColor?>",
            strokeOpacity: 1.0,
            strokeWeight: 8
        });
        subwayPath.setMap(map);
        <?php endforeach; ?>
    }
    function addMarker(location, map) {
        var marker = new google.maps.Marker({
            position: location,
            map: map,
            icon: {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 6,
                fillColor: "yellow",
                fillOpacity: 1.0,
                strokeColor: "black",
                strokeWeight: 4
            }
        });
    }
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBeu1Js2eiRwejCHm4gmhaE8I4Oxg-BFSg&signed_in=true&callback=initMap"></script>
</body>
</html>
