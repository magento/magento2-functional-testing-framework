<?php
namespace Magento\Xxyyzz\Page\Sales\Shipment;

use Magento\Xxyyzz\Page\AdminGridPage;

class AdminShipmentGrid extends AdminGridPage
{
    /**
     * UI map for admin data grid filter section.
     */
    public static $filterShipDateFromField      = '.admin__control-text[name="created_at[from]"]';
    public static $filterShipDateToField        = '.admin__control-text[name="created_at[to]"]';
    public static $filterOrderDateFromField     = '.admin__control-text[name="order_created_at[from]"]';
    public static $filterOrderDateToField       = '.admin__control-text[name="order_created_at[to]"]';
    public static $filterTotalQtyFromField      = '.admin__control-text[name="total_qty[from]"]';
    public static $filterTotalQtyToField        = '.admin__control-text[name="total_qty[to]"]';
    public static $filterPurchasedFromDropDown  = '.admin__control-select[name="store_id"]';
    public static $filterShipmentField          = '.admin__control-text[name="increment_id"]';
    public static $filterOrderField             = '.admin__control-text[name="order_increment_id"]';
    public static $filterShipToNameField        = '.admin__control-text[name="shipping_name"]';

    public function enterFilterShipDateFrom($shipDateFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterShipDateFromField, $shipDateFrom);
    }

    public function enterFilterShipDateTo($shipDateTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterShipDateToField, $shipDateTo);
    }

    public function enterFilterOrderDateFrom($orderDateFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterOrderDateFromField, $orderDateFrom);
    }

    public function enterFilterOrderDateTo($orderDateTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterOrderDateToField, $orderDateTo);
    }

    public function enterTotalQtyFrom($totalQtyFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterTotalQtyFromField, $totalQtyFrom);
    }

    public function enterTotalQtyTo($totalQtyTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterTotalQtyToField, $totalQtyTo);
    }

    public function selectPurchasedFrom($purchasedFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterPurchasedFromDropDown, $purchasedFrom);
    }

    public function enterShipment($shipment)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterShipmentField, $shipment);
    }

    public function enterOrder($order)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterOrderField, $order);
    }

    public function enterShipToName($shipToName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterShipToNameField, $shipToName);
    }
}
