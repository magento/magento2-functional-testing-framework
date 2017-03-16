<?php
namespace Step\Api;

class Product extends \ApiTester
{
    protected $endpoint = 'products';

    /**
     * Create Magento Product by REST API
     *
     * @param array $params
     * @return string
     */
    public function createProduct(array $params)
    {
        $this->amBearerAuthenticated($this->getAuthToke());
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->sendPOST($this->endpoint, $params);
        $this->seeResponseCodeIs(200);
        return $this->grabDataFromResponseByJsonPath('$.id')[0];
    }

    /**
     * Get Magento Product Data by REST API
     *
     * @param string $sku
     * @return string
     */
    public function getProduct($sku)
    {
        $this->amBearerAuthenticated($this->getAuthToke());
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->sendGET($this->endpoint . "/$sku");
        $this->seeResponseCodeIs(200);
        return $this->grabDataFromResponseByJsonPath('$..*');
    }
}