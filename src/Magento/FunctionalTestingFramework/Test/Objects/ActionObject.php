<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Test\Objects;

use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\Page\Objects\PageObject;
use Magento\FunctionalTestingFramework\Page\Objects\SectionObject;
use Magento\FunctionalTestingFramework\Page\Handlers\PageObjectHandler;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;

/**
 * Class ActionObject
 */
class ActionObject
{
    const DATA_ENABLED_ATTRIBUTES = ["userInput", "parameterArray"];
    const SELECTOR_ENABLED_ATTRIBUTES = ['selector', 'dependentSelector', "selector1", "selector2"];
    const EXTERNAL_URL_AREA_INVALID_ACTIONS = ['amOnPage'];
    const MERGE_ACTION_ORDER_AFTER = 'after';
    const MERGE_ACTION_ORDER_BEFORE = 'before';
    const ACTION_ATTRIBUTE_URL = 'url';
    const ACTION_ATTRIBUTE_SELECTOR = 'selector';
    const ACTION_ATTRIBUTE_VARIABLE_REGEX_PARAMETER = '/\(.+\)/';
    const ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN = '/{{[\w.\[\]()\',$ ]+}}/';
    const ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN_WITH_PARAMS= '/{{[\w]+\.[\w]+\([\w ,.\'{$}]+\)}}/';

    /**
     * The unique identifier for the action
     *
     * @var string $stepKey
     */
    private $stepKey;

    /**
     * The type of action (e.g. fillField, createData, etc)
     *
     * @var string $type
     */
    private $type;

    /**
     * THe attributes which describe the action (e.g. selector, userInput)
     *
     * @var array $actionAttributes
     */
    private $actionAttributes = [];

    /**
     * The name of the action to reference when merging this action into existing test steps
     *
     * @var null|string $linkedAction
     */
    private $linkedAction;

    /**
     * A value used to describe position during merge
     *
     * @var int $orderOffset
     */
    private $orderOffset = 0;

    /**
     * An array which contains variable resolution of all specified parameters in an action
     *
     * @var array $resolvedCustomAttributes
     */
    private $resolvedCustomAttributes = [];

    /**
     * A string which represents a needed timeout whenever the action is referenced
     *
     * @var string timeout
     */
    private $timeout;

    /**
     * ActionObject constructor.
     *
     * @param string $stepKey
     * @param string $type
     * @param array $actionAttributes
     * @param string|null $linkedAction
     * @param string $order
     */
    public function __construct(
        $stepKey,
        $type,
        $actionAttributes,
        $linkedAction = null,
        $order = ActionObject::MERGE_ACTION_ORDER_BEFORE
    ) {
        $this->stepKey = $stepKey;
        $this->type = $type;
        $this->actionAttributes = $actionAttributes;
        $this->linkedAction = $linkedAction;

        if ($order == ActionObject::MERGE_ACTION_ORDER_AFTER) {
            $this->orderOffset = 1;
        }
    }

    /**
     * This function returns the string property stepKey.
     *
     * @return string
     */
    public function getStepKey()
    {
        return $this->stepKey;
    }

    /**
     * This function returns the string property type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * This function returns an array of action attributes mapped by key. For example
     * the tag <seeNumberOfElements selector="value1" expected="value2" stepKey=""/> has 3 attributes,
     * only 2 of which are specific to the 'seeNumberOfElements' tag. As a result this function would
     * return the array would return [selector => value1, expected => value2]
     * The returned array is also the merged result of the resolved and normal actions, giving
     * priority to the resolved actions (resolved selector instead of section.element, etc).
     *
     * @return array
     */
    public function getCustomActionAttributes()
    {
        return array_merge($this->actionAttributes, $this->resolvedCustomAttributes);
    }

    /**
     * This function returns the string property linkedAction, describing a step to reference for a merge.
     *
     * @return string
     */
    public function getLinkedAction()
    {
        return $this->linkedAction;
    }

    /**
     * This function returns the int property orderOffset, describing before or after for a merge.
     *
     * @return int
     */
    public function getOrderOffset()
    {
        return $this->orderOffset;
    }

    /**
     * This function returns the int property timeout, this can be set as a result of the use of a section element
     * requiring a wait.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set the timeout value.
     *
     * @param int $timeout
     * @return void
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }

    /**
     * Populate the resolved custom attributes array with lookup values for the following attributes:
     *   selector
     *   url
     *   userInput
     *
     * @return void
     */
    public function resolveReferences()
    {
        if (empty($this->resolvedCustomAttributes)) {
            $this->resolveSelectorReferenceAndTimeout();
            $this->resolveUrlReference();
            $this->resolveDataInputReferences();
        }
    }

