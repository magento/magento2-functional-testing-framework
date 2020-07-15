<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Helper;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

/**
 * Class HelperContainer
 */
class HelperContainer extends \Codeception\Module
{
    /**
     * @var Helper[]
     */
    private $helpers = [];

    /**
     * Create custom helper class.
     *
     * @param string $helperClass
     * @return Helper
     * @throws \Exception
     */
    public function create(string $helperClass): Helper
    {
        if (get_parent_class($helperClass) !== Helper::class) {
            throw new \Exception("Helper class must extend " . Helper::class);
        }
        if (!isset($this->helpers[$helperClass])) {
            $this->helpers[$helperClass] = $this->moduleContainer->create($helperClass);
        }

        return $this->helpers[$helperClass];
    }

    /**
     * Returns helper object by it's class name.
     *
     * @param string $className
     * @return Helper
     * @throws TestFrameworkException
     */
    public function get(string $className): Helper
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
    public function has(string $className): bool
    {
        return array_key_exists($className, $this->helpers);
    }
}
