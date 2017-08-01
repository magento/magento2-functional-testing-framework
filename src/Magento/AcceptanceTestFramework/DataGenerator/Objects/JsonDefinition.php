<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Objects;

class JsonDefinition
{
    private $name;
    private $operation;
    private $dataType;
    private $apiMethod;
    private $baseUrl;
    private $apiUrl;
    private $auth;
    private $headers = [];
    private $params = [];
    private $jsonMetadata = [];

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

    public function getDataType()
    {
        return $this->dataType;
    }

    public function getOperation()
    {
        return $this->operation;
    }

    public function getApiMethod()
    {
        return $this->apiMethod;
    }

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

    public function getAuth()
    {
        return $this->auth;
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getJsonMetadata()
    {
        return $this->jsonMetadata;
    }

    private function cleanApiUrl()
    {
        if (substr($this->baseUrl, -1) == "/") {
            $this->apiUrl = rtrim($this->baseUrl, "/");
        } else {
            $this->apiUrl = $this->baseUrl;
        }
    }

    private function addPathParam()
    {
        foreach ($this->params['path'] as $paramName => $paramValue) {
            $this->apiUrl = $this->apiUrl . "/" . $paramValue;
        }
    }

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
