<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Test\Objects;

/**
 * Class CestObject
 */
class CestObject
{
    /**
     * Name.
     *
     * @var string
     */
    private $name;

    /**
     * Hooks.
     *
     * @var array
     */
    private $hooks = [];

    /**
     * Annotations.
     *
     * @var array
     */
    private $annotations = [];

    /**
     * Tests.
     *
     * @var array
     */
    private $tests = [];

    /**
     * CestObject constructor.
     * @param string $name
     * @param array $annotations
     * @param array $tests
     * @param array $hooks
     */
    public function __construct($name, $annotations, $tests, $hooks)
    {
        $this->name = $name;
        $this->annotations = $annotations;
        $this->tests = $tests;
        $this->hooks = $hooks;
    }

    /**
     * Returns name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns annotations.
     *
     * @return array
     */
    public function getAnnotations()
    {
        return $this->annotations;
    }

    /**
     * Returns the value(s) of an annotation by a specific name such as group
     *
     * @param string $name
     * @return array
     */
    public function getAnnotationByName($name)
    {
        if (array_key_exists($name, $this->annotations)) {
            return $this->annotations[$name];
        }

        return [];
    }

    /**
     * Returns tests.
     *
     * @return array
     */
    public function getTests()
    {
        return $this->tests;
    }

    /**
     * Returns hooks.
     *
     * @return array
     */
    public function getHooks()
    {
        return $this->hooks;
    }
}
