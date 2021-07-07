<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace tests\unit\Magento\FunctionalTestFramework\Util;

use Exception;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
use Magento\FunctionalTestingFramework\Util\ModuleResolver\ModuleResolverService;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use PHPUnit\Framework\MockObject\MockObject;
use ReflectionProperty;
use tests\unit\Util\MagentoTestCase;
use tests\unit\Util\TestLoggingUtil;

class ModuleResolverTest extends MagentoTestCase
{
    /**
     * Before test functionality.
     *
     * @return void
     */
    protected function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * After class functionality.
     *
     * @return void
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
    }

    /**
     * Validate that Paths that are already set are returned.
     *
     * @return void
     * @throws Exception
     */
    public function testGetModulePathsAlreadySet(): void
    {
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, ["example" . DIRECTORY_SEPARATOR . "paths"]);
        $this->assertEquals(["example" . DIRECTORY_SEPARATOR . "paths"], $resolver->getModulesPath());
    }

    /**
     * Validate paths are aggregated correctly.
     *
     * @return void
     * @throws Exception
     */
    public function testGetModulePathsAggregate(): void
    {
        $this->mockForceGenerate(false);

        $moduleResolverService = $this->createMock(ModuleResolverService::class);
        $moduleResolverService->expects($this->any())
            ->method('getRegisteredModuleList')
            ->willReturn(
                [
                    'Magento_example' => 'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'example',
                    'Magento_sample' => 'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'sample',
                ]
            );
        $moduleResolverService->expects($this->any())
            ->method('aggregateTestModulePaths')
            ->willReturn(
                [
                    'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'example' => ['example'],
                    'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'sample' => ['sample'],
                ]
            );

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, [0 => 'Magento_example', 1 => 'Magento_sample']);
        $this->assertEquals(
            [
                'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'example',
                'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'sample'
            ],
            $resolver->getModulesPath()
        );
    }

    /**
     * Validate aggregateTestModulePathsFromComposerJson.
     *
     * @return void
     * @throws Exception
     */
    public function testAggregateTestModulePathsFromComposerJson(): void
    {
        $this->mockForceGenerate(false);

        $moduleResolverService = $this->createMock(ModuleResolverService::class);
        $moduleResolverService->expects($this->any())
            ->method('getComposerJsonTestModulePaths')
            ->willReturn(
                [
                    'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'pathA' =>
                        [
                            'Magento_ModuleA',
                            'Magento_ModuleB'
                        ],
                    'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'pathB' =>
                        [
                            'Magento_ModuleB',
                            'Magento_ModuleC'
                        ],
                ]
            );

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, [0 => 'Magento_ModuleB', 1 => 'Magento_ModuleC']);
        $this->assertEquals(
            [
                'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'pathB'
            ],
            $resolver->getModulesPath()
        );
    }

    /**
     * Validate aggregateTestModulePathsFromComposerInstaller.
     *
     * @return void
     * @throws Exception
     */
    public function testAggregateTestModulePathsFromComposerInstaller(): void
    {
        $this->mockForceGenerate(false);
        $moduleResolverService = $this->createMock(ModuleResolverService::class);
        $moduleResolverService->expects($this->any())
            ->method('getComposerInstalledTestModulePaths')
            ->willReturn(
                [
                    'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathA' =>
                        [
                            'Magento_ModuleA',
                            'Magento_ModuleB'
                        ],
                    'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathB' =>
                        [
                            'Magento_ModuleB',
                            'Magento_ModuleC'
                        ],
                ]
            );

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties(
            $resolver,
            null,
            [0 => 'Magento_ModuleA', 1 => 'Magento_ModuleB', 2 => 'Magento_ModuleC']
        );
        $this->assertEquals(
            [
                'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathA',
                'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathB'
            ],
            $resolver->getModulesPath()
        );
    }

    /**
     * Validate mergeModulePaths() and flipAndFilterModulePathsArray().
     *
     * @return void
     * @throws Exception
     */
    public function testMergeFlipAndFilterModulePathsNoForceGenerate(): void
    {
        $this->mockForceGenerate(false);
        $moduleResolverService = $this->createMock(ModuleResolverService::class);
        $moduleResolverService->expects($this->any())
            ->method('getComposerJsonTestModulePaths')
            ->willReturn(
                [
                    'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'pathA' =>
                        [
                            'Magento_ModuleA'
                        ],
                    'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'pathB' =>
                        [
                            'Magento_ModuleB'
                        ]
                ]
            );
        $moduleResolverService->expects($this->any())
            ->method('getComposerInstalledTestModulePaths')
            ->willReturn(
                [
                    'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathD' =>
                        [
                            'Magento_ModuleD'
                        ],
                    'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathE' =>
                        [
                            'Magento_ModuleE'
                        ],
                    'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathC' =>
                        [
                            'Magento_ModuleC',
                            'Magento_ModuleB',
                        ]
                ]
            );
        $moduleResolverService->expects($this->any())
            ->method('aggregateTestModulePaths')
            ->willReturn(
                [
                    'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'example' => ['Magento_Example'],
                    'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'sample' => ['Magento_Sample'],
                    'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'path1' => ['Magento_Path1'],
                    'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'path2' => ['Magento_Path2'],
                    'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'path3' => ['Magento_Path3'],
                    'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'path4' => ['Magento_Path4']
                ]
            );

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties(
            $resolver,
            null,
            [
                0 => 'Magento_Path1',
                1 => 'Magento_Path2',
                2 => 'Magento_Path4',
                3 => 'Magento_Example',
                4 => 'Magento_ModuleB',
                5 => 'Magento_ModuleD',
                6 => 'Magento_Otherexample',
                7 => 'Magento_ModuleC',
            ]
        );
        $this->assertEquals(
            [
                'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'path1',
                'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'path2',
                'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'path4',
                'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'example',
                'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'pathB',
                'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathD',
                'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathC',

            ],
            $resolver->getModulesPath()
        );
    }

    /**
     * Validate mergeModulePaths() and flipAndSortModulePathsArray().
     *
     * @return void
     * @throws Exception
     */
    public function testMergeFlipNoSortModulePathsNoForceGenerate(): void
    {
        $this->mockForceGenerate(false);
        $moduleResolverService = $this->createMock(ModuleResolverService::class);
        $moduleResolverService->expects($this->any())
            ->method('getComposerJsonTestModulePaths')
            ->willReturn(
                [
                    'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR
                    . 'Magento' . DIRECTORY_SEPARATOR . 'ModuleA' =>
                        [
                            'Magento_ModuleA'
                        ],
                    'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR
                    . 'Magento' . DIRECTORY_SEPARATOR . 'ModuleBC' =>
                        [
                            'Magento_ModuleB',
                            'Magento_ModuleC',
                        ]
                ]
            );
        $moduleResolverService->expects($this->any())
            ->method('getComposerInstalledTestModulePaths')
            ->willReturn(
                [
                    'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR
                    . 'Magento' . DIRECTORY_SEPARATOR . 'ModuleCD' =>
                        [
                            'Magento_ModuleC',
                            'Magento_ModuleD'
                        ],
                    'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR
                    . 'Magento' . DIRECTORY_SEPARATOR . 'ModuleE' =>
                        [
                            'Magento_ModuleE'
                        ]
                ]
            );
        $moduleResolverService->expects($this->any())
            ->method('aggregateTestModulePaths')
            ->willReturn(
                [
                    'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'example' => ['Magento_Example'],
                    'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'sample' => ['Magento_Sample']
                ]
            );

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties(
            $resolver,
            null,
            [
                0 => 'Magento_ModuleB',
                1 => 'Magento_ModuleC',
                2 => 'Magento_ModuleE',
                3 => 'Magento_Example',
                4 => 'Magento_Otherexample'
            ]
        );

        $this->assertEquals(
            [
                'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'Magento' . DIRECTORY_SEPARATOR
                . 'ModuleE',
                'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'example',
                'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'Magento' . DIRECTORY_SEPARATOR
                . 'ModuleBC'
            ],
            $resolver->getModulesPath()
        );
    }

    /**
     * Validate mergeModulePaths() and flipAndSortModulePathsArray().
     *
     * @return void
     * @throws Exception
     */
    public function testMergeFlipAndSortModulePathsForceGenerate(): void
    {
        $this->mockForceGenerate(true);
        $moduleResolverService = $this->createMock(ModuleResolverService::class);
        $moduleResolverService->expects($this->any())
            ->method('getComposerJsonTestModulePaths')
            ->willReturn(
                [
                    'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR
                    . 'Magento' . DIRECTORY_SEPARATOR . 'ModuleA' =>
                        [
                            'Magento_ModuleA'
                        ],
                    'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR
                    . 'Magento' . DIRECTORY_SEPARATOR . 'ModuleBC' =>
                        [
                            'Magento_ModuleB',
                            'Magento_ModuleC',
                        ]
                ]
            );
        $moduleResolverService->expects($this->any())
            ->method('getComposerInstalledTestModulePaths')
            ->willReturn(
                [
                    'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR
                    . 'Magento' . DIRECTORY_SEPARATOR . 'ModuleCD' =>
                        [
                            'Magento_ModuleC',
                            'Magento_ModuleD'
                        ],
                    'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR
                    . 'Magento' . DIRECTORY_SEPARATOR . 'ModuleD' =>
                        [
                            'Magento_ModuleD'
                        ]
                ]
            );
        $moduleResolverService->expects($this->any())
            ->method('aggregateTestModulePaths')
            ->willReturn(
                [
                    'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'example' => ['Magento_Example'],
                    'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'sample' => ['Magento_Sample']
                ]
            );

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties(
            $resolver,
            null,
            [
                0 => 'Magento_ModuleB',
                1 => 'Magento_ModuleC',
                2 => 'Magento_ModuleD',
                3 => 'Magento_Example',
                4 => 'Magento_Otherexample'
            ]
        );

        $this->assertEquals(
            [
                'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'example',
                'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'Magento' . DIRECTORY_SEPARATOR
                    . 'ModuleA',
                'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'Magento' . DIRECTORY_SEPARATOR
                    . 'ModuleD',
                'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'sample',
                'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'Magento' . DIRECTORY_SEPARATOR
                    . 'ModuleBC',
                'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'Magento' . DIRECTORY_SEPARATOR
                    . 'ModuleCD'
            ],
            $resolver->getModulesPath()
        );
    }

    /**
     * Validate logging warning in flipAndFilterModulePathsArray().
     *
     * @return void
     * @throws Exception
     */
    public function testMergeFlipAndFilterModulePathsWithLogging(): void
    {
        $this->mockForceGenerate(false);
        $moduleResolverService = $this->createMock(ModuleResolverService::class);
        $moduleResolverService->expects($this->any())
            ->method('getComposerJsonTestModulePaths')
            ->willReturn(
                [
                    'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'pathA' =>
                        [
                            'Magento_ModuleA'
                        ],
                    'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'pathB' =>
                        [
                            'Magento_ModuleB'
                        ]
                ]
            );
        $moduleResolverService->expects($this->any())
            ->method('getComposerInstalledTestModulePaths')
            ->willReturn(
                [
                    'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathA' =>
                        [
                            'Magento_ModuleA'
                        ],
                    'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathB' =>
                        [
                            'Magento_ModuleC'
                        ]
                ]
            );

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties(
            $resolver,
            null,
            [
                0 => 'Magento_ModuleA',
                1 => 'Magento_ModuleB',
                2 => 'Magento_ModuleC'
            ]
        );
        $this->assertEquals(
            [
                'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'pathA',
                'composer' . DIRECTORY_SEPARATOR . 'json' . DIRECTORY_SEPARATOR . 'pathB',
                'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathB'
            ],
            $resolver->getModulesPath()
        );

        $warnMsg = 'Path: composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR;
        $warnMsg .= 'pathA is ignored by ModuleResolver. ' . PHP_EOL . 'Path: composer' . DIRECTORY_SEPARATOR;
        $warnMsg .= 'json' . DIRECTORY_SEPARATOR . 'pathA is set for Module: Magento_ModuleA' . PHP_EOL;
        TestLoggingUtil::getInstance()->validateMockLogStatement('warning', $warnMsg, []);
    }

    /**
     * Validate custom modules are added.
     *
     * @return void
     * @throws Exception
     */
    public function testApplyCustomModuleMethods(): void
    {
        $this->mockForceGenerate(true);
        $moduleResolverService = $this->createMock(ModuleResolverService::class);
        $moduleResolverService->expects($this->any())
            ->method('getCustomModulePaths')
            ->willReturn(['Magento_Module' => 'otherPath']);

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver);
        $this->assertEquals(['otherPath'], $resolver->getModulesPath());
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            'including custom module',
            [ 'Magento_Module' => 'otherPath']
        );
    }

    /**
     * Validate blocklisted modules are removed
     * Module paths are sorted according to module name in alphabetically ascending order
     *
     * @throws Exception
     */
    public function testGetModulePathsBlocklist(): void
    {
        $this->mockForceGenerate(true);
        $moduleResolverService = $this->createMock(ModuleResolverService::class);
        $moduleResolverService->expects($this->any())
            ->method('aggregateTestModulePaths')
            ->willReturn(
                [
                    'thisPath/some/path4' => ['Some_Module4'],
                    'devTests/Magento/path3' => ['Magento_Module3'],
                    'appCode/Magento/path2' => ['Magento_Module2'],
                    'vendor/amazon/path1' => ['Amazon_Module1']
                ]
            );

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, null, ['Magento_Module3']);
        $this->assertEquals(
            ['vendor/amazon/path1', 'appCode/Magento/path2', 'thisPath/some/path4'],
            $resolver->getModulesPath()
        );
        TestLoggingUtil::getInstance()->validateMockLogStatement(
            'info',
            'excluding module',
            ['module' => 'Magento_Module3']
        );
    }

    /**
     * Validate that getEnabledModules errors out when no Admin Token is returned and --force is false.
     *
     * @return void
     * @throws Exception
     */
    public function testGetModulePathsNoAdminToken(): void
    {
        // Set --force to false
        $this->mockForceGenerate(false);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver);

        // Cannot Generate if no --force was passed in and no Admin Token is returned successfully
        $this->expectException(FastFailException::class);
        $resolver->getModulesPath();
    }

    /**
     * Verify the getAdminToken method returns throws an exception if ENV is not fully loaded.
     */
    public function testGetAdminTokenWithMissingEnv(): void
    {
        // Set --force to false
        $this->mockForceGenerate(false);

        // Unset env
        unset($_ENV['MAGENTO_ADMIN_USERNAME']);
        $resolver = ModuleResolver::getInstance();

        // Expect exception
        $this->expectException(FastFailException::class);
        $resolver->getModulesPath();
    }

    /**
     * Verify the getAdminToken method returns throws an exception if Token was bad.
     */
    public function testGetAdminTokenWithBadResponse(): void
    {
        // Set --force to false
        $this->mockForceGenerate(false);
        $resolver = ModuleResolver::getInstance();

        // Expect exception
        $this->expectException(FastFailException::class);
        $resolver->getModulesPath();
    }

    /**
     * Function used to set mock for Resolver properties.
     *
     * @param ModuleResolver $instance
     * @param array $mockPaths
     * @param array $mockModules
     * @param array $mockBlocklist
     *
     * @return void
     * @throws Exception
     */
    private function setMockResolverProperties(
        ModuleResolver $instance,
        $mockPaths = null,
        $mockModules = null,
        $mockBlocklist = []
    ): void {
        $property = new ReflectionProperty(ModuleResolver::class, 'enabledModulePaths');
        $property->setAccessible(true);
        $property->setValue($instance, $mockPaths);

        $property = new ReflectionProperty(ModuleResolver::class, 'enabledModules');
        $property->setAccessible(true);
        $property->setValue($instance, $mockModules);

        $property = new ReflectionProperty(ModuleResolver::class, 'moduleBlocklist');
        $property->setAccessible(true);
        $property->setValue($instance, $mockBlocklist);
    }

    /**
     * Function used to set mock for ResolverCreator properties.
     *
     * @param MockObject $moduleResolverService
     *
     * @return void
     */
    private function setMockResolverCreatorProperties(MockObject $moduleResolverService): void
    {
        $property = new ReflectionProperty(ModuleResolverService::class, 'INSTANCE');
        $property->setAccessible(true);
        $property->setValue($moduleResolverService);
    }

    /**
     * Mocks MftfApplicationConfig->forceGenerateEnabled()
     * @param bool $forceGenerate
     *
     * @return void
     * @throws Exception
     */
    private function mockForceGenerate(bool $forceGenerate): void
    {
        $mockConfig = $this->createMock(MftfApplicationConfig::class);
        $mockConfig->expects($this->once())
            ->method('forceGenerateEnabled')
            ->willReturn($forceGenerate);

        $property = new ReflectionProperty(MftfApplicationConfig::class, 'MFTF_APPLICATION_CONTEXT');
        $property->setAccessible(true);
        $property->setValue($mockConfig);
    }

    /**
     * After method functionality.
     *
     * @return void
     */
    protected function tearDown(): void
    {
        // re set env
        if (!isset($_ENV['MAGENTO_ADMIN_USERNAME'])) {
            $_ENV['MAGENTO_ADMIN_USERNAME'] = "admin";
        }
    }
}
