<?php
namespace Upgle\Repositories;

use Upgle\Model\Edge;
use Upgle\Model\Vertex;

class EdgeRepositories
{
    /**
     * @var array
     */
    private $edges = [];

    public function set(Edge $edge) {
        $this->edges[$edge->getVertexA()->getName()][$edge->getVertexB()->getName()] = $edge;
    }

    public function get(Vertex $vertexA, Vertex $vertexB) {
        return $this->edges[$vertexA->getName()][$vertexB->getName()];
    }

    public function gets() {
        return $this->edges;
    }
}
