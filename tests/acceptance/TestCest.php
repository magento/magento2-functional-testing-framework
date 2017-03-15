<?php


class TestCest
{
    public function _before(\Step\Acceptance\Admin $I)
    {
        $I->goToTheAdminLoginPage();
        $I->loginAsAnExistingAdmin();
    }
    
    /**
     * @env phantomjs
     * @env chrome
     * @group example
     */
    public function accessTheSalesOrdersPage(\Step\Acceptance\Admin $I)
    {
        $I->goToTheAdminSalesOrdersPage();
        $I->shouldBeOnTheAdminSalesOrdersPage();
        $I->see('Orders');
    }

    /**
     * @env phantomjs
     * @env chrome
     * @group example
     */
    public function accessTheProductsCatalogPage(\Step\Acceptance\Admin $I)
    {
        $I->goToTheAdminProductsCatalogPage();
        $I->shouldBeOnTheAdminProductsCatalogPage();
        $I->see('Catalog');
    }
}
