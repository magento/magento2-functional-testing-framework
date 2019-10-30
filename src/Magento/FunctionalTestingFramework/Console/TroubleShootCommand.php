<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Codeception\Configuration;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Codeception\SuiteManager;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;

class TroubleShootCommand extends Command
{
    const CODECEPTION_AUTOLOAD_FILE = PROJECT_ROOT . '/vendor/codeception/codeception/autoload.php';
    const MFTF_CODECEPTION_CONFIG_FILE = ENV_FILE_PATH . 'codeception.yml';
    const SUITE = 'functional';
    const REQUIRED_PHP_EXTS = ['CURL', 'mbstring', 'bcmath', 'zip', 'dom', 'gd', 'intl'];

    /**
     * Command Output
     *
     * @var OutputInterface
     */
    private $output;

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('troubleshoot')
            ->setDescription(
                'This command checks environment readiness for generating and running MFTF tests.'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return void
     * @throws TestFrameworkException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cmdStatus = true;

        // Config application
        $verbose = $output->isVerbose();
        $this->output = $output;
        MftfApplicationConfig::create(
            false,
            MftfApplicationConfig::DIAGNOSTIC_PHASE,
            $verbose,
            MftfApplicationConfig::LEVEL_DEVELOPER,
            false
        );

        // Check required PHP extensions
        foreach (self::REQUIRED_PHP_EXTS as $ext) {
            $status = $this->checkPhpExtIsAvailable($ext);
            $cmdStatus = $cmdStatus && !$status ? false : $cmdStatus;
        }

        // Check authentication to Magento Admin
        $status = $this->checkAuthenticationToMagentoAdmin();
        $cmdStatus = $cmdStatus && !$status ? false : $cmdStatus;

        // Check connectivity and authentication to Magento Admin
        $status = $this->checkConnectivityToSeleniumServer();
        $cmdStatus = $cmdStatus && !$status ? false : $cmdStatus;

        if ($cmdStatus) {
            exit(0);
        } else {
            exit(1);
        }
    }

    /**
     * Check php extention is installed and available
     *
     * @param string $ext
     * @return boolean
     */
    private function checkPhpExtIsAvailable($ext)
    {
        $result = false;
        $this->output->writeln("\nChecking PHP extenstion \"{$ext}\" ...");
        if (extension_loaded(strtolower($ext))) {
            $this->output->writeln('Successful');
            $result = true;
        } else {
            $this->output->writeln(
                "MFTF requires \"{$ext}\" extension installed to make tests run\n"
                . "Please make sure that the PHP you run has \"{$ext}\" installed and enabled."
            );
        }
        return $result;
    }

    /**
     * Check authentication to Magento Admin
     *
     * @return boolean
     */
    private function checkAuthenticationToMagentoAdmin()
    {
        $result = false;
        try {
            $this->output->writeln("\nChecking authentication to Magento Admin ...");
            ModuleResolver::getInstance()->getAdminToken();
            $this->output->writeln('Successful');
            $result = true;
        } catch (TestFrameworkException $e) {
            $this->output->writeln($e->getMessage());
        }
        return $result;
    }

    /**
     * Check Connectivity to Selenium Server
     *
     * @return boolean
     */
    private function checkConnectivityToSeleniumServer()
    {
        $result = false;

        // Check connectivity to Selenium through Codeception
        $this->output->writeln("\nChecking connectivity to Selenium Server ...");
        require_once realpath(self::CODECEPTION_AUTOLOAD_FILE);

        $config = Configuration::config(realpath(self::MFTF_CODECEPTION_CONFIG_FILE));
        $settings = Configuration::suiteSettings(self::SUITE, $config);
        $dispatcher = new EventDispatcher();
        $suiteManager = new SuiteManager($dispatcher, self::SUITE, $settings);
        try {
            $suiteManager->initialize();
            $this->output->writeln('Successful');
            $result = true;
        } catch (TestFrameworkException $e) {
            $this->output->writeln($e->getMessage());
        }
        return $result;
    }
}
