<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Util\Filesystem;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class CestFileCreatorUtil
{
    /**
     * Singleton CestFileCreatorUtil Instance.
     *
     * @var CestFileCreatorUtil
     */
    private static $INSTANCE;

    /**
     * CestFileCreatorUtil constructor.
     */
    private function __construct()
    {
    }

    /**
     * Get CestFileCreatorUtil instance.
     *
     * @return CestFileCreatorUtil
     */
    public static function getInstance(): CestFileCreatorUtil
    {
        if (!self::$INSTANCE) {
            self::$INSTANCE = new CestFileCreatorUtil();
        }

        return self::$INSTANCE;
    }

    /**
     * Create a single PHP file containing the $cestPhp using the $filename.
     * If the _generated directory doesn't exist it will be created.
     *
     * @param string $filename
     * @param string $exportDirectory
     * @param string $testPhp
     *
     * @return void
     * @throws TestFrameworkException
     */
    public function create(string $filename, string $exportDirectory, string $testPhp): void
    {
        DirSetupUtil::createGroupDir($exportDirectory);
        $exportFilePath = $exportDirectory . DIRECTORY_SEPARATOR . $filename . '.php';
        $file = fopen($exportFilePath, 'w');

        if (!$file) {
            throw new TestFrameworkException(
                sprintf('Could not open test file: "%s"', $exportFilePath)
            );
        }

        fwrite($file, $testPhp);
        fclose($file);
    }
}
