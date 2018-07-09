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
     * Determines whether the user would like to execute mftf in a verbose run.
     *
     * @var boolean
     */
    private $debugEnabled;

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
     * @param boolean $debugEnabled
     * @throws TestFrameworkException
     */
    private function __construct(
        $forceGenerate = false,
        $phase = self::EXECUTION_PHASE,
        $verboseEnabled = null,
        $debugEnabled = null
    ) {
        $this->forceGenerate = $forceGenerate;

        if (!in_array($phase, self::MFTF_PHASES)) {
            throw new TestFrameworkException("{$phase} is not an mftf phase");
        }

        $this->phase = $phase;
        $this->verboseEnabled = $verboseEnabled;
        $this->debugEnabled = $debugEnabled;
    }

    /**
     * Creates an instance of the configuration instance for reference once application has started. This function
     * returns void and is only run once during the lifetime of the application.
     *
     * @param boolean $forceGenerate
     * @param string  $phase
     * @param boolean $verboseEnabled
     * @param boolean $debugEnabled
     * @return void
     */
    public static function create($forceGenerate, $phase, $verboseEnabled, $debugEnabled)
    {
        if (self::$MFTF_APPLICATION_CONTEXT == null) {
            self::$MFTF_APPLICATION_CONTEXT =
                new MftfApplicationConfig($forceGenerate, $phase, $verboseEnabled, $debugEnabled);
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
     * Returns a boolean indicating whether the user has indicated a debug run, which will lengthy validation
     * with some extra error messaging to be run
     *
     * @return boolean
     */
    public function debugEnabled()
    {
        return $this->debugEnabled ?? getenv('MFTF_DEBUG');
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
