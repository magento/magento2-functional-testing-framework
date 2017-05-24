<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework\Code\Generator;

use Magento\TestFramework\Code\Generator;

/**
 * Class Autoloader
 */
class Autoloader
{
    /**
     * @var Generator
     */
    protected $generator;

    /**
     * @param Generator $generator
     */
    public function __construct(Generator $generator)
    {
        $this->generator = $generator;
    }

    /**
     * Load specified class name and generate it if necessary
     *
     * @param string $className
     * @return bool True if class was loaded
     */
    public function load($className)
    {
        if (!class_exists($className)) {
            return Generator::GENERATION_SUCCESS == $this->generator->generateClass($className);
        }
        return true;
    }
}
