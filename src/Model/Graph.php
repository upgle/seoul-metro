<?php
namespace Upgle\Model;

class Graph
{
    /**
     * @var Edge[]
     */
    private $edges = [];

    /**
     * @var Vertex[]
     */
    protected $vertices = [];

    /**
     * @param Edge $edge
     */
    public function setEdge(Edge $edge) {
        $this->edges[$edge->getVertexA()->getId()][$edge->getVertexB()->getId()] = $edge;
    }

    /**
     * @param Vertex $vertexA
     * @param Vertex $vertexB
     * @return Edge
     */
    public function getEdge(Vertex $vertexA, Vertex $vertexB) {
        return $this->edges[$vertexA->getId()][$vertexB->getId()];
    }

    /**
     * @param $vertexIdOfA
     * @param $vertexIdOfB
     * @return mixed
     */
    public function getEdgeById($vertexIdOfA, $vertexIdOfB) {
        return $this->edges[$vertexIdOfA][$vertexIdOfB];
    }

    /**
     * @return array
     */
    public function getEdges() {
        return $this->edges;
    }

    /**
     * @param $id
     * @return null
     */
    public function getVertexById($id)
    {
        return (isset($this->vertices[$id])) ? $this->vertices[$id] : NULL;
    }

    /**
     * @param Vertex $vertex
     */
    public function setVertex(Vertex $vertex) {
        $this->vertices[$vertex->getId()] = $vertex;
    }

    /**
     * @return Vertex[]
     */
    public function getVertices()
    {
        return $this->vertices;
    }

    /**
     * @param Vertex[] $vertices
     */
    public function setVertices(array $vertices)
    {
        $this->vertices = $vertices;
    }
}
