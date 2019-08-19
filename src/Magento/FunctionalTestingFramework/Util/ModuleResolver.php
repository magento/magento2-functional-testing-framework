<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;
use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;
use Magento\FunctionalTestingFramework\Util\Logger\LoggingUtil;
use Symfony\Component\Config\Resource\DirectoryResource;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ModuleResolver, resolve module path based on enabled modules of target Magento instance.
 *
 * @api
 */
class ModuleResolver
{
    /**
     * Environment field name for module whitelist.
     */
    const MODULE_WHITELIST = 'MODULE_WHITELIST';

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

    /**
     * Vendor code path
     */
    const VENDOR_CODE_PATH = DIRECTORY_SEPARATOR . "vendor";

    /**
     * App code path
     */
    const APP_CODE_PATH = DIRECTORY_SEPARATOR . "app" . DIRECTORY_SEPARATOR . "code";

    /**
     * Dev test code path
     */
    const DEV_TEST_CODE_PATH = DIRECTORY_SEPARATOR
        . 'dev'
        . DIRECTORY_SEPARATOR
        . 'tests'
        . DIRECTORY_SEPARATOR
        . 'acceptance'
        . DIRECTORY_SEPARATOR
        . 'tests'
        . DIRECTORY_SEPARATOR
        . 'functional';

    /**
     * Pattern for Mftf directories
     */
    const MFTF_DIR_PATTERN = 'Test' . DIRECTORY_SEPARATOR . 'Mftf';

    /**
     * Regex to match an invalid dev test code path
     */
    const INVALID_DEV_TEST_CODE_PATH_REGEX = "~^[^:*\\?\"<>|']+"
        . self::DEV_TEST_CODE_PATH
        . "/[^/:*\\?\"<>|']+/FunctionalTest$~";

    /**
     * Test module name suffix
     */
    const TEST_MODULE_NAME_SUFFIX = 'Test';

    /**
     * Regex to match a test module name with suffix defined in TEST_MODULE_NAME_SUFFIX
     */
    const TEST_MODULE_NAME_REGEX = "~\S+" . self::TEST_MODULE_NAME_SUFFIX . "$~";

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
    protected $moduleUrl = "rest/V1/modules";

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
    protected $moduleBlacklist = [
        'SampleTests', 'SampleTemplates'
    ];

