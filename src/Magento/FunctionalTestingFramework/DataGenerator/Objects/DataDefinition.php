<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Objects;

/**
 * Class DataDefinition
 */
class DataDefinition
{
    /**
     * Data Definitions Name
     *
     * @var string
     */
    private $name;

    /**
     * Operation which the data defintion describes
     *
     * @var string
     */
    private $operation;

    /**
     * Data type for which the data defintiion is used
     *
     * @var string
     */
    private $dataType;

    /**
     * Api method such as ('POST', 'PUT', 'GET', DELETE', etc.)
     *
     * @var string
     */
    private $apiMethod;

    /**
     * Api request url.
     *
     * @var string
     */
    private $apiUrl;

    /**
     * Resource specific URI for the request
     *
     * @var string
     */
    private $apiUri;

    /**
     * Authorization path for retrieving a token
     *
     * @var string
     */
    private $auth;

    /**
     * Relevant headers for the request
     *
     * @var array
     */
    private $headers = [];

    /**
     * Relevant params for the request (e.g. query, path)
     *
     * @var array
     */
    private $params = [];

    /**
     * The metadata describing the data fields and values themselves
     *
     * @var array
     */
    private $metaData = [];

    /**
     * Store code in api url.
     *
     * @var string
     */
    private $storeCode;

    /**
     * Regex to check for request success.
     *
     * @var string
     */
    private $successRegex;

    /**
     * Regex to grab return value from response.
     *
     * @var string
     */
    private $returnRegex;

    /**
     * DataDefinition constructor.
     * @param string $name
     * @param string $operation
     * @param string $dataType
     * @param string $apiMethod
     * @param string $apiUri
     * @param string $auth
     * @param array $headers
     * @param array $params
     * @param array $metaData
     * @param string $successRegex
     * @param string $returnRegex
     * @param string $storeCode
     */
    public function __construct(
        $name,
        $operation,
        $dataType,
        $apiMethod,
        $apiUri,
        $auth,
        $headers,
        $params,
        $metaData,
        $successRegex = null,
        $returnRegex = null,
        $storeCode = 'default'
    ) {
        $this->name = $name;
        $this->operation = $operation;
        $this->dataType = $dataType;
        $this->apiMethod = $apiMethod;
        $this->apiUri = $apiUri;
        $this->auth = $auth;
        $this->headers = $headers;
        $this->params = $params;
        $this->metaData = $metaData;
        $this->storeCode = $storeCode;
        $this->successRegex = $successRegex;
        $this->returnRegex = $returnRegex;
        $this->apiUrl = null;
    }

    /**
     * Getter for data's data type
     *
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Getter for data operation
     *
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Getter for api method
     *
     * @return string
     */
    public function getApiMethod()
    {
        return $this->apiMethod;
    }

    /**
     * Getter for api url for a store.
     *
     * @param string $storeCode
     * @return string
     */
    public function getApiUrl($storeCode)
    {
        if (isset($storeCode)) {
            $this->storeCode = $storeCode;
        }

        if (strpos($this->auth, 'Formkey') === false) {
            $this->apiUrl = '/rest/' . $this->storeCode . '/' . trim($this->apiUri, '/');
        } else {
            $this->apiUrl = trim($this->apiUri, '/') . '/';
        }

        if (array_key_exists('path', $this->params)) {
            $this->addPathParam();
        }

        if (array_key_exists('query', $this->params)) {
            $this->addQueryParams();
        }

        return $this->apiUrl;
    }

    /**
     * Getter for auth path
     *
     * @return string
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Getter for request headers
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Getter for data metadata
     *
     * @return array
     */
    public function getMetaData()
    {
        return $this->metaData;
    }

    /**
     * Getter for store code.
     *
     * @return string
     */
    public function getStoreCode()
    {
        return $this->storeCode;
    }

    /**
     * Getter for success regex.
     *
     * @return string
     */
    public function getSuccessRegex()
    {
        return $this->successRegex;
    }

    /**
     * Getter for return regex.
     *
     * @return string
     */
    public function getReturnRegex()
    {
        return $this->returnRegex;
    }

    /**
     * Function to append path params where necessary
     *
     * @return void
     */
    private function addPathParam()
    {
        foreach ($this->params['path'] as $paramName => $paramValue) {
            $this->apiUrl = $this->apiUrl . "/" . $paramValue;
        }
    }

    /**
     * Function to append query params where necessary
     *
     * @return void
     */
    private function addQueryParams()
    {
        foreach ($this->params['query'] as $paramName => $paramValue) {
            if (strpos($this->apiUrl, '?') == false) {
                $this->apiUrl = $this->apiUrl . "?";
            } else {
                $this->apiUrl = $this->apiUrl . "&";
            }

            $this->apiUrl = $this->apiUrl . $paramName . "=" . $paramValue;
        }
    }
}
