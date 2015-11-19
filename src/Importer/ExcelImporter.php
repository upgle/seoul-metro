<?php
namespace Upgle\Importer;

use Upgle\Model\Edge;
use Upgle\Model\Graph;
use Upgle\Model\Vertex;

class ExcelImporter
{
    /**
     * @var string
     */
    protected $file;

    /**
     * @var Graph
     */
    protected $graph;


    /**
     * @param $filePath
     * @param Graph $graph
     */
    public function __construct($filePath, Graph $graph)
    {
        $this->file = $filePath;
        $this->graph = $graph;
    }

    public function import()
    {
        $objPHPExcel = \PHPExcel_IOFactory::load($this->file);
        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator(2);

        foreach($rowIterator as $row) {

            $vertexName1 = NULL;
            $vertexName2 = NULL;
            $minutes = 0;
            foreach($row->getCellIterator() as $cell) {
                /* @var $cell \PHPExcel_Cell */
                $column = $cell->getColumn();
                switch($column) {
                    case 'B' :
                        $vertexName1 = $cell->getValue();
                        break;
                    case 'C' :
                        $vertexName2 = $cell->getValue();
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

            $vertex1 = $this->graph->getVertexById($vertexName1);
            if($vertex1 == NULL){
                $vertex1 = new Vertex($vertexName1);
            }
            $vertex2 = $this->graph->getVertexById($vertexName2);
            if($vertex2 == NULL){
                $vertex2 = new Vertex($vertexName2);
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
