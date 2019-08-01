<?php
// @codingStandardsIgnoreFile
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\StaticCheck\StaticChecksList;
use Magento\FunctionalTestingFramework\StaticCheck\StaticCheckListInterface;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Exception;

class StaticChecksCommand extends Command
{
    /**
     * Pool of static check scripts to run
     *
     * @var StaticCheckListInterface
     */
    private $staticChecksList;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('static-checks')
            ->setDescription('This command will run all static checks on xml test materials.');
        $this->staticChecksList = new StaticChecksList();
    }

    /**
     *
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $staticCheckObjects = $this->staticChecksList->getStaticChecks();

        $errors = [];

        foreach ($staticCheckObjects as $staticCheck) {
            $staticCheck->execute($input);

            $staticOutput = $staticCheck->getOutput();
            LoggingUtil::getInstance()->getLogger(get_class($staticCheck))->info($staticOutput);
            $output->writeln($staticOutput);
            $errors += $staticCheck->getErrors();
        }

        if (empty($errors)) {
            return 0;
        } else {
            return 1;
        }
    }
}
