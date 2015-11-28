<?php
namespace Upgle\Support;

use Upgle\Model\Graph;
use Upgle\Model\SeoulMetro;

class Path
{
    /**
     * @var \Upgle\Model\Station[]
     */
    private $path;

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
     * Path constructor
     * @param SeoulMetro $graph
     * @param array $path
     */
    public function __construct(SeoulMetro $graph, array $path)
    {
        $this->path = $path;
        $this->graph = $graph;

        $this->init();
    }

    /**
     * Initialize
     */
    private function init() {
        $prev = $this->path[0];
        for($i=1; $i< count($this->path); $i++) {
            if(!$this->graph->isTransferPair($prev->getId(), $this->path[$i]->getId())) {
                $this->minutes += $this->graph->getEdge($prev, $this->path[$i])->getWeight();
                $this->stationCount++;
            } else {
                $this->transferCount++;
            }
            $prev = $this->path[$i];
        }
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


}
