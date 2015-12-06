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

    /**
     * @return array
     */
    public function getStationsToArray() {
        $stations = [];
        foreach($this->getVertices() as $station) {
            /* @var \Upgle\Model\Station $station */
            $stations[] = [
                "id" => $station->getId(),
                "name" => $station->getName(),
                "line" => Station::$LINE[$station->getLine()]
            ];
        }
        return $stations;
    }

    /**
     * 환승에 대한 가중치를 최대로 설정합니다
     * 최소 환승시 호출합니다.
     */
    public function setTransferWeightHeavy() {
        foreach($this->transferPair as $transferA => $children) {
            foreach($children as $transferB => $val) {

                //같은 노선 환성의 경우 가중치를 높이지 않음
                if($transferA[0] == '*' || $transferB[0] == '*') continue;
                $this->getEdgeById($transferA, $transferB)->setWeight(500);
            }
        }
    }

    public function setAllWeightSame($weight = 0) {
        /** @var Edge $edge */
        foreach($this->edges as $edgeA) {
            foreach($edgeA as $edge) {
                $edge->setWeight($weight);
            }
        }
    }

    /**
     * 특정 환승역에 대한 가중치를 설정합니다.
     * @param $stationId
     */
    public function setSpecificTransferWeight($stationId, $weight = 0) {
        foreach($this->transferPair[$stationId] as $transferId => $val) {
            $this->getEdgeById($stationId, $transferId)->setWeight($weight);
            $this->getEdgeById($transferId, $stationId)->setWeight($weight);
        }
    }
}
