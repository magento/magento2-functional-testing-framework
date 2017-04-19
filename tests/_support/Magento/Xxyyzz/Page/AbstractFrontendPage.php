<?php
namespace Magento\Xxyyzz\Page;

use Magento\Xxyyzz\AcceptanceTester;

abstract class AbstractFrontendPage
{
    /**
     * Include url of current page.
     */
    public static $URL = '/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $pageTitle                        = '.page-title';

    public static $welcomeMessage                   = '.greet.welcome>span';
    public static $signInLink                       = '.authorization-link a';
    public static $createAccountLink                = '.header.links>li:nth-child(4)';

    public static $pageFooterContent                = '.footer.content';

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
