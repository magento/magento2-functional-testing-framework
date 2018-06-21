<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Upgrade;

/**
 * Class UpgradeScriptList has a list of scripts.
 * @codingStandardsIgnoreFile
 */
class UpgradeScriptList implements UpgradeScriptListInterface
{
    /**
     * Property contains all upgrade scripts.
     *
     * @var \Magento\FunctionalTestingFramework\Upgrade\UpgradeInterface[]
     */
    private $scripts;

    /**
     * Constructor
     *
     * @param array $scripts
     */
    public function __construct(array $scripts = [])
    {
        $this->scripts = [
            'upgradeTestSchema' => new UpdateTestSchemaPaths(),
        ] + $scripts;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpgradeScripts()
    {
        return $this->scripts;
    }
}
