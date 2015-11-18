<?php
namespace Upgle\Importer;

use Upgle\Repositories\VertexRepository;
use Upgle\Repositories\WeightRepositoryInterface;
use Upgle\Model\Vertex;

class ExcelImporter
{
    /**
     * @var WeightRepositoryInterface
     */
    protected $minutes;

    /**
     * @var WeightRepositoryInterface
     */
    protected $km;

    /**
     * @var VertexRepository
     */
    protected $vertexs;

    /**
     * @var string
     */
    protected $file;

    /**
     * ExcelImporter constructor
     * @param $filePath
     * @param VertexRepository $vertexs
     * @param WeightRepositoryInterface $minutes
     * @param WeightRepositoryInterface $km
     */
    public function __construct($filePath,  VertexRepository $vertexs, WeightRepositoryInterface $minutes, WeightRepositoryInterface $km)
    {
        $this->file = $filePath;
        $this->minutes = $minutes;
        $this->km = $km;
        $this->vertexs = $vertexs;
    }

    public function import()
    {
        $objPHPExcel = \PHPExcel_IOFactory::load($this->file);
        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator(2);
        $vertexs = [];

        foreach($rowIterator as $row) {

            $vertexName1 = NULL;
            $vertexName2 = NULL;
            $minutes = 0;
            $km = 0;
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
            $vertex1 = $this->vertexs->getVertex($vertexName1);
            if($vertex1 == NULL){
                $vertex1 = new Vertex($vertexName1);
            }

            $vertex2 = $this->vertexs->getVertex($vertexName2);
            if($vertex2 == NULL){
                $vertex2 = new Vertex($vertexName2);
            }

            $vertex1->connectVertex($vertex2);
            $vertex2->connectVertex($vertex1);

            $this->vertexs->setVertex($vertex1->getName(), $vertex1);
            $this->vertexs->setVertex($vertex2->getName(), $vertex2);

            $this->minutes->setWeight($vertex1, $vertex2, $minutes);
            $this->minutes->setWeight($vertex2, $vertex1, $minutes);

            $this->km->setWeight($vertex1, $vertex2, $km);
            $this->km->setWeight($vertex2, $vertex1, $km);
        }
    }
}
