<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Test\Handlers;

use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Parsers\TestDataParser;
use Magento\FunctionalTestingFramework\Test\Util\ObjectExtension;
use Magento\FunctionalTestingFramework\Test\Util\TestObjectExtractor;

/**
 * Class TestObjectHandler
 */
class TestObjectHandler implements ObjectHandlerInterface
{
    const XML_ROOT = 'tests';

    /**
     * Test Object Handler
     *
     * @var TestObjectHandler
     */
    private static $testObjectHandler;

    /**
     * Array contains all test objects indexed by name
     *
     * @var TestObject[] $tests
     */
    private $tests = [];

    /**
     * Singleton method to return TestObjectHandler.
     *
     * @return TestObjectHandler
     * @throws XmlException
     */
    public static function getInstance()
    {
        if (!self::$testObjectHandler) {
            self::$testObjectHandler = new TestObjectHandler();
            self::$testObjectHandler->initTestData();
        }

        return self::$testObjectHandler;
    }

    /**
     * TestObjectHandler constructor.
     */
    private function __construct()
    {
        // private constructor
    }

    /**
     * Takes a test name and returns the corresponding test.
     *
     * @param string $testName
     * @return TestObject
     * @throws TestReferenceException
     */
    public function getObject($testName)
    {
        if (!array_key_exists($testName, $this->tests)) {
            throw new TestReferenceException("Test ${testName} not defined in xml.");
        }
        $testObject = $this->tests[$testName];

        return $this->extendTest($testObject);
    }

    /**
     * Returns all tests parsed from xml indexed by testName.
     *
     * @return array
     */
    public function getAllObjects()
    {
        foreach ($this->tests as $index => $test) {
            $this->tests[$index] = $this->extendTest($test);
        }
        return $this->tests;
    }

    /**
     * Returns tests tagged with the group name passed to the method.
     *
     * @param string $groupName
     * @return TestObject[]
     */
    public function getTestsByGroup($groupName)
    {
        $relevantTests = [];
        foreach ($this->tests as $test) {
            /** @var TestObject $test */
            if (in_array($groupName, $test->getAnnotationByName('group'))) {
                $relevantTests[$test->getName()] = $test;
                continue;
            }
        }

        return $relevantTests;
    }

    /**
     * This method reads all Test.xml files into objects and stores them in an array for future access.
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @throws XmlException
     */
    private function initTestData()
    {
        $testDataParser = ObjectManagerFactory::getObjectManager()->create(TestDataParser::class);
        $parsedTestArray = $testDataParser->readTestData();

        $testObjectExtractor = new TestObjectExtractor();

        if (!$parsedTestArray) {
            trigger_error("Could not parse any test in xml.", E_USER_NOTICE);
            return;
        }

        foreach ($parsedTestArray[TestObjectHandler::XML_ROOT] as $testName => $testData) {
            if (!is_array($testData)) {
                continue;
            }

            $this->tests[$testName] = $testObjectExtractor->extractTestData($testData);
        }
    }

    /**
     * This method checks if the test is extended and creates a new test object accordingly
     *
     * @param TestObject $testObject
     * @return TestObject
     */
    private function extendTest($testObject)
    {
        if ($testObject->getParentName() !== null) {
            return ObjectExtension::extendTest($testObject);
        }
        return $testObject;
    }
}
