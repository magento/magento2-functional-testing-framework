<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Page\Objects;

/**
 * Class SectionObject
 */
class SectionObject
{
    /**
     * Section name.
     *
     * @var string
     */
    private $name;

    /**
     * Section elements.
     *
     * @var array
     */
    private $elements = [];

    /**
     * Filename of where the section came from
     *
     * @var string
     */
    private $filename;

    /**
     * SectionObject constructor.
     * @param string $name
     * @param array  $elements
     * @param string $filename
     */
    public function __construct($name, $elements, $filename = null)
    {
        $this->name = $name;
        $this->elements = $elements;
        $this->filename = $filename;
    }

    /**
     * Getter for the name of the section
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Getter for the Section Filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }
    
    /**
     * Getter for an array containing all of a section's elements.
     *
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Checks to see if this section contains any element by the name of elementName
     * @param string $elementName
     * @return boolean
     */
    public function hasElement($elementName)
    {
        return array_key_exists($elementName, $this->elements);
    }

    /**
     * Given the name of an element, returns the element object
     *
     * @param string $elementName
     * @return ElementObject | null
     */
    public function getElement($elementName)
    {
        if ($this->hasElement($elementName)) {
            return $this->elements[$elementName];
        }

        return null;
    }
}
