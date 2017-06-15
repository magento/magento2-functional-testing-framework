<?php
namespace Magento\AcceptanceTestFramework\Util\ModuleResolver;

/**
 * Sequence sorter interface.
 */
interface SequenceSorterInterface
{
    /**
     * Sort files according to specified sequence.
     *
     * @param array $paths
     * @return mixed
     */
    public function sort(array $paths);
}
