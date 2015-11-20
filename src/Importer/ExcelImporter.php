<?php
namespace Upgle\Importer;

use Upgle\Model\Edge;
use Upgle\Model\Graph;
use Upgle\Model\Station;

class ExcelImporter
{
    /**
     * @var Graph
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
     * @return array
     * @throws \PHPExcel_Exception
     */
    protected function getStations() {
        $stations = [];
        $rowIterator = $this->objPHPExcel->getSheet(2)->getRowIterator(2);
        foreach($rowIterator as $row) {

            $line = $name = $code = NULL;
            foreach ($row->getCellIterator() as $cell) {
                /* @var $cell \PHPExcel_Cell */
                $column = $cell->getColumn();
                switch ($column) {
                    case 'B' : //코드
                        $code = $cell->getValue();
                        break;
                    case 'C' : //이름
                        $name = $cell->getValue();
                        break;
                    case 'D' : //라인
                        $line = $cell->getValue();
                        break;
                }
            }
            if(is_numeric($code)) {
                $stations[$code] = [
                    "name" => $name,
                    "line" => $line
                ];
            }
        }
        return $stations;
    }

    public function import()
    {
        $stations = $this->getStations();
        $rowIterator = $this->objPHPExcel->getSheet(0)->getRowIterator();

        foreach($rowIterator as $row) {

            $vertexId1 = NULL;
            $vertexId2 = NULL;
            $minutes = 0;
            foreach($row->getCellIterator() as $cell) {
                /* @var $cell \PHPExcel_Cell */
                $column = $cell->getColumn();
                switch($column) {
                    case 'B' :
                        $vertexId1 = $cell->getValue();
                        break;
                    case 'C' :
                        $vertexId2 = $cell->getValue();
                        break;
                    //거리(km)
                    case 'D' :
                        $km = $cell->getValue();
                        break;
                    //시간(분)
                    case 'E' :
                        $minutes = $cell->getValue();
                        break;
                }
            }

            if(!is_numeric($vertexId1) || !is_numeric($vertexId2)) continue;

            $vertex1 = $this->graph->getVertexById($vertexId1);
            if($vertex1 == NULL){
                $vertex1 = new Station($vertexId1);
                $vertex1->setName($stations[$vertexId1]["name"]);
                $vertex1->setLine($stations[$vertexId1]["line"]);
            }
            $vertex2 = $this->graph->getVertexById($vertexId2);
            if($vertex2 == NULL){
                $vertex2 = new Station($vertexId2);
                $vertex2->setName($stations[$vertexId2]["name"]);
                $vertex2->setLine($stations[$vertexId2]["line"]);
            }
            $vertex1->connect($vertex2);
            $vertex2->connect($vertex1);

            $this->graph->setEdge(new Edge($vertex1, $vertex2, $minutes));
            $this->graph->setEdge(new Edge($vertex2, $vertex1, $minutes));
            $this->graph->setVertex($vertex1);
            $this->graph->setVertex($vertex2);
        }
    }
}
