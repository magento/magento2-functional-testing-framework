<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AcceptanceTestFramework\Page\Objects;

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
    private $locator;

    /**
     * Section element timeout
     *
     * @var string $timeout
     */
    private $timeout;

    /**
     * ElementObject constructor.
     * @param string $name
     * @param string $type
     * @param string $locator
     * @param string $timeout
     */
    public function __construct($name, $type, $locator, $timeout)
    {
        $this->name = $name;
        $this->type = $type;
        $this->locator = $locator;
        $this->timeout = $timeout;
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
     * Getter for the locator of an element
     *
     * @return string
     */
    public function getLocator()
    {
        return $this->locator;
    }

    /**
     * Returns an integer representing an element's timeout
     *
     * @return int|null
     */
    public function getTimeout()
    {
        if ($this->timeout == ElementObject::DEFAULT_TIMEOUT_SYMBOL) {
            return null;
        }

        return (int)$this->timeout;
    }
}
