<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Helper;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

/**
 * Class HelperContainer
 */
class HelperContainer
{
    /**
     * @var Helper[]
     */
    private $helpers = [];

    /**
     * HelperContainer constructor.
     * @param array $helpers
     */
    public function __construct(array $helpers = [])
    {
        $this->helpers = $helpers;
    }

    /**
     * Returns helper object by it's class name.
     *
     * @param string $className
     * @return Helper
     * @throws TestFrameworkException
     */
    public function get(string $className)
    {
        if ($this->has($className)) {
            return $this->helpers[$className];
        }
        throw new TestFrameworkException('Custom helper ' . $className . 'not found.');
    }

    /**
     * Verifies that helper object exist.
     *
     * @param string $className
     * @return boolean
     */
    public function has(string $className)
    {
        return array_key_exists($className, $this->helpers);
    }
}
