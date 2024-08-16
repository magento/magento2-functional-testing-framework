<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\StaticCheck;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;

/**
 * Class StaticChecksList has a list of static checks to run on test xml
 * @codingStandardsIgnoreFile
 */
class StaticChecksList implements StaticCheckListInterface
{
    const DEPRECATED_ENTITY_USAGE_CHECK_NAME = 'deprecatedEntityUsage';
    const PAUSE_ACTION_USAGE_CHECK_NAME = 'pauseActionUsage';
    const CREATED_DATA_FROM_OUTSIDE_ACTIONGROUP = 'createdDataFromOutsideActionGroup';
    const UNUSED_ENTITY_CHECK = 'unusedEntityCheck';

    const CLASS_FILE_NAMING_CHECK = 'classFileNamingCheck';

    const STATIC_RESULTS = 'tests' . DIRECTORY_SEPARATOR .'_output' . DIRECTORY_SEPARATOR . 'static-results';

    /**
     * Property contains all static check scripts.
     *
     * @var StaticCheckInterface[]
     */
    private $checks;

    /**
     * Directory path for static checks error files
     *
     * @var string
     */
    private static $errorFilesPath = null;

    /**
     * Constructor
     *
     * @param array $checks
     * @throws TestFrameworkException
     */
    public function __construct(array $checks = [])
    {
        $this->checks = [
            'testDependencies' => new TestDependencyCheck(),
            'actionGroupArguments' => new ActionGroupStandardsCheck(),
            self::DEPRECATED_ENTITY_USAGE_CHECK_NAME => new DeprecatedEntityUsageCheck(),
            'annotations' => new AnnotationsCheck(),
            self::PAUSE_ACTION_USAGE_CHECK_NAME => new PauseActionUsageCheck(),
            self::UNUSED_ENTITY_CHECK => new UnusedEntityCheck(),
            self::CREATED_DATA_FROM_OUTSIDE_ACTIONGROUP => new CreatedDataFromOutsideActionGroupCheck(), 
            self::CLASS_FILE_NAMING_CHECK => new ClassFileNamingCheck(),

                ] + $checks;

        // Static checks error files directory
        if (null === self::$errorFilesPath) {
            self::$errorFilesPath = FilePathFormatter::format(TESTS_BP) . self::STATIC_RESULTS;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getStaticChecks()
    {
        return $this->checks;
    }

    /**
     * Return the directory path for the static check error files
     */
    public static function getErrorFilesPath()
    {
        return self::$errorFilesPath;
    }

    /**
     * Return relative path to files for unit testing purposes.
     * @param string $fileNames
     * @return string
     */
    public static function getFilePath($fileNames)
    {
        if (!empty($fileNames)) {
            $relativeFileNames = ltrim(
                str_replace(MAGENTO_BP, '', $fileNames)
            );
            if (!empty($relativeFileNames)) {
                return $relativeFileNames;
            }
        }
        return $fileNames;
    }
}
