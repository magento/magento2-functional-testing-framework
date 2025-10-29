<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Composer;

use Magento\FunctionalTestingFramework\Composer\ComposerInstall;
use tests\unit\Util\MagentoTestCase;

class ComposerInstallTest extends MagentoTestCase
{
    /**
     * ComposerInstall instance to be tested
     *
     * @var ComposerInstall
     */
    private $composer;

    public function setUp(): void
    {
        $composerJson = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'Composer' . DIRECTORY_SEPARATOR . '_files'
            . DIRECTORY_SEPARATOR . 'dir1' . DIRECTORY_SEPARATOR . 'dir2' . DIRECTORY_SEPARATOR . 'composer.json';

        $this->composer = new ComposerInstall($composerJson);
    }

    /**
     * Test isMftfTestPackage()
     */
    public function testIsMftfTestPackage()
    {
        $this->assertTrue($this->composer->isMftfTestPackage('magento/module2-functional-test'));
    }

    /**
     * Test isMagentoPackage()
     */
    public function testIsMagentoPackage()
    {
        $this->assertTrue($this->composer->isMagentoPackage('magento/module-authorization'));
    }

    /**
     * Test isInstalledPackageOfType()
     */
    public function testIsInstalledPackageOfType()
    {
        $this->assertTrue($this->composer->isInstalledPackageOfType('composer/composer', 'library'));
    }

    /**
     * Test getInstalledTestPackages()
     */
    public function testGetInstalledTestPackages()
    {
        $output = $this->composer->getInstalledTestPackages();
        $this->assertCount(1, $output);
        $this->assertArrayHasKey('magento/module2-functional-test', $output);
    }
}
