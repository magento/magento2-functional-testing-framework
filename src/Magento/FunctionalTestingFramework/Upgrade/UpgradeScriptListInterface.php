<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Upgrade;

/**
 * Contains a list of Upgrade Scripts
 * @api
 */
interface UpgradeScriptListInterface
{
    /**
     * Gets list of upgrade script instances
     *
     * @return \Magento\FunctionalTestingFramework\Upgrade\UpgradeInterface[]
     */
    public function getUpgradeScripts();
}
