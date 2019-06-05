<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Codeception\Subscriber;

use Codeception\Event\FailEvent;
use Codeception\Event\PrintResultEvent;
use Codeception\Event\StepEvent;
use Codeception\Event\SuiteEvent;
use Codeception\Event\TestEvent;
use Codeception\Events;
use Codeception\Lib\Console\Message;
use Codeception\Lib\Console\MessageFactory;
use Codeception\Lib\Console\Output;
use Codeception\Lib\Notification;
use Codeception\Step;
use Codeception\Step\Comment;
use Codeception\Suite;
use Codeception\Test\Descriptor;
use Codeception\Test\Interfaces\ScenarioDriven;
use Codeception\Util\Debug;
use Magento\FunctionalTestingFramework\Util\TestGenerator;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class Console extends \Codeception\Subscriber\Console
{
    /**
     * Test files cache.
     *
     * @var array
     */
    private $testFiles = [];

    /**
     * Printing stepKey in before step action.
     *
     * @param StepEvent $e
     * @return void
     */
    public function beforeStep(StepEvent $e)
    {
        if (!$this->steps or !$e->getTest() instanceof ScenarioDriven) {
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
     * Print output to cli with stepKey.
     *
     * @param Step $step
     * @return void
     */
    private function printStepKeys(Step $step)
    {
        if ($step instanceof Comment and $step->__toString() == '') {
            return; // don't print empty comments
        }

        $stepKey = $this->retrieveStepKey($step->getLine());

        $msg = $this->message(' ');
        if ($this->metaStep) {
            $msg->append('  ');
        }
        if ($stepKey !== null) {
            $msg->append(OutputFormatter::escape("[" . $stepKey . "] "));
        }

        if (!$this->metaStep) {
            $msg->style('bold');
        }

        $msg->append(OutputFormatter::escape($step->toString($this->width)));
        if ($this->metaStep) {
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
        $testLineTrimmed = substr(
            $this->testFiles[$filePath][$stepLine],
            strpos($this->testFiles[$filePath][$stepLine], '//')
        );

        list($stepKey) = sscanf($testLineTrimmed, TestGenerator::STEP_KEY_ANNOTATION);

        return $stepKey;
    }
}
