<?php

namespace Magento\AcceptanceTestFramework\DataGenerator\Managers;

class EntityDataManager
{
    private static $entityManagers = [];

    public static function getDataManager($type)
    {
        $typeManagerName = $type . 'EntityManager';
        if (!array_key_exists($typeManagerName, self::$entityManagers)) {
            self::$entityManagers[$typeManagerName] = new DataManager($type);
        }

        return self::$entityManagers[$typeManagerName];
    }
}