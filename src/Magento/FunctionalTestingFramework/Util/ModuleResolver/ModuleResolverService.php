<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Util\ModuleResolver;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\DataTransport\Auth\WebApiAuth;
use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\ComposerModuleResolver;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;

class ModuleResolverService
{
    /**
     * Singleton ModuleResolverCreator Instance.
     *
     * @var ModuleResolverService
     */
    private static $INSTANCE;

    /**
     * Composer json based test module paths.
     *
     * @var array
     */
    private $composerJsonModulePaths = null;

    /**
     * Composer installed test module paths.
     *
     * @var array
     */
    private $composerInstalledModulePaths = null;

    /**
     * ModuleResolverService constructor.
     */
    private function __construct()
    {
    }

    /**
     * Get ModuleResolverCreator instance.
     *
     * @return ModuleResolverService
     */
    public static function getInstance()
    {
        if (self::$INSTANCE === null) {
            self::$INSTANCE = new ModuleResolverService();
        }

        return self::$INSTANCE;
    }

    /**
     * Calls Magento method for determining registered modules.
     *
     * @return string[]
     * @throws TestFrameworkException
     */
    public function getRegisteredModuleList(): array
    {
        if (!empty($this->registeredModuleList)) {
            return $this->registeredModuleList;
        }

        if (array_key_exists('MAGENTO_BP', $_ENV)) {
            $autoloadPath = realpath(MAGENTO_BP . "/app/autoload.php");

            if ($autoloadPath) {
                require_once($autoloadPath);
            } else {
                throw new TestFrameworkException(
                    "Magento app/autoload.php not found with given MAGENTO_BP:" . MAGENTO_BP
                );
            }
        }

        try {
            $allComponents = [];

            if (!class_exists(ModuleResolver::REGISTRAR_CLASS)) {
                throw new TestFrameworkException("Magento Installation not found when loading registered modules.\n");
            }

            $components = new ComponentRegistrar();

            foreach (ModuleResolver::PATHS as $componentType) {
                $allComponents = array_merge($allComponents, $components->getPaths($componentType));
            }

            array_walk($allComponents, function (&$value) {
                // Magento stores component paths with unix DIRECTORY_SEPARATOR, need to stay uniform and convert
                $value = realpath($value);
                $value .= DIRECTORY_SEPARATOR . ModuleResolver::TEST_MFTF_PATTERN;
            });

            return $allComponents;
        } catch (TestFrameworkException $exception) {
            LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->warning("$exception");
        }
        return [];
    }

    /**
     * Function which takes a code path and a pattern and determines if there are any matching subdir paths. Matches
     * are returned as an associative array keyed by basename (the last dir excluding pattern) to an array containing
     * the matching path.
     *
     * @param string $testPath
     * @param string $pattern
     *
     * @return array
     * @throws TestFrameworkException
     */
    public function globRelevantPaths(string $testPath, string $pattern): array
    {
        $modulePaths = [];
        $relevantPaths = [];

        if (file_exists($testPath)) {
            $relevantPaths = $this->globRelevantWrapper($testPath, $pattern);
        }

        foreach ($relevantPaths as $codePath) {
            $potentialSymlink = str_replace(DIRECTORY_SEPARATOR . $pattern, "", $codePath);

            if (is_link($potentialSymlink)) {
                $codePath = realpath($potentialSymlink) . DIRECTORY_SEPARATOR . $pattern;
            }

            $mainModName = basename(str_replace($pattern, '', $codePath));
            $modulePaths[$codePath] = [$mainModName];

            if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
                LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->debug(
                    "including module",
                    ['module' => $mainModName, 'path' => $codePath]
                );
            }
        }

