<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\TestFramework\ObjectManager\ConfigLoader;

/**
 * Class Primary
 * Primary DI configuration loader
 *
 * @internal
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Primary
{
    /**
     * Framework mode
     *
     * @var string
     */
    protected $_appMode = 'developer';

    /**
     * Load primary DI configuration
     *
     * @return array
     */
    public function load()
    {
        $reader = new \Magento\TestFramework\ObjectManager\Config\Reader\Dom(
            new \Magento\TestFramework\Config\FileResolver\Primary(),
            new \Magento\TestFramework\ObjectManager\Config\Mapper\Dom(
                $this->createArgumentInterpreter()
            ),
            new \Magento\TestFramework\ObjectManager\Config\SchemaLocator(),
            new \Magento\TestFramework\Config\ValidationState($this->_appMode)
        );

        return $reader->read();
    }


    /**
     * Return newly created instance on an argument interpreter, suitable for processing DI arguments
     *
     * @return \Magento\TestFramework\Data\Argument\InterpreterInterface
     */
    protected function createArgumentInterpreter()
    {
        $booleanUtils = new \Magento\TestFramework\Stdlib\BooleanUtils();
        $constInterpreter = new \Magento\TestFramework\Data\Argument\Interpreter\Constant();
        $result = new \Magento\TestFramework\Data\Argument\Interpreter\Composite(
            [
                'boolean' => new \Magento\TestFramework\Data\Argument\Interpreter\Boolean($booleanUtils),
                'string' => new \Magento\TestFramework\Data\Argument\Interpreter\StringUtils($booleanUtils),
                'number' => new \Magento\TestFramework\Data\Argument\Interpreter\Number(),
                'null' => new \Magento\TestFramework\Data\Argument\Interpreter\NullType(),
                'object' => new \Magento\TestFramework\Data\Argument\Interpreter\DataObject($booleanUtils),
                'const' => $constInterpreter,
                'init_parameter' => new \Magento\TestFramework\Data\Argument\Interpreter\Argument($constInterpreter)
            ],
            \Magento\TestFramework\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE
        );
        // Add interpreters that reference the composite
        $result->addInterpreter('array', new \Magento\TestFramework\Data\Argument\Interpreter\ArrayType($result));
        return $result;
    }
}
