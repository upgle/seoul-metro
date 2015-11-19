<?php
namespace Example;

use Upgle\Repositories\VertexRepository;
use Upgle\Repositories\EdgeRepositories;
use Upgle\Importer\ExcelImporter;
use Upgle\Algorithm\Dijkstra;

require_once(dirname(__FILE__). '/../vendor/autoload.php');
date_default_timezone_set('Europe/London');

/**
 * Repositories
 */
$vertexs = new VertexRepository();
$edges = new EdgeRepositories();

/**
 * Data Import
 */
$excelImporter = new ExcelImporter(dirname(__FILE__). '/metro.xlsx', $vertexs, $edges);
$excelImporter->import();

$graph = new Dijkstra($vertexs->gets(), $edges);
$path = $graph->getShortestPath("시청", "동대문");

var_dump($path);
