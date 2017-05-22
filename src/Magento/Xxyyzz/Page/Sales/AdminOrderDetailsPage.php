<?php
namespace Magento\Xxyyzz\Page\Sales;

use Magento\Xxyyzz\Page\AbstractAdminPage;

class AdminOrderDetailsPage extends AbstractAdminPage
{
    /**
     * Declare UI map for this page here. CSS or XPath allowed.
     */
    public static $backButton = '#back';
    public static $cancelButton = '#order-view-cancel-button';
    public static $sendEmailButton = '#send_notification';
    public static $holdButton = '#order-view-hold-button';
    public static $invoiceButton = '#order_invoice';
    public static $shipButton = '#order_ship';
    public static $reorderButton = '#order_reorder';
    public static $editButton = '#order_edit';

    public static $youCreatedAnOrderSuccessMessage = '.message-success.success';

    public static $informationSectionLink = '#sales_order_view_tabs_order_info';
    public static $invoicesSectionLink = '#sales_order_view_tabs_order_invoices';
    public static $creditMemosSectionLink = '#sales_order_view_tabs_order_creditmemos';
    public static $shipmentsSectionLink = '#sales_order_view_tabs_order_shipments';
    public static $commentsHistoryLink = '#sales_order_view_tabs_order_history';

    /**
     * Sales - Orders - "ORDER VIEW" - Information Selectors
     */
    public static $orderIdTitle = '.order-information .title';
    public static $orderDateText = "//th[contains(text(), 'Order Date')]/parent::tr/td";
    public static $orderStatusText = '#order_status';
    public static $purchasedFromText = "//th[contains(text(), 'Purchased From')]/parent::tr/td";
    public static $customerNameText = '.order-account-information-table a[href*="customer/index/edit"]';
    public static $emailText = '.order-account-information-table a[href*="mailto"]';
    public static $customerGroupText = "//th[contains(text(), 'Customer Group')]/parent::tr/td";

    public static $billingAddressEditLink = '.order-billing-address .actions a';
    public static $billingAddressText = '.order-billing-address .admin__page-section-item-content';

    public static $shippingAddressEditLink = '.order-shipping-address .actions a';
    public static $shippingAddressText = '.order-shipping-address .admin__page-section-item-content';

    public static $paymentTypeText = '.order-payment-method-title';
    public static $orderCurrencyTypeText = '.order-payment-currency';
    public static $paymentAdditionalText = '.order-payment-additional';

    public static $shippingMethodNameText = '.order-shipping-method strong';
    public static $shippingMethodPriceText = '.order-shipping-method .price';

    // TODO: Add the "Items Ordered" selectors

    public static $orderStatusDropDown = '#history_status';
    public static $orderCommentsField = '#history_comment';
    public static $notifyCustomerByEmailCheckbox = '#history_notify';
    public static $visibleOnStorefrontCheckbox = '#history_visible';
    public static $submitCommentButton = '.order-history-comments-actions button';

    public static $subtotalPriceText = "//td[contains(text(), 'Subtotal')]/parent::tr/td/span/span";
    public static $shippingHandlingPriceText = "//td[contains(text(), 'Shipping & Handling')]/parent::tr";
    public static $grandTotalPriceText = "//strong[contains(text(), 'Grand Total')]/parent::td/parent::tr";
    public static $totalPaidPriceText = "//strong[contains(text(), 'Total Paid')]/parent::td/parent::tr";
    public static $totalRefundedPriceText = "//strong[contains(text(), 'Total Refunded')]/parent::td/parent::tr";
    public static $totalDuePriceText = "//strong[contains(text(), 'Total Due')]/parent::td/parent::tr";

    // TODO: Add the "Invoices" section Selectors
    // TODO: Add the "Credit Memos" section Selectors
    // TODO: Add the "Shipments" section Selectors
    // TODO: Add the "Comments History" section Selectors

    public function verifyThatYouCreatedAnOrderMessageIsPresent()
    {
        $I = $this->acceptanceTester;
        $I->seeElement(self::$youCreatedAnOrderSuccessMessage);
    }

