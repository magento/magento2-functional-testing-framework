<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use AspectMock\Test as AspectMock;

use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use PHPUnit\Framework\TestCase;

class ModuleResolverTest extends TestCase
{
    /**
     * Validate that Paths that are already set are returned
     * @throws \Exception
     */
    public function testGetModulePathsAlreadySet()
    {
        $this->setMockResolverClass();
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, ["example/paths"]);
        $this->assertEquals(["example/paths"], $resolver->getModulesPath());
    }

    /**
     * Validate paths are aggregated correctly
     * @throws \Exception
     */
    public function testGetModulePathsAggregate()
    {
        $this->setMockResolverClass(false, ["Magento_TestModule"]);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, null);
        $this->assertEquals(
            ['/git/magento2-functional-testing-framework/dev/tests/verification/TestModule'],
            $resolver->getModulesPath()
        );
    }

    /**
     * Validate blacklisted modules are removed
     * @throws \Exception
     */
    public function testGetModulePathsBlacklist()
    {
        $this->setMockResolverClass(false, ["Magento_TestModule"]);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, null, ["TestModule"]);
        $this->assertEquals([], $resolver->getModulesPath());
    }

    /**
     * Validate that getEnabledModules returns correctly with no admin token
     * @throws \Exception
     */
    public function testGetModulePathsNoAdminToken()
    {
        $this->setMockResolverClass(false, null, ["example/paths"]);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, null);
        $this->assertEquals(["example/paths"], $resolver->getModulesPath());
    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param string $mockToken
     * @param array $mockModules
     * @param string[] $mockCustomMethods
     * @throws \Exception
     */
    private function setMockResolverClass($mockToken = null, $mockModules = null, $mockCustomMethods = null)
    {
        $property = new \ReflectionProperty(ModuleResolver::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockMethods = [];
        if (isset($mockToken)) {
            $mockMethods['getAdminToken'] = $mockToken;
        }
        if (isset($mockModules)) {
            $mockMethods['getEnabledModules'] = $mockModules;
        }
        if (isset($mockCustomMethods)) {
            $mockMethods['applyCustomModuleMethods'] = $mockCustomMethods;
        }
        $mockMethods['printMagentoVersionInfo'] = null;

        $mockResolver = AspectMock::double(
            ModuleResolver::class,
            $mockMethods
        )->make();
        $instance = AspectMock::double(ObjectManager::class, ['create' => $mockResolver, 'get' => null])->make();
        // bypass the private constructor
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }

    /**
     * Function used to set mock for Resolver properties
     *
     * @param ModuleResolver $instance
     * @param array $mockPaths
     * @param array $mockModules
     * @throws \Exception
     */
    private function setMockResolverProperties($instance, $mockPaths = null, $mockModules = null, $mockBlacklist = [])
    {
        $property = new \ReflectionProperty(ModuleResolver::class, 'enabledModulePaths');
        $property->setAccessible(true);
        $property->setValue($instance, $mockPaths);

        $property = new \ReflectionProperty(ModuleResolver::class, 'enabledModules');
        $property->setAccessible(true);
        $property->setValue($instance, $mockModules);

        $property = new \ReflectionProperty(ModuleResolver::class, 'moduleBlacklist');
        $property->setAccessible(true);
        $property->setValue($instance, $mockBlacklist);
    }
}
