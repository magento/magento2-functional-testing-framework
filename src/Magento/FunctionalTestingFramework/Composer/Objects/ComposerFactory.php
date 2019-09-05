<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Composer\Objects;

use Composer\IO\BufferIO;

class ComposerFactory
{
    /**
     * Path to composer json
     *
     * @var string
     */
    private $composerFile;

    /**
     * ComposerFactory constructor
     *
     * @param string $composerFile
     */
    public function __construct($composerFile)
    {
        $this->composerFile = $composerFile;
    }

    /**
     * Create \Composer\Composer
     *
     * @return \Composer\Composer
     * @throws \Exception
     */
    public function create()
    {
        return \Composer\Factory::create(new BufferIO(), $this->composerFile);
    }
}
