<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 28/05/2016
 * Time: 08:47
 */

namespace Modules\IzCore\Repositories\Theme\Asset;


use Modules\IzCore\Repositories\Module;
use Modules\IzCore\Repositories\Theme;
use Modules\IzCore\Repositories\Theme\Asset\Dependency\Node;

class Dependency {

    /**
     * Luu tung dependency
     * @var array
     */
    protected $nodes = [];
    /**
     * @var \Modules\IzCore\Repositories\Module
     */
    protected $izModule;


    /**
     * Thu tu dependency sau khi da load depen
     * @var array
     */
    protected $resolved = [];
    /**
     * @var array
     */
    protected $unresolved = [];
    /**
     * @var \Modules\IzCore\Repositories\Theme
     */
    protected $izTheme;

    public function __construct(
        Module $izModule,
        Theme $izTheme
    ) {
        $this->izModule = $izModule;
        $this->izTheme  = $izTheme;
    }

    /**
     * @return array
     */
    public function getResolved() {
        return $this->resolved;
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
        return isset($this->izTheme->getAssetsTree()[$assetName]) ? $this->izTheme->getAssetsTree()[$assetName] : false;
    }

    /**
     * Chuyển tất cả các assets về dạng node để resolve dependency
     *
     * @return $this
     * @throws \Exception
     */
    protected function processModuleAssetsToNode() {
        //init note
        foreach ($this->izTheme->getAssetsTree() as $assetName => $assetInfo) {
            $this->nodes[$assetName] = new Node($assetName);
        }

        //init dependency
        foreach ($this->izTheme->getAssetsTree() as $assetName => $assetInfo) {

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