<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\Util;

class ApiClientUtil
{
    /**
     * Curl resource for request execution
     *
     * @var resource $curl
     */
    private $curl;

    /**
     * Api Path for the request
     *
     * @var string $apiPath
     */
    private $apiPath;

    /**
     * Headers to be included in the request
     *
     * @var array $headers
     */
    private $headers;

    /**
     * An HTTP Request Type (e.g. CREATE, GET, POST, DELETE)
     *
     * @var string $apiOperation
     */
    private $apiOperation;

    /**
     * The json body to be submitted in the request
     *
     * @var string $jsonBody
     */
    private $jsonBody;

    /**
     * ApiClientUtil constructor.
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
     *
     * @param bool $verbose
     * @return string|bool
     */
    public function submit($verbose = false)
    {
        if ($this->jsonBody) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->jsonBody);
        }

        curl_setopt($this->curl, CURLOPT_VERBOSE, $verbose);

        curl_setopt_array($this->curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_CUSTOMREQUEST => $this->apiOperation,
            CURLOPT_URL => HOSTNAME . ':' .  PORT . $this->apiPath
        ]);

        try {
            $response = curl_exec($this->curl);
            if ($response === false) {
                throw new \Exception(curl_error($this->curl), curl_errno($this->curl));
            }
        } catch (\Exception $e) {
            trigger_error(
                sprintf(
                    'Curl failed with error #%d: %s',
                    $e->getCode(),
                    $e->getMessage()
                ),
                E_USER_ERROR
            );
        }

        curl_close($this->curl);

        return $response;
    }
}
