<?php
namespace Upgle\Repositories;

use Upgle\Model\Vertex;

interface WeightRepositoryInterface
{
    public function setWeight(Vertex $v1, Vertex $v2, $weight);

    public function getWeight(Vertex $v1, Vertex $v2);

    public function getWeights();

}
