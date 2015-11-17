<?php

namespace Upgle;

class Vertex
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $linkedVertexs;

    /**
     * Vertex Constructor
     */
    public function __construct($name = NULL)
    {
        $this->name = $name;
        $this->linkedVertexs = [];
    }

    /**
     * @param Vertex $vertex
     */
    public function connectVertex(Vertex $vertex)
    {
        if(!in_array($vertex, $this->linkedVertexs)) {
            array_push($this->linkedVertexs, $vertex);
        }
    }

    /**
     * @return mixed
     */
    public function getConnectedVertexs()
    {
        return $this->linkedVertexs;
    }

    /**
     * @return null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param null $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        return $this->name;
    }
}
