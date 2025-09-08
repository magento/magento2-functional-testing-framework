<?php
/**
 * Copyright 2021 Adobe
 * All Rights Reserved.
 */

declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Filter\Test;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Filter\FilterInterface;
use Magento\FunctionalTestingFramework\Test\Objects\TestObject;

/**
 * Class ExcludeGroup
 */
class ExcludeGroup implements FilterInterface
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
            $testExcludeGroup = !empty(array_intersect($groups, $this->filterValues));
            if ($testExcludeGroup) {
                unset($tests[$testName]);
            }
        }
    }
}
