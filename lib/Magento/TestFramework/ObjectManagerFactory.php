<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\TestFramework;

use Magento\TestFramework\ObjectManager\Factory;
use Magento\TestFramework\Stdlib\BooleanUtils;
use Magento\TestFramework\ObjectManager as MagentoObjectManager;

/**
 * Object Manager Factory.
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ObjectManagerFactory
{
    /**
     * Object Manager class name.
     *
     * @var string
     */
    protected $locatorClassName = '\Magento\TestFramework\ObjectManager';

    /**
     * DI Config class name.
     *
     * @var string
     */
    protected $configClassName = '\Magento\TestFramework\ObjectManager\Config';

    /**
     * Create Object Manager.
     *
     * @param array $sharedInstances
     * @return ObjectManager
     */
    public function create(array $sharedInstances = [])
    {
        /** @var \Magento\TestFramework\ObjectManager\Config $diConfig */
        $diConfig = new $this->configClassName();

        $factory = new Factory($diConfig);
        $argInterpreter = $this->createArgumentInterpreter(new BooleanUtils());
        $argumentMapper = new \Magento\TestFramework\ObjectManager\Config\Mapper\Dom($argInterpreter);


        $sharedInstances['Magento\TestFramework\Data\Argument\InterpreterInterface'] = $argInterpreter;
        $sharedInstances['Magento\TestFramework\ObjectManager\Config\Mapper\Dom'] = $argumentMapper;

        /** @var \Magento\TestFramework\ObjectManager $objectManager */
        $objectManager = new $this->locatorClassName($factory, $diConfig, $sharedInstances);

        $factory->setObjectManager($objectManager);
        ObjectManager::setInstance($objectManager);

        self::configure($objectManager);

        return $objectManager;
    }

    /**
     * Return newly created instance on an argument interpreter, suitable for processing DI arguments.
     *
     * @param \Magento\TestFramework\Stdlib\BooleanUtils $booleanUtils
     * @return \Magento\TestFramework\Data\Argument\InterpreterInterface
     */
    protected function createArgumentInterpreter(
        \Magento\TestFramework\Stdlib\BooleanUtils $booleanUtils
    ) {
        $constInterpreter = new \Magento\TestFramework\Data\Argument\Interpreter\Constant();
        $result = new \Magento\TestFramework\Data\Argument\Interpreter\Composite(
            [
                'boolean' => new \Magento\TestFramework\Data\Argument\Interpreter\Boolean($booleanUtils),
                'string' => new \Magento\TestFramework\Data\Argument\Interpreter\StringType($booleanUtils),
                'number' => new \Magento\TestFramework\Data\Argument\Interpreter\Number(),
                'null' => new \Magento\TestFramework\Data\Argument\Interpreter\NullType(),
                'const' => $constInterpreter,
                'object' => new \Magento\TestFramework\Data\Argument\Interpreter\ObjectType($booleanUtils),
                'init_parameter' => new \Magento\TestFramework\Data\Argument\Interpreter\Argument($constInterpreter),
            ],
            \Magento\TestFramework\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE
        );
        // Add interpreters that reference the composite
        $result->addInterpreter('array', new \Magento\TestFramework\Data\Argument\Interpreter\ArrayType($result));
        return $result;
    }

    /**
     * Get Object Manager instance.
     *
     * @return ObjectManager
     */
    public static function getObjectManager()
    {
        if (!$objectManager = ObjectManager::getInstance()) {
            $objectManagerFactory = new self();
            $objectManager = $objectManagerFactory->create();
        }

        return $objectManager;
    }

    /**
     * Configure Object Manager.
     * This method is static to have the ability to configure multiple instances of Object manager when needed.
     *
     * @param \Magento\TestFramework\ObjectManagerInterface $objectManager
     * @return void
     */
    public static function configure(\Magento\TestFramework\ObjectManagerInterface $objectManager)
    {
        $objectManager->configure(
            $objectManager->get('Magento\TestFramework\ObjectManager\ConfigLoader\Primary')->load()
        );
    }
}
