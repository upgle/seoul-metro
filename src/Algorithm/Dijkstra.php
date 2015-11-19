<?php
namespace Upgle\Algorithm;

use Upgle\Repositories\EdgeRepositories;
use Upgle\Model\Vertex;

class Dijkstra implements  ShortestPathInterface {

    /**
     * @var EdgeRepositories
     */
    private $edges;

    /**
     * @var array
     */
    private $vertices;

    /**
     * @param array $vertices
     * @param EdgeRepositories $edges
     */
    public function __construct(array $vertices, EdgeRepositories $edges)
    {
        $this->vertices = $vertices;
        $this->edges = $edges;
    }

    /**
     * Get Shortest Path.
     * @param $source
     * @param $goal
     * @return array
     */
    public function getShortestPath($source, $goal) {

        $Q = $this->vertices;
        $dist = [];

        /* @var $prev Vertex[] */
        $prev = [];

        foreach($Q as $vertex) {
            /* @var $vertex Vertex */
            $dist[$vertex->getName()] = 99999;
        }
        $dist[$source] = 0;

        while(count($Q) > 0) {
            $Q_DIST = array_map(function($value) use ($dist){
                /** @var Vertex $value */
                return $dist[$value->getName()];
            }, $Q);

            $u = $Q[array_flip($Q_DIST)[min($Q_DIST)]];
            unset($Q[array_flip($Q_DIST)[min($Q_DIST)]]);

            /** @var Vertex $u */
            foreach($u->getConnectedVertexes() as $v){

                $edge = $this->edges->get($this->vertices[$u->getName()], $this->vertices[$v->getName()]);

                /** @var Vertex $v */
                $alt = $dist[$u->getName()] + (int)$edge->getWeight();
                if($alt < $dist[$v->getName()]) {
                    $dist[$v->getName()] = $alt;
                    $prev[$v->getName()] = $u; //바로 직전 Vertex 기록 (for path planning)
                }
            }
        }

        $start = NULL;
        $path = [
            $goal
        ];
        while($start != $source) {
            $start = $prev[$goal]->getName();
            $goal = $start;
            $path[] = $start;
        }
        return array_flip($path);
    }
}
