<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace tests\unit\Util;

use Magento\FunctionalTestingFramework\Test\Util\ActionGroupObjectExtractor;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;

class ActionGroupArrayBuilder
{
    const DEFAULT_ACTION_GROUP_KEY = 'actionGroupStepKey';

    /**
     * Action group name
     *
     * @var string
     */
    private $name = "testActionGroup";

    /**
     * Action group actions (default value set by constructor)
     *
     * @var array
     */
    private $actionObjects = [];

    /**
     * Action group annotations
     *
     * @var array
     */
    private $annotations = [];

    /**
     * Action group arguments
     *
     * @var array
     */
    private $arguments = [];

    /**
     * Action group extends name
     *
     * @var string
     */
    private $extends = null;

    /**
     * Action group filename
     *
     * @var string
     */
    private $filename = '';

    /**
     * Setter for action group name
     *
     * @param string $name
     * @return $this
     */
    public function withName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Setter for action group annotations
     *
     * @param array $annotations
     * @return $this
     */
    public function withAnnotations($annotations = [])
    {
        $this->annotations = $annotations;
        return $this;
    }

    /**
     * Setter for action group arguments
     *
     * @param array $args
     * @return $this
     */
    public function withArguments($args = [])
    {
        $this->arguments = $args;
        return $this;
    }

    /**
     * Setter for action group actions
     *
     * @param array $actionObjs
     * @return $this
     */
    public function withActionObjects($actionObjs = [])
    {
        if (!empty($actionObjs)) {
            $this->actionObjects = $actionObjs;
        }
        return $this;
    }

    /**
     * Setter for action group extended action group name
     *
     * @param string $extendedActionGroup
     * @return $this
     */
    public function withExtendedAction(?string $extendedActionGroup = null)
    {
        $this->extends = $extendedActionGroup;
        return $this;
    }

    /**
     * Setter for action group filename
     *
     * @param string $filename
     * @return $this
     */
    public function withFilename($filename = '')
    {
        if (empty($filename)) {
            $this->filename = "/magento2-functional-testing-framework/dev/tests/verification/"
                . "TestModule/ActionGroup/BasicActionGroup.xml";
        } else {
            $this->filename = $filename;
        }

        return $this;
    }

    /**
     * ActionGroupArrayBuilder constructor
     */
    public function __construct()
    {
        $this->actionObjects = [
            self::DEFAULT_ACTION_GROUP_KEY => [
                ActionObjectExtractor::NODE_NAME => 'testActionType',
                ActionObjectExtractor::TEST_STEP_MERGE_KEY => self::DEFAULT_ACTION_GROUP_KEY,
            ]
        ];
    }

    /**
     * Function which takes builder parameters and returns an action group array
     *
     * @return array
     */
    public function build()
    {
        // Build and return action group array
        return [$this->name => array_merge(
            [
                ActionGroupObjectExtractor::NAME => $this->name,
                ActionGroupObjectExtractor::ACTION_GROUP_ANNOTATIONS => $this->annotations,
                ActionGroupObjectExtractor::ACTION_GROUP_ARGUMENTS => $this->arguments,
                ActionGroupObjectExtractor::FILENAME => $this->filename,
                ActionGroupObjectExtractor::EXTENDS_ACTION_GROUP => $this->extends
            ],
            $this->actionObjects
        )];
    }
}
