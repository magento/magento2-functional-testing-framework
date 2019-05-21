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
    const UNIT_TEST_PHASE = "testing";
    const MFTF_PHASES = [self::GENERATION_PHASE, self::EXECUTION_PHASE, self::UNIT_TEST_PHASE];

    /**
     * Mftf debug levels
     */
    const LEVEL_DEFAULT = "default";
    const LEVEL_DEVELOPER = "developer";
    const LEVEL_NONE = "none";
    const MFTF_DEBUG_LEVEL = [self::LEVEL_DEFAULT, self::LEVEL_DEVELOPER, self::LEVEL_NONE];

    /**
     * Determines whether the user has specified a force option for generation
     *
     * @var boolean
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
     * @var boolean
     */
    private $verboseEnabled;

    /**
     * String which identifies the current debug level of mftf execution
     *
     * @var string
     */
    private $debugLevel;

    /**
     * MftfApplicationConfig Singelton Instance
     *
     * @var MftfApplicationConfig
     */
    private static $MFTF_APPLICATION_CONTEXT;

    /**
     * MftfApplicationConfig constructor.
     *
     * @param boolean $forceGenerate
     * @param string  $phase
     * @param boolean $verboseEnabled
     * @param string  $debugLevel
     * @throws TestFrameworkException
     */
    private function __construct(
        $forceGenerate = false,
        $phase = self::EXECUTION_PHASE,
        $verboseEnabled = null,
        $debugLevel = self::LEVEL_NONE
    ) {
        $this->forceGenerate = $forceGenerate;

        if (!in_array($phase, self::MFTF_PHASES)) {
            throw new TestFrameworkException("{$phase} is not an mftf phase");
        }

        $this->phase = $phase;
        $this->verboseEnabled = $verboseEnabled;
        switch ($debugLevel) {
            case self::LEVEL_DEVELOPER:
            case self::LEVEL_DEFAULT:
            case self::LEVEL_NONE:
                $this->debugLevel = $debugLevel;
                break;
            default:
                $this->debugLevel = self::LEVEL_DEVELOPER;
        }
    }

    /**
     * Creates an instance of the configuration instance for reference once application has started. This function
     * returns void and is only run once during the lifetime of the application.
     *
     * @param boolean $forceGenerate
     * @param string  $phase
     * @param boolean $verboseEnabled
     * @param string  $debugLevel
     * @return void
     */
    public static function create($forceGenerate, $phase, $verboseEnabled, $debugLevel)
    {
        if (self::$MFTF_APPLICATION_CONTEXT == null) {
            self::$MFTF_APPLICATION_CONTEXT =
                new MftfApplicationConfig($forceGenerate, $phase, $verboseEnabled, $debugLevel);
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
     * @return boolean
     */
    public function forceGenerateEnabled()
    {
        return $this->forceGenerate;
    }

    /**
     * Returns a boolean indicating whether the user has indicated a verbose run, which will cause all applicable
     * text to print to the console.
     *
     * @return boolean
     */
    public function verboseEnabled()
    {
        return $this->verboseEnabled ?? getenv('MFTF_DEBUG');
    }

    /**
     * Returns a string which indicates the debug level of mftf execution.
     *
     * @return string
     */
    public function getDebugLevel()
    {
        return $this->debugLevel ?? getenv('MFTF_DEBUG');
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
