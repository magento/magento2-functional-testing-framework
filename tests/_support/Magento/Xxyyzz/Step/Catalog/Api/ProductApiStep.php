<?php
namespace Magento\Xxyyzz\Step\Catalog\Api;

class ProductApiStep extends \Magento\Xxyyzz\AcceptanceTester
{
    protected $endpoint = 'products';

    /**
     * Create Magento Product by REST API.
     *
     * @param array $params
     * @return string
     */
    public function createProduct(array $params)
    {
        $I = $this;
        $I->amAdminTokenAuthenticated();
        $I->sendPOST($this->endpoint, $params);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        return $I->grabDataFromResponseByJsonPath('$.id')[0];
    }

    /**
     * Get Magento Product Data by REST API.
     *
     * @param string $sku
     * @return string
     */
    public function getProduct($sku)
    {
        $I = $this;
        $I->amAdminTokenAuthenticated();
        $I->sendGET($this->endpoint . "/$sku");
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        return $I->grabDataFromResponseByJsonPath('$..*');
    }
}
