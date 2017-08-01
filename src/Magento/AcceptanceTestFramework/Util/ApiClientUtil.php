<?php

namespace Magento\AcceptanceTestFramework\Util;

class ApiClientUtil
{
    private $curl;
    private $apiPath;
    private $headers;
    private $apiOperation;
    private $jsonBody;

    public function __construct($apiPath, $headers, $apiOperation, $jsonBody)
    {
        $this->apiPath = $apiPath;
        $this->headers = $headers;
        $this->apiOperation = $apiOperation;
        $this->jsonBody = $jsonBody;

        $this->curl = curl_init();
    }

    public function submit($verbose = false)
    {
        if ($this->jsonBody) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->jsonBody);
        }

        curl_setopt($this->curl, CURLOPT_VERBOSE, $verbose);

        curl_setopt_array($this->curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_CUSTOMREQUEST => $this->apiOperation,
            CURLOPT_URL => HOSTNAME . ':' .  PORT . $this->apiPath
        ));

        $response = curl_exec($this->curl);
        curl_close($this->curl);

        return $response;
    }
}
