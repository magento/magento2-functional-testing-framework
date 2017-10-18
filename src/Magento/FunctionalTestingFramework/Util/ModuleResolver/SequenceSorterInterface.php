<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
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
