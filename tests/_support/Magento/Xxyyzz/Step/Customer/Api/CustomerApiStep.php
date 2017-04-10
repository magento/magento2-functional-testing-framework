<?php
namespace Magento\Xxyyzz\Step\Customer\Api;

class CustomerApiStep extends \Magento\Xxyyzz\AcceptanceTester
{
    protected $endpoint = 'customers';

    /**
     * Create Magento Customer by REST API.
     *
     * @param array $params
     * @return string
     */
    public function createCustomer(array $params)
    {
        $I = $this;
        $I->amAdminTokenAuthenticated();
        $I->sendPOST($this->endpoint, $params);
        $I->seeResponseCodeIs(\Codeception\Util\HttpCode::OK);
        return $I->grabDataFromResponseByJsonPath('$.id')[0];
    }
}
