<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\ObjectManager\ConfigLoader;

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
        $reader = new \Magento\FunctionalTestingFramework\ObjectManager\Config\Reader\Dom(
            new \Magento\FunctionalTestingFramework\Config\FileResolver\Primary(),
            new \Magento\FunctionalTestingFramework\ObjectManager\Config\Mapper\Dom(
                $this->createArgumentInterpreter()
            ),
            new \Magento\FunctionalTestingFramework\ObjectManager\Config\SchemaLocator(),
            new \Magento\FunctionalTestingFramework\Config\ValidationState($this->appMode)
        );

        return $reader->read();
    }


    /**
     * Return newly created instance on an argument interpreter, suitable for processing DI arguments
     *
     * @return \Magento\FunctionalTestingFramework\Data\Argument\InterpreterInterface
     */
    protected function createArgumentInterpreter()
    {
        $booleanUtils = new \Magento\FunctionalTestingFramework\Stdlib\BooleanUtils();
        $constInterpreter = new \Magento\FunctionalTestingFramework\Data\Argument\Interpreter\Constant();
        $result = new \Magento\FunctionalTestingFramework\Data\Argument\Interpreter\Composite(
            [
                'boolean' => new \Magento\FunctionalTestingFramework\Data\Argument\Interpreter\Boolean($booleanUtils),
                'string' => new \Magento\FunctionalTestingFramework\Data\Argument\Interpreter\StringUtils($booleanUtils),
                'number' => new \Magento\FunctionalTestingFramework\Data\Argument\Interpreter\Number(),
                'null' => new \Magento\FunctionalTestingFramework\Data\Argument\Interpreter\NullType(),
                'object' => new \Magento\FunctionalTestingFramework\Data\Argument\Interpreter\DataObject($booleanUtils),
                'const' => $constInterpreter,
                'init_parameter' => new \Magento\FunctionalTestingFramework\Data\Argument\Interpreter\Argument($constInterpreter)
            ],
            \Magento\FunctionalTestingFramework\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE
        );
        // Add interpreters that reference the composite
        $result->addInterpreter('array', new \Magento\FunctionalTestingFramework\Data\Argument\Interpreter\ArrayType($result));
        return $result;
    }
}
