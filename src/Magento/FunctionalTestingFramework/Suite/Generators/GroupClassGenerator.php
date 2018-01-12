<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Suite\Generators;

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

    const GROUP_DIR_NAME = 'Group';

    /**
     * Mustache_Engine instance for template loading
     *
     * @var Mustache_Engine
     */
    private $mustacheEngine;

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
     */
    public function generateGroupClass($suiteObject)
    {
        $classContent = $this->createClassContent($suiteObject);
        $configEntry = self::GROUP_DIR_NAME . DIRECTORY_SEPARATOR . $suiteObject->getName();
        $filePath = dirname(dirname(__DIR__)) .
            DIRECTORY_SEPARATOR .
            $configEntry .
            '.php';
        file_put_contents($filePath, $classContent);

        return  str_replace(DIRECTORY_SEPARATOR, "\\", $configEntry);
    }

    /**
     * Function to create group class content based on suite object definition.
     *
     * @param SuiteObject $suiteObject
     * @return string;
     */
    private function createClassContent($suiteObject)
    {
        $mustacheData = [];
        $mustacheData[self::SUITE_NAME_TAG] = $suiteObject->getName();
        $mustacheData[self::TEST_COUNT_TAG] = count($suiteObject->getTests());

        $mustacheData[self::BEFORE_MUSTACHE_KEY] = $this->buildHookMustacheArray($suiteObject->getBeforeHook());
        $mustacheData[self::AFTER_MUSTACHE_KEY] = $this->buildHookMustacheArray($suiteObject->getAfterHook());
        $mustacheData[self::MUSTACHE_VAR_TAG] = array_merge(
            $mustacheData[self::BEFORE_MUSTACHE_KEY]['createData'] ?? [],
            $mustacheData[self::AFTER_MUSTACHE_KEY]['createData'] ?? []
        );

        return $this->mustacheEngine->render(self::MUSTACHE_TEMPLATE_NAME, $mustacheData);
    }

    /**
     * Function which takes hook objects and transforms data into array for mustache template engine.
     *
     * @param TestHookObject $hookObj
     * @return array
     */
    private function buildHookMustacheArray($hookObj)
    {
        $mustacheHookArray = [];
        foreach ($hookObj->getActions() as $action) {
            /** @var ActionObject $action */
            $entityArray = [];
            $entityArray[self::ENTITY_MERGE_KEY] = $action->getStepKey();
            $entityArray[self::ENTITY_NAME_TAG] =
                $action->getCustomActionAttributes()['entity'] ??
                $action->getCustomActionAttributes()[TestGenerator::REQUIRED_ENTITY_REFERENCE];

            // if there is more than 1 custom attribute, we can assume there are required entities
            if (count($action->getCustomActionAttributes()) > 1) {
                $entityArray[self::REQUIRED_ENTITY_KEY] =
                    $this->buildReqEntitiesMustacheArray($action->getCustomActionAttributes());
            }

            $mustacheHookArray[$action->getType()][] = $entityArray;
        }

        return $mustacheHookArray;
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
