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

/**
 * Class Severity
 */
class Severity implements FilterInterface
{
    const ANNOTATION_TAG = 'severity';
    const SEVERITY_VALUES = [
        "BLOCKER",
        "CRITICAL",
        "MAJOR",
        "AVERAGE",
        "MINOR",
    ];

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
        if (array_diff($filterValues, self::SEVERITY_VALUES) ==! []) {
            throw new TestFrameworkException(
                'Not existing severity specified.' . PHP_EOL
                . 'Possible values: '. implode(', ', self::SEVERITY_VALUES) . '.' . PHP_EOL
                . 'Provided values: ' . implode(', ', $filterValues) . '.'  . PHP_EOL
            );
        }
        $this->filterValues = $filterValues;
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
