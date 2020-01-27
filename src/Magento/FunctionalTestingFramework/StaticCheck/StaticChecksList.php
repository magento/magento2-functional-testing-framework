<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\StaticCheck;

/**
 * Class StaticChecksList has a list of static checks to run on test xml
 * @codingStandardsIgnoreFile
 */
class StaticChecksList implements StaticCheckListInterface
{
    /**
     * Property contains all static check scripts.
     *
     * @var StaticCheckInterface[]
     */
    private $checks;

    /**
     * Constructor
     *
     * @param array $checks
     */
    public function __construct(array $checks = [])
    {
        $this->checks = [
            'testDependencies' => new TestDependencyCheck(),
            'actionGroupArguments' => new ActionGroupArgumentsCheck(),
        ] + $checks;
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticChecks()
    {
        return $this->checks;
    }
}
