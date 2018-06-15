<?php
// @codingStandardsIgnoreFile
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Upgrade\UpgradeScriptList;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class UpgradeTestsCommand extends Command
{
    /**
     * Pool of upgrade scripts to run
     *
     * @var \Magento\FunctionalTestingFramework\Upgrade\UpgradeScriptListInterface
     */
    private $upgradeScriptsList;

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
        $this->upgradeScriptsList = new UpgradeScriptList();
    }

    /**
     *
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var \Magento\FunctionalTestingFramework\Upgrade\UpgradeInterface[] $upgradeScriptObjects */
        $upgradeScriptObjects = $this->upgradeScriptsList->getUpgradeScripts();
        foreach ($upgradeScriptObjects as $upgradeScriptObject) {
            $upgradeOutput = $upgradeScriptObject->execute($input);
            LoggingUtil::getInstance()->getLogger(get_class($upgradeScriptObject))->info($upgradeOutput);
            $output->writeln($upgradeOutput);
        }
    }
}
