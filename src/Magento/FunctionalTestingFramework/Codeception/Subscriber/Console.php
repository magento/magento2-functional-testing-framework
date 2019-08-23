<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Codeception\Subscriber;

use Codeception\Event\StepEvent;
use Codeception\Lib\Console\Message;
use Codeception\Step;
use Codeception\Step\Comment;
use Codeception\Test\Interfaces\ScenarioDriven;
use Magento\FunctionalTestingFramework\Test\Objects\ActionGroupObject;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Symfony\Component\Console\Formatter\OutputFormatter;

class Console extends \Codeception\Subscriber\Console
{
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
     * Console constructor. Parent constructor requires codeception CLI options, and does not have its own configs.
     * Constructor is only different than parent due to the way Codeception instantiates Extensions.
     *
     * @param array $extensionOptions
     * @param array $options
     *
     * @SuppressWarnings(PHPMD)
     */
    public function __construct($extensionOptions = [], $options = [])
    {
        parent::__construct($options);
    }

    /**
     * Printing stepKey in before step action.
     *
     * @param StepEvent $e
     * @return void
     */
    public function beforeStep(StepEvent $e)
    {
        if ($this->silent or !$this->steps or !$e->getTest() instanceof ScenarioDriven) {
            return;
        }

        $metaStep = $e->getStep()->getMetaStep();
        if ($metaStep and $this->metaStep != $metaStep) {
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
        parent::afterStep($e);
        if ($e->getStep()->hasFailed()) {
            $this->actionGroupStepKey = null;
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
        if ($step instanceof Comment and $step->__toString() == '') {
            return; // don't print empty comments
        }

        $stepKey = $this->retrieveStepKey($step->getLine());

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
            $step->toString(150)
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
     * @param string $stepLine
     * @return string|null
     */
    private function retrieveStepKey($stepLine)
    {
        $stepKey = null;
        list($filePath, $stepLine) = explode(":", $stepLine);
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
