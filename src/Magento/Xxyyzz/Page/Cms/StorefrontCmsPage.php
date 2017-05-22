<?php
namespace Magento\Xxyyzz\Page\Cms;

use Magento\Xxyyzz\Page\AbstractFrontendPage;

class StorefrontCmsPage extends AbstractFrontendPage
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