<?php
namespace Upgle\Algorithm;

use Upgle\Model\Edge;
use Upgle\Model\Graph;
use Upgle\Model\Vertex;

class DijkstraTest extends \PHPUnit_Framework_TestCase
{
    public function testGetShortestPath() {

        $vertices = [];
        $vertices["A"] = new Vertex("A");
        $vertices["B"] = new Vertex("B");
        $vertices["C"] = new Vertex("C");
        $vertices["D"] = new Vertex("D");

        $vertices["A"]->connect($vertices["B"]);
        $vertices["B"]->connect($vertices["A"]);

        $edges = [];
        $edges["AB"] = new Edge($vertices["A"], $vertices["B"], 3);
        $edges["BA"] = new Edge($vertices["B"], $vertices["A"], 3);

        $vertices["B"]->connect($vertices["C"]);
        $vertices["C"]->connect($vertices["B"]);
        $edges["BC"] = new Edge($vertices["B"], $vertices["C"], 10);
        $edges["CB"] = new Edge($vertices["C"], $vertices["B"], 10);

        $vertices["C"]->connect($vertices["D"]);
        $vertices["D"]->connect($vertices["C"]);
        $edges["CD"] = new Edge($vertices["C"], $vertices["D"], 4);
        $edges["DC"] = new Edge($vertices["D"], $vertices["C"], 4);

        $graph = new Graph();
        foreach ($vertices as $id => $vertex) {
            $graph->setVertex($vertex);
        }
        foreach ($edges as $edge) {
            $graph->setEdge($edge);
        }

        $dijkstra = new Dijkstra($graph);
        $path = $dijkstra->getShortestPath("A", "C");

        $pathString = "";
        foreach($path as $vertex) {
            $pathString .= $vertex->getId();
        }
        $this->assertEquals("ABC", $pathString);
    }
}
