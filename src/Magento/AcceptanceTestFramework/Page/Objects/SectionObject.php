<?php

namespace Magento\AcceptanceTestFramework\Page\Objects;

class SectionObject
{
    /**
     * @var string $name
     * @var array $elements
     */
    private $name;
    private $elements = [];

    /**
     * SectionObject constructor.
     * @param string $name
     * @param array $elements
     */
    public function __construct($name, $elements)
    {
        $this->name = $name;
        $this->elements = $elements;
    }

    /**
     * Getter for the name of the section
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Getter for an array containing all of a section's elements.
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Given the name of an element, returns the element object
     * @param $elementName
     * @return ElementObject
     */
    public function getElement($elementName)
    {
        return $this->elements[$elementName];
    }
}