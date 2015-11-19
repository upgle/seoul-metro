<?php
namespace Upgle\Algorithm;

use Upgle\Model\Graph;
use Upgle\Model\Vertex;

class Dijkstra implements  ShortestPathInterface {

    /**
     * @var Graph
     */
    private $graph;

    /**
     * Dijkstra Constructor
     * @param Graph $graph
     */
    public function __construct(Graph $graph)
    {
        $this->graph = $graph;
    }

    /**
     * Get Shortest Path.
     * @param $source
     * @param $goal
     * @return array
     */
    public function getShortestPath($source, $goal) {

        $Q = $this->graph->getVertices();
        $dist = [];

        /* @var $prev Vertex[] */
        $prev = [];

        foreach($Q as $vertex) {
            /* @var $vertex Vertex */
            $dist[$vertex->getId()] = 99999;
        }
        $dist[$source] = 0;

        while(count($Q) > 0) {
            $Q_DIST = array_map(function($value) use ($dist){
                /** @var Vertex $value */
                return $dist[$value->getId()];
            }, $Q);

            $u = $Q[array_flip($Q_DIST)[min($Q_DIST)]];
            unset($Q[array_flip($Q_DIST)[min($Q_DIST)]]);

            /** @var Vertex $u */
            foreach($u->getConnectedVertices() as $v){

                /** @var Vertex $v */
                $edge = $this->graph->getEdgeById($u->getId(), $v->getId());

                $alt = $dist[$u->getId()] + (int)$edge->getWeight();
                if($alt < $dist[$v->getId()]) {
                    $dist[$v->getId()] = $alt;
                    $prev[$v->getId()] = $u; //바로 직전 Vertex 기록 (for path planning)
                }
            }
        }

        $start = NULL;
        $path = [
            $goal
        ];
        while($start != $source) {
            $start = $prev[$goal]->getId();
            $goal = $start;
            $path[] = $start;
        }
        return array_reverse($path);
    }
}
