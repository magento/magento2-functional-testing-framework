<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Handlers;

use Magento\FunctionalTestingFramework\DataGenerator\Persist\DataPersistenceHandler;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;

class PersistedObjectHandler
{
    const HOOK_SCOPE = "hook";
    const TEST_SCOPE = "test";

    /**
     * The singleton instance of this class
     *
     * @var PersistedObjectHandler $INSTANCE
     */
    private static $INSTANCE;

    /**
     * Store of all hook created objects
     * @var DataPersistenceHandler[] array
     */
    private $hookObjects = [];

    /**
     * Store of all test created objects
     * @var DataPersistenceHandler[] array
     */
    private $testObjects = [];

    /**
     * Constructor
     */
    private function __construct()
    {
        // Empty Constructor
    }

    /**
     * Return the singleton instance of this class. Initialize it if needed.
     *
     * @return PersistedObjectHandler
     * @throws \Exception
     */
    public static function getInstance()
    {
        if (!self::$INSTANCE) {
            self::$INSTANCE = new PersistedObjectHandler();
        }
        return self::$INSTANCE;
    }

    /**
     * Creates and stores the entity.
     * @param string $key StepKey of the createData action.
     * @param string $scope
     * @param string $entity Name of xml entity to create.
     * @param array $dependentObjectKeys StepKeys of other createData actions that are required.
     * @param array $overrideFields Array of FieldName => Value of override fields.
     * @param string $storeCode
     * @return void
     */
    public function createEntity(
        $key,
        $scope,
        $entity,
        $dependentObjectKeys = [],
        $overrideFields = [],
        $storeCode = ""
    ) {
        $retrievedDependentObjects = [];
        foreach ($dependentObjectKeys as $objectKey) {
            $retrievedDependentObjects = $this->retrieveEntity($objectKey, $scope);
        }
        
        $retrievedEntity = DataObjectHandler::getInstance()->getObject($entity);
        $persistedObject = new DataPersistenceHandler(
            $retrievedEntity,
            $retrievedDependentObjects,
            $overrideFields
        );
        
        $persistedObject->createEntity($storeCode);

        if ($scope == self::TEST_SCOPE) {
            $this->testObjects[$key] = $persistedObject;
        } else {
            $this->hookObjects[$key] = $persistedObject;
        }
    }

    /**
     * Retrieves and updates a previously created entity.
     * @param string $key StepKey of the createData action.
     * @param $scope
     * @param string $updateEntity Name of the static XML data to update the entity with.
     * @param array $dependentObjectKeys StepKeys of other createData actions that are required.
     * @return void
     */
    public function updateEntity($key, $scope, $updateEntity, $dependentObjectKeys = [])
    {
        $retrievedDependentObjects = [];
        foreach ($dependentObjectKeys as $objectKey) {
            $retrievedDependentObjects = $this->retrieveEntity($objectKey, $scope);
        }
        
        $originalEntity = $this->retrieveEntity($key, $scope);
        $originalEntity->updateEntity($updateEntity, $retrievedDependentObjects);
    }

    /**
     * Retrieves and deletes a previously created entity.
     * @param string $key StepKey of the createData action.
     * @param string $scope
     * @return void
     */
    public function deleteEntity($key, $scope)
    {
        $originalEntity = $this->retrieveEntity($key, $scope);
        $originalEntity->deleteEntity();
    }

    /**
     * Performs GET on given entity and stores entity for use.
     * @param string $key StepKey of getData action.
     * @param string $scope
     * @param string $entity Name of XML static data to use.
     * @param array $dependentObjectKeys StepKeys of other createData actions that are required.
     * @param string $storeCode
     * @param integer $index
     * @return void
     */
    public function getEntity($key, $scope, $entity, $dependentObjectKeys = [], $storeCode = "", $index = null)
    {
        $retrievedDependentObjects = [];
        foreach ($dependentObjectKeys as $objectKey) {
            $retrievedDependentObjects = $this->retrieveEntity($objectKey, $scope);
        }

        $retrievedEntity = DataObjectHandler::getInstance()->getObject($entity);
        $persistedObject = new DataPersistenceHandler(
            $retrievedEntity,
            $retrievedDependentObjects
        );
        $persistedObject->getEntity($index, $storeCode);

        if ($scope == self::TEST_SCOPE) {
            $this->testObjects[$key] = $persistedObject;
        } else {
            $this->hookObjects[$key] = $persistedObject;
        }
    }

    /**
     * Attempts to retrieve Entity from given scope, falling back to other scope if not found.
     * @param $key
     * @param $scope
     * @return DataPersistenceHandler
     * @throws TestReferenceException
     */
    private function retrieveEntity($key, $scope)
    {
        if ($scope == self::TEST_SCOPE) {
            if (array_key_exists($key, $this->testObjects)) {
                return $this->testObjects[$key];
            } elseif (array_key_exists($key, $this->hookObjects)) {
                return $this->hookObjects[$key];
            }
        } else {
            if (array_key_exists($key, $this->hookObjects)) {
                return $this->hookObjects[$key];
            } elseif (array_key_exists($key, $this->testObjects)) {
                return $this->testObjects[$key];
            }
        }
        throw new TestReferenceException("Entity with a CreateDataKey of {$key} could not be found");
    }

    /**
     * Clears store of all persisted Objects
     * @return void
     */
    public function clearPersistedObjects()
    {
        $this->hookObjects = [];
        $this->testObjects = [];
    }
}
