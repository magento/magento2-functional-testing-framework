<?php
namespace Magento\AcceptanceTestFramework\PageObject\Section;

/**
 * Interface for Section.
 */
interface SectionInterface
{
    const TYPE = 'section';

    /**
     * Get section object data. All sections data is returned if $name is not specified.
     *
     * @param string $sectionName [optional]
     * @return array
     */
    public static function getSection($sectionName = null);

    /**
     * Get element object data.
     *
     * @param string $sectionName
     * @param string $elementName
     * @return array
     */
    public static function getElement($sectionName, $elementName);

    /**
     * Get element object data. All sections data is returned if $name is not specified.
     *
     * @param string $sectionName
     * @param string $elementName
     * @return array
     */
    public static function getElementNamesInSection($sectionName, $elementName);

    /**
     * Get element locator.
     *
     * @param string $sectionName
     * @param string $elementName
     * @return string
     */
    public static function getElementLocator($sectionName, $elementName);

    /**
     * Get element type.
     *
     * @param string $sectionName
     * @param string $elementName
     * @return string
     */
    public static function getElementType($sectionName, $elementName);

    /**
     * Get element time out value.
     *
     * @param string $sectionName
     * @param string $elementName
     * @return integer|null
     */
    public static function getElementTimeOut($sectionName, $elementName);

    /**
     * Check if element requires an explicit wait.
     *
     * @param string $sectionName
     * @param string $elementName
     * @return bool
     */
    public static function isElementRequireWait($sectionName, $elementName);
}
