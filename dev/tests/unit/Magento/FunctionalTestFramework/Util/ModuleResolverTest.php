<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use AspectMock\Proxy\Verifier;
use AspectMock\Test as AspectMock;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
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
        $this->mockForceGenerate(false);
        $this->setMockResolverClass(
            false,
            null,
            null,
            null,
            ["Magento_example" => "example" . DIRECTORY_SEPARATOR . "paths"]
        );
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, [0 => "Magento_example"]);
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
        $this->mockForceGenerate(false);
        $mockResolver = $this->setMockResolverClass(
            true,
            [0 => "example"],
            null,
            null,
            ["example" => "example" . DIRECTORY_SEPARATOR . "paths"]
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
        $magentoBaseCodePath = MAGENTO_BP;

        // Define the Module paths from default TESTS_MODULE_PATH
        $modulePath = defined('TESTS_MODULE_PATH') ? TESTS_MODULE_PATH : TESTS_BP;

        $mockResolver->verifyInvoked('globRelevantPaths', [$modulePath, '']);
        $mockResolver->verifyInvoked(
            'globRelevantPaths',
            [$magentoBaseCodePath . DIRECTORY_SEPARATOR . "vendor" , 'Test' . DIRECTORY_SEPARATOR .'Mftf']
        );
        $mockResolver->verifyInvoked(
            'globRelevantPaths',
            [
                $magentoBaseCodePath . DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "code",
                'Test' . DIRECTORY_SEPARATOR .'Mftf'
            ]
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
                } else {
                    $mockValue = ["lastPath" => "lastPath"];
                }
                return $mockValue;
            }
        );
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, null, ["somePath"]);
        $this->assertEquals(["lastPath", "lastPath"], $resolver->getModulesPath());
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            'excluding module',
            ['module' => 'somePath']
        );
    }

    /**
     * Validate that getEnabledModules errors out when no Admin Token is returned and --force is false
     * @throws \Exception
     */
    public function testGetModulePathsNoAdminToken()
    {
        // Set --force to false
        $this->mockForceGenerate(false);

        // Mock ModuleResolver and $enabledModulesPath
        $this->setMockResolverClass(false, null, ["example" . DIRECTORY_SEPARATOR . "paths"], []);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, null);

        // Cannot Generate if no --force was passed in and no Admin Token is returned succesfully
        $this->expectException(TestFrameworkException::class);
        $resolver->getModulesPath();
    }

    /**
     * Validates that getAdminToken is not called when --force is enabled
     */
    public function testGetAdminTokenNotCalledWhenForce()
    {
        // Set --force to true
        $this->mockForceGenerate(true);

        // Mock ModuleResolver and applyCustomModuleMethods()
        $mockResolver = $this->setMockResolverClass();
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, null);
        $resolver->getModulesPath();
        $mockResolver->verifyNeverInvoked("getAdminToken");

        // verifyNeverInvoked does not add to assertion count
        $this->addToAssertionCount(1);
    }

    /**
     * Verify the getAdminToken method returns throws an exception if ENV is not fully loaded.
     */
    public function testGetAdminTokenWithMissingEnv()
    {
        // Set --force to true
        $this->mockForceGenerate(false);

        // Unset env
        unset($_ENV['MAGENTO_ADMIN_USERNAME']);

        // Mock ModuleResolver and applyCustomModuleMethods()
        $mockResolver = $this->setMockResolverClass();
        $resolver = ModuleResolver::getInstance();

        // Expect exception
        $this->expectException(TestFrameworkException::class);
        $resolver->getModulesPath();
    }

    /**
     * Verify the getAdminToken method returns throws an exception if Token was bad.
     */
    public function testGetAdminTokenWithBadResponse()
    {
        // Set --force to true
        $this->mockForceGenerate(false);

        // Mock ModuleResolver and applyCustomModuleMethods()
        $mockResolver = $this->setMockResolverClass();
        $resolver = ModuleResolver::getInstance();

        // Expect exception
        $this->expectException(TestFrameworkException::class);
        $resolver->getModulesPath();
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
        if (isset($mockGetModules)) {
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
//        $mockMethods['printMagentoVersionInfo'] = null;

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
     * Mocks MftfApplicationConfig->forceGenerateEnabled()
     * @param $forceGenerate
     * @throws \Exception
     * @return void
     */
    private function mockForceGenerate($forceGenerate)
    {
        $mockConfig = AspectMock::double(
            MftfApplicationConfig::class,
            ['forceGenerateEnabled' => $forceGenerate]
        );
        $instance = AspectMock::double(
            ObjectManager::class,
            ['create' => $mockConfig->make(), 'get' => null]
        )->make();
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);
    }

    /**
     * After method functionality
     * @return void
     */
    protected function tearDown()
    {
        // re set env
        if (!isset($_ENV['MAGENTO_ADMIN_USERNAME'])) {
            $_ENV['MAGENTO_ADMIN_USERNAME'] = "admin";
        }

        AspectMock::clean();
    }
}
