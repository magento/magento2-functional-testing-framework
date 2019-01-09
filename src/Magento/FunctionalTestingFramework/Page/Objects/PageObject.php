<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Page\Objects;

use Magento\FunctionalTestingFramework\Exceptions\XmlException;
use Magento\FunctionalTestingFramework\Page\Handlers\SectionObjectHandler;

/**
 * Class PageObject
 */
class PageObject
{
    const ADMIN_AREA = 'admin';

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
     * String identifying the area the page belongs to
     *
     * @var string
     */
    private $area;

    /**
     * Filename of where the page came from
     *
     * @var string
     */
    private $filename;

    /**
     * PageObject constructor.
     * @param string  $name
     * @param string  $url
     * @param string  $module
     * @param array   $sections
     * @param boolean $parameterized
     * @param string  $area
     * @param string  $filename
     */
    public function __construct($name, $url, $module, $sections, $parameterized, $area, $filename = null)
    {
        $this->name = $name;
        $this->url = $url;
        $this->module = $module;
        $this->sectionNames = $sections;
        $this->parameterized = $parameterized;
        $this->area = $area;
        $this->filename = $filename;
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
     * Getter for the Page Filename
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
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
     * Getter for Page Area
     *
     * @return string
     */
    public function getArea()
    {
        return $this->area;
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
     * @throws XmlException
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
     * @return boolean
     */
    public function isParameterized()
    {
        return $this->parameterized;
    }
}
