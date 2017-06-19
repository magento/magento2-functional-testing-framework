<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\DataModel;

interface EntityPersistenceInterface
{
    /**
     * EntityPersistenceInterface constructor. Needs entityObject.
     * @param $entityObject
     */
    public function __construct($entityObject);

    /**
     * Inserts entity record.
     * @return mixed
     */
    public function create();

    /**
     * Deletes entity record.
     * @return mixed
     */
    public function delete();
}
