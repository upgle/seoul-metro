<?php

namespace Upgle\Model;

class Vertex
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $connectedVertexes;

    /**
     * Vertex Constructor
     */
    public function __construct($name = NULL)
    {
        $this->name = $name;
        $this->connectedVertexes = [];
    }

    /**
     * @param Vertex $vertex
     */
    public function connect(Vertex $vertex)
    {
        if(!in_array($vertex, $this->connectedVertexes)) {
            array_push($this->connectedVertexes, $vertex);
        }
    }

    /**
     * @return mixed
     */
    public function getConnectedVertexes()
    {
        return $this->connectedVertexes;
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
