<?php
namespace Magento\AcceptanceTestFramework\Page;

/**
 * Non magento pages.
 */
class ExternalPage extends Page
{
    /**
     * Init page. Set page url.
     *
     * @return void
     */
    protected function initUrl()
    {
        $this->url = static::MCA;
    }
}
