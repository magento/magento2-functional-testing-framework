<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Filter\Test;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Filter\FilterInterface;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;
use Magento\FunctionalTestingFramework\Test\Util\AnnotationExtractor;

/**
 * Class Severity
 */
class Severity implements FilterInterface
{
    const ANNOTATION_TAG = 'severity';

    /**
     * @var array
     */
    private $filterValues = [];

    /**
     * Severity constructor.
     *
     * @param array $filterValues
     * @throws TestFrameworkException
     */
    public function __construct(array $filterValues = [])
    {
        $severityValues = AnnotationExtractor::MAGENTO_TO_ALLURE_SEVERITY_MAP;

        foreach ($filterValues as $filterValue) {
            if (!isset($severityValues[$filterValue])) {
                throw new TestFrameworkException(
                    'Not existing severity specified.' . PHP_EOL
                    . 'Possible values: '. implode(', ', array_keys($severityValues)) . '.' . PHP_EOL
                    . 'Provided values: ' . implode(', ', $filterValues) . '.'  . PHP_EOL
                );
            }
            $this->filterValues[] = $severityValues[$filterValue];
        }
    }

    /**
     * Filter tests by severity.
     *
     * @param TestObject[] $tests
     * @return void
     */
    public function filter(array &$tests)
    {
        /** @var TestObject $test */
        foreach ($tests as $testName => $test) {
            $severities = $test->getAnnotationByName(self::ANNOTATION_TAG);
            foreach ($severities as $severity) {
                if (!in_array($severity, $this->filterValues, true)) {
                    unset($tests[$testName]);
                }
            }
        }
    }
}
