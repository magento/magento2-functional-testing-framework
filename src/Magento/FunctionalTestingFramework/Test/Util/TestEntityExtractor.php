<?php
/**
 * Created by PhpStorm.
 * User: imeron
 * Date: 8/25/17
 * Time: 11:11 AM
 */

namespace Magento\FunctionalTestingFramework\Test\Util;

use Robo\Exception\TaskExitException;

/**
 * Class TestEntityExtractor
 */
class TestEntityExtractor extends BaseCestObjectExtractor
{
    const TEST_STEP_ENTITY_CREATION = 'entity';
    const TEST_ENTITY_CREATION_KEY = 'key';
    const TEST_ENTITY_CREATION_VALUE = 'value';
    const TEST_STEP_DATA_CREATION = 'createData';

    /**
     * TestEntityExtractor constructor.
     */
    public function __construct()
    {
        // empty constructor
    }

    /**
     * Extracts custom entity or data definitions from test actions.
     * Returns array of entity data objects indexed by mergeKey, and an array of key-value pairs.
     * @param array $testActions
     * @return array $entityData
     */
    public function extractTestEntities($testActions)
    {
        $testEntities = [];

        foreach ($testActions as $actionName => $actionData) {
            $entityData = [];
            if ($actionData[TestEntityExtractor::NODE_NAME] === TestEntityExtractor::TEST_STEP_ENTITY_CREATION) {
                foreach ($actionData as $key => $attribute) {
                    if (is_array($attribute)) {
                        $entityData[$attribute[TestEntityExtractor::TEST_ENTITY_CREATION_KEY]]
                            = $attribute[TestEntityExtractor::TEST_ENTITY_CREATION_VALUE];
                        unset($actionData[$key]);
                    }
                }
                $testEntities[$actionData[TestEntityExtractor::NAME]] = $entityData;
            }
        }

        return $testEntities;
    }
}
