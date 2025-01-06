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
    public static array $hash = [];

    /**
     * @var array<int|string,string>
     */
    public static array $suiteHash = [];

    public static string $prefix = '';

    /**
     * @var array<string, string>
     */
    protected array $config = ['prefix' => '{id}_'];

    public function _initialize(): void
    {
        static::$prefix = $this->config['prefix'];
    }

    public function _after(TestInterface $test): void
    {
        self::$hash = [];
    }

    public function _afterSuite(): void
    {
        self::$suiteHash = [];
    }
}
