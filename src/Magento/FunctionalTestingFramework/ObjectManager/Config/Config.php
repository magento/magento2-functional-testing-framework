<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\ObjectManager\Config;

use Magento\FunctionalTestingFramework\ObjectManager\DefinitionInterface;
use Magento\FunctionalTestingFramework\ObjectManager\RelationsInterface;
use Magento\FunctionalTestingFramework\ObjectManager\Definition\Runtime as DefinitionRuntime;
use Magento\FunctionalTestingFramework\ObjectManager\Relations\Runtime as RelationsRuntime;

/**
 * Class Config
 */
class Config implements \Magento\FunctionalTestingFramework\ObjectManager\ConfigInterface
{
    /**
     * Class definitions
     *
     * @var \Magento\FunctionalTestingFramework\ObjectManager\DefinitionInterface
     */
    protected $definitions;

    /**
     * Current cache key
     *
     * @var string
     */
    protected $currentCacheKey;

    /**
     * Interface preferences
     *
     * @var array
     */
    protected $preferences = [];

    /**
     * Virtual types
     *
     * @var array
     */
    protected $virtualTypes = [];

    /**
     * Instance arguments
     *
     * @var array
     */
    protected $arguments = [];

    /**
     * Type shareability
     *
     * @var array
     */
    protected $nonShared = [];

    /**
     * List of relations
     *
     * @var RelationsInterface
     */
    protected $relations;

    /**
     * List of merged arguments
     *
     * @var array
     */
    protected $mergedArguments;

    /**
     * Config constructor.
     * @param RelationsInterface|null  $relations
     * @param DefinitionInterface|null $definitions
     */
    public function __construct(RelationsInterface $relations = null, DefinitionInterface $definitions = null)
    {
        $this->relations = $relations ? : new RelationsRuntime();
        $this->definitions = $definitions ? : new DefinitionRuntime();
    }

    /**
     * Retrieve list of arguments per type
     *
     * @param string $type
     * @return array
     */
    public function getArguments($type)
    {
        return isset($this->mergedArguments[$type])
            ? $this->mergedArguments[$type]
            : $this->collectConfiguration($type);
    }

    /**
     * Check whether type is shared
     *
     * @param string $type
     * @return boolean
     */
    public function isShared($type)
    {
        return !isset($this->nonShared[$type]);
    }

    /**
     * Retrieve instance type
     *
     * @param string $instanceName
     * @return string
     */
    public function getInstanceType($instanceName)
    {
        while (isset($this->virtualTypes[$instanceName])) {
            $instanceName = $this->virtualTypes[$instanceName];
        }
        return $instanceName;
    }

    /**
     * Retrieve preference for type
     *
     * @param string $type
     * @return string
     * @throws \LogicException
     */
    public function getPreference($type)
    {
        $type = ltrim($type, '\\');
        $preferencePath = [];
        while (isset($this->preferences[$type])) {
            if (isset($preferencePath[$this->preferences[$type]])) {
                throw new \LogicException(
                    'Circular type preference: ' .
                    $type .
                    ' relates to ' .
                    $this->preferences[$type] .
                    ' and viceversa.'
                );
            }
            $type = $this->preferences[$type];
            $preferencePath[$type] = 1;
        }
        return $type;
    }

    /**
     * Collect parent types configuration for requested type
     *
     * @param string $type
     * @return array
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function collectConfiguration($type)
    {
        if (!isset($this->mergedArguments[$type])) {
            if (isset($this->virtualTypes[$type])) {
                $arguments = $this->collectConfiguration($this->virtualTypes[$type]);
            } else {
                if ($this->relations->has($type)) {
                    $relations = $this->relations->getParents($type);
                    $arguments = [];
                    foreach ($relations as $relation) {
                        if ($relation) {
                            $relationArguments = $this->collectConfiguration($relation);
                            if ($relationArguments) {
                                $arguments = array_replace($arguments, $relationArguments);
                            }
                        }
                    }
                } else {
                    $arguments = [];
                }
            }

            if (isset($this->arguments[$type])) {
                if ($arguments && count($arguments)) {
                    $arguments = array_replace_recursive($arguments, $this->arguments[$type]);
                } else {
                    $arguments = $this->arguments[$type];
                }
            }
            $this->mergedArguments[$type] = $arguments;
            return $arguments;
        }
        return $this->mergedArguments[$type];
    }

    /**
     * Merge configuration
     *
     * @param array $configuration
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function mergeConfiguration(array $configuration)
    {
        foreach ($configuration as $key => $curConfig) {
            switch ($key) {
                case 'preferences':
                    foreach ($curConfig as $for => $to) {
                        $this->preferences[ltrim($for, '\\')] = ltrim($to, '\\');
                    }
                    break;

                default:
                    $key = ltrim($key, '\\');
                    if (isset($curConfig['type'])) {
                        $this->virtualTypes[$key] = ltrim($curConfig['type'], '\\');
                    }
                    if (isset($curConfig['arguments'])) {
                        if (!empty($this->mergedArguments)) {
                            $this->mergedArguments = [];
                        }
                        if (isset($this->arguments[$key])) {
                            $this->arguments[$key] = array_replace($this->arguments[$key], $curConfig['arguments']);
                        } else {
                            $this->arguments[$key] = $curConfig['arguments'];
                        }
                    }
                    if (isset($curConfig['shared'])) {
                        if (!$curConfig['shared']) {
                            $this->nonShared[$key] = 1;
                        } else {
                            unset($this->nonShared[$key]);
                        }
                    }
                    break;
            }
        }
    }

    /**
     * Extend configuration
     *
     * @param array $configuration
     * @return void
     */
    public function extend(array $configuration)
    {
        $this->mergeConfiguration($configuration);
    }
}
