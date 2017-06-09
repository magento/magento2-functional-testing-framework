<?php
namespace Magento\AcceptanceTestFramework\Util\ModuleResolver;

/**
 * Module sequence sorter.
 */
class SequenceSorter implements SequenceSorterInterface
{
    /**
     * Sort files according to specified sequence.
     *
     * @param array $paths
     * @return mixed
     */
    public function sort(array $paths)
    {
        return $paths;
    }
}
