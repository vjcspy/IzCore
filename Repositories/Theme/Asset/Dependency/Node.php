<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 28/05/2016
 * Time: 08:47
 */

namespace Modules\IzCore\Repositories\Theme\Asset\Dependency;


class Node {

    protected $name;
    protected $edges;

    /**
     * Node constructor.
     *
     * @param $name
     * @param $edges
     */
    public function __construct($name, $edges = []) {
        $this->name  = $name;
        $this->edges = $edges;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getEdges() {
        return $this->edges;
    }

    /**
     * @param mixed $edges
     */
    public function setEdges($edges) {
        $this->edges = $edges;
    }

    public function addEdge(Node $edge) {
        if (!array_key_exists($edge->getName(), $this->edges))
            $this->edges[$edge->getName()] = $edge;
    }


}