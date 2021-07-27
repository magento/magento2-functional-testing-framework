<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\StaticCheck;

use InvalidArgumentException;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\OperationDefinitionObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\DataGenerator\Parsers\OperationDefinitionParser;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Page\Objects\ElementObject;
use Magento\FunctionalTestingFramework\Page\Objects\PageObject;
use Magento\FunctionalTestingFramework\Page\Objects\SectionObject;
use Magento\FunctionalTestingFramework\StaticCheck\DeprecatedEntityUsageCheck;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use Symfony\Component\Console\Input\InputInterface;
use tests\unit\Util\MagentoTestCase;

/**
 * Class DeprecatedEntityUsageCheckTest
 */
class DeprecatedEntityUsageCheckTest extends MagentoTestCase
{
    /** @var DeprecatedEntityUsageCheck */
    private $staticCheck;

    /** @var ReflectionClass*/
    private $staticCheckClass;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        $this->staticCheck = new DeprecatedEntityUsageCheck();
        $this->staticCheckClass = new ReflectionClass($this->staticCheck);
    }

    /**
     * Validate testInvalidPathOption.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testInvalidPathOption(): void
    {
        $input = $this->getMockBuilder(InputInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $input->method('getOption')
            ->with('path')
            ->willReturn('/invalidPath');

        $loadAllXmlFiles = $this->staticCheckClass->getMethod('loadAllXMLFiles');
        $loadAllXmlFiles->setAccessible(true);

        $this->expectException(InvalidArgumentException::class);
        $loadAllXmlFiles->invoke($this->staticCheck, $input);
    }

    /**
     * Validate testViolatingElementReferences.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testViolatingElementReferences(): void
    {
        //variables for assertions
        $elementName = 'elementOne';
        $sectionName = 'SectionOne';
        $fileName = 'section.xml';

        $element = new ElementObject($elementName, 'type', '#selector1', null, '41', false, 'deprecated');
        $section = new SectionObject($sectionName, [$element], $fileName);
        $elementRef = $sectionName . '.' . $elementName;
        $references = [$elementRef => $element, $sectionName => $section];
        $actual = $this->callViolatingReferences($references);
        $expected = [
            'Deprecated Element(s)' => [
                0 => [
                        'name' => $elementRef,
                        'file' => $fileName
                    ]
            ]
        ];
        $this->assertEquals($actual, $expected);
    }

    /**
     * Validate testViolatingPageReferences.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testViolatingPageReferences(): void
    {
        //Page variables for assertions
        $pageName = 'Page';
        $fileName = 'page.xml';

        $page = new PageObject($pageName, '/url.html', 'Test', [], false, 'test', $fileName, 'deprecated');
        $references = ['Page' => $page];
        $actual = $this->callViolatingReferences($references);
        $expected = [
            'Deprecated Page(s)' => [
                0 => [
                    'name' => $pageName,
                    'file' => $fileName
                ]
            ]
        ];
        $this->assertEquals($actual, $expected);
    }

    /**
     * Validate testViolatingDataReferences.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testViolatingDataReferences(): void
    {
        //Data entity variables for assertions
        $entityName = 'EntityOne';
        $fileName = 'entity.xml';

        $entity = new EntityDataObject(
            $entityName,
            'testType',
            ['testkey' => 'testValue'],
            [],
            null,
            [],
            null,
            $fileName,
            'deprecated'
        );
        $references = [$entityName => $entity];
        $actual = $this->callViolatingReferences($references);
        $expected = [
            'Deprecated Data(s)' => [
                0 => [
                    'name' => $entityName,
                    'file' => $fileName
                ]
            ]
        ];
        $this->assertEquals($actual, $expected);
    }

    /**
     * Validate testViolatingTestReferences.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testViolatingTestReferences(): void
    {
        // test variables for assertions
        $testName = 'Test1';
        $fileName = 'test.xml';

        $test = new TestObject($testName, [], [], [], $fileName, null, 'deprecated');
        $references = ['Test1' => $test];
        $actual = $this->callViolatingReferences($references);
        $expected = [
            'Deprecated Test(s)' => [
                0 => [
                    'name' => $testName,
                    'file' => $fileName
                ]
            ]
        ];
        $this->assertEquals($actual, $expected);
    }

    /**
     * Validate testViolatingMetaDataReferences.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testViolatingMetaDataReferences(): void
    {
        // Data Variables for Assertions
        $dataType1 = 'type1';
        $operationType1 = 'create';
        $operationType2 = 'update';

        /**
         * Parser Output.
         * operationName
         *      createType1
         *          has field
         *              key=id, value=integer
         *      updateType1
         *          has field
         *              key=id, value=integer
         */
        $mockData = [OperationDefinitionObjectHandler::ENTITY_OPERATION_ROOT_TAG => [
            'testOperationName' => [
                OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $dataType1,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_TYPE => $operationType1,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_AUTH => 'auth',
                OperationDefinitionObjectHandler::ENTITY_OPERATION_URL => 'V1/Type1',
                OperationDefinitionObjectHandler::ENTITY_OPERATION_METHOD => 'POST',
                OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY => [
                    0 => [
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => 'id',
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => 'integer'
                    ],
                ],
                OperationDefinitionObjectHandler::OBJ_DEPRECATED => 'deprecated'
            ],[
                OperationDefinitionObjectHandler::ENTITY_OPERATION_DATA_TYPE => $dataType1,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_TYPE => $operationType2,
                OperationDefinitionObjectHandler::ENTITY_OPERATION_AUTH => 'auth',
                OperationDefinitionObjectHandler::ENTITY_OPERATION_URL => 'V1/Type1/{id}',
                OperationDefinitionObjectHandler::ENTITY_OPERATION_METHOD => 'PUT',
                OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY => [
                    0 => [
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_KEY => 'id',
                        OperationDefinitionObjectHandler::ENTITY_OPERATION_ENTRY_VALUE => 'integer'
                    ],
                ]
            ]]];

        $this->mockOperationHandlerWithData($mockData);
        $dataName = 'dataName1';
        $references = [
            $dataName => [
                    $dataType1 => [
                            $operationType1,
                            $operationType2
                        ]
                ]
        ];

        $expected = [
            '"'.$dataName.'" references deprecated' => [
                0 => [
                    'name' => $dataType1,
                    'file' => 'metadata xml file'
                ]
            ]
        ];
        $property = $this->staticCheckClass->getMethod('findViolatingMetadataReferences');
        $property->setAccessible(true);
        $actual = $property->invoke($this->staticCheck, $references);
        $this->assertEquals($actual, $expected);
    }

    /**
     * Validate testIsDeprecated.
     *
     * @return void
     * @throws ReflectionException
     */
    public function testIsDeprecated(): void
    {
        // Test Data
        $contents = '<tests>
                    <test name="test" deprecated="true">
                        <comment userInput="input1" stepKey="key1"/>
                        <comment userInput="input2" stepKey="key1"/>
                    </test>
                </tests>
                ';

        $property = $this->staticCheckClass->getMethod('isDeprecated');
        $property->setAccessible(true);
        $output = $property->invoke($this->staticCheck, $contents);
        $this->assertTrue($output);
    }

    /**
     * Create mock operation handler with data.
     *
     * @param array $mockData
     *
     * @return void
     */
    private function mockOperationHandlerWithData(array $mockData): void
    {
        $operationDefinitionObjectHandlerProperty = new ReflectionProperty(
            OperationDefinitionObjectHandler::class,
            'INSTANCE'
        );
        $operationDefinitionObjectHandlerProperty->setAccessible(true);
        $operationDefinitionObjectHandlerProperty->setValue(null);

        $mockOperationParser = $this->createMock(OperationDefinitionParser::class);
        $mockOperationParser
            ->method('readOperationMetadata')
            ->willReturn($mockData);

        $objectManager = ObjectManagerFactory::getObjectManager();
        $mockObjectManagerInstance = $this->createMock(ObjectManager::class);
        $mockObjectManagerInstance
            ->method('create')
            ->will(
                $this->returnCallback(
                    function (
                        string $class,
                        array $arguments = []
                    ) use (
                        $objectManager,
                        $mockOperationParser
                    ) {
                        if ($class === OperationDefinitionParser::class) {
                            return $mockOperationParser;
                        }

                        return $objectManager->create($class, $arguments);
                    }
                )
            );

        $property = new ReflectionProperty(ObjectManager::class, 'instance');
        $property->setAccessible(true);
        $property->setValue($mockObjectManagerInstance);
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();

        $operationDefinitionObjectHandlerProperty = new ReflectionProperty(
            OperationDefinitionObjectHandler::class,
            'INSTANCE'
        );
        $operationDefinitionObjectHandlerProperty->setAccessible(true);
        $operationDefinitionObjectHandlerProperty->setValue(null);

        $objectManagerProperty = new ReflectionProperty(ObjectManager::class, 'instance');
        $objectManagerProperty->setAccessible(true);
        $objectManagerProperty->setValue(null);
    }

    /**
     * Invoke findViolatingReferences.
     *
     * @param array $references
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function callViolatingReferences(array $references)
    {
        $property = $this->staticCheckClass->getMethod('findViolatingReferences');
        $property->setAccessible(true);

        return $property->invoke($this->staticCheck, $references);
    }
}
