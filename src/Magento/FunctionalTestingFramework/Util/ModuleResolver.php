<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

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
    protected $versionUrl = "magento_version ";

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

        $this->printMagentoVersionInfo();

        $token = $this->getAdminToken();
        if (!$token || !is_string($token)) {
            $this->enabledModules = [];
            return $this->enabledModules;
        }

        $url = ConfigSanitizerUtil::sanitizeUrl($_ENV['MAGENTO_BASE_URL']) . $this->moduleUrl;

        $headers = [
            'Authorization: Bearer ' . $token,
        ];
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        $response = curl_exec($ch);

        if (!$response) {
            $this->enabledModules = [];
        } else {
            $this->enabledModules = json_decode($response);
        }
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

        $enabledModules = $this->getEnabledModules();
        $forceGeneration = $GLOBALS['FORCE_PHP_GENERATE'] ?? false;

        if (empty($enabledModules) && !$forceGeneration) {
            trigger_error(
                "Could not retrieve enabled modules from provided 'MAGENTO_BASE_URL'," .
                "please make sure Magento is available at this url",
                E_USER_ERROR
            );
        }

        $modulePath = defined('TESTS_MODULE_PATH') ? TESTS_MODULE_PATH : TESTS_BP;

        // Build an associative array of module name to existing module filepaths based on defined TEST MODULE PATH
        $allModulePaths = [];
        foreach (glob($modulePath . '*/*') as $modPath) {
            $modName = basename($modPath);
            $allModulePaths[$modName] = $modPath;
        }

        if (empty($enabledModules)) {
            $this->enabledModulePaths = $this->applyCustomModuleMethods($allModulePaths);
            return $this->enabledModulePaths;
        }

        $enabledModules = array_merge($enabledModules, $this->getModuleWhitelist());
        $enabledDirectoryPaths = $this->getEnabledDirectoryPaths($enabledModules, $allModulePaths);

        $this->enabledModulePaths = $this->applyCustomModuleMethods($enabledDirectoryPaths);
        return $this->enabledModulePaths;
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
            $moduleShortName = explode('_', $magentoModuleName)[1];
            if (!isset($this->knownDirectories[$moduleShortName]) && !isset($allModulePaths[$moduleShortName])) {
                continue;
            } else {
                $enabledDirectoryPaths[$moduleShortName] = $allModulePaths[$moduleShortName];
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
        $url = ConfigSanitizerUtil::sanitizeUrl($_ENV['MAGENTO_BASE_URL']) . $this->versionUrl;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        print "\nVersion Information: {$response}\n";
    }

    /**
     * Get the API token for admin.
     *
     * @return string|bool
     */
    protected function getAdminToken()
    {
        $login = $_ENV['MAGENTO_ADMIN_USERNAME'] ?? null;
        $password = $_ENV['MAGENTO_ADMIN_PASSWORD'] ?? null;
        if (!$login || !$password || !isset($_ENV['MAGENTO_BASE_URL'])) {
            return false;
        }

        $url = ConfigSanitizerUtil::sanitizeUrl($_ENV['MAGENTO_BASE_URL']) . $this->adminTokenUrl;
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

        $response = curl_exec($ch);
        if (!$response) {
            return $response;
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
            print "Including module path: {$value}\n";
        }, $customModulePaths);

        return array_merge($modulePathsResult, $customModulePaths);
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
                print "Excluding module: {$moduleName}\n";
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
}
