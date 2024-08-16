<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Codeception\Subscriber;

use Codeception\Event\StepEvent;
use Codeception\Event\TestEvent;
use Codeception\Lib\Console\Message;
use Codeception\Step;
use Codeception\Step\Comment;
use Codeception\Test\Interfaces\ScenarioDriven;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Test\Objects\ActionObject;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * @SuppressWarnings(PHPMD)
 */
class Console extends \Codeception\Subscriber\Console
{
    /**
     * Regular expresion to find deprecated notices.
     */
    const DEPRECATED_NOTICE = '/<li>(?<deprecatedMessage>.*?)<\/li>/m';

    /**
     * Test files cache.
     *
     * @var array
     */
    private $testFiles = [];

    /**
     * Action group step key.
     *
     * @var null|string
     */
    private $actionGroupStepKey = null;

    /**
     * Boolean value to indicate if steps are invisible steps
     *
     * @var boolean
     */
    private $atInvisibleSteps = false;

    /**
     * Console constructor. Parent constructor requires codeception CLI options, and does not have its own configs.
     * Constructor is only different than parent due to the way Codeception instantiates Extensions.
     *
     * @param array $extensionOptions
     * @param array $options
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct($extensionOptions = [], $options = [])
    {
        parent::__construct($options);
    }

    /**
     * Triggered event before each test.
     *
     * @param TestEvent $e
     * @return void
     * @throws \Exception
     */
    public function startTest(TestEvent $e): void
    {
        $test = $e->getTest();
        $testReflection = new \ReflectionClass($test);

        try {
            $testReflection = new \ReflectionClass($test);
            $isDeprecated = preg_match_all(self::DEPRECATED_NOTICE, $testReflection->getDocComment(), $match);
            if ($isDeprecated) {
                $this->message('DEPRECATION NOTICE(S): ')
                    ->style('debug')
                    ->writeln();
                foreach ($match['deprecatedMessage'] as $deprecatedMessage) {
                    $this->message(' - ' . $deprecatedMessage)
                        ->style('debug')
                        ->writeln();
                }
            }
        } catch (\ReflectionException $e) {
            LoggingUtil::getInstance()->getLogger(self::class)->error($e->getMessage(), $e->getTrace());
        }

        parent::startTest($e);
    }

    /**
     * Printing stepKey in before step action.
     *
     * @param StepEvent $e
     * @return void
     */
    public function beforeStep(StepEvent $e): void
    {
        if ($this->silent or !$this->steps or !$e->getTest() instanceof ScenarioDriven) {
            return;
        }

        $stepAction = $e->getStep()->getAction();

        // Set atInvisibleSteps flag and return if step is in INVISIBLE_STEP_ACTIONS
        if (in_array($stepAction, ActionObject::INVISIBLE_STEP_ACTIONS)) {
            $this->atInvisibleSteps = true;
            return;
        }

        // Set back atInvisibleSteps flag
        if ($this->atInvisibleSteps && !in_array($stepAction, ActionObject::INVISIBLE_STEP_ACTIONS)) {
            $this->atInvisibleSteps = false;
        }

        $metaStep = $e->getStep()->getMetaStep();
        if ($metaStep and $this->metaStep !== $metaStep) {
            $this->message(' ' . $metaStep->getPrefix())
                ->style('bold')
                ->append($metaStep->__toString())
                ->writeln();
        }
        $this->metaStep = $metaStep;

        $this->printStepKeys($e->getStep());
    }

    /**
     * If step failed we move back from action group to test scope
     *
     * @param StepEvent $e
     * @return void
     */
    public function afterStep(StepEvent $e)
    {
        // Do usual after step if step is not INVISIBLE_STEP_ACTIONS
        if (!$this->atInvisibleSteps) {
            parent::afterStep($e);
        }

        if ($e->getStep()->hasFailed()) {
            $this->actionGroupStepKey = null;
            $this->atInvisibleSteps = false;
        }
    }

    /**
     * Print output to cli with stepKey.
     *
     * @param Step $step
     * @return void
     * @SuppressWarnings(PHPMD)
     */
    private function printStepKeys(Step $step)
    {
        if ($step instanceof Comment and $step->__toString() === '') {
            return; // don't print empty comments
        }

        $stepKey = $this->retrieveStepKey($step);

        $isActionGroup = (strpos($step->__toString(), ActionGroupObject::ACTION_GROUP_CONTEXT_START) !== false);
        if ($isActionGroup) {
            preg_match(TestGenerator::ACTION_GROUP_STEP_KEY_REGEX, $step->__toString(), $matches);
            if (!empty($matches['actionGroupStepKey'])) {
                $this->actionGroupStepKey = ucfirst($matches['actionGroupStepKey']);
            }
        }

        if (strpos($step->__toString(), ActionGroupObject::ACTION_GROUP_CONTEXT_END) !== false) {
            $this->actionGroupStepKey = null;
            return;
        }

        $msg = $this->message();
        if ($this->metaStep || ($this->actionGroupStepKey !== null && !$isActionGroup)) {
            $msg->append('  ');
        }
        if ($stepKey !== null) {
            $msg->append(OutputFormatter::escape("[" . $stepKey . "] "));
            $msg->style('bold');
        }

        if (!$this->metaStep) {
            $msg->style('bold');
        }

        $stepString = str_replace(
            [ActionGroupObject::ACTION_GROUP_CONTEXT_START, ActionGroupObject::ACTION_GROUP_CONTEXT_END],
            '',
            $step->toString(1000)
        );

        $msg->append(OutputFormatter::escape($stepString));
        if ($isActionGroup) {
            $msg->style('comment');
        }
        if ($this->metaStep || ($this->actionGroupStepKey !== null && !$isActionGroup)) {
            $msg->style('info');
        }
        $msg->writeln();
    }

    /**
     * Message instance.
     *
     * @param string $string
     * @return Message
     */
    private function message($string = '')
    {
        return $this->messageFactory->message($string);
    }

    /**
     * Reading stepKey from file.
     *
     * @param Step $step
     * @return string|null
     */
    private function retrieveStepKey(Step $step)
    {
        $stepKey = null;
        $stepLine = $step->getLineNumber();
        $filePath = $step->getFilePath();
        $stepLine = $stepLine - 1;

        if (!array_key_exists($filePath, $this->testFiles)) {
            $this->testFiles[$filePath] = explode(PHP_EOL, file_get_contents($filePath));
        }

        preg_match(TestGenerator::ACTION_STEP_KEY_REGEX, $this->testFiles[$filePath][$stepLine], $matches);
        if (!empty($matches['stepKey'])) {
            $stepKey = $matches['stepKey'];
        }

        if ($this->actionGroupStepKey !== null) {
            $stepKey = str_replace($this->actionGroupStepKey, '', $stepKey);
        }

        $stepKey = $stepKey === '[]' ? null : $stepKey;

        return $stepKey;
    }
}
