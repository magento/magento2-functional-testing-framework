<?php
namespace Magento\Xxyyzz\Page;

use Magento\Xxyyzz\AcceptanceTester;

abstract class AbstractAdminPage
{
    /**
     * Include url of current page.
     */
    public static $URL = '/admin/admin/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $pageTitle                        = '.page-title';

    public static $systemMessage                    = '.message-system';

    public static $successMessage                   = '.message.message-success.success';

    public static $pageMainActions                  = '.page-main-actions';

    public static $globalSearchButton               = '#search-global';

    public static $adminaAccountSetting             = '.admin-user-name';
    public static $customerView                     = '.store-front';
    public static $adminSignOut                     = '.account-signout';

    public static $popupLoadingSpinner              = '.popup.popup-loading';

    /**
     * @var AcceptanceTester
     */
    protected $acceptanceTester;

    /**
     * Page load timeout in seconds.
     *
     * @var string
     */
    protected $pageloadTimeout;

    public function __construct(AcceptanceTester $I)
    {
        $this->acceptanceTester = $I;
        $this->pageloadTimeout = $I->getConfiguration('pageload_timeout');
    }

    public static function of(AcceptanceTester $I)
    {
        return new static($I);
    }

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }

    public function seeInPageTitle($name)
    {
        $I = $this->acceptanceTester;
        $I->see($name, self::$pageTitle);
    }
}
