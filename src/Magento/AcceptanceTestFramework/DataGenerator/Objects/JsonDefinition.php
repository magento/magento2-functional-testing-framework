<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\DataGenerator\Objects;

/**
 * Class JsonDefinition
 */
class JsonDefinition
{
    /**
     * Name of entity definition.
     *
     * @var string
     */
    private $name;

    /**
     * Operation.
     *
     * @var string
     */
    private $operation;

    /**
     * Data type.
     *
     * @var string
     */
    private $dataType;

    /**
     * HTTP Request method.
     *
     * @var string
     */
    private $apiMethod;

    /**
     * Application base url.
     *
     * @var string
     */
    private $baseUrl;

    /**
     * API url.
     *
     * @var string
     */
    private $apiUrl;

    /**
     * Authentication.
     *
     * @var string
     */
    private $auth;

    /**
     * HTTP request headers.
     *
     * @var array
     */
    private $headers = [];

    /**
     * Request parameters.
     *
     * @var array
     */
    private $params = [];

    /**
     * Entity metadata.
     *
     * @var array
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
     * Returns data type.
     *
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Returns operation.
     *
     * @return string
     */
    public function getOperation()
    {
        return $this->operation;
    }

    /**
     * Returns HTTP request method.
     *
     * @return string
     */
    public function getApiMethod()
    {
        return $this->apiMethod;
    }

    /**
     * Returns API url.
     *
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
     * Returns auth key.
     *
     * @return string
     */
    public function getAuth()
    {
        return $this->auth;
    }

    /**
     * Returns request headers.
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Returns entity metadata.
     *
     * @return array
     */
    public function getJsonMetadata()
    {
        return $this->jsonMetadata;
    }

    /**
     * Cleans api url.
     *
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
     * Adding path params.
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
     * Adding query params.
     *
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
