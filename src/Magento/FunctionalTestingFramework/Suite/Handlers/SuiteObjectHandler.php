<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Suite\Handlers;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Suite\Objects\SuiteObject;
use Magento\FunctionalTestingFramework\Suite\Parsers\SuiteDataParser;
use Magento\FunctionalTestingFramework\Suite\Util\SuiteObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\ObjectExtractor;
use Magento\FunctionalTestingFramework\Util\GenerationErrorHandler;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

/**
 * Class SuiteObjectHandler
 */
class SuiteObjectHandler implements ObjectHandlerInterface
{
    /**
     * Singleton instance of suite object handler.
     *
     * @var SuiteObjectHandler
     */
    private static $instance;

    /**
     * Array of suite objects keyed by suite name.
     *
     * @var SuiteObject[]
     */
    private $suiteObjects;

    /**
     * Avoids instantiation of SuiteObjectHandler by new.
     * @return void
     */
    private function __construct()
    {
    }

    /**
     * Avoids instantiation of SuiteObjectHandler by clone.
     * @return void
     */
    private function __clone()
    {
    }

    /**
     * Function to enforce singleton design pattern
     *
     * @return ObjectHandlerInterface
     * @throws FastFailException
     */
    public static function getInstance(): ObjectHandlerInterface
    {
        if (self::$instance === null) {
            self::$instance = new SuiteObjectHandler();
            self::$instance->initSuiteData();
        }

        return self::$instance;
    }

    /**
     * Function to return a single suite object by name
     *
     * @param string $objectName
     * @return SuiteObject
     */
    public function getObject($objectName): SuiteObject
    {
        if (!array_key_exists($objectName, $this->suiteObjects)) {
            throw new TestReferenceException(
                "Suite {$objectName} is not defined in xml or is invalid."
            );
        }
        return $this->suiteObjects[$objectName];
    }

    /**
     * Function to return all objects the handler is responsible for
     *
     * @return array
     */
    public function getAllObjects(): array
    {
        return $this->suiteObjects;
    }

    /**
     * Function which return all tests referenced by suites.
     *
     * @return array
     * @throws TestFrameworkException
     */
    public function getAllTestReferences(): array
    {
        $testsReferencedInSuites = [];
        $suites = $this->getAllObjects();

        foreach ($suites as $suite) {
            /** @var SuiteObject $suite */
            $test_keys = array_keys($suite->getTests());
            $testToSuiteName = array_fill_keys($test_keys, [$suite->getName()]);
            $testsReferencedInSuites = array_merge_recursive($testsReferencedInSuites, $testToSuiteName);
        }

        return $testsReferencedInSuites;
    }

    /**
     * Method to parse all suite data xml into objects.
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedPrivateMethod)
     * @throws FastFailException
     */
    private function initSuiteData()
    {
        try {
            $suiteDataParser = ObjectManagerFactory::getObjectManager()->create(SuiteDataParser::class);
        } catch (\Exception $e) {
            throw new FastFailException("Suite Data Parser Error: " . $e->getMessage());
        }

        $suiteObjectExtractor = new SuiteObjectExtractor();
        $this->suiteObjects = $suiteObjectExtractor->parseSuiteDataIntoObjects($suiteDataParser->readSuiteData());
    }
}
