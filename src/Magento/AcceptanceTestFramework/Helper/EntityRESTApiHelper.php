<?php

namespace Magento\AcceptanceTestFramework\Helper;

use GuzzleHttp\Client;

class EntityRESTApiHelper
{
    private $guzzle_client;
    const INTEGRATION_ADMIN_TOKEN_URI = '/rest/V1/integration/admin/token';
    const APPLICATION_JSON_HEADER = ['Content-Type' => 'application/json'];

    public function __construct($host, $port)
    {
        $this->guzzle_client = new Client([
            'base_uri' => "http://${host}:${port}",
            'timeout' => 5.0,
        ]);
    }

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
     * @param string $apiMethod
     * @param string $requestURI
     * @param string $jsonBody
     * @param array $headers
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
