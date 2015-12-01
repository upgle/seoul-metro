<?php
namespace Upgle\Model;

class Rail extends Edge
{
    /**
     * @var int
     */
    private $minute = 0;

    /**
     * @var int
     */
    private $km = 0;

    /**
     * @param Vertex $vertexA
     * @param Vertex $vertexB
     * @param int $weight
     * @param int $minute
     * @param int $km
     */
    public function __construct(Vertex $vertexA, Vertex $vertexB, $weight = 0, $minute = 0, $km = 0)
    {
        parent::__construct($vertexA, $vertexB, $weight);
        $this->minute = $minute;
        $this->km = $km;
    }

    /**
     * @return int
     */
    public function getMinute()
    {
        return $this->minute;
    }

    /**
     * @param int $minute
     */
    public function setMinute($minute)
    {
        $this->minute = $minute;
    }

    /**
     * @return int
     */
    public function getKm()
    {
        return $this->km;
    }

    /**
     * @param int $km
     */
    public function setKm($km)
    {
        $this->km = $km;
    }
}
