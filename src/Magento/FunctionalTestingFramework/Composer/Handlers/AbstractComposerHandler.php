<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Composer\Handlers;

use Composer\Package\CompletePackageInterface;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\Composer\Objects\ComposerFactory;

/**
 *  Abstract Composer Handler
 */
abstract class AbstractComposerHandler
{
    /**
     * Mftf test module type
     */
    const TEST_MODULE_PACKAGE_TYPE = 'magento2-functional-test-module';

    /**
     * Magento module type
     */
    const MAGENTO_MODULE_PACKAGE_TYPE = 'magento2-module';

    /**#@+
     * Composer package array keys
     */
    const KEY_PACKAGE_NAME = 'name';
    const KEY_PACKAGE_TYPE = 'type';
    const KEY_PACKAGE_VERSION = 'version';
    const KEY_PACKAGE_DESCRIPTION = 'description';
    const KEY_PACKAGE_INSTALLEDPATH = 'installedPath';
    const KEY_PACKAGE_REQUIRES = 'requires';
    const KEY_PACKAGE_DEVREQUIRES = 'devRequires';
    const KEY_PACKAGE_SUGGESTS = 'suggests';
    const KEY_PACKAGE_SUGGESTED_MAGENTO_MODULES = 'suggestedMagentoModules';
    /**#@-*/

    /**#@+
     * Suggest field names
     */
    const SUGGEST_TYPE = 'type';
    const SUGGEST_NAME = 'name';
    const SUGGEST_VERSION = 'version';
    /**#@-*/

    /**
     * @var \Composer\Composer
     */
    protected $composer;

    /**
     * @var \Composer\Package\Locker
     */
    protected $locker;

    /**
     * @var ComposerFactory
     */
    protected $composerFactory;

    /**
     * @var \Composer\Package\CompletePackage
     */
    protected $rootPackage;

    /**
     * @param ComposerFactory $composerFactory
     */
    public function __construct(ComposerFactory $composerFactory)
    {
        $this->composerFactory = $composerFactory;
    }

    /**
     * Load ComposerFactory
     *
     * @return composerFactory
     */
    protected function getComposerFactory()
    {
        if (!$this->composerFactory) {
            $this->composerFactory = ObjectManager::getInstance()->get(ComposerFactory::class);
        }
        return $this->composerFactory;
    }

    /**
     * Load Composer
     *
     * @return \Composer\Composer
     */
    protected function getComposer()
    {
        if (!$this->composer) {
            $this->composer = $this->getComposerFactory()->create();
        }
        return $this->composer;
    }

    /**
     * Parse input array and return all suggested magento module names in pattern like: Magento_Store, Amazon_Core, etc
     *
     * @param array $suggests
     * @return array
     */
    protected function parseSuggestsForMagentoModuleNames($suggests)
    {
        $magentoModuleNames = [];
        foreach ($suggests as $suggest) {
            $parts = explode(',', $suggest);
            $data = [];
            foreach ($parts as $part) {
                if (strpos($part, ':') !== false) {
                    list($name, $value) = explode(':', $part, 2);
                    $data[strtolower(trim($name))] = trim($value);
                }
            }

            if (isset($data[self::SUGGEST_TYPE])
                && $data[self::SUGGEST_TYPE] == self::MAGENTO_MODULE_PACKAGE_TYPE
                && isset($data[self::SUGGEST_NAME])
                && strpos($data[self::SUGGEST_NAME], '_') !== false) {
                $magentoModuleNames[] = $data[self::SUGGEST_NAME];
            }
        }
        return array_unique($magentoModuleNames);
    }
}
