<?php
namespace Magento\Xxyyzz\Page\Content\Storefront;

use Magento\Xxyyzz\Page\AbstractFrontendPage;

class StorefrontCMSPage extends AbstractFrontendPage
{
    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $pageTitleWrapper = '.page-title-wrapper';
    public static $pageMainContent  = '.main';

    public function verifyPageContentTitle($pageContentTitle)
    {
        $I = $this->acceptanceTester;
        $I->see($pageContentTitle, self::$pageTitleWrapper);
    }

    public function verifyPageContentBody($pageContentBody)
    {
        $I = $this->acceptanceTester;
        $I->see($pageContentBody, self::$pageMainContent);
    }
}