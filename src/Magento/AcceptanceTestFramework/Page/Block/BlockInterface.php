<?php
namespace Magento\AcceptanceTestFramework\Page\Block;

/**
 * Interface for Blocks.
 */
interface BlockInterface
{
    /**
     * Check if the root element of the block is visible or not
     *
     * @return boolean
     */
    public function isVisible();
}
