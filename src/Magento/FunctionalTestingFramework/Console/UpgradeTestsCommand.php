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
    private const SUCCESS_EXIT_CODE = 0;

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
            ->setDescription(
                'This command will upgrade MFTF tests according to new MFTF Major version requirements. '
                . 'It will upgrade MFTF tests in specific path when "path" argument is specified, otherwise it will '
                . 'upgrade all MFTF tests installed.'
            )
            ->addArgument('path', InputArgument::OPTIONAL, 'path to MFTF tests to upgrade');
        $this->upgradeScriptsList = new UpgradeScriptList();
    }

    /**
     *
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var \Magento\FunctionalTestingFramework\Upgrade\UpgradeInterface[] $upgradeScriptObjects */
        $upgradeScriptObjects = $this->upgradeScriptsList->getUpgradeScripts();
        foreach ($upgradeScriptObjects as $scriptName => $upgradeScriptObject) {
            $output->writeln('Running upgrade script: ' . $scriptName . PHP_EOL);
            $upgradeOutput = $upgradeScriptObject->execute($input, $output);
            LoggingUtil::getInstance()->getLogger(get_class($upgradeScriptObject))->info($upgradeOutput);
            $output->writeln($upgradeOutput . PHP_EOL);
        }

        return self::SUCCESS_EXIT_CODE;
    }
}
