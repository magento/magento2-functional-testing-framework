<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\DataGenerator\Handlers\CredentialStore;
use Magento\FunctionalTestingFramework\Exceptions\FastFailException;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\MftfGlobals;
use Magento\FunctionalTestingFramework\Util\ModuleResolver\ModuleResolverService;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Magento\FunctionalTestingFramework\Util\Path\FilePathFormatter;
use Magento\FunctionalTestingFramework\Util\Path\UrlFormatter;
use \Magento\FunctionalTestingFramework\Util\ModuleResolver\AlphabeticSequenceSorter;
use \Magento\FunctionalTestingFramework\Util\ModuleResolver\SequenceSorterInterface;

/**
 * Class ModuleResolver, resolve module path based on enabled modules of target Magento instance.
 *
 * @api
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class ModuleResolver
{
    /**
     * Environment field name for module allowlist.
     */
    const MODULE_ALLOWLIST = 'MODULE_ALLOWLIST';

    /**
     * Environment field name for custom module paths.
     */
    const CUSTOM_MODULE_PATHS = 'CUSTOM_MODULE_PATHS';

    /**
     * List of path types present in Magento Component Registrar
     */
    const PATHS = ['module', 'library', 'theme', 'language'];

    /**
     * Magento Registrar Class
     */
    const REGISTRAR_CLASS = "\Magento\Framework\Component\ComponentRegistrar";

    const TEST_MFTF_PATTERN = 'Test' . DIRECTORY_SEPARATOR . 'Mftf';
    const VENDOR = 'vendor';
    const APP_CODE = 'app' . DIRECTORY_SEPARATOR . "code";
    const DEV_TESTS = 'dev'
    . DIRECTORY_SEPARATOR
    . 'tests'
    . DIRECTORY_SEPARATOR
    . 'acceptance'
    . DIRECTORY_SEPARATOR
    . 'tests'
    . DIRECTORY_SEPARATOR
    . 'functional';

    /**
     * Enabled modules.
     *
     * @var array|null
     */
    protected $enabledModules = null;

    /**
     * Paths for enabled modules.
     *
     * @var array|null
     */
    protected $enabledModulePaths = null;

    /**
     * Name and path for enabled modules
     *
     * @var array|null
     */
    protected $enabledModuleNameAndPaths = null;

    /**
     * Configuration instance.
     *
     * @var \Magento\FunctionalTestingFramework\Config\DataInterface
     */
    protected $configuration;

    /**
     * Admin url for integration token.
     *
     * @var string
     */
    protected $adminTokenUrl = "rest/V1/integration/admin/token";

    /**
     * Url for with module list.
     *
     * @var string
     */
    protected $moduleUrl = "V1/modules";

    /**
     * Url for magento version information.
     *
     * @var string
     */
    protected $versionUrl = "magento_version";

    /**
     * List of known directory that does not map to a Magento module.
     *
     * @var array
     */
    protected $knownDirectories = ['SampleData' => 1];

    /**
     * ModuleResolver instance.
     *
     * @var ModuleResolver
     */
    private static $instance = null;

    /**
     * SequenceSorter instance.
     *
     * @var ModuleResolver\SequenceSorterInterface
     */
    protected $sequenceSorter;

    /**
     * List of module names that will be ignored.
     *
     * @var array
     */
    protected $moduleBlocklist = [
        'SampleTests', 'SampleTemplates'
    ];

    /**
     * Get ModuleResolver instance.
     *
     * @return ModuleResolver
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new ModuleResolver();
        }
        return self::$instance;
    }

    /**
     * ModuleResolver constructor.
     */
    private function __construct()
    {
        $objectManager = \Magento\FunctionalTestingFramework\ObjectManagerFactory::getObjectManager();

        if (MftfApplicationConfig::getConfig()->getPhase() === MftfApplicationConfig::UNIT_TEST_PHASE) {
            $this->sequenceSorter = $objectManager->get(AlphabeticSequenceSorter::class);
        } else {
            $this->sequenceSorter = $objectManager->get(SequenceSorterInterface::class);
        }
    }

    /**
     * Return an array of enabled modules of target Magento instance.
     *
     * @return array
     * @throws TestFrameworkException
     * @throws FastFailException
     */
    public function getEnabledModules()
    {
        if (isset($this->enabledModules)) {
            return $this->enabledModules;
        }

        if (MftfApplicationConfig::getConfig()->getPhase() === MftfApplicationConfig::GENERATION_PHASE) {
            $this->printMagentoVersionInfo();
        }

        $token = ModuleResolverService::getInstance()->getAdminToken();

        $url = MftfGlobals::getWebApiBaseUrl() . $this->moduleUrl;

        $headers = [
            'Authorization: Bearer ' . $token,
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $response = curl_exec($ch);

        if (!$response) {
            $message = "Could not retrieve Modules from Magento Instance.";
            $encryptedSecret = CredentialStore::getInstance()->getSecret('magento/MAGENTO_ADMIN_PASSWORD');
            $secret = CredentialStore::getInstance()->decryptSecretValue($encryptedSecret);
            $context = [
                "Admin Module List Url" => $url,
                "MAGENTO_ADMIN_USERNAME" => getenv("MAGENTO_ADMIN_USERNAME"),
                "MAGENTO_ADMIN_PASSWORD" => $secret,
            ];
            throw new FastFailException($message, $context);
        }

        $this->enabledModules = json_decode($response);

        return $this->enabledModules;
    }

    /**
     * Return the modules path based on which modules are enabled in the target Magento instance.
     *
     * @param boolean $verbosePath
     * @return array
     * @throws TestFrameworkException
     * @throws FastFailException
     */
    public function getModulesPath($verbosePath = false)
    {
        if (isset($this->enabledModulePaths) && !$verbosePath) {
            return $this->enabledModulePaths;
        }

        if (isset($this->enabledModuleNameAndPaths) && $verbosePath) {
            return $this->enabledModuleNameAndPaths;
        }

        // Find test modules paths by searching patterns (Test/Mftf, etc)
        $allModulePaths = ModuleResolverService::getInstance()->aggregateTestModulePaths();

        // Find test modules paths by searching test composer.json files
        $composerBasedModulePaths = $this->aggregateTestModulePathsFromComposerJson();

        // Find test modules paths by querying composer installed packages
        $composerBasedModulePaths = array_merge(
            $composerBasedModulePaths,
            $this->aggregateTestModulePathsFromComposerInstaller()
        );

        // Merge test module paths altogether
        $allModulePaths = $this->mergeModulePaths($allModulePaths, $composerBasedModulePaths);

        // Normalize module names if we get registered module names from Magento system
        $allModulePaths = $this->normalizeModuleNames($allModulePaths);

        if (MftfApplicationConfig::getConfig()->forceGenerateEnabled()) {
            $allModulePaths = $this->flipAndSortModulePathsArray($allModulePaths, true);
            $this->enabledModulePaths = $this->applyCustomModuleMethods($allModulePaths);
            return $this->enabledModulePaths;
        }

        $enabledModules = array_merge($this->getEnabledModules(), $this->getModuleAllowlist());
        $enabledDirectoryPaths = $this->flipAndFilterModulePathsArray($allModulePaths, $enabledModules);
        $this->enabledModulePaths = $this->applyCustomModuleMethods($enabledDirectoryPaths);

        return $this->enabledModulePaths;
    }

    /**
     * Sort files according module sequence.
     *
     * @param array $files
     * @return array
     */
    public function sortFilesByModuleSequence(array $files)
    {
        return $this->sequenceSorter->sort($files);
    }

    /**
     * Return an array of module allowlist that not exist in target Magento instance.
     *
     * @return array
     */
    protected function getModuleAllowlist()
    {
        $moduleAllowlist = getenv(self::MODULE_ALLOWLIST);

        if (empty($moduleAllowlist)) {
            return [];
        }
        return array_map('trim', explode(',', $moduleAllowlist));
    }

    /**
     * Aggregate all code paths with test module composer json files
     *
     * @return array
     * @throws TestFrameworkException
     */
    private function aggregateTestModulePathsFromComposerJson()
    {
        // Define the module paths
        $magentoBaseCodePath = FilePathFormatter::format(MAGENTO_BP, false);

        // Define the module paths from default TESTS_MODULE_PATH
        $modulePath = defined('TESTS_MODULE_PATH') ? TESTS_MODULE_PATH : TESTS_BP;
        $modulePath = FilePathFormatter::format($modulePath, false);

        $searchCodePaths = [
            $magentoBaseCodePath . DIRECTORY_SEPARATOR . self::DEV_TESTS,
        ];

        // Add TESTS_MODULE_PATH if it's not included
        if (array_search($modulePath, $searchCodePaths) === false) {
            $searchCodePaths[] = $modulePath;
        }

        return ModuleResolverService::getInstance()->getComposerJsonTestModulePaths($searchCodePaths);
    }

    /**
     * Aggregate all code paths with composer installed test modules
     *
     * @return array
     */
    private function aggregateTestModulePathsFromComposerInstaller()
    {
        // Define the module paths
        $magentoBaseCodePath = MAGENTO_BP;
        $composerFile = $magentoBaseCodePath . DIRECTORY_SEPARATOR . 'composer.json';

        return ModuleResolverService::getInstance()->getComposerInstalledTestModulePaths($composerFile);
    }

    /**
     * Flip and filter module code paths
     *
     * @param array $objectArray
     * @param array $filterArray
     * @return array
     */
    private function flipAndFilterModulePathsArray($objectArray, $filterArray)
    {
        $oneToOneArray = [];
        $oneToManyArray = [];
        // Filter array by enabled modules
        foreach ($objectArray as $path => $modules) {
            if (!array_diff($modules, $filterArray)
                || (count($modules) === 1 && isset($this->knownDirectories[$modules[0]]))) {
                if (count($modules) === 1) {
                    $oneToOneArray[$path] = $modules[0];
                } else {
                    $oneToManyArray[$path] = $modules;
                }
            }
        }

        $flippedArray = [];
        // Set flipped array for "one path => one module" case first to maintain module sequencing
        foreach ($filterArray as $moduleName) {
            $path = array_search($moduleName, $oneToOneArray);
            if ($path !== false) {
                if (strpos($moduleName, '_') === false) {
                    $moduleName = $this->findVendorNameFromPath($path) . '_' . $moduleName;
                }
                $flippedArray = $this->setArrayValueWithLogging($flippedArray, $moduleName, $path);
                unset($oneToOneArray[$path]);
            }
        }

        // Set flipped array for everything else
        return $this->flipAndSortModulePathsArray(
            array_merge($oneToOneArray, $oneToManyArray),
            false,
            $flippedArray
        );
    }

    /**
     * Flip module code paths and optionally sort in alphabetical order
     *
     * @param array   $objectArray
     * @param boolean $sort
     * @param array   $inFlippedArray
     * @return array
     */
    private function flipAndSortModulePathsArray($objectArray, $sort, $inFlippedArray = [])
    {
        $flippedArray = $inFlippedArray;

        // Set flipped array from object array
        foreach ($objectArray as $path => $modules) {
            if (is_array($modules) && count($modules) > 1) {
                // The "one path => many module names" case is designed to be strictly used when it's
                // impossible to write tests in dedicated modules.
                // For now we will set module name based on path.
                // TODO: Consider saving all module names if this information is needed in the future.
                $module = $this->findVendorAndModuleNameFromPath($path);
            } elseif (is_array($modules)) {
                if (strpos($modules[0], '_') === false) {
                    $module = $this->findVendorNameFromPath($path) . '_' . $modules[0];
                } else {
                    $module = $modules[0];
                }
            } else {
                if (strpos($modules, '_') === false) {
                    $module = $this->findVendorNameFromPath($path) . '_' . $modules;
                } else {
                    $module = $modules;
                }
            }
            $flippedArray = $this->setArrayValueWithLogging($flippedArray, $module, $path);
        }

        // Sort array in alphabetical order
        if ($sort) {
            ksort($flippedArray);
        }

        return $flippedArray;
    }

    /**
     * Set array value at index only if array value at index is not yet set, skip otherwise and log warning message
     *
     * @param array  $inArray
     * @param string $index
     * @param string $value
     *
     * @return array
     */
    private function setArrayValueWithLogging($inArray, $index, $value)
    {
        $outArray = $inArray;
        if (!isset($inArray[$index])) {
            $outArray[$index] = $value;
        } else {
            $warnMsg = 'Path: ' . $value . ' is ignored by ModuleResolver. ' . PHP_EOL . 'Path: ';
            $warnMsg .= $inArray[$index] . ' is set for Module: ' . $index . PHP_EOL;
            LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->warning($warnMsg);
        }
        return $outArray;
    }

    /**
     * Merge code paths
     *
     * @param array $oneToOneArray
     * @param array $oneToManyArray
     * @return array
     */
    private function mergeModulePaths($oneToOneArray, $oneToManyArray)
    {
        $mergedArray = $oneToOneArray;
        foreach ($oneToManyArray as $path => $modules) {
            // Do nothing when array_key_exists
            if (!array_key_exists($path, $oneToOneArray)) {
                $mergedArray[$path] = $modules;
            }
        }
        return $mergedArray;
    }

    /**
     * Normalize module name if registered module list is available
     *
     * @param array $codePaths
     *
     * @return array
     */
    private function normalizeModuleNames($codePaths)
    {
        $allComponents = ModuleResolverService::getInstance()->getRegisteredModuleList();
        if (empty($allComponents)) {
            return $codePaths;
        }

        $normalizedCodePaths = [];
        foreach ($codePaths as $path => $moduleNames) {
            $mainModName = array_search($path, $allComponents);
            if ($mainModName) {
                $normalizedCodePaths[$path] = [$mainModName];
            } else {
                $normalizedCodePaths[$path] = $moduleNames;
            }
        }

        return $normalizedCodePaths;
    }

    /**
     * Takes a multidimensional array of module paths and flattens to return a one dimensional array of test paths
     *
     * @param array $modulePaths
     * @return array
     */
    private function flattenAllModulePaths($modulePaths)
    {
        $it = new \RecursiveIteratorIterator(new \RecursiveArrayIterator($modulePaths));
        $resultArray = [];

        foreach ($it as $value) {
            $resultArray[] = $value;
        }

        return $resultArray;
    }

    /**
     * Executes a REST call to the supplied Magento Base Url for version information to display during generation
     *
     * @return void
     */
    private function printMagentoVersionInfo()
    {
        if (MftfApplicationConfig::getConfig()->forceGenerateEnabled()) {
            return;
        }
        $url = UrlFormatter::format(getenv('MAGENTO_BASE_URL')) . $this->versionUrl;
        LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->info(
            "Fetching version information.",
            ['url' => $url]
        );

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);

        if (!$response) {
            $response = "No version information available.";
        }

        LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->info(
            'version information',
            ['version' => $response]
        );
    }

    /**
     * A wrapping method for any custom logic which needs to be applied to the module list
     *
     * @param array $modulesPath
     * @return string[]
     */
    protected function applyCustomModuleMethods($modulesPath)
    {
        $modulePathsResult = $this->removeBlocklistModules($modulesPath);
        $customModulePaths = ModuleResolverService::getInstance()->getCustomModulePaths();

        array_map(function ($key, $value) {
            LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->info(
                "including custom module",
                [$key => $value]
            );
        }, array_keys($customModulePaths), $customModulePaths);

        if (!isset($this->enabledModuleNameAndPaths)) {
            $this->enabledModuleNameAndPaths = array_merge($modulePathsResult, $customModulePaths);
        }
        return $this->flattenAllModulePaths(array_merge($modulePathsResult, $customModulePaths));
    }

    /**
     * Remove blocklist modules from input module paths.
     *
     * @param array $modulePaths
     * @return string[]
     */
    private function removeBlocklistModules($modulePaths)
    {
        $modulePathsResult = $modulePaths;
        foreach ($modulePathsResult as $moduleName => $modulePath) {
            // Remove module if it is in blocklist
            if (in_array($moduleName, $this->getModuleBlocklist())) {
                unset($modulePathsResult[$moduleName]);
                LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->info(
                    "excluding module",
                    ['module' => $moduleName]
                );
            }
        }

        return $modulePathsResult;
    }

    /**
     * Getter for moduleBlocklist.
     *
     * @return string[]
     */
    private function getModuleBlocklist()
    {
        return $this->moduleBlocklist;
    }

    /**
     * Find vendor and module name from path
     *
     * @param string $path
     * @return string
     */
    private function findVendorAndModuleNameFromPath($path)
    {
        $path = str_replace(DIRECTORY_SEPARATOR . self::TEST_MFTF_PATTERN, '', $path);
        return $this->findVendorNameFromPath($path) . '_' . basename($path);
    }

    /**
     * Find vendor name from path
     *
     * @param string $path
     * @return string
     */
    private function findVendorNameFromPath($path)
    {
        $possibleVendorName = 'UnknownVendor';
        $dirPaths = [
            self::VENDOR,
            self::APP_CODE,
            self::DEV_TESTS
        ];

        foreach ($dirPaths as $dirPath) {
            $regex = "~.+\\/" . $dirPath . "\/(?<" . self::VENDOR . ">[^\/]+)\/.+~";
            $match = [];
            preg_match($regex, $path, $match);
            if (isset($match[self::VENDOR])) {
                $possibleVendorName = ucfirst($match[self::VENDOR]);
                return $possibleVendorName;
            }
        }
        return $possibleVendorName;
    }
}
