<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\Util;

class ApiClientUtil
{
    /**
     * Curl resource.
     *
     * @var resource
     */
    private $curl;

    /**
     * Api path.
     *
     * @var string
     */
    private $apiPath;

    /**
     * Headers.
     *
     * @var array
     */
    private $headers;

    /**
     * API operations.
     *
     * @var array
     */
    private $apiOperation;

    /**
     * Json body.
     *
     * @var string
     */
    private $jsonBody;

    /**
     * ApiClientUtil constructor.
     * @param string $apiPath
     * @param array $headers
     * @param array $apiOperation
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
     * Submit API request.
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

        $response = curl_exec($this->curl);
        curl_close($this->curl);

        return $response;
    }
}
