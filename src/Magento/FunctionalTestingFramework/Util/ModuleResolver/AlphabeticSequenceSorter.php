<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\ModuleResolver;

/**
 * Alphabetic sequence sorter.
 */
class AlphabeticSequenceSorter implements SequenceSorterInterface
{
    /**
     * Sort files alphabetically.
     *
     * @param array $paths
     * @return array
     */
    public function sort(array $paths)
    {
        asort($paths);
        return $paths;
    }
}
