<?php
namespace Upgle\Support;

use Upgle\Model\Graph;
use Upgle\Model\SeoulMetro;
use Upgle\Model\Station;

class Path
{
    public static $COLOR = [
        "1" => "#003291",
        "2" => "#37b42d",
        "3" => "#fa5f2c",
        "4" => "#2c9ede",
        "5" => "#8539b0",
        "6" => "#9a4e0f",
        "7" => "#606d00",
        "8" => "#e71e6e",
        "9" => "#bf9f1e",
        "U" => "#fd8d00",
        "SU" => "#edb217",
        "S" => "#a6032d",
        "K" => "#7dc4a5",
        "I" => "#6691c8",
        "G" => "#26a97f",
        "E" => "#77c371",
        "B" => "#edb217",
        "A" => "#70b7e5"
    ];

    /**
     * @var \Upgle\Model\Station[]
     */
    private $path = [];

    /**
     * @var Graph
     */
    private $graph;

    /**
     * @var int
     */
    private $minutes = 0;

    /**
     * @var int
     */
    private $stationCount = 0;

    /**
     * @var int
     */
    private $transferCount = 0;

    /**
     * @var array
     */
    private $colorPath = [];

    /**
     * Path constructor
     * @param SeoulMetro $graph
     * @param array $path
     */
    public function __construct(SeoulMetro $graph, array $path)
    {
        $this->path = $path;
        $this->graph = $graph;

        $this->removeStartGoalTransfer();
        $this->init();
    }

    /**
     * 출발지와 도착지가 환승역이면 제거한다
     */
    private function removeStartGoalTransfer() {

        if(count($this->path) < 2) return;
        if($this->graph->isTransferPair($this->path[0]->getId(), $this->path[1]->getId())) {
            array_shift($this->path);
        }

        $count = count($this->path);
        if($count < 2) return;
        if($this->graph->isTransferPair($this->path[$count-2]->getId(), $this->path[$count-1]->getId())) {
            array_pop($this->path);
        }
    }

    /**
     * Initialize
     */
    private function init() {

        $count = count($this->path);
        if($count == 0) return;

        $colorIndex = 0;
        $prev = $this->path[0];
        $this->setColorPath($colorIndex, $prev);

        for($i=1; $i< $count; $i++) {
            if(!$this->graph->isTransferPair($prev->getId(), $this->path[$i]->getId())) {
                $this->minutes += $this->graph->getEdge($prev, $this->path[$i])->getMinute();
                $this->stationCount++;
            } else {
                // 환승 평균 시간 6분으로 고정
                $this->minutes += 6;
                $this->transferCount++;
            }

            if($prev->getLine() != $this->path[$i]->getLine()) {
                $colorIndex++;
            }
            $this->setColorPath($colorIndex, $this->path[$i]);
            $prev = $this->path[$i];
        }
    }

    /**
     * @param Station $station
     */
    private function setColorPath($index, Station $station) {
        $this->colorPath[$index][] = $station;
    }

    /**
     * @return array
     */
    public function getColorPath()
    {
        return $this->colorPath;
    }

    /**
     * @return int
     */
    public function getMinutes()
    {
        return $this->minutes;
    }

    /**
     * @return int
     */
    public function getStationCount()
    {
        return $this->stationCount;
    }

    /**
     * @return int
     */
    public function getTransferCount()
    {
        return $this->transferCount;
    }

    /**
     * @return \Upgle\Model\Station[]
     */
    public function getPath()
    {
        return $this->path;
    }


}
