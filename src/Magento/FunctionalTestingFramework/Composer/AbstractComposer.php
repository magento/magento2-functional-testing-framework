<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Composer;

use Composer\IO\BufferIO;

/**
 *  Abstract Composer Handler
 */
abstract class AbstractComposer
{
    const TEST_MODULE_PACKAGE_TYPE = 'magento2-functional-test-module';
    const MAGENTO_MODULE_PACKAGE_TYPE = 'magento2-module';

    const MODULE_NAME_IN_SUGGEST_REGEX_INDEX = 'module_name';
    const MODULE_NAME_IN_SUGGEST_REGEX = '/type:\s*'
    . self::MAGENTO_MODULE_PACKAGE_TYPE
    . '\s*,\s*name:\s*(?<'
    . self::MODULE_NAME_IN_SUGGEST_REGEX_INDEX
    . '>[^,\s]+_[^,\s]+)/';

    /**#@+
     * Composer package array keys
     */
    const PACKAGE_NAME = 'name';
    const PACKAGE_TYPE = 'type';
    const PACKAGE_VERSION = 'version';
    const PACKAGE_DESCRIPTION = 'description';
    const PACKAGE_INSTALLEDPATH = 'installedPath';
    const PACKAGE_REQUIRES = 'requires';
    const PACKAGE_DEVREQUIRES = 'devRequires';
    const PACKAGE_SUGGESTS = 'suggests';
    const PACKAGE_SUGGESTED_MAGENTO_MODULES = 'suggestedMagentoModules';
    /**#@-*/

    /**
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * @param string $composerFile
     */
    public function __construct($composerFile)
    {
        $this->composer = \Composer\Factory::create(new BufferIO(), $composerFile);
    }

    /**
     * Get composer
     *
     * @return \Composer\Composer
     */
    protected function getComposer()
    {
        return $this->composer;
    }

    /**
     * Parse input array and return all suggested magento module names, i.e. an example "suggest" in composer.json
     *
     * "suggest": {
     *   "magento/module-backend": "type: magento2-module, name: Magento_Backend, version: ~100.0.0",
     *   "magento/module-store": "type: magento2-module, name: Magento_Store, version: ~100.0.0"
     * }
     *
     * @param array $suggests
     * @return array
     */
    protected function parseSuggestsForMagentoModuleNames($suggests)
    {
        $magentoModuleNames = [];
        foreach ($suggests as $suggest) {
            // Expecting pattern - type: magento2-module, name: Magento_Store, version: ~100.0.0
            preg_match(self::MODULE_NAME_IN_SUGGEST_REGEX, $suggest, $match);
            if (isset($match[self::MODULE_NAME_IN_SUGGEST_REGEX_INDEX])) {
                $magentoModuleNames[] = $match[self::MODULE_NAME_IN_SUGGEST_REGEX_INDEX];
            }
        }

        return array_unique($magentoModuleNames);
    }
}
