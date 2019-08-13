<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Util;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;

class GenerationDataReferenceResolver implements DataReferenceResolverInterface
{
    /**
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
                        "Could not resolve entity reference \"{$matches['reference']}\" "
                        . "in Data entity \"{$originalDataEntity}\""
                    );
                }
                $entityData = $entityObject->getAllData();
                $result = $entityData[$var];
        }

        return $result;
    }
}
