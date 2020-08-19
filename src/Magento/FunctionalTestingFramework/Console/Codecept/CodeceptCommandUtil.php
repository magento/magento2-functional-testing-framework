<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types = 1);

namespace Magento\FunctionalTestingFramework\Console\Codecept;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArgvInput;

class CodeceptCommandUtil
{
    const CODECEPTION_AUTOLOAD_FILE = PROJECT_ROOT . '/vendor/codeception/codeception/autoload.php';

    /**
     * Current working directory
     *
     * @var string
     */
    private $cwd = null;

    /**
     * Setup Codeception
     *
     * @param InputInterface $input
     * @return void
     */
    public function setup(InputInterface $input)
    {
        require_once realpath(self::CODECEPTION_AUTOLOAD_FILE);

        $tokens = preg_split('{\\s+}', $input->__toString());
        $tokens[0] = str_replace('codecept:', '', $tokens[0]);
        \Closure::bind(function &(ArgvInput $input) use ($tokens) {
            return $input->setTokens($tokens);
        }, null, ArgvInput::class);
    }

    /**
     * Save Codeception working directory
     *
     * @return void
     * @throws TestFrameworkException
     */
    public function setCodeceptCwd()
    {
        $this->cwd = getcwd();
        chdir(FilePathFormatter::format(TESTS_BP, false));
    }

    /**
     * Restore current working directory
     *
     * @return void
     */
    public function restoreCwd()
    {
        if ($this->cwd) {
            chdir($this->cwd);
        }
    }
}
