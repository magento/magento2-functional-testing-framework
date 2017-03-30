<?php
namespace Magento\Xxyyzz\Acceptance\SampleTests;

use Magento\Xxyyzz\Step\Backend\Admin;

/**
 * group skip
 */
class TestCest
{
    public function _before(Admin $I)
    {
        $I->goToTheAdminLoginPage();
        $I->loginAsAdmin();
    }
    
    /**
     * @env phantomjs
     * @env chrome
     * @group example
     */
    public function accessTheSalesOrdersPage(Admin $I)
    {
        $I->goToTheAdminSalesOrdersPage();
        $I->shouldBeOnTheAdminSalesOrdersPage();
    }

    /**
     * @env phantomjs
     * @env chrome
     * @group example
     */
    public function accessTheProductsCatalogPage(Admin $I)
    {
        $I->goToTheAdminProductsCatalogPage();
        $I->shouldBeOnTheAdminProductsCatalogPage();
    }
}
