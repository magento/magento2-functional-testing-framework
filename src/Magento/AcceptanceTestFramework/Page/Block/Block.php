<?php
namespace Magento\AcceptanceTestFramework\Page\Block;

use Magento\AcceptanceTestFramework\AcceptanceTester;
use Facebook\WebDriver\Remote\RemoteWebElement;

/**
 * Is used for any blocks on the page.
 * Classes which implement this interface are expected to provide public methods
 * to perform all possible interactions with the corresponding part of the page.
 * Blocks provide additional level of granularity of tests for business logic encapsulation
 * (extending Page Object concept).
 *
 * @abstract
 * @api
 */
abstract class Block implements BlockInterface
{
    /**
     * The root element of the block
     */
    protected $rootElement;

    /**
     * Codeception actor.
     *
     * @var AcceptanceTester
     */
    protected $acceptanceTester;

    /**
     * Page load timeout in seconds.
     *
     * @var string
     */
    protected $pageLoadTimeout;

    /**
     * @constructor
     * @param RemoteWebElement $element
     * @param AcceptanceTester $acceptanceTester
     */
    public function __construct(
        RemoteWebElement $element,
        AcceptanceTester $acceptanceTester
    ) {
        $this->rootElement = $element;
        $this->acceptanceTester = $acceptanceTester;
        $this->init();
    }

    /**
     * Initialize for children classes
     * @return void
     */
    protected function init()
    {
        //
    }

    /**
     * Check if the root element of the block is visible or not
     *
     * @return bool
     */
    public function isVisible()
    {
        try {
            $this->waitForElementVisible($this->rootElement);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Wait for element is visible in the block
     *
     * @param string $selector
     * @return bool|null
     */
    public function waitForElementVisible($selector)
    {
        return $this->acceptanceTester->waitForElementVisible($selector);
    }

    /**
     * Wait for element is not visible in the block
     *
     * @param string $selector
     * @return bool|null
     */
    public function waitForElementNotVisible($selector)
    {
        return $this->acceptanceTester->waitForElementVisible($selector);
    }
}
