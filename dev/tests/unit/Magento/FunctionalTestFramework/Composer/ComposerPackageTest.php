<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use Magento\FunctionalTestingFramework\Composer\ComposerPackage;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;
use Composer\Package\RootPackage;

class ComposerPackageTest extends MagentoTestCase
{
    /**
     * ComposerPackage instance to be tested
     *
     * @var ComposerPackage
     */
    private $composer;

    public function setUp()
    {
        $composerJson = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Composer' . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'dir1' . DIRECTORY_SEPARATOR . 'dir2' . DIRECTORY_SEPARATOR . 'composer.json';

        $this->composer = new ComposerPackage($composerJson);
    }

    /**
     * Test getName()
     */
    public function testGetName()
    {
        $expected = 'magento/module2-functional-test';
        $this->assertEquals($expected, $this->composer->getName());
    }

    /**
     * Test getType()
     */
    public function testGetType()
    {
        $expected = 'magento2-functional-test-module';
        $this->assertEquals($expected, $this->composer->getType());
    }

    /**
     * Test getVersion()
     */
    public function testGetVersion()
    {
        $expected = '1.0.0';
        $this->assertEquals($expected, $this->composer->getVersion());
    }

    /**
     * Test getDescription()
     */
    public function testGetDescription()
    {
        $expected = 'MFTF tests for magento';
        $this->assertEquals($expected, $this->composer->getDescription());
    }

    /**
     * Test getRequires()
     */
    public function testGetRequires()
    {
        $expected = 'magento/magento2-functional-testing-framework';
        $output = $this->composer->getRequires();
        $this->assertCount(1, $output);
        $this->assertArrayHasKey($expected, $output);
    }

    /**
     * Test getDevRequires()
     */
    public function testGetDevRequires()
    {
        $expected = ['phpunit/phpunit'];
        $this->assertEquals($expected, array_keys($this->composer->getDevRequires()));
    }

    /**
     * Test getSuggests()
     */
    public function testGetSuggests()
    {
        $expected = [
            'magento/module-one',
            'magento/module-module-x',
            'magento/module-two',
            'magento/module-module-y',
            'magento/module-module-z',
            'magento/module-three',
            'magento/module-four'
        ];
        $this->assertEquals($expected, array_keys($this->composer->getSuggests()));
    }

    /**
     * Test getSuggestedMagentoModules()
     */
    public function testGetSuggestedMagentoModules()
    {
        $expected = [
            'Magento_ModuleX',
            'Magento_ModuleY',
            'Magento_ModuleZ'
        ];
        $this->assertEquals($expected, $this->composer->getSuggestedMagentoModules());
    }

    /**
     * Test isMftfTestPackage()
     */
    public function testIsMftfTestPackage()
    {
        $this->assertTrue($this->composer->isMftfTestPackage());
    }

    /**
     * Test getRequiresForPackage()
     */
    public function testGetRequiresForPackage()
    {
        $expected = [
            'php',
            'ext-curl',
            'allure-framework/allure-codeception',
            'codeception/codeception',
            'consolidation/robo',
            'csharpru/vault-php',
            'csharpru/vault-php-guzzle6-transport',
            'flow/jsonpath',
            'fzaninotto/faker',
            'monolog/monolog',
            'mustache/mustache',
            'symfony/process',
            'vlucas/phpdotenv'
        ];
        $this->assertEquals(
            $expected,
            array_keys($this->composer->getRequiresForPackage('magento/magento2-functional-testing-framework', '2.5.0'))
        );
    }

    /**
     * Test isPackageRequiredInComposerJson()
     */
    public function testIsPackageRequiredInComposerJson()
    {
        $this->assertTrue(
            $this->composer->isPackageRequiredInComposerJson('magento/magento2-functional-testing-framework')
        );
    }

    /**
     * Test getRootPackage()
     */
    public function testGetRootPackage()
    {
        $this->assertInstanceOf(
            RootPackage::class,
            $this->composer->getRootPackage()
        );
    }
}
