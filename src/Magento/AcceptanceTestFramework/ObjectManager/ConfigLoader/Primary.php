<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\ObjectManager\ConfigLoader;

/**
 * Class Primary
 * Primary DI configuration loader
 *
 * @internal
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
// @codingStandardsIgnoreFile
class Primary
{
    /**
     * Framework mode
     *
     * @var string
     */
    protected $appMode = 'developer';

    /**
     * Load primary DI configuration
     *
     * @return array
     */
    public function load()
    {
        $reader = new \Magento\AcceptanceTestFramework\ObjectManager\Config\Reader\Dom(
            new \Magento\AcceptanceTestFramework\Config\FileResolver\Primary(),
            new \Magento\AcceptanceTestFramework\ObjectManager\Config\Mapper\Dom(
                $this->createArgumentInterpreter()
            ),
            new \Magento\AcceptanceTestFramework\ObjectManager\Config\SchemaLocator(),
            new \Magento\AcceptanceTestFramework\Config\ValidationState($this->appMode)
        );

        return $reader->read();
    }


    /**
     * Return newly created instance on an argument interpreter, suitable for processing DI arguments
     *
     * @return \Magento\AcceptanceTestFramework\Data\Argument\InterpreterInterface
     */
    protected function createArgumentInterpreter()
    {
        $booleanUtils = new \Magento\AcceptanceTestFramework\Stdlib\BooleanUtils();
        $constInterpreter = new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\Constant();
        $result = new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\Composite(
            [
                'boolean' => new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\Boolean($booleanUtils),
                'string' => new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\StringUtils($booleanUtils),
                'number' => new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\Number(),
                'null' => new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\NullType(),
                'object' => new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\DataObject($booleanUtils),
                'const' => $constInterpreter,
                'init_parameter' => new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\Argument($constInterpreter)
            ],
            \Magento\AcceptanceTestFramework\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE
        );
        // Add interpreters that reference the composite
        $result->addInterpreter('array', new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\ArrayType($result));
        return $result;
    }
}
