<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Page\Objects;

use Magento\FunctionalTestingFramework\Exceptions\XmlException;

/**
 * Class ElementObject
 */
class ElementObject
{
    const DEFAULT_TIMEOUT_SYMBOL = '-';

    /**
     * Section element name
     *
     * @var string
     */
    private $name;

    /**
     * Section element type
     *
     * @var string
     */
    private $type;

    /**
     * Section element locator
     *
     * @var string
     */
    private $selector;

    /**
     * Section element locatorFunction
     *
     * @var string
     */
    private $locatorFunction;

    /**
     * Section element timeout
     *
     * @var string $timeout
     */
    private $timeout;

    /**
     * Section element locator is parameterized
     *
     * @var bool $parameterized
     */
    private $parameterized;

    /**
     * ElementObject constructor.
     * @param string  $name
     * @param string  $type
     * @param string  $selector
     * @param string  $locatorFunction
     * @param string  $timeout
     * @param boolean $parameterized
     * @throws XmlException
     */
    public function __construct($name, $type, $selector, $locatorFunction, $timeout, $parameterized)
    {
        if ($selector != null && $locatorFunction != null) {
            throw new XmlException("Element '{$name}' cannot have both a selector and a locatorFunction.");
        } elseif ($selector == null && $locatorFunction == null) {
            throw new XmlException("Element '{$name}' must have either a selector or a locatorFunction.'");
        }

        $this->name = $name;
        $this->type = $type;
        $this->selector = $selector;
        $this->locatorFunction = $locatorFunction;
        if (strpos($locatorFunction, "Locator::") === false) {
            $this->locatorFunction = "Locator::" . $locatorFunction;
        }
        $this->timeout = $timeout;
        $this->parameterized = $parameterized;
    }

    /**
     * Getter for the name of the element
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Getter for the name of the element type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Getter for the selector of an element
     *
     * @return string
     */
    public function getSelector()
    {
        return $this->selector;
    }

    /**
     * Getter for the locatorFunction of an element
     *
     * @return string
     */
    public function getLocatorFunction()
    {
        return $this->locatorFunction;
    }

    /**
     * Returns selector if not null, otherwise returns locatorFunction
     *
     * @return string
     */
    public function getPrioritizedSelector()
    {
        return $this->selector ?: $this->locatorFunction;
    }

    /**
     * Returns an integer representing an element's timeout
     *
     * @return integer|null
     */
    public function getTimeout()
    {
        if ($this->timeout == ElementObject::DEFAULT_TIMEOUT_SYMBOL) {
            return null;
        }

        return (int)$this->timeout;
    }

    /**
     * Determines if the element's selector is parameterized. Based on $parameterized property.
     *
     * @return boolean
     */
    public function isParameterized()
    {
        return $this->parameterized;
    }
}
