<?php
namespace Magento\Xxyyzz\Page\Sales;

use Magento\Xxyyzz\Page\AdminGridPage;

class AdminOrderGrid extends AdminGridPage
{
    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $createNewOrderButton = '#add';

    public static $filterPurchaseDateFromField        = '.admin__control-text[name="created_at[from]"]';
    public static $filterPurchaseDateToField          = '.admin__control-text[name="created_at[to]"]';
    public static $filterGrandTotalBaseFromField      = '.admin__control-text[name="base_grand_total[from]"]';
    public static $filterGrandTotalBaseToField        = '.admin__control-text[name="base_grand_total[to]"]';
    public static $filterGrandTotalPurchasedFromField = '.admin__control-text[name="grand_total[from]"]';
    public static $filterGrandTotalPurchasedToField   = '.admin__control-text[name="grand_total[to]"]';
    public static $filterPurchasePointDropDownMenu    = '.admin__control-select[name="store_id"]';
    public static $filterIdField                      = '.admin__control-text[name="increment_id"]';
    public static $filterBillToNameField              = '.admin__control-text[name="billing_name"]';
    public static $filterShipToNameField              = '.admin__control-text[name="shipping_name"]';
    public static $filterStatusDropDownMenu           = '.admin__control-select[name="status"]';

    public function clickOnCreateNewOrderButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$createNewOrderButton);
        $I->waitForPageLoad();
    }

    public function enterPurchaseDateFromFilter($purchaseDateFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterPurchaseDateFromField, $purchaseDateFrom);
    }

    public function enterPurchaseDateToFilter($purchaseDateTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterPurchaseDateToField, $purchaseDateTo);
    }

    public function enterGrandTotalBaseFromFilter($grandTotalBaseFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterGrandTotalBaseFromField, $grandTotalBaseFrom);
    }

    public function enterGrandTotalBaseToFilter($grandTotalBaseTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterGrandTotalBaseToField, $grandTotalBaseTo);
    }

    public function enterGrandTotalPurchasedFromFilter($grandTotalPurchasedFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterGrandTotalPurchasedFromField, $grandTotalPurchasedFrom);
    }

    public function enterGrandTotalPurchasedToFilter($grandTotalPurchasedTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterGrandTotalPurchasedToField, $grandTotalPurchasedTo);
    }

    public function selectPurchasePointFilter($purchasePoint)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$filterPurchasePointDropDownMenu, $purchasePoint);
    }

    public function enterIdFilter($id)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterIdField, $id);
    }

    public function enterBillToNameFilter($billToName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterBillToNameField, $billToName);
    }

    public function enterShipToNameFilter($shipToName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterShipToNameField, $shipToName);
    }

    public function selectStatusFilter($status)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$filterStatusDropDownMenu, $status);
    }
}