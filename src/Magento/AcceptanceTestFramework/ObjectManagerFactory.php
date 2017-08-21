<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AcceptanceTestFramework;

use Magento\AcceptanceTestFramework\ObjectManager\Factory;
use Magento\AcceptanceTestFramework\Stdlib\BooleanUtils;
use Magento\AcceptanceTestFramework\ObjectManager as MagentoObjectManager;

/**
 * Object Manager Factory.
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
// @codingStandardsIgnoreFile
class ObjectManagerFactory
{
    /**
     * Object Manager class name.
     *
     * @var string
     */
    protected $locatorClassName = '\Magento\AcceptanceTestFramework\ObjectManager';

    /**
     * DI Config class name.
     *
     * @var string
     */
    protected $configClassName = '\Magento\AcceptanceTestFramework\ObjectManager\Config';

    /**
     * Create Object Manager.
     *
     * @param array $sharedInstances
     * @return ObjectManager
     */
    public function create(array $sharedInstances = [])
    {
        /** @var \Magento\AcceptanceTestFramework\ObjectManager\Config $diConfig */
        $diConfig = new $this->configClassName();

        $factory = new Factory($diConfig);
        $argInterpreter = $this->createArgumentInterpreter(new BooleanUtils());
        $argumentMapper = new \Magento\AcceptanceTestFramework\ObjectManager\Config\Mapper\Dom($argInterpreter);


        $sharedInstances['Magento\AcceptanceTestFramework\Data\Argument\InterpreterInterface'] = $argInterpreter;
        $sharedInstances['Magento\AcceptanceTestFramework\ObjectManager\Config\Mapper\Dom'] = $argumentMapper;

        /** @var \Magento\AcceptanceTestFramework\ObjectManager $objectManager */
        $objectManager = new $this->locatorClassName($factory, $diConfig, $sharedInstances);

        $factory->setObjectManager($objectManager);
        ObjectManager::setInstance($objectManager);

        self::configure($objectManager);

        return $objectManager;
    }

    /**
     * Return newly created instance on an argument interpreter, suitable for processing DI arguments.
     *
     * @param \Magento\AcceptanceTestFramework\Stdlib\BooleanUtils $booleanUtils
     * @return \Magento\AcceptanceTestFramework\Data\Argument\InterpreterInterface
     */
    protected function createArgumentInterpreter(
        \Magento\AcceptanceTestFramework\Stdlib\BooleanUtils $booleanUtils
    ) {
        $constInterpreter = new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\Constant();
        $result = new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\Composite(
            [
                'boolean' => new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\Boolean($booleanUtils),
                'string' => new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\StringUtils($booleanUtils),
                'number' => new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\Number(),
                'null' => new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\NullType(),
                'const' => $constInterpreter,
                'object' => new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\DataObject($booleanUtils),
                'init_parameter' => new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\Argument($constInterpreter),
            ],
            \Magento\AcceptanceTestFramework\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE
        );
        // Add interpreters that reference the composite
        $result->addInterpreter('array', new \Magento\AcceptanceTestFramework\Data\Argument\Interpreter\ArrayType($result));
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
     * @param \Magento\AcceptanceTestFramework\ObjectManagerInterface $objectManager
     * @return void
     */
    public static function configure(\Magento\AcceptanceTestFramework\ObjectManagerInterface $objectManager)
    {
        $objectManager->configure(
            $objectManager->get(\Magento\AcceptanceTestFramework\ObjectManager\ConfigLoader\Primary::class)->load()
        );
    }
}
