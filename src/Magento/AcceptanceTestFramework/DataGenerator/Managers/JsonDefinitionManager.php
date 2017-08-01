<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Managers;

use Magento\AcceptanceTestFramework\DataGenerator\Objects\JsonDefinition;
use Magento\AcceptanceTestFramework\DataGenerator\Objects\JsonElement;
use Magento\AcceptanceTestFramework\DataGenerator\Parsers\MetadataParser;
use Magento\AcceptanceTestFramework\ObjectManagerFactory;

class JsonDefinitionManager
{
    private static $jsonDefinitionManager;
    private $jsonDefinitions;

    public static function getInstance()
    {
        if (!self::$jsonDefinitionManager) {
            self::$jsonDefinitionManager = new JsonDefinitionManager();
        }

        return self::$jsonDefinitionManager;
    }

    private function __construct()
    {
        // do nothing
    }

    private function getObjects()
    {
        $objectManager = ObjectManagerFactory::getObjectManager();
        $metadataParser = $objectManager->create(MetadataParser::class);
        foreach ($metadataParser->readMetadata()['metadata'] as $jsonDefName => $jsonDefArray) {
            $operation = $jsonDefArray['operation'];
            $dataType = $jsonDefArray['dataType'];
            $url = $jsonDefArray['url'] ?? null;
            $method = $jsonDefArray['method'] ?? null;
            $auth = $jsonDefArray['auth'] ?? null;
            $headers = [];
            $params = [];
            $jsonMetadata = [];

            if (array_key_exists('header', $jsonDefArray)) {
                foreach ($jsonDefArray['header'] as $headerEntry) {
                    $headers[] = $headerEntry['param'] . ': ' . $headerEntry['value'];
                }
            }

            if (array_key_exists('param', $jsonDefArray)) {
                foreach ($jsonDefArray['param'] as $paramEntry) {
                    $params[$paramEntry['type']][$paramEntry['key']] = $paramEntry['value'];
                }
            }

            if (array_key_exists('entry', $jsonDefArray)) {
                foreach ($jsonDefArray['entry'] as $jsonEntryType) {
                    $jsonMetadata[] = new JsonElement($jsonEntryType['key'], $jsonEntryType['value'], 'entry');
                }
            }

            if (array_key_exists('array', $jsonDefArray)) {
                foreach ($jsonDefArray['array'] as $jsonEntryType) {
                    $jsonMetadata[] = new JsonElement($jsonEntryType['key'], $jsonEntryType['value'], 'array');
                }
            }

            $this->jsonDefinitions[$operation][$dataType] = new JsonDefinition(
                $jsonDefName,
                $operation,
                $dataType,
                $method,
                $url,
                $auth,
                $headers,
                $params,
                $jsonMetadata
            );
        }
    }

    /**
     * @param $type
     * @return JsonDefinition
     */
    public function getJsonDefinition($operation, $type)
    {
        if (!$this->jsonDefinitions) {
            $this->getObjects();
        }

        return $this->jsonDefinitions[$operation][$type];
    }
}
