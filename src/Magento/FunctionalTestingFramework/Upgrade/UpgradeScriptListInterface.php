<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
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
