<?php
namespace Magento\AcceptanceTestFramework\Page;

use Magento\AcceptanceTestFramework\Page\Block\BlockInterface;

/**
 * Interface for Pages.
 */
interface PageInterface
{
    /**
     * Open the page URL in browser.
     *
     * @param array $params [optional]
     * @return $this
     */
    public function open(array $params = []);

    /**
     * Retrieve an instance of block.
     *
     * @param string $blockName
     * @return BlockInterface
     */
    public function getBlockInstance($blockName);
}
