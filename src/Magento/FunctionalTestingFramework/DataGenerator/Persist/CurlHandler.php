<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\DataGenerator\Persist;

use Magento\FunctionalTestingFramework\Allure\AllureHelper;
use Magento\FunctionalTestingFramework\DataGenerator\Persist\Curl\AdminExecutor;
use Magento\FunctionalTestingFramework\DataGenerator\Persist\Curl\FrontendExecutor;
use Magento\FunctionalTestingFramework\DataGenerator\Persist\Curl\WebapiExecutor;
use Magento\FunctionalTestingFramework\DataGenerator\Persist\Curl\WebapiNoAuthExecutor;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\OperationDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\OperationDefinitionObject;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Protocol\CurlInterface;

/**
 * Class CurlHandler
 */
class CurlHandler
{
    /**
     * Describes the operation for the executor ('create','update','delete')
     *
     * @var string
     */
    private $operation;

    /**
     * The entity object data being created, updated, or deleted.
     *
     * @var EntityDataObject $entityObject
     */
    private $entityObject;

    /**
     * The data definitions used to map the operation.
     *
     * @var OperationDefinitionObject $operationDefinition
     */
    private $operationDefinition;

    /**
     * The request data.
     *
     * @var array
     */
    private $requestData;

    /**
     * Store code in web api rest url.
     *
     * @var string
     */
    private $storeCode;

    /**
     * If the content type is Json.
     *
     * @var boolean
     */
    private $isJson;

    /**
     * Operation to Curl method mapping.
     *
     * @var array
     */
    private static $curlMethodMapping = [
            'create' => CurlInterface::POST,
            'delete' => CurlInterface::DELETE,
            'update' => CurlInterface::PUT,
            'get' => CurlInterface::GET,
        ];

    /**
     * ApiSubObject constructor.
     *
     * @param string           $operation
     * @param EntityDataObject $entityObject
     * @param string           $storeCode
     */
    public function __construct($operation, $entityObject, $storeCode = null)
    {
        $this->operation = $operation;
        $this->entityObject = $entityObject;
        $this->storeCode = $storeCode;

        $this->operationDefinition = OperationDefinitionObjectHandler::getInstance()->getOperationDefinition(
            $this->operation,
            $this->entityObject->getType()
        );
        $this->isJson = false;
    }

    /**
     * Executes an api request based on parameters given by constructor.
     *
     * @param array $dependentEntities
     * @return array | null
     * @throws TestFrameworkException
     * @throws \Exception
     */
    public function executeRequest($dependentEntities)
    {
        $executor = null;
        $successRegex = null;
        $returnRegex = null;
        $returnIndex = null;

        if ((null !== $dependentEntities) && is_array($dependentEntities)) {
            $entities = array_merge([$this->entityObject], $dependentEntities);
        } else {
            $entities = [$this->entityObject];
        }
        $apiUrl = $this->resolveUrlReference($this->operationDefinition->getApiUrl(), $entities);
        $headers = $this->operationDefinition->getHeaders();
        $authorization = $this->operationDefinition->getAuth();
        $contentType = $this->operationDefinition->getContentType();
        $successRegex = $this->operationDefinition->getSuccessRegex();
        $returnRegex = $this->operationDefinition->getReturnRegex();
        $returnIndex = $this->operationDefinition->getReturnIndex();
        $method = $this->operationDefinition->getApiMethod();
        AllureHelper::addAttachmentToCurrentStep($apiUrl, 'API Endpoint');
        AllureHelper::addAttachmentToCurrentStep(json_encode($headers, JSON_PRETTY_PRINT), 'Request Headers');

        $operationDataResolver = new OperationDataArrayResolver($dependentEntities);
        $this->requestData = $operationDataResolver->resolveOperationDataArray(
            $this->entityObject,
            $this->operationDefinition->getOperationMetadata(),
            $this->operationDefinition->getOperation(),
            false
        );

        if (($contentType === 'application/json') && ($authorization === 'adminOauth')) {
            $this->isJson = true;
            $executor = new WebapiExecutor($this->storeCode);
        } elseif ($authorization === 'adminFormKey') {
            $executor = new AdminExecutor($this->operationDefinition->removeUrlBackend());
        } elseif ($authorization === 'customerFormKey') {
            $executor = new FrontendExecutor(
                $this->requestData['customer_email'],
                $this->requestData['customer_password']
            );
        } elseif ($authorization === 'anonymous') {
            $this->isJson = true;
            $executor = new WebapiNoAuthExecutor($this->storeCode);
        }

        if (!$executor) {
            throw new TestFrameworkException(
                sprintf(
                    "Invalid content type and/or auth type. content type = %s, auth type = %s\n",
                    $contentType,
                    $authorization
                )
            );
        }

        $executor->write(
            $apiUrl,
            $this->requestData,
            $method ?? self::$curlMethodMapping[$this->operation],
            $headers
        );

        $response = $executor->read($successRegex, $returnRegex, $returnIndex);
        $executor->close();

        AllureHelper::addAttachmentToCurrentStep(json_encode($this->requestData, JSON_PRETTY_PRINT), 'Request Body');
        AllureHelper::addAttachmentToCurrentStep(
            json_encode(json_decode($response, true), JSON_PRETTY_PRINT+JSON_UNESCAPED_UNICODE+JSON_UNESCAPED_SLASHES),
            'Response Data'
        );

        return $response;
    }

    /**
     * Getter for request data in array.
     *
     * @return array
     */
    public function getRequestDataArray()
    {
        return $this->requestData;
    }

    /**
     * If content type of a request is Json.
     *
     * @return boolean
     */
    public function isContentTypeJson()
    {
        return $this->isJson;
    }

    /**
     * Resolve rul reference from entity objects.
     *
     * @param string $urlIn
     * @param array  $entityObjects
     * @return string
     */
    private function resolveUrlReference($urlIn, $entityObjects)
    {
        $urlOut = $urlIn;
        $matchedParams = [];
        // Find all the params ({}) references
        preg_match_all("/[{](.+?)[}]/", $urlIn, $matchedParams);

        if (!empty($matchedParams)) {
            foreach ($matchedParams[0] as $paramKey => $paramValue) {
                $paramEntityParent = "";
                $matchedParent = [];
                $dataItem = $matchedParams[1][$paramKey];
                // Find all the parent property (Type.key) references, assuming there will be only one
                // parent property reference within one param
                preg_match_all("/(.+?)\./", $dataItem, $matchedParent);

                if (!empty($matchedParent) && !empty($matchedParent[0])) {
                    $paramEntityParent = $matchedParent[1][0];
                    $dataItem = preg_replace('/^'.$matchedParent[0][0].'/', '', $dataItem);
                }

                foreach ($entityObjects as $entityObject) {
                    $param = null;

                    if ($paramEntityParent === "" || $entityObject->getType() == $paramEntityParent) {
                        $param = $entityObject->getDataByName(
                            $dataItem,
                            EntityDataObject::CEST_UNIQUE_VALUE
                        );
                    }

                    if (null !== $param) {
                        $urlOut = str_replace($paramValue, $param, $urlOut);
                        continue;
                    }
                }
            }
        }
        return $urlOut;
    }
}