    /**
     * Registered module list in magento instance being tested
     *
     * @var array
     */
    private $registeredModuleList = [];

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
        $this->sequenceSorter = $objectManager->get(
            \Magento\FunctionalTestingFramework\Util\ModuleResolver\SequenceSorterInterface::class
        );
    }

    /**
     * Return an array of enabled modules of target Magento instance.
     *
     * @return array
     */
    public function getEnabledModules()
    {
        if (isset($this->enabledModules)) {
            return $this->enabledModules;
        }

        if (MftfApplicationConfig::getConfig()->getPhase() == MftfApplicationConfig::GENERATION_PHASE) {
            $this->printMagentoVersionInfo();
        }

        $token = $this->getAdminToken();

        $url = ConfigSanitizerUtil::sanitizeUrl(getenv('MAGENTO_BASE_URL')) . $this->moduleUrl;

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
            $context = [
                "Admin Module List Url" => $url,
                "MAGENTO_ADMIN_USERNAME" => getenv("MAGENTO_ADMIN_USERNAME"),
                "MAGENTO_ADMIN_PASSWORD" => getenv("MAGENTO_ADMIN_PASSWORD"),
            ];
            throw new TestFrameworkException($message, $context);
        }

        $this->enabledModules = json_decode($response);

        return $this->enabledModules;
    }

    /**
     * Return an array of module whitelist that not exist in target Magento instance.
     *
     * @return array
     */
    protected function getModuleWhitelist()
    {
        $moduleWhitelist = getenv(self::MODULE_WHITELIST);

        if (empty($moduleWhitelist)) {
            return [];
        }
        return array_map('trim', explode(',', $moduleWhitelist));
    }

    /**
     * Return the modules path based on which modules are enabled in the target Magento instance.
     *
     * @return array
     */
    public function getModulesPath()
    {
        if (isset($this->enabledModulePaths)) {
            return $this->enabledModulePaths;
        }

        $allModulePaths = $this->aggregateTestModulePaths();

        if (MftfApplicationConfig::getConfig()->forceGenerateEnabled()) {
            $this->enabledModulePaths = $this->applyCustomModuleMethods($allModulePaths);
            return $this->enabledModulePaths;
        }

        $enabledModules = array_merge($this->getEnabledModules(), $this->getModuleWhitelist());
        $enabledDirectoryPaths = $this->getEnabledDirectoryPaths($enabledModules, $allModulePaths);

        $this->enabledModulePaths = $this->applyCustomModuleMethods($enabledDirectoryPaths);
        return $this->enabledModulePaths;
    }

    /**
     * Retrieves all module directories which might contain pertinent test code.
     *
     * @return array
     */
    private function aggregateTestModulePaths()
    {
        $allModulePaths = [];

        // Define the Module paths from magento bp
        $magentoBaseCodePath = MAGENTO_BP;

        // Define the Module paths from default TESTS_MODULE_PATH
        $modulePath = defined('TESTS_MODULE_PATH') ? TESTS_MODULE_PATH : TESTS_BP;
        $modulePath = rtrim($modulePath, DIRECTORY_SEPARATOR);

        // Add known paths
        $codePathsToPattern = [
            $magentoBaseCodePath . self::VENDOR_CODE_PATH => [
                [
                    'pattern' => 'Test' . DIRECTORY_SEPARATOR . 'Mftf',
                    'level' => null
                ],
                [
                    'pattern' => '*-test',
                    'level' => 1
                ]
            ],
            $magentoBaseCodePath . self::APP_CODE_PATH => [
                [
                    'pattern' => 'Test' . DIRECTORY_SEPARATOR . 'Mftf',
                    'level' => null
                ]
            ],
            $magentoBaseCodePath . self::DEV_TEST_CODE_PATH => [
                [
                    'pattern' => '*' . self::TEST_MODULE_NAME_SUFFIX,
                    'level' => 1
                ],
                [
                    'pattern' => 'FunctionalTest' . DIRECTORY_SEPARATOR . '*',
                    'level' => 1
                ]
            ]
        ];

        // Check if module path is a known path
        $newPath = true;
        foreach (array_keys($codePathsToPattern) as $key) {
            if (strpos($modulePath, $key) !== false) {
                $newPath = false;
            }
        }

        // Add module path if it's a new path
        if ($newPath) {
            $codePathsToPattern[$modulePath] = [
                [
                    'pattern' => 'Test' . DIRECTORY_SEPARATOR . 'Mftf',
                    'level' => 0
                ],
                [
                    'pattern' => '*' . self::TEST_MODULE_NAME_SUFFIX,
                    'level' => 0
                ]
            ];
        }

        // Glob pattern for relevant paths
        foreach ($codePathsToPattern as $codePath => $patterns) {
            foreach ($patterns as $pattern) {
                $allModulePaths = array_merge_recursive(
                    $allModulePaths,
                    $this->globRelevantPaths($codePath, $pattern['pattern'], $pattern['level'])
                );
            }
        }

        return $allModulePaths;
    }

    /**
     * Function which takes a code path and a pattern and determines if there are any matching subdir paths. Matches
     * are returned as an associative array keyed by basename (the last dir excluding pattern) to an array containing
     * the matching path.
     *
     * @param string  $testPath
     * @param string  $pattern
     * @param integer $level
     * @return array
     */
    private function globRelevantPaths($testPath, $pattern, $level)
    {
        $modulePaths = [];
        $relevantPaths = [];

        if (file_exists($testPath)) {
            $relevantPaths = $this->globRelevantWrapper($testPath, $pattern, $level);
        }

        $allComponents = $this->getRegisteredModuleList();

        foreach ($relevantPaths as $codePath) {
            // Reduce magento/app/code/Magento/AdminGws/Test/MFTF to magento/app/code/Magento/AdminGws to read symlink
            // Symlinks must be resolved otherwise they will not match Magento's filepath to the module
            if ($pattern == self::MFTF_DIR_PATTERN) {
                $codePath = str_replace(DIRECTORY_SEPARATOR . self::MFTF_DIR_PATTERN, "", $codePath);
            }
            if (is_link($codePath)) {
                $codePath = realpath($codePath);
            }

            $mainModName = array_search($codePath, $allComponents) ?: basename($codePath);

            preg_match(self::INVALID_DEV_TEST_CODE_PATH_REGEX, $codePath, $match);
            if (empty($match)) {
                if ($pattern == self::MFTF_DIR_PATTERN) {
                    $modulePaths[$mainModName][] = $codePath . DIRECTORY_SEPARATOR . self::MFTF_DIR_PATTERN;
                } else {
                    $modulePaths[$mainModName][] = $codePath;
                }

                if (MftfApplicationConfig::getConfig()->verboseEnabled()) {
                    LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->debug(
                        "including module",
                        ['module' => $mainModName, 'path' => $codePath]
                    );
                }
            }
        }

        return $modulePaths;
    }

    /**
     * Glob wrapper for globRelevantPaths function
     * When $level = null, it's recursion
     *
     * @param string  $testPath
     * @param string  $pattern
     * @param integer $level
     * @return array
     */
    private static function globRelevantWrapper($testPath, $pattern, $level = null)
    {
        $subDirectory = DIRECTORY_SEPARATOR . "*";
        if ($level !== null) {
            $subDirectories = '';
            for ($i = 0; $i < $level; $i++) {
                $subDirectories .= $subDirectory;
            }
            return glob($testPath . $subDirectories . DIRECTORY_SEPARATOR . $pattern, GLOB_ONLYDIR);
        }

        $directories = glob($testPath . $subDirectory . DIRECTORY_SEPARATOR . $pattern, GLOB_ONLYDIR);
        foreach (glob($testPath . $subDirectory, GLOB_ONLYDIR) as $dir) {
            $directories = array_merge_recursive($directories, self::globRelevantWrapper($dir, $pattern));
        }
        return $directories;
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
     * Runs through enabled modules and maps them known module paths by name.
     * @param array $enabledModules
     * @param array $allModulePaths
     * @return array
     */
    private function getEnabledDirectoryPaths($enabledModules, $allModulePaths)
    {
        $enabledDirectoryPaths = [];
        foreach ($enabledModules as $magentoModuleName) {
            $magentoTestModuleName = $magentoModuleName . self::TEST_MODULE_NAME_SUFFIX;
            if (isset($this->knownDirectories[$magentoModuleName]) && !isset($allModulePaths[$magentoModuleName])) {
                LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->warn(
                    "Known directory could not match to an existing path.",
                    ['knownDirectory' => $magentoModuleName]
                );
            } else {
                if (isset($allModulePaths[$magentoModuleName])) {
                    $enabledDirectoryPaths[$magentoModuleName] = $allModulePaths[$magentoModuleName];
                }
                if (isset($allModulePaths[$magentoTestModuleName])) {
                    $enabledDirectoryPaths[$magentoTestModuleName] = $allModulePaths[$magentoTestModuleName];
                }
            }
        }
        return $enabledDirectoryPaths;
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
        $url = ConfigSanitizerUtil::sanitizeUrl(getenv('MAGENTO_BASE_URL')) . $this->versionUrl;
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
     * Get the API token for admin.
     *
     * @return string|boolean
     */
    protected function getAdminToken()
    {
        $login = $_ENV['MAGENTO_ADMIN_USERNAME'] ?? null;
        $password = $_ENV['MAGENTO_ADMIN_PASSWORD'] ?? null;
        if (!$login || !$password || !$this->getBackendUrl()) {
            $message = "Cannot retrieve API token without credentials and base url, please fill out .env.";
            $context = [
                "MAGENTO_BASE_URL" => getenv("MAGENTO_BASE_URL"),
                "MAGENTO_BACKEND_BASE_URL" => getenv("MAGENTO_BACKEND_BASE_URL"),
                "MAGENTO_ADMIN_USERNAME" => getenv("MAGENTO_ADMIN_USERNAME"),
                "MAGENTO_ADMIN_PASSWORD" => getenv("MAGENTO_ADMIN_PASSWORD"),
            ];
            throw new TestFrameworkException($message, $context);
        }

        $url = ConfigSanitizerUtil::sanitizeUrl($this->getBackendUrl()) . $this->adminTokenUrl;
        $data = [
            'username' => $login,
            'password' => $password
        ];
        $headers = [
            'Content-Type: application/json',
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);
        $responseCode = curl_getinfo($ch)['http_code'];

        if ($responseCode !== 200) {
            if ($responseCode == 0) {
                $details = "Could not find Magento Backend Instance at MAGENTO_BACKEND_BASE_URL or MAGENTO_BASE_URL";
            } else {
                $details = $responseCode . " " . Response::$statusTexts[$responseCode];
            }

            $message = "Could not retrieve API token from Magento Instance ({$details})";
            $context = [
                "tokenUrl" => $url,
                "responseCode" => $responseCode,
                "MAGENTO_ADMIN_USERNAME" => getenv("MAGENTO_ADMIN_USERNAME"),
                "MAGENTO_ADMIN_PASSWORD" => getenv("MAGENTO_ADMIN_PASSWORD"),
            ];
            throw new TestFrameworkException($message, $context);
        }

        return json_decode($response);
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
     * A wrapping method for any custom logic which needs to be applied to the module list
     *
     * @param array $modulesPath
     * @return string[]
     */
    protected function applyCustomModuleMethods($modulesPath)
    {
        $modulePathsResult = $this->removeBlacklistModules($modulesPath);
        $customModulePaths = $this->getCustomModulePaths();

        array_map(function ($value) {
            LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->info(
                "including custom module",
                ['module' => $value]
            );
        }, $customModulePaths);

        return $this->flattenAllModulePaths(array_merge($modulePathsResult, $customModulePaths));
    }

    /**
     * Remove blacklist modules from input module paths.
     *
     * @param array $modulePaths
     * @return string[]
     */
    private function removeBlacklistModules($modulePaths)
    {
        $modulePathsResult = $modulePaths;
        foreach ($modulePathsResult as $moduleName => $modulePath) {
            if (in_array($moduleName, $this->getModuleBlacklist())) {
                unset($modulePathsResult[$moduleName]);
                LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->info(
                    "excluding module",
                    ['module' => $moduleName]
                );
            }

            preg_match(self::TEST_MODULE_NAME_REGEX, $moduleName, $match);
            if (!empty($match)) {
                $relatedModuleName = substr($moduleName, 0, -strlen(self::TEST_MODULE_NAME_SUFFIX));
                if (in_array($relatedModuleName, $this->getModuleBlacklist())) {
                    unset($modulePathsResult[$moduleName]);
                    LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->info(
                        "excluding module",
                        ['module' => $moduleName]
                    );
                }
            }
        }

        return $modulePathsResult;
    }

    /**
     * Returns an array of custom module paths defined by the user
     *
     * @return string[]
     */
    private function getCustomModulePaths()
    {
        $customModulePaths = getenv(self::CUSTOM_MODULE_PATHS);

        if (!$customModulePaths) {
            return [];
        }

        return array_map('trim', explode(',', $customModulePaths));
    }

    /**
     * Getter for moduleBlacklist.
     *
     * @return string[]
     */
    private function getModuleBlacklist()
    {
        return $this->moduleBlacklist;
    }

    /**
     * Calls Magento method for determining registered modules.
     *
     * @return string[]
     */
    private function getRegisteredModuleList()
    {
        if (!empty($this->registeredModuleList)) {
            return $this->registeredModuleList;
        }

        if (array_key_exists('MAGENTO_BP', $_ENV)) {
            $autoloadPath = realpath(MAGENTO_BP . "/app/autoload.php");
            if ($autoloadPath) {
                require_once($autoloadPath);
            } else {
                throw new TestFrameworkException("Magento app/autoload.php not found with given MAGENTO_BP:"
                    . MAGENTO_BP);
            }
        }

        try {
            $this->registeredModuleList = [];
            if (!class_exists(self::REGISTRAR_CLASS)) {
                throw new TestFrameworkException("Magento Installation not found when loading registered modules.\n");
            }
            $components = new \Magento\Framework\Component\ComponentRegistrar();
            foreach (self::PATHS as $componentType) {
                $this->registeredModuleList = array_merge(
                    $this->registeredModuleList,
                    $components->getPaths($componentType)
                );
            }
            array_walk($this->registeredModuleList, function (&$value) {
                // Magento stores component paths with unix DIRECTORY_SEPARATOR, need to stay uniform and convert
                $value = realpath($value);
            });
            return $this->registeredModuleList;
        } catch (TestFrameworkException $e) {
            LoggingUtil::getInstance()->getLogger(ModuleResolver::class)->warning(
                "$e"
            );
        }
        return [];
    }

    /**
     * Returns custom Backend URL if set, fallback to Magento Base URL
     * @return string|null
     */
    private function getBackendUrl()
    {
        return getenv('MAGENTO_BACKEND_BASE_URL') ?: getenv('MAGENTO_BASE_URL');
    }
}
