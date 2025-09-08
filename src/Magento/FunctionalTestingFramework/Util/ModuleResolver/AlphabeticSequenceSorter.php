<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
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
