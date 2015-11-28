<?php
namespace Upgle\Model;

class Station extends Vertex
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $line;

    /**
     * @var double
     */
    private $latitude;

    /**
     * @var bool
     */
    private $transferStation = false;

    /**
     * @return float
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @return boolean
     */
    public function isTransferStation()
    {
        return $this->transferStation;
    }

    /**
     * @param boolean $transferStation
     */
    public function setTransferStation($transferStation)
    {
        $this->transferStation = $transferStation;
    }

    /**
     * @return float
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @var double
     */
    private $longitude;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param string $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }

}
