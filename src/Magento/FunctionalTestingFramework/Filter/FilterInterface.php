<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Filter;

/**
 * Interface for future test filters
 * @api
 */
interface FilterInterface
{
    /**
     * @param array $filterValues
     */
    public function __construct(array $filterValues = []);

    /**
     * @param array $tests
     * @return void
     */
    public function filter(array &$tests);
}
