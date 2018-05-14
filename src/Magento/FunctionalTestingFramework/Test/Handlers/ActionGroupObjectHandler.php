<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Test\Handlers;

use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Parsers\ActionGroupDataParser;
use Magento\FunctionalTestingFramework\Test\Util\ActionGroupObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\ObjectExtensionUtil;

/**
 * Class ActionGroupObjectHandler
 */
class ActionGroupObjectHandler implements ObjectHandlerInterface
{
    const BEFORE_AFTER_ERROR_MSG = "Merge Error - Steps cannot have both before and after attributes.\tTestStep='%s'";
    const ACTION_GROUP_ROOT = 'actionGroups';
    const ACTION_GROUP = 'actionGroup';

    /**
     * Single instance of class var
     *
     * @var ActionGroupObjectHandler
     */
    private static $ACTION_GROUP_OBJECT_HANDLER;

    /**
     * Array of action groups indexed by name
     *
     * @var array
     */
    private $actionGroups = [];

    /**
     * Instance of ObjectExtensionUtil class
     *
     * @var ObjectExtensionUtil
     */
    private $extendUtil;

    /**
     * Singleton getter for instance of ActionGroupObjectHandler
     *
     * @return ActionGroupObjectHandler
     */
    public static function getInstance()
    {
        if (!self::$ACTION_GROUP_OBJECT_HANDLER) {
            self::$ACTION_GROUP_OBJECT_HANDLER = new ActionGroupObjectHandler();
            self::$ACTION_GROUP_OBJECT_HANDLER->initActionGroups();
        }

        return self::$ACTION_GROUP_OBJECT_HANDLER;
    }

    /**
     * ActionGroupObjectHandler constructor.
     */
    private function __construct()
    {
        $this->extendUtil = new ObjectExtensionUtil();
    }

    /**
     * Function to return a single object by name
     *
     * @param string $actionGroupName
     * @return ActionGroupObject
     */
    public function getObject($actionGroupName)
    {
        if (array_key_exists($actionGroupName, $this->actionGroups)) {
            $actionGroupObject = $this->actionGroups[$actionGroupName];
            return $this->extendActionGroup($actionGroupObject);
        }

        return null;
    }

    /**
     * Function to return all objects for which the handler is responsible
     *
     * @return array
     */
    public function getAllObjects()
    {
        foreach ($this->actionGroups as $actionGroupName => $actionGroup) {
            $this->actionGroups[$actionGroupName] = $this->extendActionGroup($actionGroup);
        }
        return $this->actionGroups;
    }

    /**
     * Method which populates field array with objects from parsed action_group.xml
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initActionGroups()
    {
        $actionGroupParser = ObjectManagerFactory::getObjectManager()->create(ActionGroupDataParser::class);
        $parsedActionGroups = $actionGroupParser->readActionGroupData();

        $actionGroupObjectExtractor = new ActionGroupObjectExtractor();

        foreach ($parsedActionGroups[ActionGroupObjectHandler::ACTION_GROUP_ROOT] as
                 $actionGroupName => $actionGroupData) {
            if (!is_array($actionGroupData)) {
                continue;
            }

            $this->actionGroups[$actionGroupName] =
                $actionGroupObjectExtractor->extractActionGroup($actionGroupData);
        }
    }

    /**
     * This method checks if the action group is extended and creates a new action group object accordingly
     *
     * @param ActionGroupObject $actionGroupObject
     * @return ActionGroupObject
     */
    private function extendActionGroup($actionGroupObject)
    {
        if ($actionGroupObject->getParentName() !== null) {
            return $this->extendUtil->extendActionGroup($actionGroupObject);
        }
        return $actionGroupObject;
    }
}
