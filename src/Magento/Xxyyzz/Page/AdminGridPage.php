<?php
namespace Magento\Xxyyzz\Page;

use Magento\Xxyyzz\AcceptanceTester;
use Codeception\Exception\ElementNotFound;

class AdminGridPage
{
    /**
     * Include url of
     *
     * current page.
     */
    public static $URL = '/admin/admin/';

    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $searchByField            = '.data-grid-search-control';
    public static $searchByButton           = '.data-grid-search-control-wrap .action-submit';

    //public static $filtersButton            = 'button[data-action=grid-filter-expand]';
    public static $filtersButton
        = '.admin__data-grid-outer-wrap>.admin__data-grid-header button[data-action=grid-filter-expand]';
    public static $filtersExpanded          = '.admin__data-grid-filters-wrap._show';
    public static $filtersApplyButton       = 'button[data-action=grid-filter-apply]';
    public static $filtersCancelButton      = 'button[data-action=grid-filter-cancel]';
    //public static $filtersClearAllButton    = 'button[data-action=grid-filter-reset]';
    public static $filtersClearAllButton    = '.admin__data-grid-header button[data-action=grid-filter-reset]';

    public static $viewButton          = '.admin__data-grid-action-bookmarks .admin__action-dropdown';
    public static $viewDropDownMenu    = '.admin__data-grid-action-bookmarks .admin__action-dropdown-menu';
    public static $viewDropDownOption  = '.admin__data-grid-action-bookmarks .action-dropdown-menu-link';
    public static $viewSaveViewAsLink  = '.admin__data-grid-action-bookmarks .action-dropdown-menu-item-last';
    public static $newViewField        = '.admin__data-grid-action-bookmarks .admin__control-text';
    public static $newViewButton       = '.admin__data-grid-action-bookmarks .action-submit';

    public static $columnsButton       = '.admin__data-grid-action-columns';
    public static $columnsCurrentCount = '.admin__data-grid-action-columns .admin__action-dropdown-menu-header';
    public static $columnCheckbox      = '';
    public static $columnHeaderName    = '.admin__data-grid-action-columns .admin__field-label';
    public static $columnsResetButton  = '.admin__data-grid-action-columns .admin__action-dropdown-footer-secondary-actions';
    public static $columnsCancelButton = '.admin__data-grid-action-columns .admin__action-dropdown-footer-main-actions';

    public static $exportButton        = '.admin__data-grid-action-export';
    public static $exportDropDownMenu  = '.admin__data-grid-action-export-menu';
    public static $exportLinks         = '.admin__data-grid-action-export-menu .admin__field-label';
    public static $exportCancelButton  = '.admin__data-grid-action-export-menu .action-tertiary';
    public static $exportExportButton  = '.admin__data-grid-action-export-menu .action-secondary';

    public static $actionsButton       = '.action-select-wrap';
    public static $actionsDropDownMenu = '.action-menu-items';
    public static $actionsMenuItem     = '.action-menu-items .action-menu-item';

    public static $recordsCount        = '';

    public static $perPageCountButton  = '.admin__data-grid-pager-wrap .selectmenu';
    public static $perPageDropDownMenu = '.admin__data-grid-pager-wrap .selectmenu-items';
    public static $perPageMenuItem     = '.admin__data-grid-pager-wrap .selectmenu-items .selectmenu-item-action';
    public static $perPageCustomLink   = '.admin__data-grid-pager-wrap .selectmenu-items .selectmenu-item-action:last';
    public static $perPageCustomField  = '.admin__data-grid-pager-wrap .selectmenu-items .admin__control-text';
    public static $perPageCustomButton = '.admin__data-grid-pager-wrap .selectmenu-items .action-save';

    public static $backPageButton      = '.action-previous';
    public static $pageNumberField     = '.admin__data-grid-pager .admin__control-text';
    public static $pageOfPageText      = '.admin__data-grid-pager .admin__control-support-text';
    public static $nextPageButton      = '.action-next';

    public static $gridMainArea        = '.admin__data-grid-wrap';
    public static $gridHeaderName      = '.data-grid-th';

    public static $loadingMask         = '.loading-mask';
    public static $gridLoadingMask     = '.admin__data-grid-loading-mask';

    public static $grid
        = '.admin__data-grid-outer-wrap>.admin__data-grid-wrap';

    public static $gridNthRow
        = '.admin__data-grid-outer-wrap>.admin__data-grid-wrap tbody tr:nth-child(%s)';

    public static $checkboxInGridNthRow
        = '.admin__data-grid-outer-wrap>.admin__data-grid-wrap tbody tr:nth-child(%s) .admin__control-checkbox';
    
    /**
     * @var AcceptanceTester
     */
    protected $acceptanceTester;

    public function __construct(AcceptanceTester $I)
    {
        $this->acceptanceTester = $I;
        $this->pageLoadTimeout = $I->getConfiguration('pageload_timeout');
    }

    public static function of(AcceptanceTester $I)
    {
        return new static($I);
    }

    public static function route($param)
    {
        return static::$URL.$param;
    }

