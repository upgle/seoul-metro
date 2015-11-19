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
