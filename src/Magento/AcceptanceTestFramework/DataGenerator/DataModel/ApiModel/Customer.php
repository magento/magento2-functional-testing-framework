<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\DataModel\ApiModel;

use Magento\AcceptanceTestFramework\DataGenerator\DataModel\EntityPersistenceInterface;
use Magento\AcceptanceTestFramework\Helper\EntityRESTApiHelper;

class Customer implements EntityPersistenceInterface
{
    private $entityObject;
    private static $entityRESTApiHelper;
    private static $specialDefinitions = ["address", "customattributes"];

    const CUSTOMER_CREATE_API_PATH = '/rest/V1/customers/1';

    public function __construct($entityObject)
    {
        $this->entityObject = $entityObject;

        if (!self::$entityRESTApiHelper) {
            self::$entityRESTApiHelper = new EntityRESTApiHelper(getenv('HOSTNAME'), getenv('PORT'));
        }
    }

    public function create()
    {
        $response = self::$entityRESTApiHelper->submitAuthAPiRequest(
            'PUT',
            self::CUSTOMER_CREATE_API_PATH,
            $this->entityDataToJson(),
            EntityRESTApiHelper::APPLICATION_JSON_HEADER
        );

        return $response;
    }

    public function delete()
    {
        // TODO implement delete method via customer web-api;
    }

    private function entityDataToJson()
    {
        $data = $this->entityObject->getData();

        foreach (self::$specialDefinitions as $specialKey) {
            if (array_key_exists($specialKey, $data)) {
                // logic to handle special case (new obj?)
            }
        }

        $entityArray = [
            strtolower($this->entityObject->getType()) => $data,

            // this passwordHash param is necessary to create a new customer.
            'passwordHash' => 'someHash'];

        $json = \GuzzleHttp\json_encode($entityArray);

        return $json;
    }
}
