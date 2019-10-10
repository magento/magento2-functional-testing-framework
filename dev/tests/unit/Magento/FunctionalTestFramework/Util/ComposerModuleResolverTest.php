<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use ReflectionClass;
use Magento\FunctionalTestingFramework\Util\ComposerModuleResolver;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;

class ComposerModuleResolverTest extends MagentoTestCase
{
    /**
     * Test getComposerInstalledTestModules()
     */
    public function testGetComposerInstalledTestModules()
    {
        $composerJson = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Composer' . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'dir1' . DIRECTORY_SEPARATOR . 'dir2' . DIRECTORY_SEPARATOR . 'composer.json';
        $expected = [
            'Magento_ModuleX',
            'Magento_ModuleY',
            'Magento_ModuleZ'
        ];

        $composer = new ComposerModuleResolver();
        $output = $composer->getComposerInstalledTestModules($composerJson);
        $this->assertCount(1, $output);
        $this->assertEquals($expected, array_pop($output));
    }

    /**
     * Test getTestModulesFromPaths()
     */
    public function testGetTestModulesFromPaths()
    {
        $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Composer' . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'dir1' . DIRECTORY_SEPARATOR . 'dir2';
        $expected = [
            $baseDir . DIRECTORY_SEPARATOR . 'dir31' . DIRECTORY_SEPARATOR . 'dir41' => [
                'Magento_ModuleE',
                'Magento_ModuleF'
            ],
            $baseDir . DIRECTORY_SEPARATOR . 'dir32' . DIRECTORY_SEPARATOR . 'dir41' => [
                'Magento_ModuleG'
            ],
            $baseDir . DIRECTORY_SEPARATOR . 'dir32' . DIRECTORY_SEPARATOR . 'dir42' => [
                'Magento_ModuleH'
            ],
        ];

        $composer = new ComposerModuleResolver();
        $output = $composer->getTestModulesFromPaths([$baseDir]);
        $this->assertEquals($expected, $output);
    }

    /**
     * Test findComposerJsonFilesAtDepth()
     *
     * @dataProvider findComposerJsonFilesAtDepthDataProvider
     * @param string $dir
     * @param integer $depth
     * @param array $expected
     * @throws \ReflectionException
     */
    public function testFindComposerJsonFilesAtDepth($dir, $depth, $expected)
    {
        $composer = new ComposerModuleResolver();
        $class = new ReflectionClass($composer);
        $method = $class->getMethod('findComposerJsonFilesAtDepth');
        $method->setAccessible(true);
        $output = $method->invoke($composer, $dir, $depth);
        $this->assertEquals($expected, $output);
    }

    /**
     * Test findAllComposerJsonFiles()
     *
     * @dataProvider findAllComposerJsonFilesDataProvider
     * @param string $dir
     * @param array $expected
     * @throws \ReflectionException
     */
    public function testFindAllComposerJsonFiles($dir, $expected)
    {
        $composer = new ComposerModuleResolver();
        $class = new ReflectionClass($composer);
        $method = $class->getMethod('findAllComposerJsonFiles');
        $method->setAccessible(true);
        $output = $method->invoke($composer, $dir);
        $this->assertEquals($expected, $output);
    }

    /**
     * Data provider for testFindComposerJsonFilesAtDepth()
     *
     * @return array
     */
    public function findComposerJsonFilesAtDepthDataProvider()
    {
        $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Composer' . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'dir1' . DIRECTORY_SEPARATOR . 'dir2';
        return [
            [
                $baseDir,
                0,
                [
                    $baseDir . DIRECTORY_SEPARATOR . 'composer.json'
                ]
            ],
            [
                $baseDir,
                1,
                [
                    $baseDir . DIRECTORY_SEPARATOR . 'dir31' . DIRECTORY_SEPARATOR . 'composer.json',
                    $baseDir . DIRECTORY_SEPARATOR . 'dir32' . DIRECTORY_SEPARATOR . 'composer.json',
                ]
            ],
            [
                $baseDir,
                2,
                [
                    $baseDir . DIRECTORY_SEPARATOR . 'dir31' . DIRECTORY_SEPARATOR . 'dir41' . DIRECTORY_SEPARATOR
                    . 'composer.json',
                    $baseDir . DIRECTORY_SEPARATOR . 'dir32' . DIRECTORY_SEPARATOR . 'dir41' . DIRECTORY_SEPARATOR
                    . 'composer.json',
                    $baseDir . DIRECTORY_SEPARATOR . 'dir32' . DIRECTORY_SEPARATOR . 'dir42' . DIRECTORY_SEPARATOR
                    . 'composer.json',
                ]
            ]
        ];
    }

    /**
     * Data provider for testFindAllComposerJsonFiles()
     *
     * @return array
     */
    public function findAllComposerJsonFilesDataProvider()
    {
        $baseDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Composer' . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'dir1' . DIRECTORY_SEPARATOR . 'dir2';
        return [
            [
                $baseDir,
                [
                    $baseDir . DIRECTORY_SEPARATOR . 'dir31' . DIRECTORY_SEPARATOR . 'dir41' . DIRECTORY_SEPARATOR
                    . 'composer.json',
                    $baseDir . DIRECTORY_SEPARATOR . 'dir31' . DIRECTORY_SEPARATOR . 'composer.json',
                    $baseDir . DIRECTORY_SEPARATOR . 'dir32' . DIRECTORY_SEPARATOR . 'dir41' . DIRECTORY_SEPARATOR
                    . 'composer.json',
                    $baseDir . DIRECTORY_SEPARATOR . 'dir32' . DIRECTORY_SEPARATOR . 'dir42' . DIRECTORY_SEPARATOR
                    . 'composer.json',
                    $baseDir . DIRECTORY_SEPARATOR . 'dir32' . DIRECTORY_SEPARATOR . 'composer.json',
                    $baseDir . DIRECTORY_SEPARATOR . 'composer.json',
                ]
            ]
        ];
    }
}
