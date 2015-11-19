<?php
namespace Upgle\Model;

class SeoulMetro extends Graph
{
    private $alias = [];

    /**
     * @param Station $vertex
     */
    public function setVertex(Station $vertex) {
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
}