    public function enterSearchKeyword($searchKeyboard)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$searchByField, $searchKeyboard);
    }

    public function clickOnTheSearchButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$searchByButton);
    }

    public function performSearchByKeyword($searchKeyword)
    {
        $I = $this->acceptanceTester;
        self::enterSearchKeyword($searchKeyword);
        $I->waitForPageLoad();
        self::clickOnTheSearchButton();
        $I->waitForPageLoad();
    }

    public function clickOnFiltersButtonToExpand()
    {
        $I = $this->acceptanceTester;
        try {
            $I->waitForPageLoad();
            $I->dontSeeElement(self::$filtersExpanded);
            $I->click(self::$filtersButton);
            $I->waitForPageLoad();
        } catch (ElementNotFound $e) {
        }
    }

    public function clickOnFiltersButtonToClose()
    {
        $I = $this->acceptanceTester;
        try {
            $I->waitForPageLoad();
            $I->seeElement(self::$filtersExpanded);
            $I->click(self::$filtersButton);
            $I->waitForPageLoad();
        } catch (ElementNotFound $e) {
        }
    }

    public function clickOnFiltersClearAllButton()
    {
        $I = $this->acceptanceTester;
        try {
            $I->waitForPageLoad();
            $I->click(self::$filtersClearAllButton);
        } catch (ElementNotFound $e) {
        }
    }

    /**
     * Search and filter by value.
     *
     * @param string $value
     * @param string $selector
     * @param string $type
     */
    public function searchAndFiltersByValue($value, $selector, $type = 'textfield')
    {
        $I = $this->acceptanceTester;
        $this->clickOnFiltersClearAllButton();
        $this->clickOnFiltersButtonToExpand();

        switch ($type) {
            case 'dropdown':
                $I->selectOption($selector, $value);
                break;
            case 'textfield':
            default:
            $I->fillField($selector, $value);
        }
        $I->click(self::$filtersApplyButton);
        $I->waitForPageLoad();
    }

    /**
     * @see texts in currently visible grid's nth row.
     *
     * @param int $n
     * @param array $texts
     */
    public function seeInCurrentGridNthRow(int $n, array $texts)
    {
        $I = $this->acceptanceTester;
        $I->waitForPageLoad();
        foreach ($texts as $text) {
            $I->see($text, sprintf(self::$gridNthRow, $n));
        }
    }

    /**
     * @see texts in currently visible grid.
     *
     * @param array $texts
     */
    public function seeInCurrentGrid(array $texts)
    {
        $I = $this->acceptanceTester;
        $I->waitForPageLoad();
        foreach ($texts as $text) {
            $I->see($text, self::$grid);
        }
    }

    /**
     * Check the checkbox in currently visible grid's nth row.
     *
     * @param int $n
     */
    public function checkCheckboxInCurrentNthRow(int $n)
    {
        $I = $this->acceptanceTester;
        $I->waitForPageLoad();
        try {
            $I->dontSeeCheckboxIsChecked(sprintf(self::$checkboxInGridNthRow, 1));
            $I->click(sprintf(self::$checkboxInGridNthRow, 1));
        } catch (ElementNotFound $e) {
        }
    }

    public function clickOnViewButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$viewButton);
    }

    public function clickOnSpecificView($viewName)
    {
        $I = $this->acceptanceTester;
        $I->click(self::$viewDropDownOption, $viewName);
    }

    public function clickOnSaveViewAsLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$viewSaveViewAsLink);
    }

    public function enterNewViewName($newViewName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$newViewField, $newViewName);
    }

    public function clickOnNewViewSaveButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$newViewButton);
    }

    public function saveTheCurrentView($newViewName)
    {
        self::clickOnViewButton();
        self::clickOnSaveViewAsLink();
        self::enterNewViewName($newViewName);
        self::clickOnNewViewSaveButton();
    }

    public function clickOnColumnsButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$columnsButton);
    }

    public function clickOnSpecificColumnName($columnName)
    {
        $I = $this->acceptanceTester;
        $I->click($columnName);
    }

    public function clickOnColumnReset()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$columnsResetButton);
    }

    public function clickOnColumnCancel()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$columnsCancelButton);
    }

    public function clickExportButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$exportButton);
    }

    public function clickOnCsvLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$exportLinks, 'CSV');
    }

    public function clickOnExcelXmlLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$exportLinks, 'Excel XML');
    }

    public function clickOnExportCancel()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$exportCancelButton);
    }

    public function clickOnExportExport()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$exportExportButton);
    }

    public function clickOnActionsButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$actionsButton);
    }

    public function clickOnSpecificActionLink($actionName)
    {
        $I = $this->acceptanceTester;
        $I->click(self::$actionsMenuItem, $actionName);
    }

    public function clickOnPerPageButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$perPageCountButton);
    }

    public function clickOnSpecificPerPageCount($itemsPerPage)
    {
        $I = $this->acceptanceTester;
        $I->click($itemsPerPage);
    }

    public function clickOnCustomPerPageCountLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$perPageCustomLink);
    }

    public function enterCustomerPerPageLink($customPerPageCount)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$perPageCustomField, $customPerPageCount);
    }

    public function clickOnCustomPerPageSaveLink()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$perPageCustomButton);
    }

    public function clickOnPageBackButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$backPageButton);
    }

    public function enterPageNumber($pageNumber)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$pageNumberField, $pageNumber);
    }

    public function clickOnPageNextButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$nextPageButton);
    }

    public function clickOnSpecificGridColumnHeader($columnHeaderName)
    {
        $I = $this->acceptanceTester;
        $I->click(self::$gridHeaderName, $columnHeaderName);
    }

    public function determineIndexBasedOnThisText($keyText)
    {
        $I = $this->acceptanceTester;
        $selector = "//div[contains(@class, 'data-grid-cell-content')][contains(., '" . $keyText . "')]/parent::td/parent::tr";
        $number = $I->grabAttributeFrom($selector, 'data-repeat-index');
        return $number;
    }

    public function clickOnActionLinkFor($keyText)
    {
        $I = $this->acceptanceTester;
        $actionLinkSelector = '.data-row[data-repeat-index="' . self::determineIndexBasedOnThisText($keyText) . '"] .action-menu-item';

        $I->click($actionLinkSelector);
        $I->waitForPageLoad();
    }

    public function waitForGridLoadingMaskToDisappear()
    {
        $I = $this->acceptanceTester;
        $I->waitForElementNotVisible(self::$gridLoadingMask, 30);
    }
}
