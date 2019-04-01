<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\StaticCheck;

use Symfony\Component\Console\Input\InputInterface;

/**
 * Static check script interface
 */
interface StaticCheckInterface
{
    /**
     * Executes static check script, returns output.
     * @param InputInterface $input
     * @return string
     */
    public function execute(InputInterface $input);
}
