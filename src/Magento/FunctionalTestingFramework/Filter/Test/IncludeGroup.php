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
 * Class IncludeGroup
 */
class IncludeGroup implements FilterInterface
{
    const ANNOTATION_TAG = 'group';

    /**
     * @var array
     */
    private $filterValues = [];

    /**
     * Group constructor.
     *
     * @param array $filterValues
     * @throws TestFrameworkException
     */
    public function __construct(array $filterValues = [])
    {
        $this->filterValues = $filterValues;
    }

    /**
     * Filter tests by group.
     *
     * @param TestObject[] $tests
     * @return void
     */
    public function filter(array &$tests)
    {
        if ($this->filterValues === []) {
            return;
        }
        /** @var TestObject $test */
        foreach ($tests as $testName => $test) {
            $groups = $test->getAnnotationByName(self::ANNOTATION_TAG);
            $testIncludeGroup = empty(array_intersect($groups, $this->filterValues));
            if ($testIncludeGroup) {
                unset($tests[$testName]);
            }
        }
    }
}
