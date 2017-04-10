<?php
namespace Magento\Xxyyzz\Step\Catalog\Api;

class CategoryApiStep extends \Magento\Xxyyzz\AcceptanceTester
{
    protected $endpoint = 'categories';

    /**
     * Create Magento Category by REST API.
     *
     * @param array $params
     * @return string
     */
    public function createCategory(array $params)
    {
        $I = $this;
        $I->amAdminTokenAuthenticated();
        $I->sendPOST($this->endpoint, $params);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        return $I->grabDataFromResponseByJsonPath('$.id')[0];
    }

    /**
     * Get Magento Category Data by REST API.
     *
     * @param string $id
     * @return string
     */
    public function getCategory($id)
    {
        $I = $this;
        $I->amAdminTokenAuthenticated();
        $I->sendGET($this->endpoint . "/$id");
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        return $I->grabResponse();
    }
}
