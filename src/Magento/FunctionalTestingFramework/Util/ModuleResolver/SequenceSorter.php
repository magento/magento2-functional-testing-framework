<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\Util\ModuleResolver;

/**
 * Module sequence sorter.
 */
class SequenceSorter implements SequenceSorterInterface
{
    /**
     * Sort files according to specified sequence.
     *
     * @param array $paths
     * @return array
     */
    public function sort(array $paths)
    {
        return $paths;
    }
}
