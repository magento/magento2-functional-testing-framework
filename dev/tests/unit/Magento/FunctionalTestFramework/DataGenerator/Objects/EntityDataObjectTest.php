<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Objects;

use Magento\FunctionalTestingFramework\Util\MagentoTestCase;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use tests\unit\Util\TestLoggingUtil;

/**
 * The following function declarations override the global function_exists and declare msq/msqs for use
 * in the Magento\FunctionalTestingFramework\DataGenerator\Objects, which EntityDataObject needs.
 */
// @codingStandardsIgnoreStart
function function_exists($val)
{
    return true;
}

function msq($id = null)
{
    return "msqUnique";
}

function msqs($id = null)
{
    return "msqsUnique";
}
// @codingStandardsIgnoreEnd

/**
 * Class EntityDataObjectTest
 */
class EntityDataObjectTest extends MagentoTestCase
{
    /**
     * Before test functionality
     * @return void
     */
    public function setUp()
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    public function testBasicGetters()
    {
        $data = ["datakey1" => "value1"];
        $dataObject = new EntityDataObject("name", "type", $data, null, null, null);
        // Perform Asserts
        $this->assertEquals("name", $dataObject->getName());
        $this->assertEquals("type", $dataObject->getType());
    }

    public function testGetDataByName()
    {
        $data = ["datakey1" => "value1", "datakey2" => "value2", "datakey3" => "value3"];
        $dataObject = new EntityDataObject("name", "type", $data, null, null, null);
        // Perform Asserts
        $this->assertNull($dataObject->getDataByName("someInvalidName", 0));
        $this->assertEquals("value1", $dataObject->getDataByName("dataKey1", 0));
        $this->assertEquals("value2", $dataObject->getDataByName("dataKey2", 0));
        $this->assertEquals("value3", $dataObject->getDataByName("dataKey3", 0));
    }

    public function testGetUniqueDataByName()
    {
        $data = ["datakey1" => "value1", "datakey2" => "value2"];
        $uniquenessKeys = ["datakey1" => "suffix", "datakey2" => "prefix"];
        $dataObject = new EntityDataObject("name", "type", $data, null, $uniquenessKeys, null);
        // Perform Asserts
        $this->assertEquals("value1msqsUnique", $dataObject->getDataByName("datakey1", 1));
        $this->assertEquals("msqsUniquevalue2", $dataObject->getDataByName("datakey2", 1));
        $this->assertEquals("value1msqUnique", $dataObject->getDataByName("datakey1", 2));
        $this->assertEquals("msqUniquevalue2", $dataObject->getDataByName("datakey2", 2));
        $this->assertEquals('value1msqs("name")', $dataObject->getDataByName("datakey1", 3));
        $this->assertEquals('msqs("name")value2', $dataObject->getDataByName("datakey2", 3));
        $this->assertEquals('value1msq("name")', $dataObject->getDataByName("datakey1", 4));
        $this->assertEquals('msq("name")value2', $dataObject->getDataByName("datakey2", 4));
    }

    public function testVarGetter()
    {
        $data = ["datakey1" => "value1", "datakey2" => "value2", "datakey3" => "value3"];
        $vars = ["someOtherEntity" => "id"];
        $dataObject = new EntityDataObject("name", "type", $data, null, null, $vars);
        // Perform Asserts
        $this->assertEquals("id", $dataObject->getVarReference("someOtherEntity"));
    }

    public function testGetDataByNameInvalidUniquenessFormatValue()
    {
        $this->expectException(TestFrameworkException::class);
        $data = ["datakey1" => "value1", "datakey2" => "value2", "datakey3" => "value3"];
        $dataObject = new EntityDataObject("name", "type", $data, null, null, null);
        // Trigger Exception
        $dataObject->getDataByName("dataKey1", 9999);
    }

    public function testUniquenessFunctionsDontExist()
    {
        $this->markTestIncomplete('Test fails, as msqMock is always declared in test runs.');
        $this->expectException(TestFrameworkException::class);
        $data = ["datakey1" => "value1", "datakey2" => "value2", "datakey3" => "value3"];
        $uniquenessKeys = ["datakey1" => "suffix"];
        $dataObject = new EntityDataObject("name", "type", $data, null, $uniquenessKeys, null);
        // Trigger Exception
        $dataObject->getDataByName("datakey1", 1);
    }

    public function testGetLinkedEntities()
    {
        $data = ["datakey1" => "value1", "datakey2" => "value2", "datakey3" => "value3"];
        $entities = ["linkedEntity1" => "linkedEntityType", "linkedEntity2" => "otherEntityType"];
        $dataObject = new EntityDataObject("name", "type", $data, $entities, null, null);
        // Perform Asserts
        $this->assertEquals("linkedEntity1", $dataObject->getLinkedEntitiesOfType("linkedEntityType")[0]);
        $this->assertEquals("linkedEntity2", $dataObject->getLinkedEntitiesOfType("otherEntityType")[0]);
    }

    public function testGetCamelCaseKeys()
    {
        $data = [
            "lowercasekey1" => "value1",
            "camelCaseKey2" => "value2",
            "lowercasekey3" => "value3",
            "camelCaseKey4" => "value4"
        ];

        $dataObject = new EntityDataObject("name", "type", $data, null, null, null);

        $this->assertEquals("value1", $dataObject->getDataByName("lowercasekey1", 0));
        $this->assertEquals("value2", $dataObject->getDataByName("camelCaseKey2", 0));
        $this->assertEquals("value3", $dataObject->getDataByName("lowercasekey3", 0));
        $this->assertEquals("value4", $dataObject->getDataByName("camelCaseKey4", 0));
    }

    /**
     * After class functionality
     * @return void
     */
    public static function tearDownAfterClass()
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }
}
