<?php
namespace Upgle\Support;

use Upgle\Model\Station;

class GoogleMap
{
    /**
     * @var Station[] | array
     */
    private $path;

    /**
     * default Latitude (Seoul)
     */
    const defaultLatitude = 37.55982;

    /**
     * default Longitude (Seoul)
     */
    const defaultLongitude = 126.989143;

    /**
     * @var float
     */
    private $centerLatitude = GoogleMap::defaultLatitude;

    /**
     * @var float
     */
    private $centerLongitude = GoogleMap::defaultLongitude;


    /**
     * GoogleMap constructor.
     * @param array $path
     */
    public function __construct(array $path)
    {
        $this->path = $path;
        $this->setCenter();
    }

    /**
     * set center using path
     */
    private function setCenter() {
        $count = count($this->path);
        if($count > 0)  {
            $lastIndex = $count-1;
            $latitude = ($this->path[0]->getLatitude() + $this->path[$lastIndex]->getLatitude()) / 2;
            $longitude = ($this->path[0]->getLongitude() + $this->path[$lastIndex]->getLongitude()) / 2;
            $this->centerLatitude = $latitude;
            $this->centerLongitude = $longitude;
        }
    }

    /**
     * get center
     * @return array
     */
    public function getCenter() {
        return [
            "latitude" => $this->centerLatitude,
            "longitude" => $this->centerLongitude
        ];
    }


}
