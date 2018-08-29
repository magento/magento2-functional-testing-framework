<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Suite\Generators;

use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Test\Objects\TestHookObject;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;

class GroupClassGenerator
{
    const MUSTACHE_TEMPLATE_NAME = 'SuiteClass';
    const SUITE_NAME_TAG = 'suiteName';
    const TEST_COUNT_TAG = 'testCount';
    const BEFORE_MUSTACHE_KEY = 'before';
    const AFTER_MUSTACHE_KEY = 'after';
    const ENTITY_NAME_TAG = 'entityName';
    const ENTITY_MERGE_KEY = 'stepKey';
    const REQUIRED_ENTITY_KEY = 'requiredEntities';
    const LAST_REQUIRED_ENTITY_TAG = 'last';
    const MUSTACHE_VAR_TAG = 'var';
    const MAGENTO_CLI_COMMAND_COMMAND = 'command';
    const REPLACEMENT_ACTIONS = [
        'comment' => 'print'
    ];
    const GROUP_DIR_NAME = 'Group';

    /**
     * Mustache_Engine instance for template loading
     *
     * @var Mustache_Engine
     */
    private $mustacheEngine;

    /**
     * Static function to return group directory path for precondition files.
     *
     * @return string
     */
    public static function getGroupDirPath()
    {
        return dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . self::GROUP_DIR_NAME . DIRECTORY_SEPARATOR;
    }

    /**
     * GroupClassGenerator constructor
     */
    public function __construct()
    {
        $this->mustacheEngine = new Mustache_Engine([
            'loader' => new Mustache_Loader_FilesystemLoader(dirname(__DIR__) . DIRECTORY_SEPARATOR . "views"),
            'partials_loader' => new Mustache_Loader_FilesystemLoader(
                dirname(__DIR__) . DIRECTORY_SEPARATOR . "views" . DIRECTORY_SEPARATOR . "partials"
            )
        ]);
    }

    /**
     * Method for adding preconditions and creating a corresponding group file for codeception. After generation,
     * the method returns the config path for the group file.
     *
     * @param SuiteObject $suiteObject
     * @return string
     * @throws TestReferenceException
     */
    public function generateGroupClass($suiteObject)
    {
        $classContent = $this->createClassContent($suiteObject);
        $configEntry = self::GROUP_DIR_NAME . DIRECTORY_SEPARATOR . $suiteObject->getName();
        $filePath = self::getGroupDirPath() . $suiteObject->getName() . '.php';
        file_put_contents($filePath, $classContent);

        return  str_replace(DIRECTORY_SEPARATOR, "\\", $configEntry);
    }

    /**
     * Function to create group class content based on suite object definition.
     *
     * @param SuiteObject $suiteObject
     * @return string;
     * @throws TestReferenceException
     */
    private function createClassContent($suiteObject)
    {
        $mustacheData = [];
        $mustacheData[self::SUITE_NAME_TAG] = $suiteObject->getName();
        $mustacheData[self::TEST_COUNT_TAG] = count($suiteObject->getTests());

        $mustacheData[self::BEFORE_MUSTACHE_KEY] = $this->buildHookMustacheArray($suiteObject->getBeforeHook());
        $mustacheData[self::AFTER_MUSTACHE_KEY] = $this->buildHookMustacheArray($suiteObject->getAfterHook());
        $mustacheData[self::MUSTACHE_VAR_TAG] = $this->extractClassVar(
            $mustacheData[self::BEFORE_MUSTACHE_KEY],
            $mustacheData[self::AFTER_MUSTACHE_KEY]
        );

        return $this->mustacheEngine->render(self::MUSTACHE_TEMPLATE_NAME, $mustacheData);
    }

    /**
     * Function which takes the before and after arrays containing the steps for the hook objects and extracts
     * any variables names needed by the class template.
     *
     * @param array $beforeArray
     * @param array $afterArray
     * @return array
     */
    private function extractClassVar($beforeArray, $afterArray)
    {
        $beforeVar = $beforeArray[self::MUSTACHE_VAR_TAG] ?? [];
        $afterVar = $afterArray[self::MUSTACHE_VAR_TAG] ?? [];

        return array_merge($beforeVar, $afterVar);
    }

