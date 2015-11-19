<?php

namespace Upgle\Model;

class Vertex
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var array
     */
    private $connectedVertices;

    /**
     * Vertex Constructor
     * @param null $id
     */
    public function __construct($id = NULL)
    {
        $this->id = $id;
        $this->connectedVertices = [];
    }

    /**
     * @param Vertex $vertex
     */
    public function connect(Vertex $vertex)
    {
        if(!in_array($vertex, $this->connectedVertices)) {
            array_push($this->connectedVertices, $vertex);
        }
    }

    /**
     * @return mixed
     */
    public function getConnectedVertices()
    {
        return $this->connectedVertices;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $name
     */
    public function setId($name)
    {
        $this->id = $name;
    }

    /**
     * @return null|string
     */
    public function __toString()
    {
        return $this->id;
    }
}
