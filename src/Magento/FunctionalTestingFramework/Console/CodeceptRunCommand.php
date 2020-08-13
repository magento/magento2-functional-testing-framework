<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Codeception\Command\Run;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\FunctionalTestingFramework\Console\Codecept\CodeceptCommandUtil;

class CodeceptRunCommand extends Run
{
    /**
     * Configures the current command
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('codecept:run')
            ->setDescription(
                "Wrapper command to vendor/bin/codecept:run. See https://codeception.com/docs/reference/Commands#Run"
            );

        parent::configure();
    }

    /**
     * Executes the current command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return integer
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $commandUtil = new CodeceptCommandUtil();
        $commandUtil->setup($input);
        $commandUtil->setCodeceptCwd();

        try {
            $exitCode = parent::execute($input, $output);
        } catch (\Exception $e) {
            throw new TestFrameworkException(
                'Make sure cest files are generated before running bin/mftf '
                . $this->getName()
                . PHP_EOL
                . $e->getMessage()
            );
        }

        $commandUtil->restoreCwd();
        return $exitCode;
    }
}
