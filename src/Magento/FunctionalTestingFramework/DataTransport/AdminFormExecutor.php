<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataTransport;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;
use Magento\FunctionalTestingFramework\Util\MftfGlobals;
use Magento\FunctionalTestingFramework\DataTransport\Protocol\CurlInterface;
use Magento\FunctionalTestingFramework\DataTransport\Protocol\CurlTransport;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\DataTransport\Auth\Tfa\OTP;
use Magento\FunctionalTestingFramework\DataTransport\Auth\Tfa;

/**
 * Curl executor for requests to Admin.
 */
class AdminFormExecutor implements CurlInterface
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
     * Flag describes whether the request is to Magento Base URL, removes backend_name from api url
     * @var boolean
     */
    private $removeBackend;

    /**
     * Constructor.
     * @param boolean $removeBackend
     *
     * @constructor
     * @throws TestFrameworkException
     */
    public function __construct($removeBackend)
    {
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
        $this->transport->write(MftfGlobals::getBackendBaseUrl(), [], CurlInterface::GET);
        $this->read();

        // Authenticate admin user
        $authUrl = MftfGlobals::getBackendBaseUrl() . 'admin/auth/login/';
        $encryptedSecret = CredentialStore::getInstance()->getSecret('magento/MAGENTO_ADMIN_PASSWORD');
        $secret = CredentialStore::getInstance()->decryptSecretValue($encryptedSecret);
        $data = [
            'login[username]' => getenv('MAGENTO_ADMIN_USERNAME'),
            'login[password]' => $secret,
            'form_key' => $this->formKey,
        ];
        $this->transport->write($authUrl, $data, CurlInterface::POST);
        $response = $this->read();

        if (strpos($response, 'login-form')) {
            throw new TestFrameworkException('Admin user authentication failed!');
        }

        // Get OTP
        if (Tfa::isEnabled()) {
            $authUrl = MftfGlobals::getBackendBaseUrl() . Tfa::getProviderAdminFormEndpoint('google');
            $data = [
                'tfa_code' => OTP::getOTP(),
                'form_key' => $this->formKey,
            ];
            $this->transport->write($authUrl, $data, CurlInterface::POST);
            $response = json_decode($this->read());

            if (!$response->success) {
                throw new TestFrameworkException('Admin user 2FA authentication failed!');
            }
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
        $apiUrl = MftfGlobals::getBackendBaseUrl() . $url;

        if ($this->removeBackend) {
            //TODO
            //Cannot find usage. Do we need this?
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
     * @param string      $successRegex
     * @param string      $returnRegex
     * @param string|null $returnIndex
     * @return string|array
     * @throws TestFrameworkException
     */
    public function read($successRegex = null, $returnRegex = null, $returnIndex = null)
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
                return $returnMatches[$returnIndex] ?? $returnMatches[0];
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
