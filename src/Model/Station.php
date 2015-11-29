<?php
namespace Upgle\Model;

class Station extends Vertex
{
    public static $LINE = [
        "1" => "1호선",
        "2" => "2호선",
        "3" => "3호선",
        "4" => "4호선",
        "5" => "5호선",
        "6" => "6호선",
        "7" => "7호선",
        "8" => "8호선",
        "9" => "9호선",
        "U" => "의정부경전철",
        "SU" => "수인선",
        "S" => "신분당선",
        "K" => "경의중앙선",
        "I" => "인천1호선",
        "G" => "경춘선",
        "E" => "에버라인",
        "B" => "분당선",
        "A" => "공항철도"
    ];

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
