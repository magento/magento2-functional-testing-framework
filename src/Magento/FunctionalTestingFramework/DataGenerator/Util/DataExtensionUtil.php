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
        try {
            $parentEntity = DataObjectHandler::getInstance()->getObject($entityObject->getParentName());
        } catch (XmlException $error) {
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
                "Cannot extend an entity that already extends another entity. Entity: " . $parentEntity->getName()
            );
        }
        if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
            echo("Extending Test: " . $parentEntity->getName() . " => " . $entityObject->getName() . PHP_EOL);
        }

        // Get all data for both parent and child and merge
        $referencedTestSteps = $parentEntity->getAllData();
        $newData = array_merge($referencedTestSteps, $entityObject->getAllData());

        // Get all var references for both parent and child and merge
        $referencedTestSteps = $parentEntity->getVarReferences();
        $newVarReferences = array_merge($referencedTestSteps, $entityObject->getVarReferences());

        // Create new Test object to return
        $extendedEntity = new EntityDataObject(
            $entityObject->getName(),
            $parentEntity->getType(),
            $newData,
            $parentEntity->getLinkedEntities(),
            $parentEntity->getUniquenessData(),
            $newVarReferences
        );
        return $extendedEntity;
    }
}
