<?php

declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Codeception\Module;

use Codeception\Module;
use Codeception\Exception\ModuleException;
use Codeception\TestInterface;

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
     * @phpcs:ignore
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
