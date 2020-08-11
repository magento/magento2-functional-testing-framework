<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataTransport;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\MftfGlobals;
use Magento\FunctionalTestingFramework\DataTransport\Protocol\CurlInterface;
use Magento\FunctionalTestingFramework\DataTransport\Protocol\CurlTransport;
use Magento\FunctionalTestingFramework\DataTransport\Auth\WebApiAuth;

/**
 * Curl executor for Magento Web Api requests.
 */
class WebApiExecutor implements CurlInterface
{
    /**
     * Curl transport protocol
     *
     * @var CurlTransport
     */
    private $transport;

    /**
     * Rest request headers
     *
     * @var string[]
     */
    private $headers = [
        'Accept: application/json',
        'Content-Type: application/json',
    ];

    /**
     * Store code in API request
     *
     * @var string
     */
    private $storeCode;

    /**
     * WebApiExecutor Constructor
     *
     * @param string $storeCode
     * @throws TestFrameworkException
     */
    public function __construct($storeCode = null)
    {
        $this->storeCode = $storeCode;
        $this->transport = new CurlTransport();
        $this->authorize();
    }

    /**
     * Acquire and store the authorization token needed for REST requests
     *
     * @return void
     * @throws TestFrameworkException
     */
    protected function authorize()
    {
        $this->headers = array_merge(
            ['Authorization: Bearer ' . WebApiAuth::getAdminToken()],
            $this->headers
        );
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
     * @param string      $successRegex
     * @param string      $returnRegex
     * @param string|null $returnIndex
     * @return string
     * @throws TestFrameworkException
     */
    public function read($successRegex = null, $returnRegex = null, $returnIndex = null)
    {
        return $this->transport->read();
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
     * Builds and returns URL for request, appending storeCode if needed
     *
     * @param string $resource
     * @return string
     * @throws TestFrameworkException
     */
    protected function getFormattedUrl($resource)
    {
        $urlResult = MftfGlobals::getWebApiBaseUrl();
        if ($this->storeCode != null) {
            $urlResult .= $this->storeCode . '/';
        }
        $urlResult .= trim($resource, '/');
        return $urlResult;
    }
}
