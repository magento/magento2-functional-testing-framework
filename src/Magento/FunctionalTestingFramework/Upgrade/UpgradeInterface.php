<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Upgrade;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Upgrade script interface
 */
interface UpgradeInterface
{
    /**
     * Executes upgrade script, returns output.
     * @param InputInterface $input
     * @return string
     */
    public function execute(InputInterface $input);
}
