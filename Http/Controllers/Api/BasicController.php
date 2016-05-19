<?php
/**
 * Created by PhpStorm.
 * User: vjcspy
 * Date: 18/05/2016
 * Time: 18:01
 */

namespace Modules\IzCore\Http\Controllers\Api;

use Pingpong\Modules\Routing\Controller;
use Illuminate\Http\Request;
use Response;

abstract class BasicController extends Controller {


    const STATUS_BAD_REQUEST = 400;
    const STATUS_NOT_MODIFIED = 304;
    const STATUS_CREATED = 201;
    const STATUS_UNAUTHORIZED = 401;
    const STATUS_404 = 404;
    /**
     * @var
     */
    protected $_responseData = [];

    /**
     * @var
     */
    protected $_responseCode = 200;

    /**
     * @return mixed
     */
    public function getResponseData() {
        return $this->_responseData;
    }

    /**
     * @param mixed $responseData
     */
    public function setResponseData($responseData) {
        $this->_responseData = $responseData;
    }

    /**
     * @return mixed
     */
    public function getResponseCode() {
        return $this->_responseCode;
    }

    /**
     * @param mixed $responseCode
     */
    public function setResponseCode($responseCode) {
        $this->_responseCode = $responseCode;
    }

    /**
     * Tra ve request data
     *
     * @param Request $reqest
     *
     * @return array
     */
    public function getRequestData(Request $reqest) {
        return $reqest->all();
    }

    /**
     * Tra ve nhung response co data la json
     *
     * @return mixed
     */
    public function responseJson() {
        return Response::json($this->_responseData, $this->_responseCode);
    }

    public function setErrorData($error, $status = self::STATUS_BAD_REQUEST) {
        $this->setResponseCode($status);
        $this->_responseData = [
            'error' => true,
            'mess'  => $error
        ];

        return $this;
    }
}
