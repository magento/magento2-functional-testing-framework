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
        $this->amBearerAuthenticated($this->getAdminAuthToken());
        $this->haveHttpHeader('Content-Type', 'application/json');
        $this->sendPOST($this->endpoint, $params);
        $this->seeResponseCodeIs(200);
        return $this->grabDataFromResponseByJsonPath('$.id')[0];
    }
}
