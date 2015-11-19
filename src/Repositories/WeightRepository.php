<?php
namespace Upgle\Repositories;

use Upgle\Model\Vertex;

class WeightRepository implements WeightRepositoryInterface
{
    private $weights = [];

    public function setWeight(Vertex $v1, Vertex $v2, $weight) {
        $this->weights[$v1->getName()][$v2->getName()] = (int)$weight;
    }

    public function getWeight(Vertex $v1, Vertex $v2) {
        return $this->weights[$v1->getName()][$v2->getName()];
    }

    public function getWeights() {
        return $this->weights;
    }
}
