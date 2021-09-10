<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\ObjectManager;

use Magento\FunctionalTestingFramework\System\Code\ClassReader;

/**
 * Class Factory
 *
 * @internal
 */
class Factory extends \Magento\FunctionalTestingFramework\ObjectManager\Factory\Dynamic\Developer
{
    /**
     * Class reader.
     *
     * @var \Magento\FunctionalTestingFramework\System\Code\ClassReader
     */
    protected $classReader;

    /**
     * Factory constructor.
     * @param ConfigInterface                                                 $config
     * @param \Magento\FunctionalTestingFramework\ObjectManagerInterface|null $objectManager
     * @param DefinitionInterface|null                                        $definitions
     * @param array                                                           $globalArguments
     */
    public function __construct(
        ConfigInterface $config,
        \Magento\FunctionalTestingFramework\ObjectManagerInterface $objectManager = null,
        DefinitionInterface $definitions = null,
        $globalArguments = []
    ) {
        parent::__construct($config, $objectManager, $definitions, $globalArguments);
        $this->classReader = new ClassReader();
    }

    // @codingStandardsIgnoreStart
    /**
     * Invoke class method and prepared arguments
     *
     * @param mixed $object
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function invoke($object, $method, array $args = [])
    {
        $args = $this->prepareArguments($object, $method, $args);

        $type = get_class($object);
        $class = new \ReflectionClass($type);
        $method = $class->getMethod($method);

        return $method->invokeArgs($object, $args);
    }
    // @codingStandardsIgnoreEnd

    /**
     * Get list of parameters for class method
     *
     * @param string $type
     * @param string $method
     * @return array|null
     */
    public function getParameters($type, $method)
    {
        return $this->classReader->getParameters($type, $method);
    }

    /**
     * Resolve and prepare arguments for class method
     *
     * @param object $object
     * @param string $method
     * @param array  $arguments
     * @return array
     */
    public function prepareArguments($object, $method, array $arguments = [])
    {
        $type = get_class($object);
        $parameters = $this->classReader->getParameters($type, $method);

        if ($parameters === null) {
            return [];
        }

        return $this->resolveArguments($type, $parameters, $arguments);
    }

    /**
     * Resolve constructor arguments
     *
     * @param string $requestedType
     * @param array  $parameters
     * @param array  $arguments
     * @return array
     * @throws \UnexpectedValueException
     * @throws \BadMethodCallException
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * Revisited to reduce cyclomatic complexity, left unrefactored for readability
     */
    protected function resolveArguments($requestedType, array $parameters, array $arguments = [])
    {
        $resolvedArguments = [];
        $arguments = count($arguments)
            ? array_replace($this->config->getArguments($requestedType), $arguments)
            : $this->config->getArguments($requestedType);
        foreach ($parameters as $parameter) {
            list($paramName, $paramType, $paramRequired, $paramDefault) = $parameter;
            $argument = null;
            if (array_key_exists($paramName, $arguments)) {
                $argument = $arguments[$paramName];
            } elseif (array_key_exists('options', $arguments) && array_key_exists($paramName, $arguments['options'])) {
                // The parameter name doesn't exist in the arguments, but it is contained in the 'options' argument.
                $argument = $arguments['options'][$paramName];
            } else {
                if ($paramRequired) {
                    if ($paramType) {
                        $argument = ['instance' => $paramType];
                    } else {
                        $this->creationStack = [];
                        throw new \BadMethodCallException(
                            'Missing required argument $' . $paramName . ' of ' . $requestedType . '.'
                        );
                    }
                } else {
                    $argument = $paramDefault;
                }
            }
            if ($paramType && !is_object($argument) && $argument !== $paramDefault) {
                if (!is_array($argument)) {
                    throw new \UnexpectedValueException(
                        'Invalid parameter configuration provided for $' . $paramName . ' argument of ' . $requestedType
                    );
                }
                if (isset($argument['instance']) && !empty($argument['instance'])) {
                    $argumentType = $argument['instance'];
                    unset($argument['instance']);
                    if (array_key_exists('shared', $argument)) {
                        $isShared = $argument['shared'];
                        unset($argument['shared']);
                    } else {
                        $isShared = $this->config->isShared($argumentType);
                    }
                } else {
                    $argumentType = $paramType;
                    $isShared = $this->config->isShared($argumentType);
                }

                $_arguments = !empty($argument) ? $argument : [];

                $argument = $isShared
                    ? $this->objectManager->get($argumentType)
                    : $this->objectManager->create($argumentType, $_arguments);
            } else {
                if (is_array($argument)) {
                    if (isset($argument['argument'])) {
                        $argKey = $argument['argument'];
                        $argument = isset($this->globalArguments[$argKey])
                            ? $this->globalArguments[$argKey]
                            : $paramDefault;
                    } else {
                        $this->parseArray($argument);
                    }
                }
            }
            $resolvedArguments[$paramName] = $argument;
        }
        return $resolvedArguments;
    }

    /**
     * Parse array argument
     *
     * @param array $array
     * @return void
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * Revisited to reduce cyclomatic complexity, left unrefactored for readability
     */
    protected function parseArray(&$array)
    {
        foreach ($array as $key => $item) {
            if (!is_array($item)) {
                continue;
            }
            if (isset($item['instance'])) {
                $itemType = $item['instance'];
                $isShared = isset($item['shared']) ? $item['shared'] : $this->config->isShared($itemType);

                unset($item['instance']);
                if (array_key_exists('shared', $item)) {
                    unset($item['shared']);
                }

                $_arguments = !empty($item) ? $item : [];

                $array[$key] = $isShared
                    ? $this->objectManager->get($itemType)
                    : $this->objectManager->create($itemType, $_arguments);
            } elseif (isset($item['argument'])) {
                $array[$key] = isset($this->globalArguments[$item['argument']])
                    ? $this->globalArguments[$item['argument']]
                    : null;
            } else {
                $this->parseArray($item);
            }
        }
    }

    /**
     * Create instance with call time arguments
     *
     * @param string $requestedType
     * @param array  $arguments
     * @return object
     * @throws \Exception
     */
    public function create($requestedType, array $arguments = [])
    {
        $instanceType = $this->config->getInstanceType($requestedType);
        $parameters = $this->definitions->getParameters($instanceType);

        if ($parameters === null) {
            return new $instanceType();
        }
        if (isset($this->creationStack[$requestedType])) {
            $lastFound = end($this->creationStack);
            $this->creationStack = [];
            throw new \LogicException("Circular dependency: {$requestedType} depends on {$lastFound} and vice versa.");
        }
        $this->creationStack[$requestedType] = $requestedType;
        try {
            $args = $this->resolveArguments($requestedType, $parameters, $arguments);
            unset($this->creationStack[$requestedType]);
        } catch (\Exception $e) {
            unset($this->creationStack[$requestedType]);
            throw $e;
        }

        $reflection = new \ReflectionClass($instanceType);

        return $reflection->newInstanceArgs($args);
    }
}
