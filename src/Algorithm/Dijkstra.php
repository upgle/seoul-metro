<?php
namespace Upgle\Algorithm;

use Upgle\Model\Graph;
use Upgle\Model\Vertex;

class Dijkstra implements ShortestPathInterface {

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
     * Q에 속한 Vertex중 가장 가까운 거리의 Vertex를 반환합니다
     * Dist 배열 안에서 최소값(Min)을 찾습니다.
     *
     * @param array $dist
     * @param Vertex[] $Q
     * @return Vertex|null
     */
    private function getClosetVertex(array $dist, $Q) {
        $v = NULL;
        $minWeight = NULL;
        foreach($Q as $vertex) {
            if($v == NULL || $minWeight > $dist[$vertex->getId()]) {
                $minWeight = $dist[$vertex->getId()];
                $v = $vertex;
            }
        }
        return $v;
    }

    /**
     *
     * ## 수도코드(Pseudocode)
     *
     * 1.  S ← empty sequence
     * 2.  u ← target
     * 3.  while prev[u] is defined:                  // Construct the shortest path with a stack S
     * 4.      insert u at the beginning of S         // Push the vertex onto the stack
     * 5.      u ← prev[u]                            // Traverse from target to source
     * 6.  insert u at the beginning of S             // Push the source onto the stack
     *
     * @param $goalId
     * @param $prev
     * @return array
     */
    private function makePath($goalId, $prev) {
        $S = [];
        $target = $this->graph->getVertexById($goalId);

        while(isset($prev[$target->getId()])) {
            array_push($S, $target);
            $target = $prev[$target->getId()];
        }
        array_push($S, $target);

        //스택 자료구조를 사용하지 않아 Array Reverse 처리
        return array_reverse($S);
    }

    /**
     * Get Shortest Path.
     * @param $startId
     * @param $goalId
     * @return array
     */
    public function getShortestPath($startId, $goalId) {

        /**
         * @var Vertex[] $Q
         * @var Vertex[] $prev
         * @var int[] $dist
         */
        $Q = $this->graph->getVertices();
        $dist = [];
        $prev = [];

        /**
         * 시작점을 제외한 Vertex들의 거리 값 무한대로 설정
         */
        foreach($Q as $vertex) {
            /* @var $vertex Vertex */
            $dist[$vertex->getId()] = 999999;
        }
        $dist[$startId] = 0;

        /**
         * 탐색 작업
         * @var Vertex $u
         * @var Vertex $v
         */
        while(!empty($Q)) {

            $u = $this->getClosetVertex($dist, $Q);
            unset($Q[$u->getId()]);

            //도착지점 발견 시 중단
            if($u->getId() == $goalId) break;

            foreach($u->getConnectedVertices() as $v){
                $edge = $this->graph->getEdgeById($u->getId(), $v->getId());

                $alt = $dist[$u->getId()] + $edge->getWeight();
                if($alt < $dist[$v->getId()]) {
                    $dist[$v->getId()] = $alt;
                    $prev[$v->getId()] = $u; //바로 직전 Vertex 기록 (for path planning)
                }
            }
        }
        return $this->makePath($goalId, $prev);
    }
}
