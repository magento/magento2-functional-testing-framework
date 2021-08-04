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

/**
 * Class ModuleResolverTest
 */
class ModuleResolverTest extends MagentoTestCase
{
    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        TestLoggingUtil::getInstance()->setMockLoggingUtil();
    }

    /**
     * @inheritDoc
     */
    public static function tearDownAfterClass(): void
    {
        TestLoggingUtil::getInstance()->clearMockLoggingUtil();
        
        $moduleResolverServiceInstance = new ReflectionProperty(ModuleResolverService::class, 'INSTANCE');
        $moduleResolverServiceInstance->setAccessible(true);
        $moduleResolverServiceInstance->setValue(null);

        $mftfAppConfigInstance = new ReflectionProperty(MftfApplicationConfig::class, 'MFTF_APPLICATION_CONTEXT');
        $mftfAppConfigInstance->setAccessible(true);
        $mftfAppConfigInstance->setValue(null);
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
        $this->setMockResolverProperties($resolver, ['example' . DIRECTORY_SEPARATOR . 'paths']);
        $this->assertEquals(['example' . DIRECTORY_SEPARATOR . 'paths'], $resolver->getModulesPath());
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

        $moduleResolverService = $this->createPartialMock(
            ModuleResolverService::class,
            ['getRegisteredModuleList', 'aggregateTestModulePaths']
        );
        $moduleResolverService->expects($this->any())
            ->method('getRegisteredModuleList')
            ->willReturn(
                [
                    'Magento_example' => 'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'example',
                    'Magento_sample' => 'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'sample'
                ]
            );
        $moduleResolverService->expects($this->any())
            ->method('aggregateTestModulePaths')
            ->willReturn(
                [
                    'some' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'example' => ['example'],
                    'other' . DIRECTORY_SEPARATOR . 'path' . DIRECTORY_SEPARATOR . 'sample' => ['sample']
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
     * Validate aggregateTestModulePaths() when module path part of DEV_TESTS.
     *
     * @return void
     * @throws Exception
     */
    public function testAggregateTestModulePathsDevTests(): void
    {
        $origin = TESTS_MODULE_PATH;
        $modulePath = ModuleResolver::DEV_TESTS . DIRECTORY_SEPARATOR . "Magento";
        putenv("TESTS_MODULE_PATH=$modulePath");

        $this->mockForceGenerate(false);
        $moduleResolverService = $this->createPartialMock(ModuleResolverService::class, ['globRelevantPaths']);
        $moduleResolverService
            ->method('globRelevantPaths')
            ->will(
                $this->returnCallback(
                    function ($codePath, $pattern) use ($modulePath) {
                        if ($codePath === $modulePath && $pattern === '') {
                            $this->fail(sprintf(
                                'Not expected parameter: \'%s\' when invoked method globRelevantPaths().',
                                $modulePath
                            ));
                        }

                        return [];
                    }
                )
            );
        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, []);
        $this->assertEquals([], $resolver->getModulesPath());

        putenv("TESTS_MODULE_PATH=$origin");
    }

    /**
     * Validate correct path locations are fed into globRelevantPaths.
     *
     * @return void
     * @throws Exception
     */
    public function testGetModulePathsLocations(): void
    {
        // clear test object handler value to inject parsed content
        $property = new ReflectionProperty(ModuleResolver::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);

        $this->mockForceGenerate(false);
        // Define the Module paths from default TESTS_MODULE_PATH
        $modulePath = defined('TESTS_MODULE_PATH') ? TESTS_MODULE_PATH : TESTS_BP;
        $invokedWithParams = $expectedParams = [
            [
                $modulePath,
                ''
            ],
            [
                MAGENTO_BP . '/vendor',
                'Test/Mftf'
            ],
            [
                MAGENTO_BP . '/app/code',
                'Test/Mftf'
            ]
        ];

        $moduleResolverService = $this->createPartialMock(ModuleResolverService::class, ['globRelevantPaths']);
        $moduleResolverService
            ->method('globRelevantPaths')
            ->will(
                $this->returnCallback(
                    function ($codePath, $pattern) use (&$invokedWithParams, $expectedParams) {
                        foreach ($expectedParams as $key => $parameter) {
                            list($expectedCodePath, $expectedPattern) = $parameter;

                            if ($codePath === $expectedCodePath && $pattern === $expectedPattern) {
                                if (isset($invokedWithParams[$key])) {
                                    unset($invokedWithParams[$key]);
                                }

                                return [];
                            }
                        }

                        $this->fail(sprintf(
                            'Not expected parameter: [%s] when invoked method globRelevantPaths().',
                            $codePath . ';' . $pattern
                        ));
                    }
                )
            );

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, []);
        $this->assertEquals([], $resolver->getModulesPath());

        if ($invokedWithParams) {
            $parameters = '';

            foreach ($invokedWithParams as $parameter) {
                $parameters .= sprintf('[%s]', implode(';', $parameter));
            }

            $this->fail('The method globRelevantPaths() was not called with expected parameters:' . $parameters);
        }
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

        $moduleResolverService = $this->createPartialMock(
            ModuleResolverService::class,
            ['getComposerJsonTestModulePaths']
        );
        $moduleResolverService
            ->expects($this->any())
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
                        ]
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
     * Validate getComposerJsonTestModulePaths with paths invocation.
     *
     * @return void
     * @throws Exception
     */
    public function testGetComposerJsonTestModulePathsForPathInvocation(): void
    {
        $this->mockForceGenerate(false);

        // Expected dev tests path
        $expectedSearchPaths[] = MAGENTO_BP
            . DIRECTORY_SEPARATOR
            . 'dev'
            . DIRECTORY_SEPARATOR
            . 'tests'
            . DIRECTORY_SEPARATOR
            . 'acceptance'
            . DIRECTORY_SEPARATOR
            . 'tests'
            . DIRECTORY_SEPARATOR
            . 'functional';

        // Expected test module path
        $testModulePath = defined('TESTS_MODULE_PATH') ? TESTS_MODULE_PATH : TESTS_BP;

        if (array_search($testModulePath, $expectedSearchPaths) === false) {
            $expectedSearchPaths[] = $testModulePath;
        }

        $moduleResolverService = $this->createPartialMock(
            ModuleResolverService::class,
            ['getComposerJsonTestModulePaths']
        );
        $moduleResolverService
            ->method('getComposerJsonTestModulePaths')
            ->will(
                $this->returnCallback(
                    function ($codePaths) use ($expectedSearchPaths) {
                        if ($codePaths === $expectedSearchPaths) {
                            return [];
                        }

                        $this->fail(sprintf(
                            'Not expected parameter: \'%s\' when invoked method getComposerJsonTestModulePaths().',
                            $codePaths
                        ));
                    }
                )
            );

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, []);
        $this->assertEquals([], $resolver->getModulesPath());
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
        $moduleResolverService = $this->createPartialMock(
            ModuleResolverService::class,
            ['getComposerInstalledTestModulePaths']
        );
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
                        ]
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
     * Validate getComposerInstalledTestModulePaths with paths invocation.
     *
     * @return void
     * @throws Exception
     */
    public function testGetComposerInstalledTestModulePathsForPathInvocation(): void
    {
        $this->mockForceGenerate(false);

        // Expected file path
        $expectedSearchPath = MAGENTO_BP . DIRECTORY_SEPARATOR . 'composer.json';
        $moduleResolverService = $this->createPartialMock(
            ModuleResolverService::class,
            ['getComposerInstalledTestModulePaths']
        );
        $moduleResolverService
            ->method('getComposerInstalledTestModulePaths')
            ->will(
                $this->returnCallback(
                    function ($composerFile) use ($expectedSearchPath) {
                        if ($composerFile === $expectedSearchPath) {
                            return [];
                        }

                        $this->fail(sprintf(
                            'Not expected parameter: \'%s\' when invoked method getComposerInstalledTestModulePaths().',
                            $composerFile
                        ));
                    }
                )
            );

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, []);
        $this->assertEquals([], $resolver->getModulesPath());
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
        $moduleResolverService = $this->createPartialMock(
            ModuleResolverService::class,
            ['getComposerJsonTestModulePaths', 'getComposerInstalledTestModulePaths', 'aggregateTestModulePaths']
        );
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
                7 => 'Magento_ModuleC'
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
                'composer' . DIRECTORY_SEPARATOR . 'install' . DIRECTORY_SEPARATOR . 'pathC'

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
        $moduleResolverService = $this->createPartialMock(
            ModuleResolverService::class,
            ['getComposerJsonTestModulePaths', 'getComposerInstalledTestModulePaths', 'aggregateTestModulePaths']
        );
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
                            'Magento_ModuleC'
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
        $moduleResolverService = $this->createPartialMock(
            ModuleResolverService::class,
            ['getComposerJsonTestModulePaths', 'getComposerInstalledTestModulePaths', 'aggregateTestModulePaths']
        );
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
                            'Magento_ModuleC'
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
        $moduleResolverService = $this->createPartialMock(
            ModuleResolverService::class,
            ['getComposerJsonTestModulePaths', 'getComposerInstalledTestModulePaths']
        );
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
        $moduleResolverService = $this->createPartialMock(
            ModuleResolverService::class,
            ['getCustomModulePaths', 'aggregateTestModulePaths']
        );
        $moduleResolverService->expects($this->any())
            ->method('getCustomModulePaths')
            ->willReturn(['Magento_Module' => 'otherPath']);

        $moduleResolverService
            ->method('aggregateTestModulePaths')
            ->willReturn([]);

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
        $moduleResolverService = $this->createPartialMock(ModuleResolverService::class, ['aggregateTestModulePaths']);
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
     * Validates that getAdminToken is not called when --force is enabled.
     *
     * @return void
     * @throws Exception
     */
    public function testGetAdminTokenNotCalledWhenForce(): void
    {
        // Set --force to true
        $this->mockForceGenerate(true);

        // Mock ModuleResolver and applyCustomModuleMethods()
        $moduleResolverService = $this->createMock(ModuleResolverService::class);
        $moduleResolverService
            ->method('getAdminToken')
            ->with(
                $this->returnCallback(
                    function () {
                        $this->fail('Not expected to call method \'getAdminToken()\'.');
                    }
                )
            );

        $this->setMockResolverCreatorProperties($moduleResolverService);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver, null, []);
        $resolver->getModulesPath();
        $this->addToAssertionCount(1);
    }

    /**
     * Verify the getAdminToken method returns throws an exception if ENV is not fully loaded.
     *
     * @return void
     * @throws Exception
     */
    public function testGetAdminTokenWithMissingEnv(): void
    {
        // Set --force to false
        $this->mockForceGenerate(false);
        $this->setMockResolverCreatorProperties(null);

        // Unset env
        unset($_ENV['MAGENTO_ADMIN_USERNAME']);
        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver);

        // Expect exception
        $this->expectException(FastFailException::class);
        $resolver->getModulesPath();
    }

    /**
     * Verify the getAdminToken method returns throws an exception if Token was bad.
     *
     * @return void
     * @throws Exception
     */
    public function testGetAdminTokenWithBadResponse(): void
    {
        // Set --force to false
        $this->mockForceGenerate(false);
        $this->setMockResolverCreatorProperties(null);

        $resolver = ModuleResolver::getInstance();
        $this->setMockResolverProperties($resolver);

        // Expect exception
        $this->expectException(FastFailException::class);
        $resolver->getModulesPath();
    }

    /**
     * Function used to set mock for Resolver properties.
     *
     * @param ModuleResolver $instance
     * @param null $mockPaths
     * @param null $mockModules
     * @param array $mockBlockList
     *
     * @return void
     */
    private function setMockResolverProperties(
        ModuleResolver $instance,
        $mockPaths = null,
        $mockModules = null,
        $mockBlockList = []
    ): void {
        $property = new ReflectionProperty(ModuleResolver::class, 'enabledModulePaths');
        $property->setAccessible(true);
        $property->setValue($instance, $mockPaths);

        $property = new ReflectionProperty(ModuleResolver::class, 'enabledModules');
        $property->setAccessible(true);
        $property->setValue($instance, $mockModules);

        $property = new ReflectionProperty(ModuleResolver::class, 'moduleBlocklist');
        $property->setAccessible(true);
        $property->setValue($instance, $mockBlockList);
    }

    /**
     * Function used to set mock for ResolverCreator properties.
     *
     * @param MockObject|null $moduleResolverService
     *
     * @return void
     */
    private function setMockResolverCreatorProperties(?MockObject $moduleResolverService): void
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
            $_ENV['MAGENTO_ADMIN_USERNAME'] = 'admin';
        }
    }
}
