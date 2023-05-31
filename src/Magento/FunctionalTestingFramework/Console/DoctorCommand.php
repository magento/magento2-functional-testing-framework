<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console;

use Codeception\Configuration;
use Magento\FunctionalTestingFramework\DataTransport\Auth\WebApiAuth;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Codeception\SuiteManager;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\FunctionalTestingFramework\Module\MagentoWebDriver;
use Magento\FunctionalTestingFramework\Module\MagentoWebDriverDoctor;
use Symfony\Component\Console\Style\SymfonyStyle;

class DoctorCommand extends Command
{
    const CODECEPTION_AUTOLOAD_FILE = PROJECT_ROOT . '/vendor/codeception/codeception/autoload.php';
    const MFTF_CODECEPTION_CONFIG_FILE = ENV_FILE_PATH . 'codeception.yml';
    const SUITE = 'functional';

    /**
     * Console output style
     *
     * @var SymfonyStyle
     */
    private $ioStyle;

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
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // For output style
        $this->ioStyle = new SymfonyStyle($input, $output);

        $cmdStatus = true;

        // Config application
        $verbose = $output->isVerbose();
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
        $status = $this->checkContextOnStep(
            MagentoWebDriverDoctor::EXCEPTION_CONTEXT_SELENIUM,
            'Connecting to Selenium Server'
        );
        $cmdStatus = $cmdStatus && !$status ? false : $cmdStatus;

        // Check opening Magento Admin in web browser
        $status = $this->checkContextOnStep(
            MagentoWebDriverDoctor::EXCEPTION_CONTEXT_ADMIN,
            'Loading Admin page'
        );
        $cmdStatus = $cmdStatus && !$status ? false : $cmdStatus;

        // Check opening Magento Storefront in web browser
        $status = $this->checkContextOnStep(
            MagentoWebDriverDoctor::EXCEPTION_CONTEXT_STOREFRONT,
            'Loading Storefront page'
        );
        $cmdStatus = $cmdStatus && !$status ? false : $cmdStatus;

        // Check access to Magento CLI
        $status = $this->checkContextOnStep(
            MagentoWebDriverDoctor::EXCEPTION_CONTEXT_CLI,
            'Running Magento CLI'
        );
        $cmdStatus = $cmdStatus && !$status ? false : $cmdStatus;

        return $cmdStatus ? 0 : 1;
    }

    /**
     * Check admin account authentication
     *
     * @return boolean
     */
    private function checkAuthenticationToMagentoAdmin()
    {
        $result = false;
        try {
            $this->ioStyle->text("Requesting API token for admin user through cURL ...");
            WebApiAuth::getAdminToken();
            $this->ioStyle->success('Successful');
            $result = true;
        } catch (TestFrameworkException $e) {
            if (getenv('MAGENTO_BACKEND_BASE_URL')) {
                $urlVar = 'MAGENTO_BACKEND_BASE_URL';
            } else {
                $urlVar = 'MAGENTO_BASE_URL';
            }
            $this->ioStyle->error(
                $e->getMessage() . "\nPlease verify if " . $urlVar . ", "
                . "MAGENTO_ADMIN_USERNAME and MAGENTO_ADMIN_PASSWORD in .env are valid."
            );
        }
        return $result;
    }

    /**
     * Check exception context after runMagentoWebDriverDoctor
     *
     * @param string $exceptionType
     * @param string $message
     * @return boolean
     * @throws TestFrameworkException
     */
    private function checkContextOnStep($exceptionType, $message)
    {
        $this->ioStyle->text($message . ' ...');
        $this->runMagentoWebDriverDoctor();

        if (isset($this->context[$exceptionType])) {
            $this->ioStyle->error($this->context[$exceptionType]);
            return false;
        } else {
            $this->ioStyle->success('Successful');
            return true;
        }
    }

    /**
     * Run diagnose through MagentoWebDriverDoctor
     *
     * @return void
     * @throws TestFrameworkException
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
            if ($module === $magentoWebDriver) {
                unset($settings['modules']['enabled'][$index]);
                break;
            }
        }
        unset($settings['modules']['config'][$magentoWebDriver]);

        $dispatcher = new EventDispatcher();
        $suiteManager = new SuiteManager($dispatcher, self::SUITE, $settings, []);
        try {
            $suiteManager->initialize();
            $this->context = ['Successful'];
        } catch (TestFrameworkException $e) {
            $this->context = $e->getContext();
        }
    }
}
