<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use AspectMock\Proxy\Verifier;
use AspectMock\Test as AspectMock;

use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;

class ModuleResolverTest extends MagentoTestCase
{
    /**
     * Before test functionality
     * @return void
     */
    public function setUp()
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * After class functionality
     * @return void
     */
    public static function tearDownAfterClass()
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }

    /**
     * Validate that Paths that are already set are returned
     * @throws \Exception
     */
    public function testGetModulePathsAlreadySet()
    {
        $this->setMockResolverClass();
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, ["example" . DIRECTORY_SEPARATOR . "paths"]);
        $this->assertEquals(["example" . DIRECTORY_SEPARATOR . "paths"], $resolver->getModulesPath());
    }

    /**
     * Validate paths are aggregated correctly
     * @throws \Exception
     */
    public function testGetModulePathsAggregate()
    {
        $this->setMockResolverClass(false, null, null, null, ["example" . DIRECTORY_SEPARATOR . "paths"]);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, null);
        $this->assertEquals(
            [
                "example" . DIRECTORY_SEPARATOR . "paths",
                "example" . DIRECTORY_SEPARATOR . "paths",
                "example" . DIRECTORY_SEPARATOR . "paths"
            ],
            $resolver->getModulesPath()
        );
    }

    /**
     * Validate correct path locations are fed into globRelevantPaths
     * @throws \Exception
     */
    public function testGetModulePathsLocations()
    {
        $mockResolver = $this->setMockResolverClass(
            false,
            null,
            null,
            null,
            ["example" . DIRECTORY_SEPARATOR . "paths"]
        );
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, null);
        $this->assertEquals(
            [
                "example" . DIRECTORY_SEPARATOR . "paths",
                "example" . DIRECTORY_SEPARATOR . "paths",
                "example" . DIRECTORY_SEPARATOR . "paths"
            ],
            $resolver->getModulesPath()
        );

        // Define the Module paths from app/code
        $appCodePath = MAGENTO_BP
            . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'code' . DIRECTORY_SEPARATOR;

        // Define the Module paths from default TESTS_MODULE_PATH
        $modulePath = defined('TESTS_MODULE_PATH') ? TESTS_MODULE_PATH : TESTS_BP;

        // Define the Module paths from vendor modules
        $vendorCodePath = PROJECT_ROOT
            . DIRECTORY_SEPARATOR
            . 'vendor' . DIRECTORY_SEPARATOR;

        $mockResolver->verifyInvoked('globRelevantPaths', [$modulePath, '']);
        $mockResolver->verifyInvoked(
            'globRelevantPaths',
            [$appCodePath, DIRECTORY_SEPARATOR . 'Test' . DIRECTORY_SEPARATOR .'Mftf']
        );
        $mockResolver->verifyInvoked(
            'globRelevantPaths',
            [$vendorCodePath, DIRECTORY_SEPARATOR . 'Test' . DIRECTORY_SEPARATOR .'Mftf']
        );
    }

    /**
     * Validate custom modules are added
     * @throws \Exception
     */
    public function testGetCustomModulePath()
    {
        $this->setMockResolverClass(false, ["Magento_TestModule"], null, null, [], ['otherPath']);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, null, null);
        $this->assertEquals(['otherPath'], $resolver->getModulesPath());
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            'including custom module',
            ['module' => 'otherPath']
        );
    }

    /**
     * Validate blacklisted modules are removed
     * @throws \Exception
     */
    public function testGetModulePathsBlacklist()
    {
        $this->setMockResolverClass(
            false,
            null,
            null,
            null,
            function ($arg1, $arg2) {
                if ($arg2 === "") {
                    $mockValue = ["somePath" => "somePath"];
                } elseif (strpos($arg1, "app")) {
                    $mockValue = ["otherPath" => "otherPath"];
                } else {
                    $mockValue = ["lastPath" => "lastPath"];
                }
                return $mockValue;
            }
        );
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, null, ["somePath"]);
        $this->assertEquals(["otherPath", "lastPath"], $resolver->getModulesPath());
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            'excluding module',
            ['module' => 'somePath']
        );
    }

    /**
     * Validate that getEnabledModules returns correctly with no admin token
     * @throws \Exception
     */
    public function testGetModulePathsNoAdminToken()
    {
        $this->setMockResolverClass(false, null, ["example" . DIRECTORY_SEPARATOR . "paths"], []);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, null);
        $this->assertEquals(["example" . DIRECTORY_SEPARATOR . "paths"], $resolver->getModulesPath());
    }

    /**
     * Function used to set mock for parser return and force init method to run between tests.
     *
     * @param string $mockToken
     * @param array $mockGetModules
     * @param string[] $mockCustomMethods
     * @param string[] $mockGlob
     * @param string[] $mockRelativePaths
     * @param string[] $mockCustomModules
     * @throws \Exception
     * @return Verifier ModuleResolver double
     */
    private function setMockResolverClass(
        $mockToken = null,
        $mockGetModules = null,
        $mockCustomMethods = null,
        $mockGlob = null,
        $mockRelativePaths = null,
        $mockCustomModules = null
    ) {
        $property = new \ReflectionProperty(ModuleResolver::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockMethods = [];
        if (isset($mockToken)) {
            $mockMethods['getAdminToken'] = $mockToken;
        }
        if (isset($mockModules)) {
            $mockMethods['getEnabledModules'] = $mockGetModules;
        }
        if (isset($mockCustomMethods)) {
            $mockMethods['applyCustomModuleMethods'] = $mockCustomMethods;
        }
        if (isset($mockGlob)) {
            $mockMethods['globRelevantWrapper'] = $mockGlob;
        }
        if (isset($mockRelativePaths)) {
            $mockMethods['globRelevantPaths'] = $mockRelativePaths;
        }
        if (isset($mockCustomModules)) {
            $mockMethods['getCustomModulePaths'] = $mockCustomModules;
        }
        $mockMethods['printMagentoVersionInfo'] = null;

        $mockResolver = AspectMock::double(
            ModuleResolver::class,
            $mockMethods
        );
        $instance = AspectMock::double(
            ObjectManager::class,
            ['create' => $mockResolver->make(), 'get' => null]
        )->make();
        // bypass the private constructor
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);

        return $mockResolver;
    }

    /**
     * Function used to set mock for Resolver properties
     *
     * @param ModuleResolver $instance
     * @param array $mockPaths
     * @param array $mockModules
     * @param array $mockBlacklist
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

    /**
     * After method functionality
     * @return void
     */
    protected function tearDown()
    {
        AspectMock::clean();
    }
}
