<?php
require_once(dirname(__FILE__). '/../vendor/autoload.php');
date_default_timezone_set('Europe/London');

use Upgle\Repositories\WeightRepository;
use Upgle\Repositories\VertexRepository;
use Upgle\Importer\ExcelImporter;

/**
 * Repositories
 */
$minutesWeight = new WeightRepository();
$kmWeight = new WeightRepository();
$vertexs = new VertexRepository();

/**
 * Import Seoul Metro Information
 */
$excelImporter = new ExcelImporter(
    dirname(__FILE__). '/metro.xlsx',
    $vertexs,
    $minutesWeight,
    $kmWeight
);
$excelImporter->import();

$graph = new \Upgle\Algorithm\Dijkstra($vertexs->getVertexes(), $minutesWeight);
$graph->getShortestPath("시청", "동대문");
