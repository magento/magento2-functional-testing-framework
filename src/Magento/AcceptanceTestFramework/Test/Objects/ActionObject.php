<?php

namespace Magento\AcceptanceTestFramework\Test\Objects;

use Magento\AcceptanceTestFramework\PageObject\Page\Page;
use Magento\AcceptanceTestFramework\PageObject\Section\Section;

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
     * Resolves all references
     */
    public function resolveReferences()
    {
        if(empty($this->resolvedCustomAttributes)){
            $this->resolveSelectorReference();
            $this->resolveUrlReference();
        }
    }

    /**
     * Checks if selector is an attribute, and if the selector refers to a defined section.
     * If not, assume selector is CSS/xpath literal and leave it be.
     */
    private function resolveSelectorReference()
    {
        if(array_key_exists('selector', $this->actionAttributes)
            and array_key_exists(strtok($this->actionAttributes['selector'], '.'), Section::getSection()) ) {
            list($section, $element) = explode('.', $this->actionAttributes['selector']);
            $this->resolvedCustomAttributes['selector'] = Section::getElementLocator($section, $element);
            $this->timeout = Section::getElementTimeOut($section, $element);
        }
    }

    /**
     * Checks if url is an attribute, and if the url given is a defined page.
     * If not, assume url is literal and leave it be.
     */
    private function resolveUrlReference()
    {
        if (array_key_exists('url', $this->actionAttributes)
            and array_key_exists($this->actionAttributes['url'], Page::getPage())) {
            $this->resolvedCustomAttributes['url'] = $_ENV['MAGENTO_BASE_URL'] . Page::getPageUrl($this->actionAttributes['url']);
        }
    }
}