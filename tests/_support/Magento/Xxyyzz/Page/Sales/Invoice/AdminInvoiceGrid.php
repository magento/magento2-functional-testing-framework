<?php
namespace Magento\Xxyyzz\Page\Sales\Invoice;

use Magento\Xxyyzz\Page\AdminGridPage;

class AdminInvoiceGrid extends AdminGridPage
{
    /**
     * UI map for admin data grid filter section.
     */
    public static $filterInvoiceDateFromField    = '.admin__control-text[name="created_at[from]"]';
    public static $filterInvoiceDateToField      = '.admin__control-text[name="created_at[to]"]';
    public static $filterOrderDateFromField      = '.admin__control-text[name="order_created_at[from]"]';
    public static $filterOrderDateToField        = '.admin__control-text[name="order_created_at[to]"]';
    public static $filterBaseGrandTotalFromField = '.admin__control-text[name="base_grand_total[from]"]';
    public static $filterBaseGrandTotalToField   = '.admin__control-text[name="base_grand_total[to]"]';
    public static $filterGrandTotalFromField     = '.admin__control-text[name="grand_total[from]"]';
    public static $filterGrandTotalToField       = '.admin__control-text[name="grand_total[to]"]';
    public static $filterPurchasedFromDropDown   = '.admin__control-select[name="store_id"]';
    public static $filterStatusDropDown          = '.admin__control-select[name="state"]';
    public static $filterInvoiceField            = '.admin__control-text[name="increment_id"]';
    public static $filterOrderField              = '.admin__control-text[name="order_increment_id"]';
    public static $filterBillToNameField         = '.admin__control-text[name="billing_name"]';

    public function enterFilterInvoiceDateFrom($invoiceDateFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterInvoiceDateFromField, $invoiceDateFrom);
    }

    public function enterFilterInvoiceDateTo($invoiceDateTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterInvoiceDateToField, $invoiceDateTo);
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

    public function enterFilterBaseGrandTotalFrom($baseGrandTotalFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterBaseGrandTotalFromField, $baseGrandTotalFrom);
    }

    public function enterFilterBaseGrandTotalTo($baseGrandTotalTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterBaseGrandTotalToField, $baseGrandTotalTo);
    }

    public function enterFilterGrandTotalFrom($grandTotalFrom)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterGrandTotalFromField, $grandTotalFrom);
    }

    public function enterFilterGrandTotalTo($grandTotalTo)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterGrandTotalToField, $grandTotalTo);
    }

    public function selectPurchasedFrom($purchasedFrom)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$filterPurchasedFromDropDown, $purchasedFrom);
    }

    public function selectStatus($status)
    {
        $I = $this->acceptanceTester;
        $I->selectOption(self::$filterStatusDropDown, $status);
    }

    public function enterInvoice($invoice)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterInvoiceField, $invoice);
    }

    public function enterOrder($order)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterOrderField, $order);
    }

    public function enterBillToName($billToName)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$filterBillToNameField, $billToName);
    }
}
