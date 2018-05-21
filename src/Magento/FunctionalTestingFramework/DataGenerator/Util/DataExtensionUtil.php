<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Util;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;

class DataExtensionUtil
{
    /**
     * ObjectExtensionUtil constructor.
     */
    public function __construct()
    {
        // empty
    }

    /**
     * Resolves test references for extending test objects
     *
     * @param EntityDataObject $entityObject
     * @return EntityDataObject
     * @throws XmlException
     */
    public function extendEntity($entityObject)
    {
        // Check to see if the parent test exists
        $parentEntity = DataObjectHandler::getInstance()->getObject($entityObject->getParentName());
        if ($parentEntity == null) {
            throw new XmlException(
                "Parent Entity " .
                $entityObject->getParentName() .
                " not defined for Entity " .
                $entityObject->getName() .
                "." .
                PHP_EOL
            );
        }

        // Check to see if the parent test is already an extended test
        if ($parentEntity->getParentName() !== null) {
            throw new XmlException(
                "Cannot extend an entity that already extends another entity. Entity: " .
                $parentEntity->getName() .
                "." .
                PHP_EOL
            );
        }
        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            echo("Extending Data: " . $parentEntity->getName() . " => " . $entityObject->getName() . PHP_EOL);
        }

        // Get all data for both parent and child and merge
        $referencedData = $parentEntity->getAllData();
        $newData = array_merge($referencedData, $entityObject->getAllData());

        // Get all linked references for both parent and child and merge
        $referencedLinks = $parentEntity->getLinkedEntities();
        $newLinkedReferences = array_merge($referencedLinks, $entityObject->getLinkedEntities());

        // Get all unique references for both parent and child and merge
        $referencedUniqueData = $parentEntity->getUniquenessData();
        $newUniqueReferences = array_merge($referencedUniqueData, $entityObject->getUniquenessData());

        // Get all var references for both parent and child and merge
        $referencedVars = $parentEntity->getVarReferences();
        $newVarReferences = array_merge($referencedVars, $entityObject->getVarReferences());

        // Remove unique references for objects that are replaced without such reference
        $unmatchedUniqueReferences = array_diff_key($referencedUniqueData, $entityObject->getUniquenessData());
        foreach ($unmatchedUniqueReferences as $uniqueKey => $uniqueData) {
            if (array_key_exists($uniqueKey, $entityObject->getAllData())) {
                unset($newUniqueReferences[$uniqueKey]);
            }
        }

        // Create new Test object to return
        // Create new Test object to return
        $extendedEntity = new EntityDataObject(
            $entityObject->getName(),
            $parentEntity->getType(),
            $newData,
            $newLinkedReferences,
            $newUniqueReferences,
            $newVarReferences,
            $entityObject->getParentName()
        );
        return $extendedEntity;
    }
}
