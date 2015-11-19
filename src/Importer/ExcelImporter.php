<?php
namespace Upgle\Importer;

use Upgle\Model\Edge;
use Upgle\Repositories\EdgeRepositories;
use Upgle\Repositories\VertexRepository;
use Upgle\Model\Vertex;

class ExcelImporter
{
    /**
     * @var VertexRepository
     */
    protected $vertexs;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var EdgeRepositories
     */
    protected $edges;

    /**
     * @param $filePath
     * @param VertexRepository $vertexs
     * @param EdgeRepositories $edges
     */
    public function __construct($filePath,  VertexRepository $vertexs, EdgeRepositories $edges)
    {
        $this->file = $filePath;
        $this->vertexs = $vertexs;
        $this->edges = $edges;
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
            $vertex1 = $this->vertexs->get($vertexName1);
            if($vertex1 == NULL){
                $vertex1 = new Vertex($vertexName1);
            }

            $vertex2 = $this->vertexs->get($vertexName2);
            if($vertex2 == NULL){
                $vertex2 = new Vertex($vertexName2);
            }
            $vertex1->connect($vertex2);
            $vertex2->connect($vertex1);

            $this->edges->set(new Edge($vertex1, $vertex2, $minutes));
            $this->edges->set(new Edge($vertex2, $vertex1, $minutes));

            $this->vertexs->set($vertex1->getName(), $vertex1);
            $this->vertexs->set($vertex2->getName(), $vertex2);
        }
    }
}
