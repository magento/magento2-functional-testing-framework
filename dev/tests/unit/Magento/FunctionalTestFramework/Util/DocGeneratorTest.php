<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Tests\unit\Magento\FunctionalTestFramework\Test\Handlers;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;
use Magento\FunctionalTestingFramework\Util\DocGenerator;
use tests\unit\Util\ActionGroupObjectBuilder;

class DocGeneratorTest extends MagentoTestCase
{
    const DOC_FILENAME = "documentation";

    /**
     * Basic test to check creation of documentation
     *
     * @throws \Exception
     */
    public function testBasicCreateDocumentation()
    {
        $annotations = [
            "description" => "someDescription"
        ];
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withAnnotations($annotations)
            ->withFilename("filename")
            ->build();
        $docGenerator = new DocGenerator();
        $docGenerator->createDocumentation(
            [$actionGroupUnderTest->getName() => $actionGroupUnderTest],
            DOCS_OUTPUT_DIR,
            true
        );

        $docFile = DOCS_OUTPUT_DIR . DIRECTORY_SEPARATOR . self::DOC_FILENAME . ".md";

        $this->assertTrue(file_exists($docFile));

        $this->assertFileEquals(
            RESOURCE_DIR . DIRECTORY_SEPARATOR . "basicDocumentation.txt",
            $docFile
        );
    }

    /**
     * Test to check creation of documentation when overwriting previous
     *
     * @throws \Exception
     */
    public function testCreateDocumentationWithOverwrite()
    {
        $annotations = [
            "description" => "someDescription"
        ];
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withAnnotations($annotations)
            ->withFilename("filename")
            ->build();
        $docGenerator = new DocGenerator();
        $docGenerator->createDocumentation(
            [$actionGroupUnderTest->getName() => $actionGroupUnderTest],
            DOCS_OUTPUT_DIR,
            true
        );

        $annotations = [
            "description" => "alteredDescription"
        ];
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withAnnotations($annotations)
            ->withFilename("filename")
            ->build();
        $docGenerator = new DocGenerator();
        $docGenerator->createDocumentation(
            [$actionGroupUnderTest->getName() => $actionGroupUnderTest],
            DOCS_OUTPUT_DIR,
            true
        );

        $docFile = DOCS_OUTPUT_DIR . DIRECTORY_SEPARATOR . self::DOC_FILENAME . ".md";

        $this->assertTrue(file_exists($docFile));

        $this->assertFileEquals(
            RESOURCE_DIR . DIRECTORY_SEPARATOR . "alteredDocumentation.txt",
            $docFile
        );
    }

    /**
     * Test for existing documentation without clean
     *
     * @throws \Exception
     */
    public function testCreateDocumentationNotCleanException()
    {
        $annotations = [
            "description" => "someDescription"
        ];
        $actionGroupUnderTest = (new ActionGroupObjectBuilder())
            ->withAnnotations($annotations)
            ->withFilename("filename")
            ->build();
        $docGenerator = new DocGenerator();
        $docGenerator->createDocumentation(
            [$actionGroupUnderTest->getName() => $actionGroupUnderTest],
            DOCS_OUTPUT_DIR,
            true
        );

        $docFile = DOCS_OUTPUT_DIR . DIRECTORY_SEPARATOR . self::DOC_FILENAME . ".md";

        $this->expectException(TestFrameworkException::class);
        $this->expectExceptionMessage("$docFile already exists, please add --clean if you want to overwrite it.");

        $docGenerator = new DocGenerator();
        $docGenerator->createDocumentation(
            [$actionGroupUnderTest->getName() => $actionGroupUnderTest],
            DOCS_OUTPUT_DIR,
            false
        );
    }
}
