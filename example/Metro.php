<?php
namespace Example;

use Upgle\Importer\ExcelImporter;
use Upgle\Algorithm\Dijkstra;
use Upgle\Model\SeoulMetro;
use Upgle\Model\Station;

require_once(dirname(__FILE__). '/../vendor/autoload.php');
date_default_timezone_set('Europe/London');

$excelPath = dirname(__FILE__). '/metro.xlsx';
$seoulMetro = new SeoulMetro();

$importer = new ExcelImporter($excelPath, $seoulMetro);
$importer->import();

$algorithm = new Dijkstra($seoulMetro);
$path = $algorithm->getShortestPath($seoulMetro->getStationIdByName("종각", 1), $seoulMetro->getStationIdByName("동대문", 1));

foreach ($path as $station) {
    /* @var Station $station */
    echo $station->getName();
}
