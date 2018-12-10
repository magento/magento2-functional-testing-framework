<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Objects;

/**
 * Class ArgumentObject
 */
class ArgumentObject
{
    const ARGUMENT_NAME = 'name';
    const ARGUMENT_DEFAULT_VALUE = 'defaultValue';
    const ARGUMENT_DATA_TYPE = 'type';
    const ARGUMENT_DATA_ENTITY = 'entity';
    const ARGUMENT_DATA_STRING = 'string';

    /**
     * Name of the argument.
     * @var string
     */
    private $name;

    /**
     * Value of the argument. DefaultValue on argument creation
     * @var string
     */
    private $value;

    /**
     * Data type of the argument.
     * @var string
     */
    private $dataType;

    /**
     * ArgumentObject constructor.
     * @param string $name
     * @param string $value
     * @param string $dataType
     */
    public function __construct($name, $value, $dataType)
    {
        $this->name = $name;
        $this->value = $value;
        $this->dataType = $dataType;
    }

    /**
     * Function to return string property name.
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Function to return string property value.
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Function to return string property dataType.
     * @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Override's private string property value.
     * @param string $value
     * @return void
     */
    public function overrideValue($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the resolved value that the argument needs to have, depending on scope of where argument is referenced.
     * @param boolean $isInnerArgument
     * @return string
     */
    public function getResolvedValue($isInnerArgument)
    {
        if ($this->dataType === ArgumentObject::ARGUMENT_DATA_ENTITY) {
            return $this->value;
        } else {
            return $this->resolveStringArgument($isInnerArgument);
        }
    }

    /**
     * Resolves simple string arguments and returns the appropriate format for simple replacement.
     * Takes in boolean to determine if the replacement is being done with an inner argument (as in if it's a parameter)
     *
     * Example Type     Non Inner           Inner
     * {{XML.DATA}}     {{XML.DATA}}        XML.DATA
     * $TEST.DATA$      $TEST.DATA$         $TEST.DATA$
     * stringLiteral    stringLiteral       'stringLiteral'
     *
     * @param boolean $isInnerArgument
     * @return string
     */
    private function resolveStringArgument($isInnerArgument)
    {
        if ($isInnerArgument) {
            if (preg_match('/{{[\w.\[\]]+}}/', $this->value)) {
                return ltrim(rtrim($this->value, "}"), "{");
            } elseif (preg_match('/\${1,2}[\w.\[\]]+\${1,2}/', $this->value)) {
                return $this->value;
            }
            return "'" . $this->value . "'";
        } else {
            return $this->value;
        }
    }
}
