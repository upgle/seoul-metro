<?php
namespace Upgle\Model;

class SeoulMetro extends Graph
{
    private $alias = [];

    /**
     * @param Vertex $vertex
     */
    public function setVertex(Vertex $vertex) {
        $this->vertices[$vertex->getId()] = $vertex;
        $this->alias[$vertex->getLine()][$vertex->getName()] = $vertex->getId();
    }

    /**
     * @param $stationName
     * @param $stationLine
     * @return null
     */
    public function getStationIdByName($stationName, $stationLine) {
        if(isset($this->alias[$stationLine][$stationName])) {
            return $this->alias[$stationLine][$stationName];
        }
        return NULL;
    }

    /**
     * @param $stationId
     * @return null
     */
    public function getStationNameById($stationId) {
        if($this->getVertexById($stationId) !== NULL) {
            return $this->getVertexById($stationId)->getName();
        }
        return NULL;
    }
}
