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
use Magento\FunctionalTestingFramework\Module\MagentoWebDriver;
use Magento\FunctionalTestingFramework\Module\MagentoWebDriverDoctor;

class DoctorCommand extends Command
{
    const CODECEPTION_AUTOLOAD_FILE = PROJECT_ROOT . '/vendor/codeception/codeception/autoload.php';
    const MFTF_CODECEPTION_CONFIG_FILE = ENV_FILE_PATH . 'codeception.yml';
    const SUITE = 'functional';

    /**
     * Command Output
     *
     * @var OutputInterface
     */
    private $output;

    /**
     * Exception Context
     *
     * @var array
     */
    private $context = [];

    /**
     * Configures the current command.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('doctor')
            ->setDescription(
                'This command checks environment readiness for generating and running MFTF tests.'
            );
    }

    /**
     * Executes the current command.
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return integer
     * @throws TestFrameworkException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cmdStatus = true;

        // Config application
        $verbose = $output->isVerbose();
        $this->output = $output;
        MftfApplicationConfig::create(
            false,
            MftfApplicationConfig::GENERATION_PHASE,
            $verbose,
            MftfApplicationConfig::LEVEL_DEVELOPER,
            false
        );

        // Check authentication to Magento Admin
        $status = $this->checkAuthenticationToMagentoAdmin();
        $cmdStatus = $cmdStatus && !$status ? false : $cmdStatus;

        // Check connection to Selenium
        $status = $this->checkConnectionToSeleniumServer();
        $cmdStatus = $cmdStatus && !$status ? false : $cmdStatus;

        // Check access to Magento CLI
        $status = $this->checkAccessToMagentoCLI();
        $cmdStatus = $cmdStatus && !$status ? false : $cmdStatus;

        if ($cmdStatus) {
            return 0;
        } else {
            return 1;
        }
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
     * Check Connection to Selenium Server
     *
     * @return boolean
     */
    private function checkConnectionToSeleniumServer()
    {
        // Check connection to Selenium through Codeception
        $this->output->writeln("\nChecking connection to Selenium Server ...");
        $this->runMagentoWebDriverDoctor();

        if (isset($this->context[MagentoWebDriverDoctor::EXCEPTION_TYPE_SELENIUM])) {
            $this->output->write($this->context[MagentoWebDriverDoctor::EXCEPTION_TYPE_SELENIUM] . "\n");
            return false;
        } else {
            $this->output->writeln('Successful');
            return true;
        }
    }

    /**
     * Check access to Magento CLI setup
     *
     * @return boolean
     */
    private function checkAccessToMagentoCLI()
    {
        // Check Magento CLI setup
        $this->output->writeln("\nChecking access to Magento CLI ...");
        $this->runMagentoWebDriverDoctor();

        if (isset($this->context[MagentoWebDriverDoctor::EXCEPTION_TYPE_MAGENTO_CLI])) {
            $this->output->write($this->context[MagentoWebDriverDoctor::EXCEPTION_TYPE_MAGENTO_CLI] . "\n");
            return false;
        } else {
            $this->output->writeln('Successful');
            return true;
        }
    }

    /**
     * Run diagnose through MagentoWebDriverDoctor
     *
     * @return void
     */
    private function runMagentoWebDriverDoctor()
    {
        if (!empty($this->context)) {
            return;
        }

        $magentoWebDriver = '\\' . MagentoWebDriver::class;
        $magentoWebDriverDoctor = '\\' . MagentoWebDriverDoctor::class;

        require_once realpath(self::CODECEPTION_AUTOLOAD_FILE);

        $config = Configuration::config(realpath(self::MFTF_CODECEPTION_CONFIG_FILE));
        $settings = Configuration::suiteSettings(self::SUITE, $config);

        // Enable MagentoWebDriverDoctor
        $settings['modules']['enabled'][] = $magentoWebDriverDoctor;
        $settings['modules']['config'][$magentoWebDriverDoctor] =
            $settings['modules']['config'][$magentoWebDriver];

        // Disable MagentoWebDriver to avoid conflicts
        foreach ($settings['modules']['enabled'] as $index => $module) {
            if ($module == $magentoWebDriver) {
                unset($settings['modules']['enabled'][$index]);
                break;
            }
        }
        unset($settings['modules']['config'][$magentoWebDriver]);

        $dispatcher = new EventDispatcher();
        $suiteManager = new SuiteManager($dispatcher, self::SUITE, $settings);
        try {
            $suiteManager->initialize();
            $this->context = ['Successful'];
        } catch (TestFrameworkException $e) {
            $this->context = $e->getContext();
        }
    }
}
