<?php
// @codingStandardsIgnoreFile
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\Widget\Model\Layout\UpdateTest;
use Magento\FunctionalTestingFramework\Upgrade;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class UpgradeTestsCommand extends Command
{
    private $upgradePool = [
        'UpdateTestSchemaPaths'
    ];

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('upgrade:tests')
            ->setDescription('This command will upgrade all tests in the provided path according to new MFTF Major version requirements.')
            ->addArgument('path', InputArgument::REQUIRED, 'path to MFTF tests to upgrade');
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return void
     * @throws \Symfony\Component\Console\Exception\LogicException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->upgradePool as $upgradeClass) {
            $fullClassName = 'Magento\FunctionalTestingFramework\Upgrade\\' . $upgradeClass;
            $upgrade = new $fullClassName();
            $upgradeOutput = $upgrade->execute($input);
            LoggingUtil::getInstance()->getLogger(GenerateDevUrnCommand::class)->info($upgradeOutput);
            $output->writeln($upgradeOutput);
        }
    }
}
