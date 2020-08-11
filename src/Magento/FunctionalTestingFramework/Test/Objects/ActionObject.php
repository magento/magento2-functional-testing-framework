<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Test\Objects;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\FunctionalTestingFramework\DataGenerator\Objects\EntityDataObject;
use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\ObjectManager\ObjectHandlerInterface;
use Magento\FunctionalTestingFramework\Page\Objects\PageObject;
use Magento\FunctionalTestingFramework\Page\Objects\SectionObject;
use Magento\FunctionalTestingFramework\Page\Handlers\PageObjectHandler;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;
use Magento\FunctionalTestingFramework\Exceptions\TestReferenceException;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;

/**
 * Class ActionObject
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ActionObject
{
    const COMMENT_ACTION = '#comment';
    const __ENV = "_ENV";
    const __CREDS = "_CREDS";
    const RUNTIME_REFERENCES = [
        self::__ENV,
        self::__CREDS
    ];

    const DATA_ENABLED_ATTRIBUTES = [
        "userInput",
        "parameterArray",
        "expected",
        "actual",
        "x",
        "y",
        "expectedResult",
        "actualResult",
        "command",
        "regex",
        "date",
        "format"
    ];
    const SELECTOR_ENABLED_ATTRIBUTES = [
        'selector',
        'dependentSelector',
        "selector1",
        "selector2",
        "function",
        'filterSelector',
        'optionSelector',
        "command",
        "html"
    ];
    const ASSERTION_ATTRIBUTES = ["expectedResult" => "expected", "actualResult" => "actual"];
    const ASSERTION_TYPE_ATTRIBUTE = "type";
    const ASSERTION_VALUE_ATTRIBUTE = "value";
    const ASSERTION_ELEMENT_ATTRIBUTES = ["selector", "attribute"];
    const DELETE_DATA_MUTUAL_EXCLUSIVE_ATTRIBUTES = ["url", "createDataKey"];
    const EXTERNAL_URL_AREA_INVALID_ACTIONS = ['amOnPage'];
    const FUNCTION_CLOSURE_ACTIONS = ['waitForElementChange'];
    const COMMAND_ACTION_ATTRIBUTES = ['magentoCLI', 'magentoCLISecret'];
    const MERGE_ACTION_ORDER_AFTER = 'after';
    const MERGE_ACTION_ORDER_BEFORE = 'before';
    const ACTION_ATTRIBUTE_TIMEZONE = 'timezone';
    const ACTION_ATTRIBUTE_URL = 'url';
    const ACTION_ATTRIBUTE_SELECTOR = 'selector';
    const ACTION_ATTRIBUTE_VARIABLE_REGEX_PARAMETER = '/\(.+\)/';
    const ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN = '/({{[\w]+\.[\w\[\]]+}})|({{[\w]+\.[\w]+\((?(?!}}).)+\)}})/';
    const STRING_PARAMETER_REGEX = "/'[^']+'/";
    const DEFAULT_COMMAND_WAIT_TIMEOUT = 60;
    const ACTION_ATTRIBUTE_USERINPUT = 'userInput';
    const ACTION_TYPE_COMMENT = 'comment';
    const ACTION_TYPE_HELPER = 'helper';
    const INVISIBLE_STEP_ACTIONS = ['retrieveEntityField', 'getSecret'];

    /**
     * The unique identifier for the action
     *
     * @var string $stepKey
     */
    private $stepKey;

    /**
     * Array of deprecated entities used in action.
     *
     * @var array
     */
    private $deprecatedUsage = [];

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
     * An array with items containing information of the origin of this action.
     *
     * @var array
     */
    private $actionOrigin = [];

    /**
     * ActionObject constructor.
     *
     * @param string      $stepKey
     * @param string      $type
     * @param array       $actionAttributes
     * @param string|null $linkedAction
     * @param string      $order
     * @param array       $actionOrigin
     * @param array       $deprecatedUsage
     */
    public function __construct(
        $stepKey,
        $type,
        $actionAttributes,
        $linkedAction = null,
        $order = ActionObject::MERGE_ACTION_ORDER_BEFORE,
        $actionOrigin = null,
        $deprecatedUsage = []
    ) {
        $this->stepKey = $stepKey;
        $this->type = $type === self::COMMENT_ACTION ? self::ACTION_TYPE_COMMENT : $type;
        $this->actionAttributes = $actionAttributes;
        $this->linkedAction = $linkedAction;
        $this->actionOrigin = $actionOrigin;
        $this->deprecatedUsage = $deprecatedUsage;

        if ($order === ActionObject::MERGE_ACTION_ORDER_AFTER) {
            $this->orderOffset = 1;
        }
    }

    /**
     * Retrieve default timeout in seconds for 'wait*' actions
     *
     * @return integer
     */
    public static function getDefaultWaitTimeout()
    {
        return getenv('WAIT_TIMEOUT');
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
     * Getter for actionOrigin
     *
     * @return array
     */
    public function getActionOrigin()
    {
        return $this->actionOrigin;
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
     * @return integer
     */
    public function getOrderOffset()
    {
        return $this->orderOffset;
    }

    /**
     * This function returns the int property timeout, this can be set as a result of the use of a section element
     * requiring a wait.
     *
     * @return integer
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Set the timeout value.
     *
     * @param integer $timeout
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
     * @throws TestReferenceException
     * @throws XmlException
     */
    public function resolveReferences()
    {
        if (empty($this->resolvedCustomAttributes)) {
            $this->resolveHelperReferences();
            $this->trimAssertionAttributes();
            $this->resolveSelectorReferenceAndTimeout();
            $this->resolveUrlReference();
            $this->resolveDataInputReferences();
            $this->validateTimezoneAttribute();
            if ($this->getType() == "deleteData") {
                $this->validateMutuallyExclusiveAttributes(self::DELETE_DATA_MUTUAL_EXCLUSIVE_ATTRIBUTES);
            }
        }
    }

    /**
     * Resolves references for helpers.
     *
     * @throws TestReferenceException
     * @return void
     */
    private function resolveHelperReferences()
    {
        if ($this->getType() !== 'helper') {
            return;
        }
        $isResolved = false;

        try {
            foreach ($this->actionAttributes as $attrKey => $attrValue) {
                $this->actionAttributes[$attrKey] = $this->findAndReplaceReferences(
                    SectionObjectHandler::getInstance(),
                    $attrValue
                );
            }
            $isResolved = true;
        } catch (\Exception $e) {
            // catching exception to allow other entity type resolution to proceed
        }

        try {
            foreach ($this->actionAttributes as $attrKey => $attrValue) {
                $this->actionAttributes[$attrKey] = $this->findAndReplaceReferences(
                    PageObjectHandler::getInstance(),
                    $attrValue
                );
            }
            $isResolved = true;
        } catch (\Exception $e) {
            // catching exception to allow other entity type resolution to proceed
        }

        try {
            foreach ($this->actionAttributes as $attrKey => $attrValue) {
                $this->actionAttributes[$attrKey] = $this->findAndReplaceReferences(
                    DataObjectHandler::getInstance(),
                    $attrValue
                );
            }
            $isResolved = true;
        } catch (\Exception $e) {
            // catching exception to allow other entity type resolution to proceed
        }

        if ($isResolved !== true) {
            throw new TestReferenceException(
                "Could not resolve entity reference \"{$attrValue}\" "
                . "in Action with stepKey \"{$this->getStepKey()}\"",
                ["input" => $attrValue, "stepKey" => $this->getStepKey()]
            );
        }
    }

    /**
     * Flattens expectedResult/actualResults/array nested elements, if necessary.
     * e.g. expectedResults[] -> ["expectedType" => "string", "expected" => "value"]
     * Warns user if they are using old Assertion syntax.
     *
     * @return void
     */
    public function trimAssertionAttributes()
    {
        $actionAttributeKeys = array_keys($this->actionAttributes);
        $relevantKeys = array_keys(ActionObject::ASSERTION_ATTRIBUTES);
        $relevantAssertionAttributes = array_intersect($actionAttributeKeys, $relevantKeys);

        if (empty($relevantAssertionAttributes)) {
            return;
        }

        // Flatten nested Elements's type and value into key=>value entries
        // Also, add selector/value attributes if they are present in nested Element
        foreach ($this->actionAttributes as $key => $subAttributes) {
            foreach (self::ASSERTION_ELEMENT_ATTRIBUTES as $ATTRIBUTE) {
                if (isset($subAttributes[$ATTRIBUTE])) {
                    $this->actionAttributes[$ATTRIBUTE] = $subAttributes[$ATTRIBUTE];
                }
            }
            if (in_array($key, $relevantKeys)) {
                $prefix = ActionObject::ASSERTION_ATTRIBUTES[$key];
                $this->actionAttributes[$prefix . ucfirst(ActionObject::ASSERTION_TYPE_ATTRIBUTE)] =
                    $subAttributes[ActionObject::ASSERTION_TYPE_ATTRIBUTE] ?? "NO_TYPE";
                $this->actionAttributes[$prefix] =
                    $subAttributes[ActionObject::ASSERTION_VALUE_ATTRIBUTE] ?? "";
                unset($this->actionAttributes[$key]);
            }
        }
    }

    /**
     * Look up the selector for SomeSectionName.ElementName and set it as the selector attribute in the
     * resolved custom attributes. Also set the timeout value.
     * e.g. {{SomeSectionName.ElementName}} becomes #login-button
     *
     * @return void
     * @throws XmlException
     * @throws \Exception
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
     * @throws TestReferenceException
     * @throws XmlException
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
            $allPages = PageObjectHandler::getInstance()->getAllObjects();
            if ($replacement === $url && array_key_exists(trim($url, "{}"), $allPages)
            ) {
                throw new TestReferenceException(
                    "page url attribute not found and is required",
                    ["action" => $this->type, "url" => $url, "stepKey" => $this->stepKey]
                );
            }
        }
    }

    /**
     * Look up the value for EntityDataObjectName.Key and set it as the corresponding attribute in the resolved custom
     * attributes.
     * e.g. {{CustomerEntityFoo.FirstName}} becomes Jerry
     *
     * @return void
     * @throws TestReferenceException
     * @throws \Exception
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
            if ($replacement !== null) {
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
     * Returns an array containing all parameters found inside () block of test input. Expected delimiter is ', '.
     * Returns null if no parameters were found.
     *
     * @param string $reference
     * @return array|null
     */
    private function stripAndReturnParameters($reference)
    {
        $postCleanupDelimiter = "::::";

        preg_match(ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PARAMETER, $reference, $matches);
        if (!empty($matches)) {
            $strippedReference = ltrim(rtrim($matches[0], ")"), "(");

            // Pull out all 'string' references, as they can contain 'string with , comma in it'
            // 'string', or 'string,!@#$%^&*()_+, '
            preg_match_all(self::STRING_PARAMETER_REGEX, $strippedReference, $literalReferences);
            $strippedReference = preg_replace(self::STRING_PARAMETER_REGEX, '&&stringReference&&', $strippedReference);

            // Sanitize 'string, data.field,$persisted.field$' => 'string::::data.field::::$persisted.field$'
            $strippedReference = preg_replace('/,/', ', ', $strippedReference);
            $strippedReference = str_replace(',', $postCleanupDelimiter, $strippedReference);
            $strippedReference = str_replace(' ', '', $strippedReference);

            // Replace string references into string, needed to keep sequence
            foreach ($literalReferences[0] as $key => $value) {
                $strippedReference = preg_replace('/&&stringReference&&/', $value, $strippedReference, 1);
            }

            return explode($postCleanupDelimiter, $strippedReference);
        }
        return null;
    }

    /**
     * Return a string based on a reference to a page, section, or data field (e.g. {{foo.ref}} resolves to 'data')
     *
     * @param ObjectHandlerInterface $objectHandler
     * @param string                 $inputString
     * @return string | null
     * @throws TestReferenceException
     * @throws \Exception
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function findAndReplaceReferences($objectHandler, $inputString)
    {
        //look for parameter area, if so use different regex
        $regex = ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN;

        preg_match_all($regex, $inputString, $matches);

        $outputString = $inputString;

        foreach ($matches[0] as $match) {
            $replacement = null;
            $parameterized = false;
            list($objName) = $this->stripAndSplitReference($match);

            $obj = $objectHandler->getObject($objName);

            // Leave runtime references to be replaced in TestGenerator with getter function accessing "VARIABLE"
            if (in_array($objName, ActionObject::RUNTIME_REFERENCES)) {
                continue;
            }

            if ($obj == null) {
                // keep initial values for subsequent logic
                $replacement = null;
                $parameterized = false;
            } elseif (get_class($obj) == PageObject::class) {
                if ($obj->getDeprecated() !== null) {
                    $this->deprecatedUsage[] = "DEPRECATED PAGE in Test: " . $match . ' ' . $obj->getDeprecated();
                }
                $this->validateUrlAreaAgainstActionType($obj);
                $replacement = $obj->getUrl();
                $parameterized = $obj->isParameterized();
            } elseif (get_class($obj) == SectionObject::class) {
                if ($obj->getDeprecated() !== null) {
                    $this->deprecatedUsage[] = "DEPRECATED SECTION in Test: " . $match . ' ' . $obj->getDeprecated();
                }
                list(,$objField) = $this->stripAndSplitReference($match);

                if ($obj->getElement($objField) == null) {
                    throw new TestReferenceException(
                        "Could not resolve entity reference \"{$inputString}\" "
                        . "in Action with stepKey \"{$this->getStepKey()}\"",
                        ["input" => $inputString, "stepKey" => $this->getStepKey()]
                    );
                }
                $parameterized = $obj->getElement($objField)->isParameterized();
                $replacement = $obj->getElement($objField)->getPrioritizedSelector();
                $this->setTimeout($obj->getElement($objField)->getTimeout());
                if ($obj->getElement($objField)->getDeprecated() !== null) {
                    $this->deprecatedUsage[] = "DEPRECATED ELEMENT in Test: " . $match . ' '
                        . $obj->getElement($objField)->getDeprecated();
                }
            } elseif (get_class($obj) == EntityDataObject::class) {
                if ($obj->getDeprecated() !== null) {
                    $this->deprecatedUsage[] = "DEPRECATED DATA ENTITY in Test: "
                        . $match . ' ' . $obj->getDeprecated();
                }
                $replacement = $this->resolveEntityDataObjectReference($obj, $match);

                if (is_array($replacement)) {
                    $replacement = '["' . implode('","', array_map('addSlashes', $replacement)) . '"]';
                }
            }

            if ($replacement === null) {
                if (!($objectHandler instanceof DataObjectHandler)) {
                    return $this->findAndReplaceReferences(DataObjectHandler::getInstance(), $outputString);
                } else {
                    throw new TestReferenceException(
                        "Could not resolve entity reference \"{$inputString}\" "
                        . "in Action with stepKey \"{$this->getStepKey()}\"",
                        ["input" => $inputString, "stepKey" => $this->getStepKey()]
                    );
                }
            }

            $replacement = $this->resolveParameterization($parameterized, $replacement, $match, $obj);

            $outputString = str_replace($match, $replacement, $outputString);
        }
        return $outputString;
    }

    /**
     * Validates that the mutually exclusive attributes passed in don't all occur.
     * @param array $attributes
     * @return void
     * @throws TestReferenceException
     */
    private function validateMutuallyExclusiveAttributes(array $attributes)
    {
        $matches = array_intersect($attributes, array_keys($this->getCustomActionAttributes()));
        if (count($matches) > 1) {
            throw new TestReferenceException(
                "Actions of type '{$this->getType()}' must only contain one attribute of types '"
                . implode("', '", $attributes) . "'",
                ["type" => $this->getType(), "attributes" => $attributes]
            );
        } elseif (count($matches) == 0) {
            throw new TestReferenceException(
                "Actions of type '{$this->getType()}' must contain at least one attribute of types '"
                . implode("', '", $attributes) . "'",
                ["type" => $this->getType(), "attributes" => $attributes]
            );
        }
    }

    /**
     * Validates the page objects area 'external' against a list of known incompatible types
     *
     * @param PageObject $obj
     * @return void
     * @throws TestReferenceException
     */
    private function validateUrlAreaAgainstActionType($obj)
    {
        if ($obj->getArea() == 'external' &&
            in_array($this->getType(), self::EXTERNAL_URL_AREA_INVALID_ACTIONS)) {
            throw new TestReferenceException(
                "Page of type 'external' is not compatible with action type '{$this->getType()}'",
                ["type" => $this->getType()]
            );
        }
    }

    /**
     * Validates that the timezone attribute contains a valid value.
     *
     * @return void
     * @throws TestReferenceException
     */
    private function validateTimezoneAttribute()
    {
        $attributes = $this->getCustomActionAttributes();
        if (isset($attributes[self::ACTION_ATTRIBUTE_TIMEZONE])) {
            $timezone = $attributes[self::ACTION_ATTRIBUTE_TIMEZONE];
            try {
                new \DateTimeZone($timezone);
            } catch (\Exception $e) {
                throw new TestReferenceException(
                    "Timezone '{$timezone}' is not a valid timezone",
                    ["stepKey" => $this->getStepKey(), self::ACTION_ATTRIBUTE_TIMEZONE => $timezone]
                );
            }
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
     * @param string  $replacement
     * @param string  $match
     * @param object  $object
     * @return string
     * @throws \Exception
     */
    private function resolveParameterization($isParameterized, $replacement, $match, $object)
    {
        if ($isParameterized) {
            $parameterList = $this->stripAndReturnParameters($match) ?: [];
            $resolvedReplacement = $this->matchParameterReferences($replacement, $parameterList);
        } else {
            $resolvedReplacement = $replacement;
        }
        if (get_class($object) == PageObject::class && $object->getArea() == PageObject::ADMIN_AREA) {
            $urlSegments = [
                '{{_ENV.MAGENTO_BACKEND_BASE_URL}}',
                '{{_ENV.MAGENTO_BACKEND_NAME}}',
                $resolvedReplacement
            ];
            $resolvedReplacement = implode('/', $urlSegments);
        }
        return $resolvedReplacement;
    }

    /**
     * Finds all {{var}} occurrences in reference, and replaces them in sequence with parameters list given.
     * Parameter list given is also resolved, attempting to match {{data.field}} references.
     *
     * @param string $reference
     * @param array  $parameters
     * @return string
     * @throws \Exception
     */
    private function matchParameterReferences($reference, $parameters)
    {
        preg_match_all('/{{[\w.]+}}/', $reference, $varMatches);
        $varMatches[0] = array_unique($varMatches[0]);
        $this->checkParameterCount($varMatches[0], $parameters, $reference);

        //Attempt to Resolve {{data}} references to actual output. Trim parameter for whitespace before processing it.
        //If regex matched it means that it's either a 'StringLiteral' or $key.data$/$$key.data$$ reference.
        //Elseif regex match for {$data}
        //Else assume it's a normal {{data.key}} reference and recurse through findAndReplace
        $resolvedParameters = [];
        foreach ($parameters as $parameter) {
            $parameter = trim($parameter);
            preg_match_all("/[$'][\w\D]*[$']/", $parameter, $stringOrPersistedMatch);
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

    /**
     * Checks count of parameters versus matches
     *
     * @param array  $matches
     * @param array  $parameters
     * @param string $reference
     * @return void
     * @throws \Exception
     */
    private function checkParameterCount($matches, $parameters, $reference)
    {
        if (count($matches) > count($parameters)) {
            if (is_array($parameters)) {
                $parametersGiven = implode(",", $parameters);
            } elseif ($parameters == null) {
                $parametersGiven = "NONE";
            } else {
                $parametersGiven = $parameters;
            }
            throw new TestReferenceException(
                "Parameter Resolution Failed: Not enough parameters given for reference " .
                $reference . ". Parameters Given: " . $parametersGiven,
                ["reference" => $reference, "parametersGiven" => $parametersGiven]
            );
        } elseif (count($matches) < count($parameters)) {
            throw new TestReferenceException(
                "Parameter Resolution Failed: Too many parameters given for reference " .
                $reference . ". Parameters Given: " . implode(", ", $parameters),
                ["reference" => $reference, "parametersGiven" => $parameters]
            );
        } elseif (count($matches) == 0) {
            throw new TestReferenceException(
                "Parameter Resolution Failed: No parameter matches found in parameterized element with selector " .
                $reference,
                ["reference" => $reference]
            );
        }
    }

    /**
     * Returns array of deprecated usages in Action.
     *
     * @return array
     */
    public function getDeprecatedUsages()
    {
        return $this->deprecatedUsage;
    }
}
