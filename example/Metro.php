<?php
namespace Example;

use Upgle\Model\Graph;
use Upgle\Importer\ExcelImporter;
use Upgle\Algorithm\Dijkstra;

require_once(dirname(__FILE__). '/../vendor/autoload.php');
date_default_timezone_set('Europe/London');

$graph = new Graph();

/**
 * Data Import
 */
$excelImporter = new ExcelImporter(dirname(__FILE__). '/metro.xlsx', $graph, $vertexs, $edges);
$excelImporter->import();

$graph = new Dijkstra($graph);
$path = $graph->getShortestPath("시청", "동대문");

var_dump($path);
