<?php
/**
 * Created by IntelliJ IDEA.
 * User: vjcspy
 * Date: 6/8/16
 * Time: 10:52 AM
 */

namespace Modules\IzCore\Repositories;


use Modules\IzCore\Repositories\Object\IzObject;
use Pingpong\Modules\Repository;

/**
 * Work with XML Theme
 * Class IzXml
 *
 * @package Modules\IzCore\Repositories
 */
class IzXml extends IzObject {

    /**
     * All xml file in current theme
     *
     * @var array
     */
    protected $xmlThemeData = [];
    /**
     * @var \Pingpong\Modules\Repository
     */
    protected $module;

    /**
     * IzXml constructor.
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
     * Param is current path of router include layout name.
     * Function only search in current theme.
     * Current theme will get from cache or database.
     *
     * @param $path
     *
     * @return mixed
     */
    public function getXmlByPath($path) {
        $key = $this->getKey(['xml', 'layout_router_action', $path]);
        if (is_null($this->getData($key))) {
            /*Get dir path of modules*/
            $this->setData($key, isset($this->scanThemeXml()[$path]) ? $this->scanThemeXml()[$path] : []);
        }

        return $this->getData($key);
    }

    /**
     * Retrieve all file xml in all theme but just in current theme
     * FIXME: Need cache here
     *
     * @return mixed
     */
    protected function scanThemeXml() {
        $key = $this->getKey(['retrieve', 'all-xml', 'in', 'all-modules', 'only', 'current-theme']);
        if (is_null($this->getData($key))) {
            $pathModules       = $this->module->getPath();
            $moduleDirectories = scandir($pathModules);

            foreach ($moduleDirectories as $moduleDir) {
                if (!in_array($moduleDir, [".", ".."])) {
                    /*Xml path  của module hiện tại*/
                    $currentFolderXmlPathInModule = $pathModules . '/' . $moduleDir . '/themes/' . $this->getCurrentTheme() . '/xml';

                    /*Check xml path file exist*/
                    if (!file_exists($currentFolderXmlPathInModule))
                        continue;

                    $xmlFiles = scandir($currentFolderXmlPathInModule);

                    foreach ($xmlFiles as $xmlFile) {
                        if (!in_array($xmlFile, [".", ".."])) {

                            if (pathinfo($xmlFile, PATHINFO_EXTENSION) !== 'xml')
                                continue;

                            $currentXMLDir = $currentFolderXmlPathInModule . '/' . $xmlFile;
                            $xml           = simplexml_load_file($currentXMLDir);

                            $xmlFile = preg_replace('/\\.[^.\\s]{3,4}$/', '', $xmlFile);

                            // May have many xml for this path in themes each modules
                            if (!isset($this->xmlThemeData[$xmlFile]))
                                $this->xmlThemeData[$xmlFile] = [];

                            foreach ($xml as $node => $scope) {
                                switch ($node) {
                                    case 'bower_components':
                                        $this->convertXmlScopeBowerComponent($scope, $xmlFile);
                                        break;
                                    case 'custom_assets':
                                        $this->convertXmlScopeCustomAssets($scope, $xmlFile);
                                        break;
                                    case 'view_data':
                                        $this->convertXmlScopeViewData($scope, $xmlFile);
                                        break;
                                    case 'app_dependencies':
                                        $this->convertXmlScopeAppDependency($scope, $xmlFile);
                                        break;
                                }
                            }
                        }
                    }
                }
            }
            $this->setData($key, $this->xmlThemeData);
        }

        return $this->getData($key);
    }

    /**
     * @param $xml
     * @param $xmlFile
     *
     * @return $this
     */
    private function convertXmlScopeBowerComponent($xml, $xmlFile) {
        if (!isset($this->xmlThemeData[$xmlFile]['bower_components']))
            $this->xmlThemeData[$xmlFile]['bower_components'] = [];

        foreach ($xml->asset as $item) {
            $itemsAttribute = [];
            foreach ($item->attributes() as $attrName => $attrValue) {
                $itemsAttribute[$attrName] = (string)$attrValue;
            }
            $this->xmlThemeData[$xmlFile]['bower_components'][] = $itemsAttribute;
        }

        return $this;
    }

    /**
     * @param $xml
     * @param $xmlFile
     *
     * @return $this
     */
    private function convertXmlScopeCustomAssets($xml, $xmlFile) {
        if (!isset($this->xmlThemeData[$xmlFile]['custom_assets']))
            $this->xmlThemeData[$xmlFile]['custom_assets'] = [];

        foreach ($xml->asset as $item) {
            $itemsAttribute = [];
            foreach ($item->attributes() as $attrName => $attrValue) {
                $itemsAttribute[$attrName] = (string)$attrValue;
            }
            $this->xmlThemeData[$xmlFile]['custom_assets'][] = $itemsAttribute;
        }

        return $this;
    }

    /**
     * @param $xml
     * @param $xmlFile
     *
     * @return $this
     */
    private function convertXmlScopeViewData($xml, $xmlFile) {
        if (!isset($this->xmlThemeData[$xmlFile]['view_data']))
            $this->xmlThemeData[$xmlFile]['view_data'] = [];

        foreach ($xml->instance as $item) {
            $itemsAttribute = [];
            foreach ($item->attributes() as $attrName => $attrValue) {
                $itemsAttribute[$attrName] = (string)$attrValue;
            }
            $this->xmlThemeData[$xmlFile]['view_data'][] = $itemsAttribute;
        }

        return $this;
    }

    /**
     * @param $xml
     * @param $xmlFile
     * @param $node
     *
     * @return $this
     */
    private function convertXmlScopeAppDependency($xml, $xmlFile) {
        if (!isset($this->xmlThemeData[$xmlFile]['app_dependencies']))
            $this->xmlThemeData[$xmlFile]['app_dependencies'] = [];

        foreach ($xml->dependency as $item) {
            $itemsAttribute = [];
            foreach ($item->attributes() as $attrName => $attrValue) {
                $itemsAttribute[$attrName] = (string)$attrValue;
            }
            $this->xmlThemeData[$xmlFile]['app_dependencies'][] = $itemsAttribute;
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrentTheme() {
        return 'admin.default';
    }

}