    /**
     * Look up the selector for SomeSectionName.ElementName and set it as the selector attribute in the
     * resolved custom attributes. Also set the timeout value.
     * e.g. {{SomeSectionName.ElementName}} becomes #login-button
     *
     * @return void
     */
    private function resolveSelectorReferenceAndTimeout()
    {
        $actionAttributeKeys = array_keys($this->actionAttributes);
        $relevantSelectorAttributes = array_intersect($actionAttributeKeys, ActionObject::SELECTOR_ENABLED_ATTRIBUTES);

        if (empty($relevantSelectorAttributes)) {
            return;
        }

        foreach ($relevantSelectorAttributes as $selectorAttribute) {
            $selector = $this->actionAttributes[$selectorAttribute];

            $replacement = $this->findAndReplaceReferences(SectionObjectHandler::getInstance(), $selector);
            if ($replacement) {
                $this->resolvedCustomAttributes[$selectorAttribute] = $replacement;
            }
        }
    }

    /**
     * Look up the url for SomePageName and set it, with MAGENTO_BASE_URL prepended, as the url attribute in the
     * resolved custom attributes.
     * e.g. {{SomePageName}} becomes http://localhost:76543/some/url
     *
     * @return void
     */
    private function resolveUrlReference()
    {
        if (!array_key_exists(ActionObject::ACTION_ATTRIBUTE_URL, $this->actionAttributes)) {
            return;
        }

        $url = $this->actionAttributes[ActionObject::ACTION_ATTRIBUTE_URL];

        $replacement = $this->findAndReplaceReferences(PageObjectHandler::getInstance(), $url);
        if ($replacement) {
            $this->resolvedCustomAttributes[ActionObject::ACTION_ATTRIBUTE_URL] = $replacement;
        }
    }

    /**
     * Look up the value for EntityDataObjectName.Key and set it as the corresponding attribute in the resolved custom
     * attributes.
     * e.g. {{CustomerEntityFoo.FirstName}} becomes Jerry
     *
     * @return void
     */
    private function resolveDataInputReferences()
    {
        $actionAttributeKeys = array_keys($this->actionAttributes);
        $relevantDataAttributes = array_intersect($actionAttributeKeys, ActionObject::DATA_ENABLED_ATTRIBUTES);

        if (empty($relevantDataAttributes)) {
            return;
        }

        foreach ($relevantDataAttributes as $dataAttribute) {
            $varInput = $this->actionAttributes[$dataAttribute];
            $replacement = $this->findAndReplaceReferences(DataObjectHandler::getInstance(), $varInput);
            if ($replacement) {
                $this->resolvedCustomAttributes[$dataAttribute] = $replacement;
            }
        }
    }

    /**
     * Return an array containing the name (before the period) and key (after the period) in a {{reference.foo}}.
     * Also truncates variables inside parenthesis.
     *
     * @param string $reference
     * @return string[] The name and key that is referenced.
     */
    private function stripAndSplitReference($reference)
    {
        $strippedReference = str_replace('}}', '', str_replace('{{', '', $reference));
        $strippedReference = preg_replace(
            ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PARAMETER,
            '',
            $strippedReference
        );
        return explode('.', $strippedReference);
    }

    /**
     * Returns an array containing all parameters found inside () block of test input.
     * Returns null if no parameters were found.
     *
     * @param string $reference
     * @return array|null
     */
    private function stripAndReturnParameters($reference)
    {
        preg_match(ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PARAMETER, $reference, $matches);
        if (!empty($matches)) {
            $strippedReference = str_replace(')', '', str_replace('(', '', $matches[0]));
            return explode(',', $strippedReference);
        }
        return null;
    }

    /**
     * Return a string based on a reference to a page, section, or data field (e.g. {{foo.ref}} resolves to 'data')
     *
     * @param ObjectHandlerInterface $objectHandler
     * @param string $inputString
     * @return string | null
     * @throws \Exception
     */
    private function findAndReplaceReferences($objectHandler, $inputString)
    {
        //look for parameter area, if so use different regex
        $regex = $this->resolveRegexPatternForReference($inputString);

        preg_match_all($regex, $inputString, $matches);

        $outputString = $inputString;

        foreach ($matches[0] as $match) {
            $replacement = null;
            $parameterized = false;
            list($objName) = $this->stripAndSplitReference($match);

            $obj = $objectHandler->getObject($objName);

            // specify behavior depending on field
            switch (get_class($obj)) {
                case PageObject::class:
                    $this->validateUrlAreaAgainstActionType($obj);
                    $replacement = $obj->getUrl();
                    $parameterized = $obj->isParameterized();
                    break;
                case SectionObject::class:
                    list(,$objField) = $this->stripAndSplitReference($match);
                    if ($obj->getElement($objField) == null) {
                        throw new TestReferenceException("Could not resolve entity reference " . $inputString);
                    }
                    $parameterized = $obj->getElement($objField)->isParameterized();
                    $replacement = $obj->getElement($objField)->getPrioritizedSelector();
                    $this->setTimeout($obj->getElement($objField)->getTimeout());
                    break;
                case EntityDataObject::class:
                    $replacement = $this->resolveEntityDataObjectReference($obj, $match);
                    break;
            }

            if ($replacement == null && get_class($objectHandler) != DataObjectHandler::class) {
                return $this->findAndReplaceReferences(DataObjectHandler::getInstance(), $outputString);
            } elseif ($replacement == null) {
                throw new TestReferenceException("Could not resolve entity reference " . $inputString);
            }

            $replacement = $this->resolveParameterization($parameterized, $replacement, $match);
            $outputString = str_replace($match, $replacement, $outputString);
        }
        return $outputString;
    }

