<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Objects;

class JsonDefinition
{
    /**
     * Json Definitions Name
     * @var string $name
     */
    private $name;

    /**
     * Operation which the json defintion describes
     * @var string $operation
     */
    private $operation;

    /**
     * Data type for which the json defintiion is used
     * @var string $dataType
     */
    private $dataType;

    /**
     * Api method such as ('POST', 'PUT', 'GET', DELETE', etc.)
     * @var string $apiMethod
     */
    private $apiMethod;

    /**
     * Base URL for the request
     * @var string $baseUrl
     */
    private $baseUrl;

    /**
     * Resource specific URI for the request
     * @var string $apiUrl
     */
    private $apiUrl;

    /**
     * Authorization path for retrieving a token
     * @var string $auth
     */
    private $auth;

    /**
     * Relevant headers for the request
     * @var array $headers
     */
    private $headers = [];

    /**
     * Relevant params for the request (e.g. query, path)
     * @var array $params
     */
    private $params = [];

    /**
     * The metadata describing the json fields and values themselves
     * @var array $jsonMetadata
     */
    private $jsonMetadata = [];

    /**
     * JsonDefinition constructor.
     * @param string $name
     * @param string $operation
     * @param string $dataType
     * @param string $apiMethod
     * @param string $apiUrl
     * @param string $auth
     * @param array $headers
     * @param array $params
     * @param array $jsonMetadata
     */
    public function __construct(
        $name,
        $operation,
        $dataType,
        $apiMethod,
        $apiUrl,
        $auth,
        $headers,
        $params,
        $jsonMetadata
    ) {
        $this->name = $name;
        $this->operation = $operation;
        $this->dataType = $dataType;
        $this->apiMethod = $apiMethod;
        $this->baseUrl = $apiUrl;
        $this->auth = $auth;
        $this->headers = $headers;
        $this->params = $params;
        $this->jsonMetadata = $jsonMetadata;
    }

    /**
     * Getter for json data type
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Getter for json operation
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Getter for api method
     * @return string
     */
    public function getApiMethod()
    {
        return $this->apiMethod;
    }

    /**
     * Getter for api url
     * @return string
     */
    public function getApiUrl()
    {
        $this->cleanApiUrl();

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
     * @return string
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Getter for request headers
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Getter for json metadata
     * @return array
     */
    public function getJsonMetadata()
    {
        return $this->jsonMetadata;
    }

    /**
     * Function to validate api format and add "/" char where necessary
     * @return void
     */
    private function cleanApiUrl()
    {
        if (substr($this->baseUrl, -1) == "/") {
            $this->apiUrl = rtrim($this->baseUrl, "/");
        } else {
            $this->apiUrl = $this->baseUrl;
        }
    }

    /**
     * Function to append path params where necessary
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
     * @return void
     */
    private function addQueryParams()
    {

        foreach ($this->params['query'] as $paramName => $paramValue) {
            if (!stringContains("?", $this->apiUrl)) {
                $this->apiUrl = $this->apiUrl . "?";
            } else {
                $this->apiUrl = $this->apiUrl . "&";
            }

            $this->apiUrl = $paramName . "=" . $paramValue;
        }
    }

}
