<?php
namespace Upgle\Algorithm;

use Upgle\Repositories\WeightRepositoryInterface;
use Upgle\Vertex;

class Dijkstra implements  ShortestPathInterface {

    /**
     * @var WeightRepositoryInterface
     */
    private $edges;

    /**
     * @var array
     */
    private $vertices;

    /**
     * @param array $vertices
     * @param WeightRepositoryInterface $edges
     */
    public function __construct(array $vertices, WeightRepositoryInterface $edges)
    {
        $this->vertices = $vertices;
        $this->edges = $edges;
    }

    public function getShortestPath($source, $goal) {
        $Q = $this->vertices;
        $dist = [];
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
            foreach($u->getConnectedVertexs() as $v){
                /** @var Vertex $v */
                $alt = $dist[$u->getName()] + $this->edges->getWeight($u, $v);
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
        var_dump($path);
    }
}
