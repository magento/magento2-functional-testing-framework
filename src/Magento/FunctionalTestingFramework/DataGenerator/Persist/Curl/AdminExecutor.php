<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Persist\Curl;

use Magento\FunctionalTestingFramework\Util\Protocol\CurlInterface;
use Magento\FunctionalTestingFramework\Util\Protocol\CurlTransport;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

/**
 * Curl executor for requests to Admin.
 */
class AdminExecutor extends AbstractExecutor implements CurlInterface
{
    /**
     * Curl transport protocol.
     *
     * @var CurlTransport
     */
    private $transport;

    /**
     * Form key.
     *
     * @var string
     */
    private $formKey = null;

    /**
     * Response data.
     *
     * @var string
     */
    private $response;

    /**
     * Should executor remove backend_name from api url
     * @var boolean
     */
    private $removeBackend;

    /**
     * Backend url.
     *
     * @var string
     */
    private static $adminUrl;

    /**
     * Constructor.
     * @param boolean $removeBackend
     *
     * @constructor
     * @throws TestFrameworkException
     */
    public function __construct($removeBackend)
    {
        if (!isset(parent::$baseUrl)) {
            parent::resolveBaseUrl();
        }
        self::$adminUrl = parent::$baseUrl . getenv('MAGENTO_BACKEND_NAME') . '/';
        $this->removeBackend = $removeBackend;
        $this->transport = new CurlTransport();
        $this->authorize();
    }

    /**
     * Authorize admin on backend.
     *
     * @return void
     * @throws TestFrameworkException
     */
    private function authorize()
    {
        // Perform GET to backend url so form_key is set
        $this->transport->write(self::$adminUrl, [], CurlInterface::GET);
        $this->read();

        // Authenticate admin user
        $authUrl = self::$adminUrl . 'admin/auth/login/';
        $data = [
            'login[username]' => getenv('MAGENTO_ADMIN_USERNAME'),
            'login[password]' => getenv('MAGENTO_ADMIN_PASSWORD'),
            'form_key' => $this->formKey,
        ];
        $this->transport->write($authUrl, $data, CurlInterface::POST);
        $response = $this->read();
        if (strpos($response, 'login-form')) {
            throw new TestFrameworkException('Admin user authentication failed!');
        }
    }

    /**
     * Set Form Key from response.
     *
     * @return void
     */
    private function setFormKey()
    {
        preg_match('!var FORM_KEY = \'(\w+)\';!', $this->response, $matches);
        if (!empty($matches[1])) {
            $this->formKey = $matches[1];
        }
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
        $url = ltrim($url, "/");
        $apiUrl = self::$adminUrl . $url;

        if ($this->removeBackend) {
            $apiUrl = parent::$baseUrl . $url;
        }

        if ($this->formKey) {
            $data['form_key'] = $this->formKey;
        } else {
            throw new TestFrameworkException(
                sprintf('Form key is absent! Url: "%s" Response: "%s"', $apiUrl, $this->response)
            );
        }

        $this->transport->write($apiUrl, str_replace('null', '', http_build_query($data)), $method, $headers);
    }

    /**
     * Read response from server.
     *
     * @param string $successRegex
     * @param string $returnRegex
     * @return string|array
     * @throws TestFrameworkException
     */
    public function read($successRegex = null, $returnRegex = null)
    {
        $this->response = $this->transport->read();
        $this->setFormKey();

        if (!empty($successRegex)) {
            preg_match($successRegex, $this->response, $successMatches);
            if (empty($successMatches)) {
                throw new TestFrameworkException("Entity creation was not successful! Response: $this->response");
            }
        }

        if (!empty($returnRegex)) {
            preg_match($returnRegex, $this->response, $returnMatches);
            if (!empty($returnMatches)) {
                return $returnMatches;
            }
        }
        return $this->response;
    }

    /**
     * Add additional option to cURL.
     *
     * @param integer                      $option CURLOPT_* constants.
     * @param integer|string|boolean|array $value
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
}
