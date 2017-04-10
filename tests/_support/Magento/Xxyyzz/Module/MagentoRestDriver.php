<?php
namespace Magento\Xxyyzz\Module;

use Codeception\Module\REST;

/**
 * MagentoRestDriver module provides Magento REST WebService.
 *
 * This module can be used either with frameworks or PHPBrowser.
 * If a framework module is connected, the testing will occur in the application directly.
 * Otherwise, a PHPBrowser should be specified as a dependency to send requests and receive responses from a server.
 *
 * ## Configuration
 *
 * * url *optional* - the url of api
 *
 * This module requires PHPBrowser or any of Framework modules enabled.
 *
 * ### Example
 *
 *     modules:
 *        enabled:
 *            - MagentoRestDriver:
 *                depends: PhpBrowser
 *                url: 'http://magento_base_url/rest/default/V1/'
 *
 *
 * ## Public Properties
 *
 * * headers - array of headers going to be sent.
 * * params - array of sent data
 * * response - last response (string)
 *
 * ## Parts
 *
 * * Json - actions for validating Json responses (no Xml responses)
 * * Xml - actions for validating XML responses (no Json responses)
 *
 * ## Conflicts
 *
 * Conflicts with SOAP module
 *
 */
class MagentoRestDriver extends REST
{
    /**
     * Module required fields.
     *
     * @var array
     */
    protected $requiredFields = [
        'url',
        'username',
        'password'
    ];

    /**
     * Module configurations.
     *
     * @var array
     */
    protected $config = [
        'url' => '',
        'username' => '',
        'password' => ''
    ];

    /**
     * Admin tokens for Magento webapi access.
     *
     * @var array
     */
    protected static $adminTokens = [];

    /**
     * Before suite.
     *
     * @param array $settings
     */
    public function _beforeSuite($settings = [])
    {
        parent::_beforeSuite($settings);
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->sendPOST(
            'integration/admin/token',
            ['username' => $this->config['username'], 'password' => $this->config['password']]
        );
        $token = substr($this->grabResponse(), 1, strlen($this->grabResponse())-2);
        $this->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        $this->haveHttpHeader('Authorization', 'Bearer ' . $token);
        self::$adminTokens[$this->config['username']] = $token;
    }

    /**
     * After suite.
     */
    public function _afterSuite()
    {
        parent::_afterSuite();
        $this->deleteHeader('Authorization');
    }

    /**
     * Get admin auth token by username and password.
     *
     * @param string $username
     * @param string $password
     * @param bool $newToken
     * @return string
     * @part json
     * @part xml
     */
    public function getAdminAuthToken($username = null, $password = null, $newToken = false)
    {
        $username = !is_null($username) ? $username : $this->config['username'];
        $password = !is_null($password) ? $password : $this->config['password'];

        // Use existing token if it exists
        if (!$newToken
            && (isset(self::$adminTokens[$username]) || array_key_exists($username, self::$adminTokens))) {
            return self::$adminTokens[$username];
        }
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->sendPOST('integration/admin/token', ['username' => $username, 'password' => $password]);
        $token = substr($this->grabResponse(), 1, strlen($this->grabResponse())-2);
        $this->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        self::$adminTokens[$username] = $token;
        return $token;
    }

    /**
     * Admin token authentication for a given user.
     *
     * @param string $username
     * @param string $password
     * @param bool $newToken
     * @part json
     * @part xml
     */
    public function amAdminTokenAuthenticated($username = null, $password = null, $newToken = false)
    {
        $username = !is_null($username) ? $username : $this->config['username'];
        $password = !is_null($password) ? $password : $this->config['password'];

        $this->haveHttpHeader('Content-Type', 'application/json');
        if ($newToken || !isset(self::$adminTokens[$username])) {
            $this->sendPOST('integration/admin/token', ['username' => $username, 'password' => $password]);
            $token = substr($this->grabResponse(), 1, strlen($this->grabResponse()) - 2);
            $this->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
            self::$adminTokens[$username] = $token;
        }
        $this->amBearerAuthenticated(self::$adminTokens[$username]);
    }
}