        return $modulePaths;
    }

    /**
     * Glob wrapper for globRelevantPaths function.
     *
     * @param string $testPath
     * @param string $pattern
     *
     * @return array
     */
    private static function globRelevantWrapper(string $testPath, string $pattern): array
    {
        if ($pattern === '') {
            return glob($testPath . '*' . DIRECTORY_SEPARATOR . '*' . $pattern);
        }

        $subDirectory = '*' . DIRECTORY_SEPARATOR;
        $directories = glob($testPath . $subDirectory . $pattern, GLOB_ONLYDIR);

        foreach (glob($testPath . $subDirectory, GLOB_ONLYDIR) as $dir) {
            $directories = array_merge_recursive($directories, self::globRelevantWrapper($dir, $pattern));
        }

        return $directories;
    }

    /**
     * Retrieve all module code paths that have test module composer json files.
     *
     * @param array $codePaths
     *
     * @return array
     */
    public function getComposerJsonTestModulePaths(array $codePaths): array
    {
        if (null !== $this->composerJsonModulePaths) {
            return $this->composerJsonModulePaths;
        }

        try {
            $this->composerJsonModulePaths = [];
            $resolver = new ComposerModuleResolver();
            $this->composerJsonModulePaths = $resolver->getTestModulesFromPaths($codePaths);
        } catch (TestFrameworkException $e) {
        }

        return $this->composerJsonModulePaths;
    }

    /**
     * Retrieve composer installed test module code paths.
     *
     * @param string $composerFile
     *
     * @return array
     */
    public function getComposerInstalledTestModulePaths(string $composerFile): array
    {
        if (null !== $this->composerInstalledModulePaths) {
            return $this->composerInstalledModulePaths;
        }

        try {
            $this->composerInstalledModulePaths = [];
            $resolver = new ComposerModuleResolver();
            $this->composerInstalledModulePaths = $resolver->getComposerInstalledTestModules($composerFile);
        } catch (TestFrameworkException $e) {
        }

        return $this->composerInstalledModulePaths;
    }

    /**
     * Retrieves all module directories which might contain pertinent test code.
     *
     * @return array
     * @throws TestFrameworkException
     */
    public function aggregateTestModulePaths(): array
    {
        $allModulePaths = [];

        // Define the Module paths from magento bp
        $magentoBaseCodePath = FilePathFormatter::format(MAGENTO_BP, false);

        // Define the Module paths from default TESTS_MODULE_PATH
        $modulePath = defined('TESTS_MODULE_PATH') ? TESTS_MODULE_PATH : TESTS_BP;
        $modulePath = FilePathFormatter::format($modulePath, false);

        // If $modulePath is DEV_TESTS path, we don't need to search by pattern
        if (strpos($modulePath, ModuleResolver::DEV_TESTS) === false) {
            $codePathsToPattern[$modulePath] = '';
        }

        $vendorCodePath = DIRECTORY_SEPARATOR . ModuleResolver::VENDOR;
        $codePathsToPattern[$magentoBaseCodePath . $vendorCodePath] = ModuleResolver::TEST_MFTF_PATTERN;

        $appCodePath = DIRECTORY_SEPARATOR . ModuleResolver::APP_CODE;
        $codePathsToPattern[$magentoBaseCodePath . $appCodePath] = ModuleResolver::TEST_MFTF_PATTERN;

        foreach ($codePathsToPattern as $codePath => $pattern) {
            $allModulePaths = array_merge_recursive($allModulePaths, $this->globRelevantPaths($codePath, $pattern));
        }

        return $allModulePaths;
    }

    /**
     * Returns an array of custom module paths defined by the user.
     *
     * @return string[]
     */
    public function getCustomModulePaths(): array
    {
        $customModulePaths = [];
        $paths = getenv(ModuleResolver::CUSTOM_MODULE_PATHS);

        if (!$paths) {
            return $customModulePaths;
        }

        foreach (explode(',', $paths) as $path) {
            $customModulePaths[$this->findVendorAndModuleNameFromPath(trim($path))] = $path;
        }

        return $customModulePaths;
    }

    /**
     * Find vendor and module name from path.
     *
     * @param string $path
     *
     * @return string
     */
    private function findVendorAndModuleNameFromPath(string $path): string
    {
        $path = str_replace(DIRECTORY_SEPARATOR . ModuleResolver::TEST_MFTF_PATTERN, '', $path);

        return $this->findVendorNameFromPath($path) . '_' . basename($path);
    }

    /**
     * Find vendor name from path.
     *
     * @param string $path
     *
     * @return string
     */
    private function findVendorNameFromPath(string $path): string
    {
        $possibleVendorName = 'UnknownVendor';
        $dirPaths = [
            ModuleResolver::VENDOR,
            ModuleResolver::APP_CODE,
            ModuleResolver::DEV_TESTS
        ];

        foreach ($dirPaths as $dirPath) {
            $regex = "~.+\\/" . $dirPath . "\/(?<" . ModuleResolver::VENDOR . ">[^\/]+)\/.+~";
            $match = [];
            preg_match($regex, $path, $match);

            if (isset($match[ModuleResolver::VENDOR])) {
                $possibleVendorName = ucfirst($match[ModuleResolver::VENDOR]);
                return $possibleVendorName;
            }
        }

        return $possibleVendorName;
    }

    /**
     * Get admin token.
     *
     * @return string
     * @throws FastFailException
     */
    public function getAdminToken(): string
    {
        return WebApiAuth::getAdminToken();
    }
}
