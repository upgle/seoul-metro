<?php
namespace Upgle\Model;

class Edge
{
    /**
     * @var Vertex
     */
    private $vertexA;

    /**
     * @var Vertex
     */
    private $vertexB;

    /**
     * @var int
     */
    private $weight;

    /**
     * Edge constructor.
     * @param Vertex $vertexA
     * @param Vertex $vertexB
     * @param int $weight
     */
    public function __construct(Vertex $vertexA, Vertex $vertexB, $weight = 0)
    {
        $this->vertexA = $vertexA;
        $this->vertexB = $vertexB;
        $this->weight = $weight;
    }

    /**
     * @return Vertex
     */
    public function getVertexA()
    {
        return $this->vertexA;
    }

    /**
     * @param Vertex $vertexA
     */
    public function setVertexA($vertexA)
    {
        $this->vertexA = $vertexA;
    }

    /**
     * @return Vertex
     */
    public function getVertexB()
    {
        return $this->vertexB;
    }

    /**
     * @param Vertex $vertexB
     */
    public function setVertexB($vertexB)
    {
        $this->vertexB = $vertexB;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
}
