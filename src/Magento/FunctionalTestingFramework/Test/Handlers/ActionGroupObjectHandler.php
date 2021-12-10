<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Test\Handlers;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Parsers\ActionGroupDataParser;
use Magento\FunctionalTestingFramework\Test\Util\ActionGroupObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\ObjectExtensionUtil;
use Magento\FunctionalTestingFramework\Util\Validation\NameValidationUtil;

/**
 * Class ActionGroupObjectHandler
 */
class ActionGroupObjectHandler implements ObjectHandlerInterface
{
    const BEFORE_AFTER_ERROR_MSG = "Merge Error - Steps cannot have both before and after attributes.\tTestStep='%s'";
    const ACTION_GROUP_ROOT = 'actionGroups';
    const ACTION_GROUP = 'actionGroup';
    const ACTION_GROUP_FILENAME_ATTRIBUTE = 'filename';

    /**
     * Single instance of class var
     *
     * @var ActionGroupObjectHandler
     */
    private static $instance;

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
     * @throws XmlException
     */
    public static function getInstance(): ActionGroupObjectHandler
    {
        if (!self::$instance) {
            self::$instance = new ActionGroupObjectHandler();
        }

        return self::$instance;
    }

    /**
     * ActionGroupObjectHandler constructor.
     * @throws XmlException
     */
    private function __construct()
    {
        $this->extendUtil = new ObjectExtensionUtil();
        $this->initActionGroups();
    }

    /**
     * Function to return a single object by name
     *
     * @param string $actionGroupName
     * @return ActionGroupObject
     * @throws TestFrameworkException
     * @throws XmlException
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
     * @throws TestFrameworkException
     * @throws XmlException
     */
    public function getAllObjects(): array
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
     * @throws XmlException
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     */
    private function initActionGroups()
    {
        $actionGroupParser = ObjectManagerFactory::getObjectManager()->create(ActionGroupDataParser::class);
        $parsedActionGroups = $actionGroupParser->readActionGroupData();

        $actionGroupObjectExtractor = new ActionGroupObjectExtractor();
        $neededActionGroup = $parsedActionGroups[ActionGroupObjectHandler::ACTION_GROUP_ROOT];

        $actionGroupNameValidator = new NameValidationUtil();
        foreach ($neededActionGroup as $actionGroupName => $actionGroupData) {
            if (!in_array($actionGroupName, ["nodeName", "xsi:noNamespaceSchemaLocation"])) {
                $filename = $actionGroupData[ActionGroupObjectHandler::ACTION_GROUP_FILENAME_ATTRIBUTE];
                $actionGroupNameValidator->validatePascalCase(
                    $actionGroupName,
                    NameValidationUtil::ACTION_GROUP_NAME,
                    $filename
                );
            }

            if (!is_array($actionGroupData)) {
                continue;
            }

            $this->actionGroups[$actionGroupName] =
                $actionGroupObjectExtractor->extractActionGroup($actionGroupData);
        }
        $actionGroupNameValidator->summarize(NameValidationUtil::ACTION_GROUP_NAME);
    }

    /**
     * This method checks if the action group is extended and creates a new action group object accordingly
     *
     * @param ActionGroupObject $actionGroupObject
     * @return ActionGroupObject
     * @throws XmlException
     * @throws TestFrameworkException
     */
    private function extendActionGroup($actionGroupObject): ActionGroupObject
    {
        if ($actionGroupObject->getParentName() !== null) {
            if ($actionGroupObject->getParentName() === $actionGroupObject->getName()) {
                throw new TestFrameworkException(
                    'Mftf Action Group can not extend from itself: ' . $actionGroupObject->getName()
                );
            }
            return $this->extendUtil->extendActionGroup($actionGroupObject);
        }
        return $actionGroupObject;
    }
}