    /**
     * Validates the page objects area 'external' against a list of known incompatible types
     *
     * @param PageObject $obj
     * @throws TestReferenceException
     */
    private function validateUrlAreaAgainstActionType($obj)
    {
        if ($obj->getArea() == 'external' &&
            in_array($this->getType(), self::EXTERNAL_URL_AREA_INVALID_ACTIONS)) {
            throw new TestReferenceException(
                "Page of type 'external' is not compatible with action type '{$this->getType()}'"
            );
        }
    }

    /**
     * Determines whether the given $inputString has (params), and returns the appropriate regex for use in matching.
     * @param string $inputString
     * @return string
     */
    private function resolveRegexPatternForReference($inputString)
    {
        if (preg_match(ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PARAMETER, $inputString) === 1) {
            return ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN_WITH_PARAMS;
        } else {
            return ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN;
        }
    }

    /**
     * Gets the object's dataByName with given $match, differentiating behavior between <array> and <data> nodes.
     * @param string $obj
     * @param string $match
     * @return string
     */
    private function resolveEntityDataObjectReference($obj, $match)
    {
        list(,$objField) = $this->stripAndSplitReference($match);

        if (strpos($objField, '[') == true) {
            // Access <array>...</array>
            $parts = explode('[', $objField);
            $name = $parts[0];
            $index = str_replace(']', '', $parts[1]);
            return $obj->getDataByName($name, EntityDataObject::CEST_UNIQUE_NOTATION)[$index];
        } else {
            // Access <data></data>
            return $obj->getDataByName($objField, EntityDataObject::CEST_UNIQUE_NOTATION);
        }
    }

    /**
     * Resolves $replacement parameterization with given conditional.
     * @param boolean $isParameterized
     * @param string $replacement
     * @param string $match
     * @return string
     */
    private function resolveParameterization($isParameterized, $replacement, $match)
    {
        if ($isParameterized) {
            $parameterList = $this->stripAndReturnParameters($match);
            return $this->matchParameterReferences($replacement, $parameterList);
        } else {
            return $replacement;
        }
    }

    /**
     * Finds all {{var}} occurrences in reference, and replaces them in sequence with parameters list given.
     * Parameter list given is also resolved, attempting to match {{data.field}} references.
     *
     * @param string $reference
     * @param array $parameters
     * @return string
     * @throws \Exception
     */
    private function matchParameterReferences($reference, $parameters)
    {
        preg_match_all('/{{[\w.]+}}/', $reference, $varMatches);
        $varMatches[0] = array_unique($varMatches[0]);
        if (count($varMatches[0]) > count($parameters)) {
            if (is_array($parameters)) {
                $parametersGiven = implode(",", $parameters);
            } elseif ($parameters == null) {
                $parametersGiven = "NONE";
            } else {
                $parametersGiven = $parameters;
            }
            throw new TestReferenceException(
                "Parameter Resolution Failed: Not enough parameters given for reference " .
                $reference . ". Parameters Given: " . $parametersGiven
            );
        } elseif (count($varMatches[0]) < count($parameters)) {
            throw new TestReferenceException(
                "Parameter Resolution Failed: Too many parameters given for reference " .
                $reference . ". Parameters Given: " . implode(", ", $parameters)
            );
        }

        //Attempt to Resolve {{data}} references to actual output. Trim parameter for whitespace before processing it.
        //If regex matched it means that it's either a 'StringLiteral' or $key.data$/$$key.data$$ reference.
        //Elseif regex match for {$data}
        //Else assume it's a normal {{data.key}} reference and recurse through findAndReplace
        $resolvedParameters = [];
        foreach ($parameters as $parameter) {
            $parameter = trim($parameter);
            preg_match_all("/[$'][\w\D]+[$']/", $parameter, $stringOrPersistedMatch);
            preg_match_all('/{\$[a-z][a-zA-Z\d]+}/', $parameter, $variableMatch);
            if (!empty($stringOrPersistedMatch[0])) {
                $resolvedParameters[] = ltrim(rtrim($parameter, "'"), "'");
            } elseif (!empty($variableMatch[0])) {
                $resolvedParameters[] = $parameter;
            } else {
                $resolvedParameters[] = $this->findAndReplaceReferences(
                    DataObjectHandler::getInstance(),
                    '{{' . $parameter . '}}'
                );
            }
        }

        $resolveIndex = 0;
        foreach ($varMatches[0] as $var) {
            $reference = str_replace($var, $resolvedParameters[$resolveIndex++], $reference);
        }
        return $reference;
    }
}
