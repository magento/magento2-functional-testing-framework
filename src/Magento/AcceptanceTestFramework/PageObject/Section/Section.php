<?php
namespace Magento\AcceptanceTestFramework\PageObject\Section;

use Magento\AcceptanceTestFramework\ObjectManagerFactory;
use Magento\AcceptanceTestFramework\XmlParser\SectionParser;

class Section implements SectionInterface
{
    const SUB_TYPE = 'element';
    const LOCATOR_ATTR = 'locator';
    const LOCATOR_VARS_ATTR = 'locatorVariables';
    const TYPE_ATTR = 'type';
    const TIMEOUT_ATTR = 'timeOut';
    const DEFAULT_TIMEOUT_SAMBOL = '-';

    /**
     * @var array
     */
    private static $sectionObjects = [];

    /**
     * Get section object data. All sections data is returned if $name is not specified.
     *
     * @param string $sectionName [optional]
     * @return mixed
     */
    public static function getSection($sectionName = null)
    {
        self::initSectionObjects();
        return !$sectionName ? self::$sectionObjects : self::$sectionObjects[$sectionName];
    }

    /**
     * Get element object data.
     *
     * @param string $sectionName
     * @param string $elementName
     * @return mixed
     */
    public static function getElement($sectionName, $elementName)
    {
        return [$elementName => self::getSection($sectionName)[self::SUB_TYPE][$elementName]];
    }

    /**
     * Get element object data. All sections data is returned if $name is not specified.
     *
     * @param string $sectionName
     * @param string $elementName
     * @return mixed
     */
    public static function getElementNamesInSection($sectionName, $elementName)
    {
        return array_keys(self::getSection($sectionName)[self::SUB_TYPE]);
    }

    /**
     * Get element locator.
     *
     * @param string $sectionName
     * @param string $elementName
     * @return string|null
     */
    public static function getElementLocator($sectionName, $elementName)
    {
        $element = self::getSection($sectionName)[self::SUB_TYPE][$elementName];
        $locator = $element[self::LOCATOR_ATTR];
        if (isset($element[self::LOCATOR_VARS_ATTR])) {
            $locatorVarsStr = $element[self::LOCATOR_VARS_ATTR];
            $locatorVarsArray = explode(',', $locatorVarsStr);
            $newLocatorVarsArray = [];
            foreach ($locatorVarsArray as $variable) {
                $variable = trim($variable);
                if (substr($variable, 0, 1) !== '$') {
                    $variable = '$' . $variable;
                    $newLocatorVarsArray[] = $variable;
                }
            }
            $locatorVarsStr = implode(',', $newLocatorVarsArray);
            return 'sprintf("' . $locator . '", ' . $locatorVarsStr . ')';
        } else {
            return $locator;
        }
    }

    /**
     * Get element type.
     *
     * @param string $sectionName
     * @param string $elementName
     * @return string
     */
    public static function getElementType($sectionName, $elementName)
    {
        return self::getSection($sectionName)[self::SUB_TYPE][$elementName][self::TYPE_ATTR];
    }

    /**
     * Get element time out value.
     *
     * @param string $sectionName
     * @param string $elementName
     * @return integer|null
     */
    public static function getElementTimeOut($sectionName, $elementName)
    {
        if(self::isElementRequireWait($sectionName, $elementName)) {
            $timeOut = self::getSection($sectionName)[self::SUB_TYPE][$elementName][self::TIMEOUT_ATTR];
            if ($timeOut === self::DEFAULT_TIMEOUT_SAMBOL) {
                return null;
            } else {
                return (int)$timeOut;
            }
        }
        return null;
    }

    /**
     * Check if element requires an explicit wait.
     *
     * @param string $sectionName
     * @param string $elementName
     * @return bool
     */
    public static function isElementRequireWait($sectionName, $elementName)
    {
        return array_key_exists(self::TIMEOUT_ATTR, self::getSection($sectionName)[self::SUB_TYPE][$elementName]);
    }

    /**
     * Parse section objects if it's not previously done.
     *
     * @return void
     */
    private static function initSectionObjects()
    {
        if (empty(self::$sectionObjects)) {
            $objectManager = ObjectManagerFactory::getObjectManager();
            /** @var $parser \Magento\AcceptanceTestFramework\XmlParser\SectionParser */
            $parser = $objectManager->get(SectionParser::class);
            self::$sectionObjects = $parser->getData(self::TYPE);
        }
    }
}
