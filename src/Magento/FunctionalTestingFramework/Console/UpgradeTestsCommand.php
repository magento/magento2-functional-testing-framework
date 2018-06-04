<?php
// @codingStandardsIgnoreFile
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Console\Input\InputArgument;

class UpgradeTestsCommand extends Command
{
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
        $testsPath = $input->getArgument('path');

        $fileSystem = new Filesystem();
        $finder = new Finder();
        $finder->files()->name('*.php')->in(realpath(FW_BP . '/upgrade/'));

        foreach ($finder->files() as $file) {
            $result = include($file->getRealPath());
            $output->writeln($result);
        }
    }
}
