<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

namespace tests\verification\Tests;

use tests\util\MftfTestCase;

class SkippedGenerationTest extends MftfTestCase
{
    /**
     * Tests skipped test generation.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testSkippedGeneration()
    {
        $this->generateAndCompareTest('SkippedTest');
    }

    /**
     * Tests skipped test generation does not generate hooks.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testSkippedWithHooksGeneration()
    {
        $this->generateAndCompareTest('SkippedTestWithHooks');
    }

    /**
     * Tests skipped test with multiple issues generation.
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testMultipleSkippedIssuesGeneration()
    {
        $this->generateAndCompareTest('SkippedTestTwoIssues');
    }

    /**
     * Tests skipped test doesnt fail to generate when there is issue in test
     *
     * @throws \Exception
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     */
    public function testSkippedTestMustNotFailToGenerateWithErrorWhenThereIsIssueWithAnyOfTheStepsAsTheTestIsSkipped()
    {
        $this->generateAndCompareTest('SkippedTestWithIssueMustGetSkippedWithoutErrorExitCode');
    }
}
