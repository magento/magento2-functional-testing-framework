<?php
namespace Magento\Xxyyzz;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method \Codeception\Lib\Friend haveFriend($name, $actorClass = NULL)
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    /**
     * Magento admin username
     * @var string
     */
    protected static $adminUsername;

    /**
     * Magento Admin password
     * @var string
     */
    protected static $adminPassword;

    public function __construct(\Codeception\Scenario $scenario)
    {
        parent::__construct($scenario);
        $config = \Codeception\Configuration::suiteSettings('acceptance', \Codeception\Configuration::config());
        self::$adminUsername = $config['data']['magento']['admin_username'];
        self::$adminPassword = $config['data']['magento']['admin_password'];
    }
}
