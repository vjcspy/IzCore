<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 28/05/2016
 * Time: 08:47
 */

namespace Modules\IzCore\Repositories\Theme\Asset;


use Modules\IzCore\Repositories\Module;
use Modules\IzCore\Repositories\Theme\Asset\Dependency\Node;

class Dependency {

    protected $nodes = [];
    protected $izModule;

    /**
     * @return array
     */
    public function getResolved() {
        return $this->resolved;
    }

    protected $resolved = [];
    protected $unresolved = [];

    public function __construct(Module $izModule) {
        $this->izModule = $izModule;
    }

    /**
     * Tìm thứ tự cài đặt của assets
     * Lưu ý là sẽ phân tích toàn bộ assets trong modules. Bởi vì có thể chúng phụ thuộc lẫn nhau ngoài những cái mà người dùng yêu cầu cài.
     *
     * @param array $assets
     *
     * @return $this
     * @throws \Exception
     */
    public function processDependency(array $assets) {
        $resolved = $this->processModuleAssetsToNode()->findDepResolve()->getResolved();
        $diff     = array_diff($resolved, $assets);
        foreach ($diff as $key => $name) {
            unset($resolved[$key]);
        }

        return $resolved;
    }

    protected function findDepResolve() {
        foreach ($this->nodes as $nodeName => $nodeInfo) {
            $this->depResolve($nodeInfo);
        }

        return $this;
    }

    protected function depResolve(Node $node) {

        $this->unresolved[] = $node->getName();

        /** @var Node $edge */
        foreach ($node->getEdges() as $edge) {

            if (!in_array($edge, $this->resolved)) {

                if (in_array($edge->getName(), $this->unresolved))
                    throw new \Exception('Circular reference detected: ' . $node->getName() . ' AND ' . $edge->getName());
                $this->depResolve($edge);
            }

        }

        if (!in_array($node->getName(), $this->resolved))
            $this->resolved[] = $node->getName();

        $this->unresolved = array_diff($this->unresolved, [$node->getName()]);
    }

    /**
     * Get Asset information from modules configs
     *
     * @param $assetName
     *
     * @return bool
     */
    protected function getInfoAsset($assetName) {
        return isset($this->izModule->getAssets()[$assetName]) ? $this->izModule->getAssets()[$assetName] : false;
    }

    /**
     * Chuyển tất cả các assets về dạng node để resolve dependency
     *
     * @return $this
     * @throws \Exception
     */
    protected function processModuleAssetsToNode() {
        //init note
        foreach ($this->izModule->getAssets() as $assetName => $assetInfo) {
            $this->nodes[$assetName] = new Node($assetName);
        }

        //init dependency
        foreach ($this->izModule->getAssets() as $assetName => $assetInfo) {

            if (isset($assetInfo['dependency']) && is_array($assetInfo['dependency']) && count($assetInfo['dependency']) > 0)
                foreach ($assetInfo['dependency'] as $dep) {
                    if (isset($this->nodes[$dep]))
                        $this->nodes[$assetName]->addEdge($this->nodes[$dep]);
                    else
                        throw new \Exception("Not found dependency: " . $dep);
                }
        }

        return $this;
    }
}