<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Extension\ReadinessMetrics;

use Codeception\TestInterface;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\StaleElementReferenceException;
use Magento\FunctionalTestingFramework\Extension\PageReadinessExtension;
use Magento\FunctionalTestingFramework\Module\MagentoWebDriver;
use WebDriverBy;

/**
 * Class MagentoLoadingMasks
 *
 * Looks for all loading masks to disappear before passing the readiness check
 */
class MagentoLoadingMasks extends AbstractMetricCheck
{
    /**
     * Metric passes once all loading masks are absent or invisible
     *
     * @param string|null $value
     * @return bool
     */
    protected function doesMetricPass($value)
    {
        return is_null($value);
    }

    /**
     * Get the locator and ID for the first active loading mask or null if there are none visible
     *
     * @return string|null
     */
    protected function fetchValueFromPage()
    {
        foreach (MagentoWebDriver::$loadingMasksLocators as $maskLocator) {
            $driverLocator = WebDriverBy::xpath($maskLocator);
            $maskElements = $this->getDriver()->webDriver->findElements($driverLocator);
            foreach ($maskElements as $element) {
                try {
                    if ($element->isDisplayed()) {
                        return "$maskLocator : " . $element ->getID();
                    }
                }
                catch (NoSuchElementException $e) {}
                catch (StaleElementReferenceException $e) {}
            }
        }
        return null;
    }
}
