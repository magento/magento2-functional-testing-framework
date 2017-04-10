<?php
namespace Magento\Xxyyzz\Acceptance\SampleTests;

use Magento\Xxyyzz\Step\Backend\AdminStep;

/**
 * group skip
 */
class TestCest
{
    public function _before(AdminStep $I)
    {
        $I->goToTheAdminLoginPage();
        $I->loginAsAdmin();
    }
    
    /**
     * @env phantomjs
     * @env chrome
     * @group example
     */
    public function accessTheSalesOrdersPage(AdminStep $I)
    {
        $I->goToTheAdminSalesOrdersPage();
        $I->shouldBeOnTheAdminSalesOrdersPage();
    }

    /**
     * @env phantomjs
     * @env chrome
     * @group example
     */
    public function accessTheProductsCatalogPage(AdminStep $I)
    {
        $I->goToTheAdminProductsCatalogPage();
        $I->shouldBeOnTheAdminProductsCatalogPage();
    }
}
