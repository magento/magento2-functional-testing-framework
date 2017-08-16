<?php

namespace Magento\AcceptanceTestFramework\Util;

class ApiClientUtil
{
    /**
     * curl resource for request execution
     * @var resource $curl
     */
    private $curl;

    /**
     * Api Path for the request
     * @var string $apiPath
     */
    private $apiPath;

    /**
     * Headers to be included in the request
     * @var array $headers
     */
    private $headers;

    /**
     * An HTTP Request Type (e.g. CREATE, GET, POST, DELETE)
     * @var string $apiOperation
     */
    private $apiOperation;

    /**
     * The json body to be submitted in the request
     * @var string $jsonBody
     */
    private $jsonBody;


    /**
     * ApiClientUtil constructor.
     * @constructor
     * @param string $apiPath
     * @param array $headers
     * @param string $apiOperation
     * @param string $jsonBody
     */
    public function __construct($apiPath, $headers, $apiOperation, $jsonBody)
    {
        $this->apiPath = $apiPath;
        $this->headers = $headers;
        $this->apiOperation = $apiOperation;
        $this->jsonBody = $jsonBody;

        $this->curl = curl_init();
    }

    /**
     * Submits the request based on object properties
     * @param bool $verbose
     * @return string | false
     */
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
