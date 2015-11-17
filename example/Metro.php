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

// 동대문 -> 시청
class Graph {

    public function run($source, array $vertexs, $minutesWeight) {

        $Q = $vertexs;
        $dist = [];
        $prev = [];
        foreach($vertexs as $vertex) {
            /* @var $vertex Upgle\Vertex */
            $dist[$vertex->getName()] = 9999;
        }
        $dist[$source] = 0;

        while(count($Q) > 0) {

            $Q_DIST = array_map(function($value) use ($dist){
                return $dist[$value->getName()];
            }, $Q);

            $u = $Q[array_flip($Q_DIST)[min($Q_DIST)]];
            unset($Q[array_flip($Q_DIST)[min($Q_DIST)]]);

            foreach($u->getConnectedVertexs() as $v){
                $alt = $dist[$u->getName()] + $minutesWeight->getWeight($u, $v);
                if($alt < $dist[$v->getName()]) {
                    $dist[$v->getName()] = $alt;
                    $prev[$v->getName()] = $u;
                }
            }
            var_dump($dist);
        }
    }
}

$graph = new Graph();
$graph->run("시청", $vertexs->getVertexes(), $minutesWeight);
