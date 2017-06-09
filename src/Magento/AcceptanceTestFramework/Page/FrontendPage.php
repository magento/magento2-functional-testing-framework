<?php
namespace Magento\AcceptanceTestFramework\Page;

/**
 * Magento frontend pages.
 */
class FrontendPage extends Page
{
    /**
     * Frontend page base url.
     */
    const FRONTEND_BASE_URL = '/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $pageTitle                        = '.page-title';

    public static $welcomeMessage                   = '.greet.welcome>span';
    public static $signInLink                       = '.authorization-link a';
    public static $createAccountLink                = '.header.links>li:nth-child(4)';

    public static $pageFooterContent                = '.footer.content';

    protected function initUrl()
    {
        $this->url = static::FRONTEND_BASE_URL . static::MCA;
    }

    public function seeInPageTitle($name)
    {
        $I = $this->acceptanceTester;
        $I->see($name, self::$pageTitle);
    }

    public function clickSignInLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$signInLink);
    }

    public function clickCreateAccountLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$createAccountLink);
    }

    public function seeWelcomeMessage($msg)
    {
        $I = $this->acceptanceTester;
        $I->see($msg, self::$welcomeMessage);
    }
}
