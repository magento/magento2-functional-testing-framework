<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Magento\FunctionalTestFramework\Test\Util;

use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Util\Validation\NameValidationUtil;
use Magento\FunctionalTestingFramework\Util\MagentoTestCase;

class NameValidationUtilTest extends MagentoTestCase
{
    /**
     * Validate name with curly braces throws exception
     */
    public function testCurlyBracesInTestName()
    {
        $this->validateBlacklistedTestName("{{curlyBraces}}");
    }

    /**
     * Validate name with quotation marks throws exception
     */
    public function testQuotesInTestName()
    {
        $this->validateBlacklistedTestName("\"quotes\"");
    }

    /**
     * Validate name with single quotes throws exception
     */
    public function testSingleQuotesInTestName()
    {
        $this->validateBlacklistedTestName("'singleQuotes'");
    }

    /**
     * Validate name with parenthesis throws execption
     */
    public function testParenthesesInTestName()
    {
        $this->validateBlacklistedTestName("(parenthesis)");
    }

    /**
     * Validate name with dollar signs throws exception
     */
    public function testDollarSignInTestName()
    {
        $this->validateBlacklistedTestName("\$dollarSign\$");
    }

    /**
     * Validate name with spaces throws exception
     */
    public function testSpacesInTestName()
    {
        $this->validateBlacklistedTestName("Test Name With Spaces");
    }

    /**
     * Method which takes the name of the test expecting an invalid char. Runs the validation method against name.
     *
     * @param string $testName
     * @return void
     */
    private function validateBlacklistedTestName($testName)
    {
        $this->expectException(XmlException::class);
        NameValidationUtil::validateName($testName, "Test");
    }
}