    /**
     * Function which takes hook objects and transforms data into array for mustache template engine.
     *
     * @param TestHookObject $hookObj
     * @return array
     * @throws TestReferenceException
     */
    private function buildHookMustacheArray($hookObj)
    {
        $actions = [];
        $mustacheHookArray['actions'][] = ['webDriverInit' => true];

        foreach ($hookObj->getActions() as $action) {
            /** @var ActionObject $action */
            $index = count($actions);
            //deleteData contains either url or createDataKey, if it contains the former it needs special formatting
            if ($action->getType() !== "createData"
                && !array_key_exists(TestGenerator::REQUIRED_ENTITY_REFERENCE, $action->getCustomActionAttributes())) {
                $actions = $this->buildWebDriverActionsMustacheArray($action, $actions, $index);
                continue;
            }

            // add these as vars to be created a class level in the template
            if ($action->getType() == 'createData') {
                $mustacheHookArray[self::MUSTACHE_VAR_TAG][] = [self::ENTITY_MERGE_KEY => $action->getStepKey()];
            }

            $entityArray = [];
            $entityArray[self::ENTITY_MERGE_KEY] = $action->getStepKey();
            $entityArray[$action->getType()] = $action->getStepKey();

            $entityArray = $this->buildPersistenceMustacheArray($action, $entityArray);
            $actions[$index] = $entityArray;
        }
        $mustacheHookArray['actions'] = array_merge($mustacheHookArray['actions'], $actions);
        $mustacheHookArray['actions'][] = ['webDriverReset' => true];

        return $mustacheHookArray;
    }

    /**
     * Takes an action object and array of generated action steps. Converst the action object into generated php and
     * appends the entry to the given array. The result is returned by the function.
     *
     * @param ActionObject $action
     * @param array        $actionEntries
     * @return array
     * @throws TestReferenceException
     */
    private function buildWebDriverActionsMustacheArray($action, $actionEntries)
    {
        $step = TestGenerator::getInstance()->generateStepsPhp([$action], TestGenerator::SUITE_SCOPE, 'webDriver');
        $rawPhp = str_replace(["\t", "\n"], "", $step);
        $multipleCommands = explode(";", $rawPhp, -1);
        foreach ($multipleCommands as $command) {
            $actionEntries = $this->replaceReservedTesterFunctions($command . ";", $actionEntries, 'webDriver');
        }

        return $actionEntries;
    }

    /**
     * Takes a generated php step, an array containing generated php entries for the template, and the actor name
     * for the generated step.
     *
     * @param string $formattedStep
     * @param array  $actionEntries
     * @param string $actor
     * @return array
     */
    private function replaceReservedTesterFunctions($formattedStep, $actionEntries, $actor)
    {
        foreach (self::REPLACEMENT_ACTIONS as $testAction => $replacement) {
            $testActionCall = "\${$actor}->{$testAction}";
            if (substr($formattedStep, 0, strlen($testActionCall)) == $testActionCall) {
                $resultingStep = str_replace($testActionCall, $replacement, $formattedStep);
                $actionEntries[] = ['action' => $resultingStep];
            } else {
                $actionEntries[] = ['action' => $formattedStep];
            }
        }

        return $actionEntries;
    }

    /**
     * Takes an action object of persistence type and formats an array entiry for mustache template interpretation.
     *
     * @param ActionObject $action
     * @param array        $entityArray
     * @return array
     */
    private function buildPersistenceMustacheArray($action, $entityArray)
    {
        $entityArray[self::ENTITY_NAME_TAG] =
            $action->getCustomActionAttributes()['entity'] ??
            $action->getCustomActionAttributes()[TestGenerator::REQUIRED_ENTITY_REFERENCE];

        // append entries for any required entities to this entry
        if (array_key_exists('requiredEntities', $action->getCustomActionAttributes())) {
            $entityArray[self::REQUIRED_ENTITY_KEY] =
                $this->buildReqEntitiesMustacheArray($action->getCustomActionAttributes());
        }

        // append entries for customFields if specified by the user.
        if (array_key_exists('customFields', $action->getCustomActionAttributes())) {
            $entityArray['customFields'] = $action->getStepKey() . 'Fields';
        }
        
        return $entityArray;
    }

    /**
     * Function which takes any required entities under a 'createData' tag and transforms data into array to be consumed
     * by mustache template.
     * (<createData entity="" stepKey="">
     *      <requiredEntity'...)
     *
     * @param array $customAttributes
     * @return array
     */
    private function buildReqEntitiesMustacheArray($customAttributes)
    {
        $requiredEntities = [];
        foreach ($customAttributes as $attribute) {
            if (!is_array($attribute)) {
                continue;
            }

            if ($attribute[ActionObjectExtractor::NODE_NAME] == 'requiredEntity') {
                $requiredEntities[] = [self::ENTITY_NAME_TAG => $attribute[TestGenerator::REQUIRED_ENTITY_REFERENCE]];
            }
        }

        //append "last" attribute to final entry for mustache template (omit trailing comma)
        $requiredEntities[count($requiredEntities)-1][self::LAST_REQUIRED_ENTITY_TAG] = true;

        return $requiredEntities;
    }
}
