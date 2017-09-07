<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

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
     * A list of successful HTTP responses that will not trigger an exception
     *
     * @var int[] SUCCESSFUL_HTTP_CODES
     */
    const SUCCESSFUL_HTTP_CODES = [200, 201, 202, 203, 204, 205];

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
     * @throws \Exception
     */
    public function submit($verbose = false)
    {
        $url = null;

        if ($this->jsonBody) {
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->jsonBody);
        }

        curl_setopt($this->curl, CURLOPT_VERBOSE, $verbose);

        if ((getenv('MAGENTO_RESTAPI_SERVER_HOST') !== false)
            && (getenv('MAGENTO_RESTAPI_SERVER_HOST') !== '') ) {
            $url = getenv('MAGENTO_RESTAPI_SERVER_HOST');
        } else {
            $url = getenv('MAGENTO_BASE_URL');
        }

        if ((getenv('MAGENTO_RESTAPI_SERVER_PORT') !== false)
            && (getenv('MAGENTO_RESTAPI_SERVER_PORT') !== '')) {
            $url .= ':' . getenv('MAGENTO_RESTAPI_SERVER_PORT');
        }

        curl_setopt_array($this->curl, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_HTTPHEADER => $this->headers,
            CURLOPT_CUSTOMREQUEST => $this->apiOperation,
            CURLOPT_URL => $url . $this->apiPath
        ]);

        $response = curl_exec($this->curl);
        $http_code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);

        if ($response === false || !in_array($http_code, ApiClientUtil::SUCCESSFUL_HTTP_CODES)) {
            throw new \Exception('API returned response code: ' . $http_code . '    Response:' . $response);
        }

        curl_close($this->curl);

        return $response;
    }
}
