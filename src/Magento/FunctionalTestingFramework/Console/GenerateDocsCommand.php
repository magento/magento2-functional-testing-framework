<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Util\DocGenerator;
use PhpParser\Comment\Doc;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateDocsCommand extends Command
{
    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('generate:docs')
            ->setDescription('This command generates documentation for created MFTF files.')
            ->addOption(
                "output",
                'o',
                InputOption::VALUE_REQUIRED,
                'Output Directory'
            )->addOption(
                "clean",
                'c',
                InputOption::VALUE_NONE,
                'Clean Output Directory'
            )->addOption(
                "force",
                'f',
                InputOption::VALUE_NONE,
                'Force Document Generation For All Action Groups'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     * @throws TestFrameworkException
     * @throws \Magento\FunctionalTestingFramework\Exceptions\TestReferenceException
     * @throws \Magento\FunctionalTestingFramework\Exceptions\XmlException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        defined('COMMAND') || define('COMMAND', 'generate:docs');
        $config = $input->getOption('output');
        $clean = $input->getOption('clean');
        $force = $input->getOption('force');

        MftfApplicationConfig::create(
            $force,
            MftfApplicationConfig::GENERATION_PHASE,
            false,
            MftfApplicationConfig::LEVEL_NONE,
            true
        );

        $allActionGroups = ActionGroupObjectHandler::getInstance()->getAllObjects();
        $docGenerator = new DocGenerator();
        $docGenerator->createDocumentation($allActionGroups, $config, $clean);

        $output->writeln("Generate Docs Command Run");

        if (empty($config)) {
            $output->writeln("Output to ". DocGenerator::DEFAULT_OUTPUT_DIR);
        } else {
            $output->writeln("Output to ". $config);
        }
    }
}
