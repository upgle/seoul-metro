<?php
namespace Upgle\Model;

class SeoulMetro extends Graph
{
    private $alias = [];

    private $transferPair = [];

    /**
     * @param Vertex $vertex
     */
    public function setVertex(Vertex $vertex) {
        $this->vertices[$vertex->getId()] = $vertex;
        $this->alias[$vertex->getLine()][$vertex->getName()] = $vertex->getId();
    }

    /**
     * @param $stationIdA
     * @param $stationIdB
     * @return bool
     */
    public function isTransferPair($stationIdA, $stationIdB)
    {
        return isset($this->transferPair[$stationIdA][$stationIdB]);
    }

    /**
     * @param $stationIdA
     * @param $stationIdB
     */
    public function setTransferPair($stationIdA, $stationIdB)
    {
        $this->transferPair[$stationIdA][$stationIdB]
            = $this->transferPair[$stationIdB][$stationIdA]
            = TRUE;
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
