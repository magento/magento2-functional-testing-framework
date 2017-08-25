<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AcceptanceTestFramework\Test\Objects;

use Magento\AcceptanceTestFramework\DataGenerator\Handlers\DataObjectHandler;
use Magento\AcceptanceTestFramework\DataGenerator\Objects\EntityDataObject;
use Magento\AcceptanceTestFramework\ObjectManager\ObjectHandlerInterface;
use Magento\AcceptanceTestFramework\Page\Objects\PageObject;
use Magento\AcceptanceTestFramework\Page\Objects\SectionObject;
use Magento\AcceptanceTestFramework\Page\Handlers\PageObjectHandler;
use Magento\AcceptanceTestFramework\Page\Handlers\SectionObjectHandler;

/**
 * Class ActionObject
 */
class ActionObject
{
    const DATA_ENABLED_ATTRIBUTES = ["userInput", "parameterArray"];
    const MERGE_ACTION_ORDER_AFTER = 'after';
    const ACTION_ATTRIBUTE_URL = 'url';
    const ACTION_ATTRIBUTE_SELECTOR = 'selector';
    const ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN = '/{{[\w.]+}}/';

    /**
     * The unique identifier for the action
     *
     * @var string $mergeKey
     */
    private $mergeKey;

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
     * @param string $mergeKey
     * @param string $type
     * @param array $actionAttributes
     * @param string|null $linkedAction
     * @param int $order
     */
    public function __construct($mergeKey, $type, $actionAttributes, $linkedAction = null, $order = 0)
    {
        $this->mergeKey = $mergeKey;
        $this->type = $type;
        $this->actionAttributes = $actionAttributes;
        $this->linkedAction = $linkedAction;

        if ($order == ActionObject::MERGE_ACTION_ORDER_AFTER) {
            $this->orderOffset = 1;
        }
    }

    /**
     * This function returns the string property mergeKey.
     *
     * @return string
     */
    public function getMergeKey()
    {
        return $this->mergeKey;
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
     * the tag <seeNumberOfElements selector="value1" expected="value2" mergeKey=""/> has 3 attributes,
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
        if (!array_key_exists(ActionObject::ACTION_ATTRIBUTE_SELECTOR, $this->actionAttributes)) {
            return;
        }

        $selector = $this->actionAttributes[ActionObject::ACTION_ATTRIBUTE_SELECTOR];

        $replacement = $this->findAndReplaceReferences(SectionObjectHandler::getInstance(), $selector);
        if ($replacement) {
            $this->resolvedCustomAttributes[ActionObject::ACTION_ATTRIBUTE_SELECTOR] = $replacement;
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
     *
     * @param string $reference
     * @return string[] The name and key that is referenced.
     */
    private function stripAndSplitReference($reference)
    {
        $strippedReference = str_replace('}}', '', str_replace('{{', '', $reference));
        return explode('.', $strippedReference);
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
        preg_match_all(ActionObject::ACTION_ATTRIBUTE_VARIABLE_REGEX_PATTERN, $inputString, $matches);

        if (empty($matches[0])) {
            return null;
        }

        $outputString = $inputString;

        foreach ($matches[0] as $match) {
            $replacement = null;
            list($objName) = $this->stripAndSplitReference($match);

            $obj = $objectHandler->getObject($objName);

            // specify behavior depending on field
            switch (get_class($obj)) {
                case PageObject::class:
                    $replacement = $obj->getUrl();
                    break;
                case SectionObject::class:
                    list(,$objField) = $this->stripAndSplitReference($match);
                    $replacement = $obj->getElement($objField)->getLocator();
                    $this->timeout = $obj->getElement($objField)->getTimeout();
                    break;
                case (get_class($obj) == EntityDataObject::class):
                    list(,$objField) = $this->stripAndSplitReference($match);
                    $replacement = $obj->getDataByName($objField);
                    $replacement = $this->resolveEntityDataUniquenessReference($replacement, $obj, $objField);
                    break;
            }

            if ($replacement == null && get_class($objectHandler) != DataObjectHandler::class) {
                return $this->findAndReplaceReferences(DataObjectHandler::getInstance(), $outputString);
            } elseif ($replacement == null) {
                throw new \Exception("Could not resolve entity reference " . $inputString);
            }

            $outputString = str_replace($match, $replacement, $outputString);

        }

        return $outputString;
    }

    /**
     * @param string $reference
     * @param EntityDataObject $entityDataObject
     * @param string $entityKey
     * @return string
     */
    private function resolveEntityDataUniquenessReference($reference, $entityDataObject, $entityKey)
    {
        $uniquenessData = $entityDataObject->getUniquenessDataByName($entityKey);
        $entityName = $entityDataObject->getName();

        if ($uniquenessData == DataObjectHandler::DATA_ELEMENT_UNIQUENESS_ATTR_VALUE_PREFIX) {
            $reference =
                DataObjectHandler::UNIQUENESS_FUNCTION . '("' . $entityName . '.' . $entityKey . '")' . $reference;
        } elseif ($uniquenessData == DataObjectHandler::DATA_ELEMENT_UNIQUENESS_ATTR_VALUE_SUFFIX) {
            $reference .= DataObjectHandler::UNIQUENESS_FUNCTION . '("' . $entityName . '.' . $entityKey . '")';
        }
        return $reference;
    }
}
