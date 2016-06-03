<?php

namespace Modules\IzCore\Repositories;


use Modules\IzCore\Repositories\Object\DataObject;
use Pingpong\Modules\Repository;

class Module extends DataObject {

    /**
     * Config all modules
     *
     * @var array
     */
    protected $configs = [];

    /**
     * Assets all modules
     *
     * @var
     */
    protected $assets;
    /**
     * @var \Pingpong\Modules\Repository
     */
    protected $module;

    /**
     * Asset constructor.
     *
     * @param \Pingpong\Modules\Repository $module
     * @param array                        $data
     */
    public function __construct(
        Repository $module,
        array $data = []
    ) {
        $this->module = $module;
        parent::__construct($data);
    }

    /**
     * Get all assets from all modules
     *
     * @return array
     * @throws \Exception
     */
    public function getAssets() {

        if (!isset($isSupport))
            throw new \Exception('Please get From Theme');

        if (is_null($this->assets)) {

            $this->assets = [];

            foreach ($this->module->getByStatus(1) as $module) {
                /** @var \Pingpong\Modules\Module $module */
                if ($asset = $this->getAssetsByModuleName($module->getName()))
                    $this->assets = array_merge($this->assets, $asset);

            }
        }

        return $this->assets;
    }

    /**
     * @param $name
     *
     * @return bool
     */
    public function getAssetsByModuleName($name) {
        if (isset($this->getConfigByModuleName($name)['assets']))
            return $this->getConfigByModuleName($name)['assets'];
        else return false;
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function getConfigByModuleName($name) {
        if (!isset($this->configs[$name]))
            $this->configs[$name] = config($name);

        return $this->configs[$name];
    }
}