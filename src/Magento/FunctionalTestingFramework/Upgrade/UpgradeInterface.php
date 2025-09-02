<?php
/**
 * Copyright 2018 Adobe
 * All Rights Reserved.
 */

namespace Magento\FunctionalTestingFramework\Upgrade;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Upgrade script interface
 */
interface UpgradeInterface
{
    /**
     * Executes upgrade script, returns output.
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return string
     */
    public function execute(InputInterface $input, OutputInterface $output);
}
