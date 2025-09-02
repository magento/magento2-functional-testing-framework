<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\Config;

/**
 * Config replacer interface.
 */
interface ReplacerInterface
{
    /**
     * Apply specified node in 'replace' attribute instead of original.
     *
     * @param array $output
     * @return array
     */
    public function apply(array &$output);
}
