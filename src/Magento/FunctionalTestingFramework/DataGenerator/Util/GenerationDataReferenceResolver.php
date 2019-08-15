<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;

/**
 * Class resolves data references in data entities while generating static test files.
 */
class GenerationDataReferenceResolver implements DataReferenceResolverInterface
{
    /**
     * Returns data uniqueness for data entity field.
     *
     *
     * @param string $data
     * @param string $originalDataEntity
     * @return string|null
     * @throws TestReferenceException
     */
    public function getDataUniqueness(string $data, string $originalDataEntity)
    {
        preg_match(
            ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN,
            $data,
            $matches
        );

        if (empty($matches['reference'])) {
            return null;
        }

        $strippedReference = str_replace(['{{', '}}'], '', $matches['reference']);
        list($entity, $var) = explode('.', $strippedReference);
        $entityObject = DataObjectHandler::getInstance()->getObject($entity);
        if ($entityObject === null) {
            throw new TestReferenceException(
                "Could not resolve entity reference \"{$matches['reference']}\" "
                . "in Data entity \"{$originalDataEntity}\""
            );
        }

        return $entityObject->getUniquenessDataByName($var);
    }

    /**
     * Returns data by reference if reference exist.
     *
     * @param string $data
     * @param string $originalDataEntity
     * @return string|null
     * @throws TestReferenceException
     */
    public function getDataReference(string $data, string $originalDataEntity)
    {
        $result = null;
        preg_match(self::REFERENCE_REGEX_PATTERN, $data, $matches);

        if (empty($matches['reference'])) {
            return $data;
        }

        $strippedReference = str_replace(['{{', '}}'], '', $matches['reference']);
        list($entity, $var) = explode('.', $strippedReference);
        switch ($entity) {
            case ActionObject::__ENV:
            case ActionObject::__CREDS:
                $result = $data;
                break;
            default:
                $entityObject = DataObjectHandler::getInstance()->getObject($entity);
                if ($entityObject === null) {
                    throw new TestReferenceException(
                        "Could not find data entity by name \"{$entityObject}\" "
                        . "referenced in Data entity \"{$originalDataEntity}\"" . PHP_EOL
                    );
                }
                $entityData = $entityObject->getAllData();
                if (!isset($entityData[$var])) {
                    throw new TestReferenceException(
                        "Could not resolve entity reference \"{$matches['reference']}\" "
                        . "in Data entity \"{$originalDataEntity}\"" . PHP_EOL
                    );
                }
                $result = $entityData[$var];
        }

        return $result;
    }
}
