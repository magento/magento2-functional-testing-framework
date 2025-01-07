<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Codeception\Module;

use Codeception\Module;
use Codeception\Exception\ModuleException;
use Codeception\TestInterface;

/**
 * Class Sequence
 * Implemented here as a replacement for codeception/module-sequence due to PHP 8.4 deprecation errors.
 * This class can be removed when PHP 8.4 compatibility is updated in codeception/module-sequence.
 */
class Sequence extends Module
{
    /**
     * @var array<int|string,string>
     */
    public static array $hash = [];// phpcs:ignore

    /**
     * @var array<int|string,string>
     */
    public static array $suiteHash = [];// phpcs:ignore

    /**
     * @var string
     */
    public static string $prefix = '';// phpcs:ignore

    /**
     * @var array<string, string>
     */
    protected array $config = ['prefix' => '{id}_'];// phpcs:ignore

    /**
     * Initialise method
     * @return  void
     */
    public function _initialize(): void
    {
        static::$prefix = $this->config['prefix'];
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * after method
     * @return  void
     */
    public function _after(TestInterface $test): void
    {
        self::$hash = [];
    }

    /**
     * after suite method
     * @return  void
     */
    public function _afterSuite(): void
    {
        self::$suiteHash = [];
    }
}
