<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Persist\Curl;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Protocol\CurlInterface;
use Magento\FunctionalTestingFramework\Util\Protocol\CurlTransport;

/**
 * Curl executor for Magento Web Api requests.
 */
class WebapiExecutor extends AbstractExecutor implements CurlInterface
{
    /**
     * Curl transport protocol.
     *
     * @var CurlTransport
     */
    private $transport;

    /**
     * Api headers.
     *
     * @var array
     */
    private $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    /**
     * Response data.
     *
     * @var string
     */
    private $response;

    /**
     *  Admin authentication url.
     */
    const ADMIN_AUTH_URL = '/V1/integration/admin/token';

    /**
     * Store code in api request.
     *
     * @var string
     */
    private $storeCode;

    /**
     * Admin user auth token.
     *
     * @var string
     */
    private $authToken;

    /**
     * WebapiExecutor Constructor.
     *
     * @param string $storeCode
     * @throws TestFrameworkException
     */
    public function __construct($storeCode = null)
    {
        $this->storeCode = $storeCode;
        $this->authToken = null;
        $this->transport = new CurlTransport();
        $this->authorize();
    }

    /**
     * Returns base URL for Magento Web API instance
     * @return string
     */
    public function getBaseUrl(): string
    {
        $baseUrl = parent::getBaseUrl();

        $webapiHost = getenv('MAGENTO_RESTAPI_SERVER_HOST');
        $webapiPort = getenv("MAGENTO_RESTAPI_SERVER_PORT");
        $webapiProtocol = getenv("MAGENTO_RESTAPI_SERVER_PROTOCOL");

        if ($webapiHost) {
            $baseUrl = sprintf('%s://%s/', $webapiProtocol, $webapiHost);
        }

        if ($webapiPort) {
            $baseUrl = rtrim($baseUrl, '/') . ':' . $webapiPort . '/';
        }

        return $baseUrl;
    }

    /**
     * Acquire and store the authorization token needed for REST requests.
     *
     * @return void
     * @throws TestFrameworkException
     */
    protected function authorize()
    {
        $authUrl = $this->getFormattedUrl(self::ADMIN_AUTH_URL);
        $authCreds = [
            'username' => getenv('MAGENTO_ADMIN_USERNAME'),
            'password' => getenv('MAGENTO_ADMIN_PASSWORD')
        ];

        $this->transport->write($authUrl, json_encode($authCreds), CurlInterface::POST, $this->headers);
        $this->authToken = str_replace('"', "", $this->read());
        $this->headers = array_merge(['Authorization: Bearer ' . $this->authToken], $this->headers);
    }

    /**
     * Send request to the remote server.
     *
     * @param string $url
     * @param array  $data
     * @param string $method
     * @param array  $headers
     * @return void
     * @throws TestFrameworkException
     */
    public function write($url, $data = [], $method = CurlInterface::POST, $headers = [])
    {
        $this->transport->write(
            $this->getFormattedUrl($url),
            json_encode($data, JSON_PRETTY_PRINT),
            $method,
            array_unique(array_merge($headers, $this->headers))
        );
    }

    /**
     * Read response from server.
     *
     * @param string $successRegex
     * @param string $returnRegex
     * @return string
     * @throws TestFrameworkException
     */
    public function read($successRegex = null, $returnRegex = null)
    {
        $this->response = $this->transport->read();
        return $this->response;
    }

    /**
     * Add additional option to cURL.
     *
     * @param  integer                      $option CURLOPT_* constants.
     * @param  integer|string|boolean|array $value
     * @return void
     */
    public function addOption($option, $value)
    {
        $this->transport->addOption($option, $value);
    }

    /**
     * Close the connection to the server.
     *
     * @return void
     */
    public function close()
    {
        $this->transport->close();
    }

    /**
     * Builds and returns URL for request, appending storeCode if needed.
     * @param string $resource
     * @return string
     */
    public function getFormattedUrl($resource)
    {
        $urlResult = $this->getBaseUrl() . 'rest/';
        if ($this->storeCode != null) {
            $urlResult .= $this->storeCode . "/";
        }
        $urlResult .= trim($resource, "/");
        return $urlResult;
    }

    /**
     * Return admin auth token.
     *
     * @throws TestFrameworkException
     * @return string
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }
}
