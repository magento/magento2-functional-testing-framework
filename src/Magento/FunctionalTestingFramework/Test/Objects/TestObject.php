<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Objects;

use Magento\FunctionalTestingFramework\Test\Handlers\ActionGroupObjectHandler;
use Magento\FunctionalTestingFramework\Test\Util\ActionMergeUtil;
use Magento\FunctionalTestingFramework\Test\Util\ActionObjectExtractor;

/**
 * Class TestObject
 */
class TestObject
{
    const STEP_MISSING_ERROR_MSG =
        "Merge Error - Step could not be found in either TestXML or DeltaXML.
        \tTest = '%s'\tTestStep='%s'\tLinkedStep'%s'";

    /**
     * Name of the test
     *
     * @var string
     */
    private $name;

    /**
     * Array which contains steps parsed in and are the default set
     *
     * @var array
     */
    private $parsedSteps = [];

    /**
     * Array which contains annotations indexed by name
     *
     * @var array
     */
    private $annotations = [];

    /**
     * Array that contains test-defined data.
     * @var array
     */
    private $customData = [];

    /**
     * TestObject constructor.
     *
     * @param string $name
     * @param array $parsedSteps
     * @param array $annotations
     * @param array $customData
     */
    public function __construct($name, $parsedSteps, $annotations, $customData = null)
    {
        $this->name = $name;
        $this->parsedSteps = $parsedSteps;
        $this->annotations = $annotations;
        $this->customData = $customData;
    }

    /**
     * Getter for the Test Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Getter for the Test Annotations
     *
     * @return array
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * Getter for the custom data
     * @return array|null
     */
    public function getCustomData()
    {
        return $this->customData;
    }

    /**
     * This method calls a function to merge custom steps and returns the resulting ordered set of steps.
     *
     * @return array
     */
    public function getOrderedActions()
    {
        $mergeUtil = new ActionMergeUtil();
        return $mergeUtil->resolveActionSteps($this->parsedSteps);
    }
}
