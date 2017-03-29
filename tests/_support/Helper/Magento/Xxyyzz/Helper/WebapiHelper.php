<?php
namespace Magento\Xxyyzz\Helper;

/**
 * Class WebapiHelper
 * 
 * Define general Magento Webapi actions
 * All public methods declared in helper class will be available in $I
 */
class WebapiHelper extends \Codeception\Module
{
    /**
     * Magento admin username.
     *
     * @var string
     */
    protected static $adminUsername;

    /**
     * Magento admin password.
     *
     * @var string
     */
    protected static $adminPassword;

    /**
     * Admin tokens for Magento webapi access.
     *
     * @var array
     */
    protected static $adminTokens = [];

    /**
     * @var \Codeception\Module\REST
     */
    protected $restDriver;

    /**
     * Initialize helper.
     */
    public function _initialize()
    {
        parent::_initialize();
        $this->restDriver = $this->getModule('REST');
        if (!isset(self::$adminUsername) || !isset(self::$adminUsername)) {
            $config = \Codeception\Configuration::suiteSettings('acceptance', \Codeception\Configuration::config());
            self::$adminUsername = $config['data']['magento']['admin_username'];
            self::$adminPassword = $config['data']['magento']['admin_password'];
        }
    }

    /**
     * Get admin auth token by username and password.
     *
     * @param string $username
     * @param string $password
     * @param bool $newToken
     * @return string
     */
    public function getAdminAuthToken($username = null, $password = null, $newToken = false)
    {
        $username = !is_null($username) ? $username : self::$adminUsername;
        $password = !is_null($password) ? $password : self::$adminPassword;

        // Use existing token if it exists
        if (!$newToken
            && (isset(self::$adminTokens[$username]) || array_key_exists($username, self::$adminTokens))) {
            return self::$adminTokens[$username];
        }
        $this->restDriver = $this->getModule('REST');
        $this->restDriver->haveHttpHeader('Content-Type', 'application/json');
        $this->restDriver->sendPOST('integration/admin/token', ['username' => $username, 'password' => $password]);
        $token = substr($this->restDriver->grabResponse(), 1, strlen($this->restDriver->grabResponse())-2);
        $this->restDriver->seeResponseCodeIs(200);
        self::$adminTokens[$username] = $token;
        return $token;
    }
}
