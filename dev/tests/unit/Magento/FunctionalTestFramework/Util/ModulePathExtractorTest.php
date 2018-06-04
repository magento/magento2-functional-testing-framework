<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use Magento\FunctionalTestingFramework\Util\ModulePathExtractor;
use PHPUnit\Framework\TestCase;

class ModulePathExtractorTest extends TestCase
{
    /**
     * Validate correct module is returned for dev/tests path
     * @throws \Exception
     */
    public function testGetMagentoModule()
    {
        $modulePathExtractor = new ModulePathExtractor();
        $this->assertEquals(
            '[Analytics]',
            $modulePathExtractor->extractModuleName(
                "dev/tests/acceptance/tests/functional/Magento/FunctionalTest/[Analytics]/Test/SomeText.xml"
            )
        );
    }

    /**
     * Validate correct module is returned for extension path
     * @throws \Exception
     */
    public function testGetExtensionModule()
    {
        $modulePathExtractor = new ModulePathExtractor();
        $this->assertEquals(
            '[Analytics]',
            $modulePathExtractor->extractModuleName(
                "app/code/Magento/[Analytics]/Test/Mftf/Test/SomeText.xml"
            )
        );
    }

    /**
     * Validate Magento is returned for dev/tests/acceptance
     * @throws \Exception
     */
    public function testMagentoModulePath()
    {
        $modulePathExtractor = new ModulePathExtractor();
        $this->assertEquals(
            'Magento',
            $modulePathExtractor->getExtensionPath(
                "dev/tests/acceptance/tests/functional/Magento/FunctionalTest/[Analytics]/Test/SomeText.xml"
            )
        );
    }

    /**
     * Validate correct extension path is returned
     * @throws \Exception
     */
    public function testExtensionModulePath()
    {
        $modulePathExtractor = new ModulePathExtractor();
        $this->assertEquals(
            'TestExtension',
            $modulePathExtractor->getExtensionPath(
                "app/code/TestExtension/[Analytics]/Test/Mftf/Test/SomeText.xml"
            )
        );
    }
}
