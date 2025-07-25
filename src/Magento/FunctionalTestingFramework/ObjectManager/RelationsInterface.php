<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\ObjectManager;

/**
 * Interface RelationsInterface
 */
interface RelationsInterface
{
    /**
     * Check whether requested type is available for read
     *
     * @param string $type
     * @return boolean
     */
    public function has($type);

    /**
     * Retrieve list of parents
     *
     * @param string $type
     * @return array
     */
    public function getParents($type);
}
