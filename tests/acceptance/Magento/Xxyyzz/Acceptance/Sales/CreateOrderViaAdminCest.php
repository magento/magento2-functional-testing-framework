<?php
namespace Magento\Xxyyzz\Acceptance\Sales;

use Magento\Xxyyzz\Page\Catalog\AdminCategoryPage;
use Magento\Xxyyzz\Page\Catalog\AdminProductPage;
use Magento\Xxyyzz\Page\Customer\AdminCustomerPage;
use Magento\Xxyyzz\Page\Sales\AdminOrderAddPage;
use Magento\Xxyyzz\Page\Sales\AdminOrderDetailsPage;
use Magento\Xxyyzz\Page\Sales\AdminOrderGrid;
use Magento\Xxyyzz\Step\Backend\AdminStep;
use Yandex\Allure\Adapter\Annotation\Stories;
use Yandex\Allure\Adapter\Annotation\Features;
use Yandex\Allure\Adapter\Annotation\Title;
use Yandex\Allure\Adapter\Annotation\Description;
use Yandex\Allure\Adapter\Annotation\Severity;
use Yandex\Allure\Adapter\Annotation\Parameter;
use Yandex\Allure\Adapter\Model\SeverityLevel;

/**
 * Class CreateOrderViaAdminCest
 *
 * Allure annotations
 * @Features({"Sales"})
 * @Stories({"Create an Order via the Admin"})
 *
 * Codeception annotations
 * @group add
 * @group sales
 * @group orders
 * @env chrome
 * @env firefox
 * @env phantomjs
 */
class CreateOrderViaAdminCest
{
    public function _before(AdminStep $I)
    {
        $I->loginAsAdmin();
    }

    /**
     * Allure annotations
     * @Title("Create an Order via the Admin")
     * @Description("Setup a Category, Product, Customer and place an Order using them via the Admin.")
     * @Severity(level = SeverityLevel::CRITICAL)
     * @Parameter(name = "AdminStep", value = "$I")
     * @Parameter(name = "AdminOrderGrid", value = "$adminOrderGrid")
     * @Parameter(name = "AdminOrderPage", value = "$adminOrderAddPage")
     * @Parameter(name = "AdminOrderDetailsPage", value = "$adminOrderDetailsPage")
     *
     * Codeception annotations
     * @param AdminStep $I
     * @param AdminCustomerPage $adminCustomerPage
     * @param AdminCategoryPage $adminCategoryPage
     * @param AdminProductPage $adminProductPage
     * @param AdminOrderGrid $adminOrderGrid
     * @param AdminOrderAddPage $adminOrderAddPage
     * @param AdminOrderDetailsPage $adminOrderDetailsPage
     * @return void
     */
    public function createOrderViaAdmin(
        AdminStep $I,
        AdminCustomerPage $adminCustomerPage,
        AdminCategoryPage $adminCategoryPage,
        AdminProductPage $adminProductPage,
        AdminOrderGrid $adminOrderGrid,
        AdminOrderAddPage $adminOrderAddPage,
        AdminOrderDetailsPage $adminOrderDetailsPage
    )
    {
        $customerDetails = $I->getCustomerData();
        $categoryDetails = $I->getCategoryData();
        $productDetails  = $I->getProductData();

        $customerName = $customerDetails['firstname'] . " " . $customerDetails['lastname'];

        $I->goToTheAdminAllCustomersGrid();
        $adminCustomerPage->addBasicCustomerWithAddress($customerDetails);

        $I->goToTheAdminCategoriesPage();
        $adminCategoryPage->addBasicCategory($categoryDetails);

        $I->goToTheAdminCatalogPage();
        $adminProductPage->addBasicProductUnderCategory($productDetails, $categoryDetails);

        $I->goToTheAdminOrdersGrid();
        $adminOrderGrid->clickOnCreateNewOrderButton();

        $adminOrderAddPage->enterCustomerEmailSearchTerm($customerDetails['email']);
        $adminOrderAddPage->clickOnCustomerSearchButton();
        $adminOrderAddPage->clickOnCustomerFor($customerDetails['email']);

        $adminOrderAddPage->clickOnDefaultStoreView();

        $adminOrderAddPage->clickOnAddProductsButton();
        $adminOrderAddPage->enterProductSkuSearchField($productDetails['sku']);
        $adminOrderAddPage->clickOnProductsSearchButton();
        $adminOrderAddPage->clickOnProductSkuFor($productDetails['sku']);
        $adminOrderAddPage->clickOnAddSelectedProductsToOrderButton();

        $adminOrderAddPage->clickOnGetShippingMethodsAndRatesLink();
        $adminOrderAddPage->clickOnFixedShippingMethod();

        $adminOrderAddPage->clickOnBottomSubmitButton();

        $adminOrderDetailsPage->verifyThatYouCreatedAnOrderMessageIsPresent();
        $adminOrderDetailsPage->verifyThereIsAnOrderNumber();
        $adminOrderDetailsPage->verifyThatTheOrderWasPlacedToday();
        $adminOrderDetailsPage->verifyOrderStatusPending();
        $adminOrderDetailsPage->verifyPurchasedFromDefaultStoreView();

        $adminOrderDetailsPage->verifyThatYouCreatedAnOrderMessageIsPresent();
        $adminOrderDetailsPage->verifyThatTheOrderWasPlacedToday();
        $adminOrderDetailsPage->verifyOrderStatusPending();
        $adminOrderDetailsPage->verifyPurchasedFromDefaultStoreView();
        $adminOrderDetailsPage->verifyCustomerName($customerName);
        $adminOrderDetailsPage->verifyCustomerEmail($customerDetails['email']);
        $adminOrderDetailsPage->verifyCustomerGroup('General');

        $adminOrderDetailsPage->verifyBillingAddressInformation($customerDetails);
        $adminOrderDetailsPage->verifyShippingAddressInformation($customerDetails);

        $adminOrderDetailsPage->verifyPaymentTypeCheckMoneyOrder();
        $adminOrderDetailsPage->verifyPaymentCurrencyUSD();

        $adminOrderDetailsPage->verifyShippingMethodFixedRate();
        $adminOrderDetailsPage->verifyShippingHandlingPrice('$0.00');
        
        // TODO: Add verification for Product Details in the Order
        $adminOrderDetailsPage->verifyItemsOrderedFor($productDetails);

        $adminOrderDetailsPage->verifyOrderStatusDropDownPending();
        $adminOrderDetailsPage->verifyOrderComments('');

        $adminOrderDetailsPage->verifySubTotalPrice($productDetails['price']);
        $adminOrderDetailsPage->verifyShippingHandlingPrice('$0.00');
   }
}