    public function clickOnBackButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$backButton);
    }

    public function clickOnCancelButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$cancelButton);
    }

    public function clickOnSendEmailButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$sendEmailButton);
    }

    public function clickOnHoldButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$holdButton);
    }

    public function clickOnInvoiceButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$invoiceButton);
    }

    public function clickOnShipButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$shipButton);
    }

    public function clickOnReorderButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$reorderButton);
    }

    public function clickOnEditOrderButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$editButton);
    }

    public function clickOnInformationSection()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$informationSectionLink);
    }

    public function clickOnInvoicesSection()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$invoicesSectionLink);
    }

    public function clickOnCreditMemosSection()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$creditMemosSectionLink);
    }

    public function clickOnShipmentsSection()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$shipmentsSectionLink);
    }

    public function clickOnCommentsHistorySection()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$commentsHistoryLink);
    }

    public function verifyThereIsAnOrderNumber()
    {
        $I = $this->acceptanceTester;
        $I->see('Order #', self::$orderIdTitle);
    }

    public function verifyThatTheOrderWasPlacedToday()
    {
        $I = $this->acceptanceTester;
        $today = date("M j, Y");
        $I->see($today, self::$orderDateText);
    }

    public function verifyOrderStatus($expectedStatus)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedStatus, self::$orderStatusText);
    }

    public function verifyOrderStatusPending()
    {
        self::verifyOrderStatus('Pending');
    }

    public function verifyPurchasedFrom($expectedStore)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedStore, self::$purchasedFromText);
    }

    public function verifyPurchasedFromDefaultStoreView()
    {
        self::verifyPurchasedFrom('Default Store View');
    }

    public function verifyCustomerName($customerName)
    {
        $I = $this->acceptanceTester;
        $I->see($customerName, self::$customerNameText);
    }

    public function verifyCustomerEmail($customerEmail)
    {
        $I = $this->acceptanceTester;
        $I->see($customerEmail, self::$emailText);
    }

    public function verifyCustomerGroup($customerGroup)
    {
        $I = $this->acceptanceTester;
        $I->see($customerGroup, self::$customerGroupText);
    }

    public function verifyBillingFirstName($billingFirstName)
    {
        $I = $this->acceptanceTester;
        $I->see($billingFirstName, self::$billingAddressText);
    }

    public function verifyBillingLastName($billingLastName)
    {
        $I = $this->acceptanceTester;
        $I->see($billingLastName, self::$billingAddressText);
    }

    public function verifyBillingCompany($billingCompany)
    {
        $I = $this->acceptanceTester;
        $I->see($billingCompany, self::$billingAddressText);
    }

    public function verifyBillingAddress1($billingAddress1)
    {
        $I = $this->acceptanceTester;
        $I->see($billingAddress1, self::$billingAddressText);
    }

    public function verifyBillingAddress2($billingAddress2)
    {
        $I = $this->acceptanceTester;
        $I->see($billingAddress2, self::$billingAddressText);
    }

    public function verifyBillingCity($billingCity)
    {
        $I = $this->acceptanceTester;
        $I->see($billingCity, self::$billingAddressText);
    }

    public function verifyBillingCountry($billingCountry)
    {
        $I = $this->acceptanceTester;
        $I->see($billingCountry, self::$billingAddressText);
    }

    public function verifyBillingState($billingState)
    {
        $I = $this->acceptanceTester;
        $I->see($billingState, self::$billingAddressText);
    }

    public function verifyBillingProvince($billingProvince)
    {
        $I = $this->acceptanceTester;
        $I->see($billingProvince, self::$billingAddressText);
    }

    public function verifyBillingZipPostalCode($billingZipPostalCode)
    {
        $I = $this->acceptanceTester;
        $I->see($billingZipPostalCode, self::$billingAddressText);
    }

    public function verifyBillingPhoneNumber($billingPhoneNumber)
    {
        $I = $this->acceptanceTester;
        $I->see($billingPhoneNumber, self::$billingAddressText);
    }

    public function verifyBillingVatTaxNumber($billingVatTaxNumber)
    {
        $I = $this->acceptanceTester;
        $I->see($billingVatTaxNumber, self::$billingAddressText);
    }

    public function verifyBillingAddressInformation($customerData)
    {
        self::verifyBillingFirstName($customerData['firstname']);
        self::verifyBillingLastName($customerData['lastname']);
        self::verifyBillingCompany($customerData['company']);
        self::verifyBillingAddress1($customerData['address']['address1']);
        self::verifyBillingAddress2($customerData['address']['address2']);
        self::verifyBillingCity($customerData['address']['city']);
        self::verifyBillingState($customerData['address']['state']);
        self::verifyBillingZipPostalCode($customerData['address']['zipCode']);
        self::verifyBillingCountry($customerData['address']['country']);
        self::verifyBillingPhoneNumber($customerData['phoneNumber']);
        self::verifyBillingVatTaxNumber($customerData['taxVatNumber']);
    }

    public function verifyShippingFirstName($shippingFirstName)
    {
        $I = $this->acceptanceTester;
        $I->see($shippingFirstName, self::$shippingAddressText);
    }

    public function verifyShippingLastName($shippingLastName)
    {
        $I = $this->acceptanceTester;
        $I->see($shippingLastName, self::$shippingAddressText);
    }

    public function verifyShippingCompany($shippingCompany)
    {
        $I = $this->acceptanceTester;
        $I->see($shippingCompany, self::$shippingAddressText);
    }

    public function verifyShippingAddress1($shippingAddress1)
    {
        $I = $this->acceptanceTester;
        $I->see($shippingAddress1, self::$shippingAddressText);
    }

    public function verifyShippingAddress2($shippingAddress2)
    {
        $I = $this->acceptanceTester;
        $I->see($shippingAddress2, self::$shippingAddressText);
    }

    public function verifyShippingCity($shippingCity)
    {
        $I = $this->acceptanceTester;
        $I->see($shippingCity, self::$shippingAddressText);
    }

    public function verifyShippingCountry($shippingCountry)
    {
        $I = $this->acceptanceTester;
        $I->see($shippingCountry, self::$shippingAddressText);
    }

    public function verifyShippingState($shippingState)
    {
        $I = $this->acceptanceTester;
        $I->see($shippingState, self::$shippingAddressText);
    }

    public function verifyShippingProvince($shippingProvince)
    {
        $I = $this->acceptanceTester;
        $I->see($shippingProvince, self::$shippingAddressText);
    }

    public function verifyShippingZipPostalCode($shippingZipPostalCode)
    {
        $I = $this->acceptanceTester;
        $I->see($shippingZipPostalCode, self::$shippingAddressText);
    }

    public function verifyShippingPhoneNumber($shippingPhoneNumber)
    {
        $I = $this->acceptanceTester;
        $I->see($shippingPhoneNumber, self::$shippingAddressText);
    }

    public function verifyShippingVatTaxNumber($shippingVatTaxNumber)
    {
        $I = $this->acceptanceTester;
        $I->see($shippingVatTaxNumber, self::$shippingAddressText);
    }

    public function verifyShippingAddressInformation($customerData)
    {
        self::verifyShippingFirstName($customerData['firstname']);
        self::verifyShippingLastName($customerData['lastname']);
        self::verifyShippingCompany($customerData['company']);
        self::verifyShippingAddress1($customerData['address']['address1']);
        self::verifyShippingAddress2($customerData['address']['address2']);
        self::verifyShippingCity($customerData['address']['city']);
        self::verifyShippingState($customerData['address']['state']);
        self::verifyShippingZipPostalCode($customerData['address']['zipCode']);
        self::verifyShippingCountry($customerData['address']['country']);
        self::verifyShippingPhoneNumber($customerData['phoneNumber']);
        self::verifyShippingVatTaxNumber($customerData['taxVatNumber']);
    }

    public function verifyPaymentType($expectedPaymentType)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedPaymentType, self::$paymentTypeText);
    }

    public function verifyPaymentTypeCheckMoneyOrder()
    {
        self::verifyPaymentType('Check / Money order');
    }

    public function verifyPaymentCurrencyType($expectedCurrency)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedCurrency, self::$orderCurrencyTypeText);
    }

    public function verifyPaymentCurrencyUSD()
    {
        self::verifyPaymentCurrencyType('USD');
    }

    public function verifyShippingMethodType($expectedShippingMethod)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedShippingMethod, self::$shippingMethodNameText);
    }

    public function verifyShippingMethodFixedRate()
    {
        self::verifyShippingMethodType('Flat Rate - Fixed');
    }

    public function verifyShippingMethodPrice($expectedPrice)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedPrice, self::$shippingMethodPriceText);
    }

    public function verifyItemsOrderedFor($productDetails)
    {
        $I = $this->acceptanceTester;
        $selector =  "//div[contains(@class, 'product-title')][contains(text(), '" . $productDetails['productName'] . "')]/parent::div/parent::td/parent::tr";

        $I->see($productDetails['productName'], $selector);
        $I->see($productDetails['sku'], $selector);
        $I->see($productDetails['price'], $selector);

        // TODO: Add more "Items Ordered" Methods
    }

    public function verifyOrderStatusDropDown($expectedStatus)
    {
        $I = $this->acceptanceTester;
        $I->seeOptionIsSelected(self::$orderStatusDropDown, $expectedStatus);
    }

    public function verifyOrderStatusDropDownPending()
    {
        self::verifyOrderStatusDropDown('Pending');
    }

    public function verifyOrderComments($expectedOrderComments)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedOrderComments, self::$orderCommentsField);
    }

    public function enterOrderComments($orderComments)
    {
        $I = $this->acceptanceTester;
        $I->fillField(self::$orderCommentsField, $orderComments);
    }

    public function clickOnNotifyCustomerByEmail()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$notifyCustomerByEmailCheckbox);
    }

    public function clickOnVisibleOnStorefront()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$visibleOnStorefrontCheckbox);
    }

    public function clickOnSubmitCommentButton()
    {
        $I = $this->acceptanceTester;
        $I->click(self::$submitCommentButton);
    }

    public function verifySubTotalPrice($expectedSubTotal)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedSubTotal, self::$subtotalPriceText);
    }

    public function verifyShippingHandlingPrice($expectedShippingHandling)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedShippingHandling, self::$shippingHandlingPriceText);
    }

    public function verifyGrandTotalPrice($expectedGrandTotal)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedGrandTotal, self::$grandTotalPriceText);
    }

    public function verifyTotalPaidPrice($expectedTotalPaid)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedTotalPaid, self::$totalPaidPriceText);
    }

    public function verifyTotalRefundedPrice($expectedRefunded)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedRefunded, self::$totalRefundedPriceText);
    }

    public function verifyTotalDuePrice($expectedTotalDue)
    {
        $I = $this->acceptanceTester;
        $I->see($expectedTotalDue, self::$totalDuePriceText);
    }
}