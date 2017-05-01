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
    public static $systemMessage                  = '.message-system';

    public static $pageTitle                      = '.page-title';
    public static $globalSearchButton             = '#search-global';
    public static $globalSearchInput              = '.search-global-input';

    public static $adminNotificationsLink         = '.notifications-action';
    public static $adminNotificationsCounter      = '.notifications-counter';
    public static $adminNotificationsMenu         = '.notifications-wrapper .admin__action-dropdown-menu';
    public static $adminNotificationMenuItem      = '.notifications-wrapper .notifications-entry';

    public static $userActionsMenuLink            = '.admin-user .admin__action-dropdown';
    public static $adminAccountSetting            = '.admin-user-name';
    public static $customerView                   = '.store-front';
    public static $adminSignOut                   = '.account-signout';

    public static $successMessage                 = '.message.message-success.success';

    public static $popupLoadingSpinner            = '.popup.popup-loading';

    public static $pageMainActionsArea            = '.page-main-actions';
    public static $pageMainActionsBack            = '#back';
    public static $pageMainActionsReset           = '#reset';
    public static $pageMainActionsSaveAndContinue = '#save_and_continue';
    public static $pageMainActionsSave            = '#save';
    public static $pageMainActionsAdd             = '#add';

    /**
     * @var AcceptanceTester
     */
    protected $acceptanceTester;

    /**
     * Page load timeout in seconds.
     *
     * @var string
     */
    protected $pageLoadTimeout;

    public function __construct(AcceptanceTester $I)
    {
        $this->acceptanceTester = $I;
        $this->pageLoadTimeout = $I->getConfiguration('pageload_timeout');
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

    public function openTabGoToAndVerifyUrl($pageUrl)
    {
        $I = $this->acceptanceTester;
        $I->openNewTab();
        $I->amOnPage($pageUrl);
        $I->seeInCurrentUrl($pageUrl);
    }

    public function seeInPageTitle($name)
    {
        $I = $this->acceptanceTester;
        $I->see($name, self::$pageTitle);
    }

    public function clickOnTheGlobalSearchButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$globalSearchButton);
    }

    public function enterGlobalSearchValue($searchValue)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$globalSearchInput, $searchValue);
    }

    public function pressEnterOnTheKeyboard()
    {
        $I = $this->acceptanceTester;
        $I->pressKey(self::$globalSearchInput, \WebDriverKeys::ENTER);
    }

    public function performGlobalSearchFor($searchValue)
    {
        self::clickOnTheGlobalSearchButton();
        self::enterGlobalSearchValue($searchValue);
        self::pressEnterOnTheKeyboard();
    }

    public function clickOnUserActionMenuLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$userActionsMenuLink);
    }

    public function clickOnAdminAccountSetting()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$adminAccountSetting);
    }

    public function clickOnCustomerView()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$customerView);
    }

    public function clickOnSignOut()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$adminSignOut);
    }

    public function clickOnAdminBackButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$pageMainActionsBack);
    }

    public function clickOnAdminResetButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$pageMainActionsReset);
    }

    public function clickOnAdminSaveAndContinueEdit()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$pageMainActionsSaveAndContinue);
        $I->waitForPageLoad();
    }

    public function clickOnAdminSaveButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$pageMainActionsSave);
        $I->waitForPageLoad();
    }

    public function clickOnAdminAddButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$pageMainActionsAdd);
        $I->waitForPageLoad();
    }

    public function clickOnCollapsibleArea($areaName)
    {
        $I = $this->acceptanceTester;
        $I->click('//div[@class="fieldset-wrapper-title"]/strong/span[contains(text(), "' . $areaName . '")]');
    }

    public function expandCollapsibleArea($areaIndex)
    {
        $I = $this->acceptanceTester;
        $context = sprintf('.fieldset-wrapper[data-index=%s]', $areaIndex);
        try {
            $I->seeElement($context . ' .fieldset-wrapper-title[data-state-collapsible=closed]');
            $I->click($context . ' .admin__collapsible-title>span');
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
        }
    }

    public function closeCollapsibleArea($areaIndex)
    {
        $I = $this->acceptanceTester;
        $context = sprintf('.fieldset-wrapper[data-index=%s]', $areaIndex);
        try {
            $I->seeElement($context . ' .fieldset-wrapper-title[data-state-collapsible=open]');
            $I->click($context . ' .admin__collapsible-title>span');
        } catch (\PHPUnit_Framework_AssertionFailedError $e) {
        }
    }

    public function seeSuccessMessage()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$successMessage);
    }
}
