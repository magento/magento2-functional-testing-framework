<?php

namespace Magento\AcceptanceTestFramework\Page\Objects;


use Magento\AcceptanceTestFramework\Page\Managers\SectionObjectHandler;

class PageObject
{
    /**
     * Page name
     * @var string $name
     */
    private $name;

    /**
     * Page url
     * @var string $url
     */
    private $url;

    /**
     * Page module
     * @var string $module
     */
    private $module;

    /**
     * Array of page section names
     * @var array $sectionNames
     */
    private $sectionNames = [];

    /**
     * PageObject constructor.
     * @constructor
     * @param string $name
     * @param string $urlPath
     * @param string $module
     * @param array $sections
     */
    public function __construct($name, $urlPath, $module, $sections)
    {
        $this->name = $name;
        $this->url = $urlPath;
        $this->module = $module;
        $this->sectionNames = $sections;
    }

    /**
     * Getter for Page Name
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Getter for Page URL
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Getter for Page Module
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Getter for Section Names
     * @return array
     */
    public function getSectionNames()
    {
        return $this->sectionNames;
    }

    /**
     * Checks the section names in the page for existence of the section name passed into the method.
     * @param string $sectionName
     * @return boolean
     */
    public function hasSection($sectionName)
    {
        return in_array($sectionName, $this->sectionNames);
    }

    /**
     * Given a section name referenced by the page, returns the section object
     * @param $sectionName
     * @return SectionObject | null
     */
    public function getSection($sectionName)
    {
        if ($this->hasSection($sectionName)) {
            return SectionObjectHandler::getInstance()->getObject($sectionName);
        }

        return null;
    }
}
