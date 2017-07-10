<?php
namespace Magento\AcceptanceTestFramework\Generate;

/**
 * List of generators
 */
class Pool
{
    /**
     * List of generators
     *
     * @var array
     */
    private $generatorPool = [];

    /**
     * @param array $pool
     */
    public function __construct(array $pool)
    {
        $this->generatorPool = $pool;
    }

    /**
     * Retrieve generator pool
     *
     * @return array
     */
    public function getGenerators()
    {
        return $this->generatorPool;
    }
}
