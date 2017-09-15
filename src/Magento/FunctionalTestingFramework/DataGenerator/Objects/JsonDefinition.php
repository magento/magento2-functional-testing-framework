<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Objects;

/**
 * Class JsonDefinition
 */
class JsonDefinition
{
    /**
     * Json Definitions Name
     *
     * @var string
     */
    private $name;

    /**
     * Operation which the json defintion describes
     *
     * @var string
     */
    private $operation;

    /**
     * Data type for which the json defintiion is used
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
     * The metadata describing the json fields and values themselves
     *
     * @var array
     */
    private $jsonMetadata = [];

    /**
     * Store code in api url.
     *
     * @var string
     */
    private $apiStoreCode;

    /**
     * JsonDefinition constructor.
     * @param string $name
     * @param string $operation
     * @param string $dataType
     * @param string $apiMethod
     * @param string $apiUri
     * @param string $auth
     * @param array $headers
     * @param array $params
     * @param array $jsonMetadata
     * @param string $apiStoreCode
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
        $jsonMetadata,
        $apiStoreCode = 'default'
    ) {
        $this->name = $name;
        $this->operation = $operation;
        $this->dataType = $dataType;
        $this->apiMethod = $apiMethod;
        $this->apiUri = $apiUri;
        $this->auth = $auth;
        $this->headers = $headers;
        $this->params = $params;
        $this->jsonMetadata = $jsonMetadata;
        $this->apiStoreCode = $apiStoreCode;
        $this->apiUrl = null;
    }

    /**
     * Getter for json data type
     *
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Getter for json operation
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
     * Getter for api url
     *
     * @return string
     */
    public function getApiUrl()
    {
        $this->apiUrl = '/rest/' . $this->apiStoreCode . '/' . trim($this->apiUri, '/');

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
        return '/rest/' . $this->apiStoreCode . '/' . trim($this->auth, '/');
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
     * Getter for json metadata
     *
     * @return array
     */
    public function getJsonMetadata()
    {
        return $this->jsonMetadata;
    }

    /**
     * Set store code.
     *
     * @param $newStoreCode
     * @return void
     */
    public function setStoreCode($newStoreCode)
    {
        $this->apiStoreCode = $newStoreCode;
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
