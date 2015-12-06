<?php
namespace Upgle\Algorithm;

use Upgle\Model\Graph;
use Upgle\Model\Vertex;

/**
 * Class Dijkstra
 * @package Upgle\Algorithm
 */
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
     *
     * ## 수도코드(Pseudocode)
     *
     * 1  function Dijkstra(Graph, source):
     * 2
     * 3      create vertex set Q
     * 4
     * 5      for each vertex v in Graph:             // Initialization
     * 6          dist[v] ← INFINITY                  // Unknown distance from source to v
     * 7          prev[v] ← UNDEFINED                 // Previous node in optimal path from source
     * 8          add v to Q                          // All nodes initially in Q (unvisited nodes)
     * 9
     * 10      dist[source] ← 0                        // Distance from source to source
     * 11
     * 12      while Q is not empty:
     * 13          u ← vertex in Q with min dist[u]    // Source node will be selected first
     * 14          remove u from Q
     * 15
     * 16          for each neighbor v of u:           // where v is still in Q.
     * 17              alt ← dist[u] + length(u, v)
     * 18              if alt < dist[v]:               // A shorter path to v has been found
     * 19                  dist[v] ← alt
     * 20                  prev[v] ← u
     * 21
     * 22      return dist[], prev[]
     *
     * @param $startId
     * @param $goalId
     * @return array
     */
    public function getShortestPath($startId, $goalId) {

        /**
         * create vertex set Q
         *
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
            $dist[$vertex->getId()] = 99999999;
        }
        $dist[$startId] = 0;

        /**
         * 최단거리 탐색 작업
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

                    //바로 직전 Vertex 기록 (for path planning)
                    $prev[$v->getId()] = $u;
                }
            }
        }
        return $this->makePath($goalId, $prev);
    }
}
