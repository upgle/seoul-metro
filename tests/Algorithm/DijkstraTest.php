<?php
namespace Upgle\Algorithm;

use Upgle\Model\Graph;
use Upgle\Model\Vertex;

class DijkstraTest extends \PHPUnit_Framework_TestCase
{
    public function testGetShortestPath() {

        $vertexIds = ["A", "B", "C", "D"];

        $graph = new Graph();
        foreach($vertexIds as $id) {
            $graph->setVertex(new Vertex($id));
        }
        $graph->connectTwoWay("A", "B", 3);
        $graph->connectTwoWay("B", "C", 10);
        $graph->connectTwoWay("C", "D", 4);

        $dijkstra = new Dijkstra($graph);
        $path = $dijkstra->getShortestPath("A", "C");

        /**
         * 최단 경로 체크
         * @var Vertex $vertex
         */
        $pathString = "";
        foreach($path as $vertex) {
            $pathString .= $vertex->getId();
        }
        $this->assertEquals("ABC", $pathString);
    }
}
