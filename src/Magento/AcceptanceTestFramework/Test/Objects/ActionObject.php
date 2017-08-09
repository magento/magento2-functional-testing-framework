<?php

namespace Magento\AcceptanceTestFramework\Test\Objects;

use Magento\AcceptanceTestFramework\PageObject\Page\Page;
use Magento\AcceptanceTestFramework\PageObject\Section\Section;
use Magento\AcceptanceTestFramework\DataGenerator\Managers\DataManager;

class ActionObject
{
    private $mergeKey;
    private $type;
    private $actionAttributes = [];
    private $linkedAction;
    private $orderOffset = 0;
    private $resolvedCustomAttributes = [];
    private $timeout;

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

    public function getMergeKey()
    {
        return $this->mergeKey;
    }

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

    public function getLinkedAction()
    {
        return $this->linkedAction;
    }

    public function getOrderOffset()
    {
        return $this->orderOffset;
    }

    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * Populate the resolved custom attributes array with lookup values for the following attributes:
     *
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
            $this->resolveUserInputReference();
        }
    }

    /**
     * Look up the selector for SomeSectionName.ElementName and set it as the selector attribute in the
     * resolved custom attributes. Also set the timeout value.
     *
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

        $reference = $this->findReference($selector);
        if ($reference == null) {
            // Nothing to replace
            return;
        }

        list($sectionName, $elementName) = $this->stripAndSplitReference($reference);
        $section = Section::getSection($sectionName);
        if ($section == null) {
            // Bad section reference
            return;
        }
        $replacement = Section::getElementLocator($sectionName, $elementName);

        $this->resolvedCustomAttributes['selector'] = str_replace($reference, $replacement, $selector);
        $this->timeout = Section::getElementTimeOut($sectionName, $elementName);
    }

    /**
     * Look up the url for SomePageName and set it, with MAGENTO_BASE_URL prepended, as the url attribute in the
     * resolved custom attributes.
     *
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

        $reference = $this->findReference($url);
        if ($reference == null) {
            // Nothing to replace
            return;
        }

        list($pageName) = $this->stripAndSplitReference($reference);
        $page = Page::getPage($pageName);
        if ($page == null) {
            // Bad page reference
            return;
        }
        $replacement = $_ENV['MAGENTO_BASE_URL'] . Page::getPageUrl($pageName);

        $this->resolvedCustomAttributes['url'] = str_replace($reference, $replacement, $url);
    }


    /**
     * Look up the value for EntityDataObjectName.Key and set it as the userInput attribute in the resolved custom
     * attributes.
     *
     * e.g. {{CustomerEntityFoo.FirstName}} becomes Jerry
     *
     * @return void
     */
    private function resolveUserInputReference()
    {
        if (!array_key_exists('userInput', $this->actionAttributes)) {
            return;
        }
        $userInput = $this->actionAttributes['userInput'];

        $reference = $this->findReference($userInput);
        if ($reference == null) {
            // Nothing to replace
            return;
        }

        list($entityName, $entityKey) = $this->stripAndSplitReference($userInput);
        $entityObj = DataManager::getInstance()->getEntity($entityName);
        if ($entityObj == null) {
            // Bad entity reference
            return;
        }

        $replacement = $entityObj->getDataByName($entityKey);
        if ($replacement == null) {
            // Bad entity.key reference
            return;
        }

        $this->resolvedCustomAttributes['userInput'] = str_replace($reference, $replacement, $userInput);
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
     * Return a {{reference.foo}} if it exists in the string.
     *
     * @param string $str
     * @return string|null
     */
    private function findReference($str)
    {
        preg_match('/{{[\w.]+}}/', $str, $matches);
        if (empty($matches)) {
            return null;
        } else {
            return $matches[0];
        }
    }
}
