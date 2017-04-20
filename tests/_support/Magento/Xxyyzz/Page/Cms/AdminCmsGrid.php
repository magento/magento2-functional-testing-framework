<?php
namespace Magento\Xxyyzz\Page\Cms;

use Magento\Xxyyzz\Page\AbstractAdminGridPage;

class AdminCmsGrid extends AbstractAdminGridPage
{
    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $actionEdit   = '.action-menu-item[data-action="item-edit"]';
    public static $actionDelete = '.action-menu-item[data-action="item-delete"]';
    public static $actionView   = '.action-menu-item[data-action="item-preview"]';

    public function clickOnActionSelectLinkFor($keyText)
    {
        $I = $this->acceptanceTester;
        $actionSelectLinkSelector = '.data-row[data-repeat-index="' . self::determineIndexBasedOnThisText($keyText) . '"] .action-select';

        $I->click($actionSelectLinkSelector);
    }

    public function clickOnActionEditFor($keyText)
    {
        $I = $this->acceptanceTester;
        $selector = '.data-row[data-repeat-index="' . self::determineIndexBasedOnThisText($keyText) . '"] ' . self::$actionEdit;

        self::clickOnActionSelectLinkFor($keyText);
        $I->click($selector);
        self::waitForLoadingMaskToDisappear();
    }

    public function clickOnActionDeleteFor($keyText)
    {
        $I = $this->acceptanceTester;
        $selector = '.data-row[data-repeat-index="' . self::determineIndexBasedOnThisText($keyText) . '"] ' . self::$actionEdit;

        self::clickOnActionSelectLinkFor($keyText);
        $I->click($selector);
        self::waitForLoadingMaskToDisappear();
    }

    public function clickOnActionViewFor($keyText)
    {
        $I = $this->acceptanceTester;
        $selector = '.data-row[data-repeat-index="' . self::determineIndexBasedOnThisText($keyText) . '"] ' . self::$actionEdit;

        self::clickOnActionSelectLinkFor($keyText);
        $I->click($selector);
        self::waitForLoadingMaskToDisappear();
    }
}