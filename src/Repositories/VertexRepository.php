<?php
namespace Upgle\Repositories;

use Upgle\Model\Vertex;

class VertexRepository
{
    /**
     * @var int
     */
    private $position = 0;

    /**
     * @var array
     */
    protected $vertexes = [];

    /**
     * VertexRepository constructor.
     */
    public function __construct()
    {
        $this->position = 0;
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name)
    {
        return (isset($this->vertexes[$name])) ? $this->vertexes[$name] : NULL;
    }

    /**
     * @param $name
     * @param Vertex $vertex
     */
    public function set($name, Vertex $vertex) {
        $this->vertexes[$name] = $vertex;
    }

    /**
     * @return array
     */
    public function gets()
    {
        return $this->vertexes;
    }

    /**
     * @param array $vertexes
     */
    public function sets($vertexes)
    {
        $this->vertexes = $vertexes;
    }

}
