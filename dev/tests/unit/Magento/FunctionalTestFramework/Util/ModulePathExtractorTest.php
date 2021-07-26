<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Util;

use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\Util\ModulePathExtractor;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;

class ModulePathExtractorTest extends MagentoTestCase
{
    /**
     * Mock test module paths
     *
     * @var array
     */
    private $mockTestModulePaths = [
        'Magento_ModuleA' => '/base/path/app/code/Magento/ModuleA/Test/Mftf',
        'VendorB_ModuleB' => '/base/path/app/code/VendorB/ModuleB/Test/Mftf',
        'Magento_ModuleC' => '/base/path/dev/tests/acceptance/tests/functional/Magento/ModuleCTest',
        'VendorD_ModuleD' => '/base/path/dev/tests/acceptance/tests/functional/VendorD/ModuleDTest',
        'SomeModuleE' => '/base/path/dev/tests/acceptance/tests/functional/FunctionalTest/SomeModuleE',
        'Magento_ModuleF' => '/base/path/vendor/magento/module-modulef/Test/Mftf',
        'VendorG_ModuleG' => '/base/path/vendor/vendorg/module-moduleg-test',
    ];

    /**
     * Validate module for app/code path
     *
     * @throws \Exception
     */
    public function testGetModuleAppCode()
    {
        $mockPath = '/base/path/app/code/Magento/ModuleA/Test/Mftf/Test/SomeTest.xml';

        $this->mockModuleResolver($this->mockTestModulePaths);
        $extractor = new ModulePathExtractor();
        $this->assertEquals('ModuleA', $extractor->extractModuleName($mockPath));
    }

    /**
     * Validate vendor for app/code path
     *
     * @throws \Exception
     */
    public function testGetVendorAppCode()
    {
        $mockPath = '/base/path/app/code/VendorB/ModuleB/Test/Mftf/Test/SomeTest.xml';

        $this->mockModuleResolver($this->mockTestModulePaths);
        $extractor = new ModulePathExtractor();
        $this->assertEquals('VendorB', $extractor->getExtensionPath($mockPath));
    }

    /**
     * Validate module for dev/tests path
     *
     * @throws \Exception
     */
    public function testGetModuleDevTests()
    {
        $mockPath = '/base/path/dev/tests/acceptance/tests/functional/Magento/ModuleCTest/Test/SomeTest.xml';

        $this->mockModuleResolver($this->mockTestModulePaths);
        $extractor = new ModulePathExtractor();
        $this->assertEquals('ModuleC', $extractor->extractModuleName($mockPath));
    }

    /**
     * Validate vendor for dev/tests path
     *
     * @throws \Exception
     */
    public function testGetVendorDevTests()
    {
        $mockPath = '/base/path/dev/tests/acceptance/tests/functional/VendorD/ModuleDTest/Test/SomeTest.xml';

        $this->mockModuleResolver($this->mockTestModulePaths);
        $extractor = new ModulePathExtractor();
        $this->assertEquals('VendorD', $extractor->getExtensionPath($mockPath));
    }

    /**
     * Validate module with no _
     *
     * @throws \Exception
     */
    public function testGetModule()
    {
        $mockPath = '/base/path/dev/tests/acceptance/tests/functional/FunctionalTest/SomeModuleE/Test/SomeTest.xml';

        $this->mockModuleResolver($this->mockTestModulePaths);
        $extractor = new ModulePathExtractor();
        $this->assertEquals('NO MODULE DETECTED', $extractor->extractModuleName($mockPath));
    }

    /**
     * Validate module for vendor/tests path
     *
     * @throws \Exception
     */
    public function testGetModuleVendorDir()
    {
        $mockPath = '/base/path/vendor/magento/module-modulef/Test/Mftf/Test/SomeTest.xml';

        $this->mockModuleResolver($this->mockTestModulePaths);
        $extractor = new ModulePathExtractor();
        $this->assertEquals('ModuleF', $extractor->extractModuleName($mockPath));
    }

    /**
     * Validate vendor for vendor path
     *
     * @throws \Exception
     */
    public function testGetVendorVendorDir()
    {
        $mockPath = '/base/path/vendor/vendorg/module-moduleg-test/Test/SomeTest.xml';

        $this->mockModuleResolver($this->mockTestModulePaths);
        $extractor = new ModulePathExtractor();
        $this->assertEquals('VendorG', $extractor->getExtensionPath($mockPath));
    }

    /**
     * Mock module resolver.
     *
     * @param array $paths
     *
     * @return void
     */
    private function mockModuleResolver(array $paths): void
    {
        $mockResolver = $this->createMock(ModuleResolver::class);
        $mockResolver
            ->method('getEnabledModules')
            ->willReturn([]);

        $objectManagerMockInstance = $this->createMock(ObjectManager::class);
        $objectManagerMockInstance
            ->method('create')
            ->will(
                $this->returnCallback(
                    function ($class) use ($mockResolver) {
                        if ($class === ModuleResolver::class) {
                            return $mockResolver;
                        }

                        return null;
                    }
                )
            );

        $objectManagerProperty = new ReflectionProperty(ObjectManager::class, 'instance');
        $objectManagerProperty->setAccessible(true);
        $objectManagerProperty->setValue($objectManagerMockInstance);

        $resolver = ModuleResolver::getInstance();
        $property = new ReflectionProperty(ModuleResolver::class, 'enabledModuleNameAndPaths');
        $property->setAccessible(true);
        $property->setValue($resolver, $paths);
    }
}
