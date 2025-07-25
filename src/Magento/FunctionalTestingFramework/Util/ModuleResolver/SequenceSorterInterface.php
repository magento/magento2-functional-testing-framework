<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\Util\ModuleResolver;

/**
 * Sequence sorter interface.
 */
interface SequenceSorterInterface
{
    /**
     * Sort files according to specified sequence.
     *
     * @param array $paths
     * @return array
     */
    public function sort(array $paths);
}
