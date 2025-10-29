<?php
/**
 * Copyright 2019 Adobe
 * All Rights Reserved.
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
     * @return void
     */
    public function execute(InputInterface $input);

    /**
     * Return array containing all errors found after running the execute() function.
     * @return array
     */
    public function getErrors();

    /**
     * Return string of a short human readable result of the check. For example: "No Dependency errors found."
     * @return string
     */
    public function getOutput();
}
