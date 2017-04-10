<?php
namespace Magento\Xxyyzz\Helper;

/**
 * Class Acceptance
 *
 * Define global actions
 * All public methods declared in helper class will be available in $I
 */
class Acceptance extends \Codeception\Module
{
    /**
     * Reconfig WebDriver.
     *
     * @param string $config
     * @param string $value
     * @return void
     */
    public function changeConfiguration($config, $value)
    {
        $this->getModule('MagentoWebDriver')->_reconfigure(array($config => $value));
    }
}
