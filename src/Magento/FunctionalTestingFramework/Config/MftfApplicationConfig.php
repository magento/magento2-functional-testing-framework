<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Config;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class MftfApplicationConfig
{
    const GENERATION_PHASE = "generation";
    const EXECUTION_PHASE = "execution";
    const MFTF_PHASES = [self::GENERATION_PHASE, self::EXECUTION_PHASE];

    /**
     * Determines whether the user has specified a force option for generation
     *
     * @var bool
     */
    private $forceGenerate;

    /**
     * String which identifies the current phase of mftf execution
     *
     * @var string
     */
    private $phase;

    /**
     * Determines whether the user would like to execute mftf in a verbose run.
     *
     * @var bool
     */
    private $verboseEnabled;

    /**
     * MftfApplicationConfig Singelton Instance
     *
     * @var MftfApplicationConfig
     */
    private static $MFTF_APPLICATION_CONTEXT;

    /**
     * MftfApplicationConfig constructor.
     *
     * @param bool $forceGenerate
     * @param string $phase
     * @param bool $verboseEnabled
     * @throws TestFrameworkException
     */
    private function __construct($forceGenerate = false, $phase = self::EXECUTION_PHASE, $verboseEnabled = null)
    {
        $this->forceGenerate = $forceGenerate;

        if (!in_array($phase, self::MFTF_PHASES)) {
            throw new TestFrameworkException("{$phase} is not an mftf phase");
        }

        $this->phase = $phase;
        $this->verboseEnabled = $verboseEnabled;
    }

    /**
     * Creates an instance of the configuration instance for reference once application has started. This function
     * returns void and is only run once during the lifetime of the application.
     *
     * @param bool $forceGenerate
     * @param string $phase
     * @param bool $verboseEnabled
     * @return void
     */
    public static function create($forceGenerate, $phase, $verboseEnabled)
    {
        if (self::$MFTF_APPLICATION_CONTEXT == null) {
            self::$MFTF_APPLICATION_CONTEXT = new MftfApplicationConfig($forceGenerate, $phase, $verboseEnabled);
        }
    }

    /**
     * This function returns an instance of the MftfApplicationConfig which is created once the application starts.
     *
     * @return MftfApplicationConfig
     */
    public static function getConfig()
    {
        // TODO explicitly set this with AcceptanceTester or MagentoWebDriver
        // during execution we cannot guarantee the use of the robofile so we return the default application config,
        // we don't want to set the application context in case the user explicitly does so at a later time.
        if (self::$MFTF_APPLICATION_CONTEXT == null) {
            return new MftfApplicationConfig();
        }

        return self::$MFTF_APPLICATION_CONTEXT;
    }

    /**
     * Returns a booelan indiciating whether or not the user has indicated a forced generation.
     *
     * @return bool
     */
    public function forceGenerateEnabled()
    {
        return $this->forceGenerate;
    }

    /**
     * Returns a boolean indicating whether the user has indicated a verbose run, which will cause all applicable
     * text to print to the console.
     *
     * @return bool
     */
    public function verboseEnabled()
    {
        return $this->verboseEnabled ?? getenv('MFTF_DEBUG');
    }

    /**
     * Returns a string which indicates the phase of mftf execution.
     *
     * @return string
     */
    public function getPhase()
    {
        return $this->phase;
    }
}
