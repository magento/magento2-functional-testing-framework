<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\AcceptanceTestFramework\Page\Objects;


use Magento\AcceptanceTestFramework\Page\Handlers\SectionObjectHandler;

/**
 * Class PageObject
 */
class PageObject
{
    /**
     * Page name
     *
     * @var string
     */
    private $name;

    /**
     * Page url
     *
     * @var string
     */
    private $url;

    /**
     * Page module
     *
     * @var string
     */
    private $module;

    /**
     * Page url is parameterized
     *
     * @var bool $parameterized
     */
    private $parameterized;

    /**
     * Array of page section names
     *
     * @var array
     */
    private $sectionNames = [];

    /**
     * PageObject constructor.
     * @param string $name
     * @param string $urlPath
     * @param string $module
     * @param array $sections
     * @param bool $parameterized
     */
    public function __construct($name, $urlPath, $module, $sections, $parameterized)
    {
        $this->name = $name;
        $this->url = $urlPath;
        $this->module = $module;
        $this->sectionNames = $sections;
        $this->parameterized = $parameterized;
    }

    /**
     * Getter for Page Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Getter for Page URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Getter for Page Module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Getter for Section Names
     *
     * @return array
     */
    public function getSectionNames()
    {
        return $this->sectionNames;
    }

    /**
     * Checks the section names in the page for existence of the section name passed into the method.
     *
     * @param string $sectionName
     * @return boolean
     */
    public function hasSection($sectionName)
    {
        return in_array($sectionName, $this->sectionNames);
    }

    /**
     * Given a section name referenced by the page, returns the section object
     *
     * @param string $sectionName
     * @return SectionObject | null
     */
    public function getSection($sectionName)
    {
        if ($this->hasSection($sectionName)) {
            return SectionObjectHandler::getInstance()->getObject($sectionName);
        }

        return null;
    }

    /**
     * Determines if the page's url is parameterized. Based on $parameterized property.
     *
     * @return bool
     */

    public function isParameterized()
    {
        return $this->parameterized;
    }
}
