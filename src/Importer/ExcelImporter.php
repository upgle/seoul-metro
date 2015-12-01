<?php
namespace Upgle\Importer;

use Upgle\Model\Edge;
use Upgle\Model\Graph;
use Upgle\Model\Rail;
use Upgle\Model\Station;
use Upgle\Model\SeoulMetro;

class ExcelImporter
{
    /**
     * @var SeoulMetro
     */
    protected $graph;

    /**
     * @var \PHPExcel
     */
    protected $objPHPExcel;

    /**
     * @param $filePath
     * @param Graph $graph
     */
    public function __construct($filePath, Graph $graph)
    {
        $this->graph = $graph;
        $this->objPHPExcel = \PHPExcel_IOFactory::load($filePath);
    }

    /**
     * Import data from excel
     * @throws \PHPExcel_Exception
     */
    public function import()
    {
        $this->importStations();
        $this->importStationEdges();
        $this->importTransferEdges();
    }

    /**
     * @throws \PHPExcel_Exception
     */
    protected function importStations() {
        $rowIterator = $this->objPHPExcel->getSheetByName("역사코드")->getRowIterator(2);
        foreach($rowIterator as $row) {

            $line = $name = $code = NULL;
            $latitude = NULL;
            $longitude = NULL;

            foreach ($row->getCellIterator() as $cell) {
                /* @var $cell \PHPExcel_Cell */
                $column = $cell->getColumn();
                switch ($column) {
                    //코드
                    case 'B' :
                        $code = $cell->getValue();
                        break;
                    //역 이름
                    case 'C' :
                        $name = $cell->getValue();
                        break;
                    //라인
                    case 'D' :
                        $line = $cell->getValue();
                        break;
                    //GPS X 위도
                    case 'F' :
                        $latitude = $cell->getValue();
                        break;
                    //GPS Y 경도
                    case 'G' :
                        $longitude = $cell->getValue();
                        break;
                }
            }
            if(preg_match('/^[\d\*]{4,5}$/', $code)) {
                $station = new Station($code);
                $station->setName($name);
                $station->setLine($line);
                $station->setLatitude($latitude);
                $station->setLongitude($longitude);
                $this->graph->setVertex($station);
            }
        }
    }

    /**
     * Import station edges
     */
    protected function importStationEdges() {

        $rowIterator = $this->objPHPExcel->getSheetByName("소요시간")->getRowIterator(2);

        $prevCode = null;
        foreach($rowIterator as $row) {

            $km = null;
            $code = null;
            $minute = null;
            $isOneWay = false;

            /* @var $cell \PHPExcel_Cell */
            foreach($row->getCellIterator('I','L') as $cell) {
                switch($cell->getColumn()) {
                    //역코드
                    case 'I' :
                        $code = $cell->getValue();
                        break;
                    //시간
                    case 'K' :
                        $minute = $cell->getValue();
                        break;
                    //단방향여부
                    case 'L' :
                        $isOneWay = ($cell->getValue() == 'o');
                        break;
                }
            }
            $this->connectStation($code, $prevCode, $minute, $isOneWay);
            $prevCode = $code;
        }
    }

    /**
     * Import transfer edges
     */
    protected function importTransferEdges() {

        $rowIterator = $this->objPHPExcel->getSheetByName("환승역")->getRowIterator();
        foreach($rowIterator as $row) {

            $stationCodeA = null;
            $stationCodeB = null;
            $minute = 0;

            /* @var $cell \PHPExcel_Cell */
            foreach($row->getCellIterator('I','L') as $cell) {
                switch($cell->getColumn()) {
                    //역코드B
                    case 'I' :
                        $stationCodeA = $cell->getValue();
                        break;
                    //역코드B
                    case 'J' :
                        $stationCodeB = $cell->getValue();
                        break;
                    //시간
                    case 'L' :
                        $minute = $cell->getValue();
                        break;
                }
            }
            $this->connectStation($stationCodeA, $stationCodeB, $minute, false, true);
        }
    }

    /**
     * @param $stationCodeA
     * @param $stationCodeB
     * @param $minute
     * @param bool|false $isOneWay
     * @param bool|false $isTransfer
     */
    protected function connectStation($stationCodeA, $stationCodeB, $minute, $isOneWay = false, $isTransfer = false) {

        //Validation Check (code + code)
        if(preg_match('/^[\d\*]{8,10}$/', $stationCodeA.$stationCodeB)) {

            /** @var Station $stationA */
            $stationA = $this->graph->getVertexById($stationCodeA);

            /** @var Station $stationB */
            $stationB = $this->graph->getVertexById($stationCodeB);

            //Vertex 연결
            $stationB->connect($stationA);
            if(!$isOneWay) {
                $stationA->connect($stationB);
            }

            //환승역 연결
            if($isTransfer) {
                $this->graph->setTransferPair($stationA->getId(), $stationB->getId());
                $stationA->setTransferStation(true);
                $stationB->setTransferStation(true);
            }

            //Edge 연결
            $this->graph->setEdge(new Rail($stationB, $stationA, $minute, $minute));
            if(!$isOneWay) {
                $this->graph->setEdge(new Rail($stationA, $stationB, $minute, $minute));
            }
        }
    }

}
