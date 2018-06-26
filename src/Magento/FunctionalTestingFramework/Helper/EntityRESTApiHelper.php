<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Helper;

use GuzzleHttp\Client;

/**
 * Class EntityRESTApiHelper
 * @package Magento\FunctionalTestingFramework\Helper
 */
class EntityRESTApiHelper
{
    /**
     * Integration admin token uri.
     */
    const INTEGRATION_ADMIN_TOKEN_URI = '/rest/V1/integration/admin/token';

    /**
     * Application json header.
     */
    const APPLICATION_JSON_HEADER = ['Content-Type' => 'application/json'];

    /**
     * Rest API client.
     *
     * @var Client
     */
    private $guzzle_client;

    /**
     * EntityRESTApiHelper constructor.
     * @param string $host
     * @param string $port
     */
    public function __construct($host, $port)
    {
        $this->guzzle_client = new Client([
            'base_uri' => "http://{$host}:{$port}",
            'timeout' => 5.0,
        ]);
    }

    /**
     * Submit Auth API Request.
     *
     * @param string $apiMethod
     * @param string $requestURI
     * @param string $jsonBody
     * @param array  $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function submitAuthAPIRequest($apiMethod, $requestURI, $jsonBody, $headers)
    {
        $allHeaders = $headers;
        $authTokenVal = $this->getAuthToken();
        $authToken = ['Authorization' => 'Bearer ' . $authTokenVal];
        $allHeaders = array_merge($allHeaders, $authToken);

        return $this->submitAPIRequest($apiMethod, $requestURI, $jsonBody, $allHeaders);
    }

    /**
     * Function that sends a REST call to the integration endpoint for an authorization token.
     *
     * @return string
     */
    private function getAuthToken()
    {
        $jsonArray = json_encode(['username' => 'admin', 'password' => 'admin123']);

        $response = $this->submitAPIRequest(
            'POST',
            self::INTEGRATION_ADMIN_TOKEN_URI,
            $jsonArray,
            self::APPLICATION_JSON_HEADER
        );

        if ($response->getStatusCode() != 200) {
            throwException($response->getReasonPhrase() .' Could not get admin token from service, please check logs.');
        }

        $authToken = str_replace('"', "", $response->getBody()->getContents());
        return $authToken;
    }

    /**
     * Function that submits an api request from the guzzle client using the following parameters:
     *
     * @param string $apiMethod
     * @param string $requestURI
     * @param string $jsonBody
     * @param array  $headers
     * @return \Psr\Http\Message\ResponseInterface
     */
    private function submitAPIRequest($apiMethod, $requestURI, $jsonBody, $headers)
    {
        $response = $this->guzzle_client->request(
            $apiMethod,
            $requestURI,
            [
                'headers' => $headers,
                'body' => $jsonBody
            ]
        );

        return $response;
    }
}
