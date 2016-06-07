<?php
namespace Modules\IzCore\Repositories;

use Modules\IzCore\Repositories\CoreConfig\ConfigInterface;

/**
 * Class IzAdminConfigProvider
 * Generate config to iz admin app and frontend
 * Have 3 path: global, admin, frontend.
 * In Admin include 3 path, but in frontend just have: global and frontend
 *
 * @package Modules\IzCore\Respositories
 */
class CoreConfig {

    /**
     * @var array
     */
    protected $izConfigProvider = [];

    protected $izConfigResolved;

    /**
     * Add config to Provider
     * Class add must instance of AdminConfig
     * If not set configName will add to global
     * Each config name can have multiple config. Will merger by priority
     *
     * @param        $className
     * @param string $configName
     *
     * @param int    $priority
     *
     * @return $this
     * @throws \Exception
     */
    public function addConfigProvider($className, $configScopeName = 'global', $priority = 0) {
        if (!in_array($configScopeName, ['global', 'frontend', 'admin'])) {
            throw new \Exception('configName must in: global, frontend, admin');
        }
        if (!isset($this->izConfigProvider[$configScopeName])) {
            $this->izConfigProvider[$configScopeName] = [];
        }
        $this->izConfigProvider[$configScopeName][] = [
            'className' => $className,
            'priority'  => $priority
        ];

        return $this;
    }

    /**
     * Get Config by $configName. Maybe all, frontend
     *
     * @param string $configScopeName
     *
     * @return array
     * @internal param string $configName
     *
     */
    public function initConfig($configScopeName = 'all') {
        if (is_null($this->izConfigResolved)) {
            $this->izConfigResolved = [];
            foreach ($this->izConfigProvider as $configName => $arrayConfigs) {
                if (!isset($this->izConfigResolved[$configName]))
                    $this->izConfigResolved[$configName] = [];
                $newArrangedArrayConfig = $this->arrangeArrayAdminConfig($arrayConfigs);
                foreach ($newArrangedArrayConfig as $instanceClass) {
                    /** @var ConfigInterface $instance */
                    $instance                            = app()->make($instanceClass['className']);
                    $this->izConfigResolved[$configName] = array_merge($this->izConfigResolved[$configName], $instance->handle());
                }
                $this->izConfigResolved[$configName] = json_encode($this->izConfigResolved[$configName]);
            }
        }
        if (in_array($configScopeName, ['all', 'admin']))
            return $this->izConfigResolved;
        if ($configScopeName == 'frontend') {
            $data = [];
            if (isset($this->izConfigResolved['fronted']))
                $data['frontend'] = $this->izConfigResolved['fronted'];
            if (isset($this->izConfigResolved['global']))
                $data['frontend'] = $this->izConfigResolved['global'];

            return $data;
        }
        else
            return $this->izConfigResolved['global'];
    }

    /**
     * Arrange array admin config by priority
     *
     * @param array $adminConfigs
     * @param int   $NKey
     *
     * @return array
     */
    private function arrangeArrayAdminConfig(array &$adminConfigs, $NKey = 1) {
        foreach ($adminConfigs as $key => $adminConfig) {
            if ($key >= (count($adminConfigs) - $NKey))
                break;
            if ($adminConfig['priority'] > $adminConfigs[$key + 1]['priority']) {
                $adminConfigs[$key]     = $adminConfigs[$key + 1];
                $adminConfigs[$key + 1] = $adminConfig;

                return $this->arrangeArrayAdminConfig($adminConfigs, $NKey);
            }
        }

        if ($NKey < (count($adminConfigs) - 1))
            $this->arrangeArrayAdminConfig($adminConfigs, $NKey + 1);

        return $adminConfigs;
    }
}