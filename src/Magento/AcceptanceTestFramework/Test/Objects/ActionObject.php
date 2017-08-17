<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\AcceptanceTestFramework\Test\Objects;

use Magento\AcceptanceTestFramework\PageObject\Page\Page;
use Magento\AcceptanceTestFramework\PageObject\Section\Section;
use Magento\AcceptanceTestFramework\DataGenerator\Managers\DataManager;

/**
 * Class ActionObject
 */
class ActionObject
{
    /**
     * Merge key
     *
     * @var string $mergeKey
     */
    private $mergeKey;

    /**
     * Class property.
     *
     * @var string
     */
    private $type;

    /**
     * Class property.
     *
     * @var array
     */
    private $actionAttributes = [];

    /**
     * Class property.
     *
     * @var null|string
     */
    private $linkedAction;

    /**
     * Class property.
     *
     * @var int
     */
    private $orderOffset = 0;

    /**
     * Class property.
     *
     * @var array
     */
    private $resolvedCustomAttributes = [];

    /**
     * Class property.
     *
     * @var int
     */
    private $timeout;

    const DATA_ENABLED_ATTRIBUTES = ["userInput", "parameterArray"];

    /**
     * ActionObject constructor.
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

        if ($order == 'after') {
            $this->orderOffset = 1;
        }
    }

    /**
     * This function returns the string property mergeKey.
     * @return string
     */
    public function getMergeKey()
    {
        return $this->mergeKey;
    }

    /**
     * This function returns the string property type.
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
     * @return array
     */
    public function getCustomActionAttributes()
    {
        return array_merge($this->actionAttributes, $this->resolvedCustomAttributes);
    }

    /**
     * This function returns the string property linkedAction, describing a step to reference for a merge.
     * @return string
     */
    public function getLinkedAction()
    {
        return $this->linkedAction;
    }

    /**
     * This function returns the int property orderOffset, describing before or after for a merge.
     * @return int
     */
    public function getOrderOffset()
    {
        return $this->orderOffset;
    }

    /**
     * This function returns the int property timeout, this can be set as a result of the use of a section element
     * requiring a wait.
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Populate the resolved custom attributes array with lookup values for the following attributes:
     *  - selector
     *  - url
     *  - userInput
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
        if (!array_key_exists('selector', $this->actionAttributes)) {
            return;
        }
        $selector = $this->actionAttributes['selector'];

        $reference = $this->findAllReferences($selector);
        if (count($reference) == 1) {
            $reference = $reference[0];
        } else {
            // Selectors can only handle a single var reference
            return;
        }

        list($sectionName, $elementName) = $this->stripAndSplitReference($reference);
        $section = Section::getSection($sectionName);
        if ($section == null) {
            // Bad section reference
            return;
        }

        $replacement = Section::getElementLocator($sectionName, $elementName);

        if ($replacement == null) {
            // Bad section reference
            return;
        }
        $this->resolvedCustomAttributes['selector'] = str_replace($reference, $replacement, $selector);
        $this->timeout = Section::getElementTimeOut($sectionName, $elementName);
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
        if (!array_key_exists('url', $this->actionAttributes)) {
            return;
        }
        $url = $this->actionAttributes['url'];

        foreach ($this->findAllReferences($url) as $reference) {
            $replacement = null;

            // assume this is a page
            list($pageName) = $this->stripAndSplitReference($reference);
            $page = Page::getPage($pageName);

            if ($page != null) {
                $replacement = Page::getPageUrl($pageName);
            } else {
                // try to resolve as data
                list($entityName, $entityField) = $this->stripAndSplitReference($reference);
                $replacement = DataManager::getInstance()->getEntity($entityName)->getDataByName($entityField);
            }

            if ($replacement == null) {
                continue;
                // Bad var ref
            }
            $url = str_replace($reference, $replacement, $url);
        }

        $this->resolvedCustomAttributes['url'] = $url;
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

            foreach ($this->findAllReferences($varInput) as $reference) {
                list($entityName, $entityKey) = $this->stripAndSplitReference($reference);
                $entityObj = DataManager::getInstance()->getEntity($entityName);
                if ($entityObj == null) {
                    // Bad entity reference
                    continue;
                }

                $replacement = $entityObj->getDataByName($entityKey) ?? null;
                if ($replacement == null) {
                    // Bad entity.key reference
                    return;
                }

                $varInput  = str_replace($reference, $replacement, $varInput);
            }

            $this->resolvedCustomAttributes[$dataAttribute] = $varInput;
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
     * Return an array of {{reference.foo}} if any exist in the string, otherwise returns an empty array.
     *
     * @param string $str
     * @return array
     */
    private function findAllReferences($str)
    {
        preg_match_all('/{{[\w.]+}}/', $str, $matches);
        if (empty($matches)) {
            return [];
        } else {
            return $matches[0];
        }
    }
}
