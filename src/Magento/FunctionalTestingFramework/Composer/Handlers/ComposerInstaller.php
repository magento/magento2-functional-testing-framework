<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Composer\Handlers;

use Composer\Package\CompletePackageInterface;

/**
 * Class ComposerInstaller handles information and dependencies for composer installed packages
 */
class ComposerInstaller extends AbstractComposerHandler
{
    /**
     * Determines if package is a mftf test package
     *
     * @param string $packageName
     * @return boolean
     */
    public function isMftfTestPackage($packageName)
    {
        return $this->isInstalledPackageOfType($packageName, self::TEST_MODULE_PACKAGE_TYPE);
    }

    /**
     * Determines if package is a magento package
     *
     * @param string $packageName
     * @return boolean
     */
    public function isMagentoPackage($packageName)
    {
        return $this->isInstalledPackageOfType($packageName, self::MAGENTO_MODULE_PACKAGE_TYPE);
    }

    /**
     * Determines if an installed package is of a certain type
     *
     * @param string $packageName
     * @param string $packageType
     * @return boolean
     */
    public function isInstalledPackageOfType($packageName, $packageType)
    {
        /** @var CompletePackageInterface $package */
        foreach ($this->getLocker()->getLockedRepository()->getPackages() as $package) {
            if (($package->getName() == $packageName) && ($package->getType() == $packageType)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Collect all installed mftf test packages from composer lock
     *
     * @return array
     */
    public function getInstalledTestPackages()
    {
        $packages = [];
        /** @var CompletePackageInterface $package */
        foreach ($this->getLocker()->getLockedRepository()->getPackages() as $package) {
            if ($package->getType() == self::TEST_MODULE_PACKAGE_TYPE) {
                $packages[$package->getName()] = [
                    self::KEY_PACKAGE_NAME => $package->getName(),
                    self::KEY_PACKAGE_TYPE => $package->getType(),
                    self::KEY_PACKAGE_VERSION => $package->getPrettyVersion(),
                    self::KEY_PACKAGE_DESCRIPTION => $package->getDescription(),
                    self::KEY_PACKAGE_SUGGESTS => $package->getSuggests(),
                    self::KEY_PACKAGE_REQUIRES => $package->getRequires(),
                    self::KEY_PACKAGE_DEVREQUIRES => $package->getDevRequires(),
                    self::KEY_PACKAGE_SUGGESTED_MAGENTO_MODULES => $this->parseSuggestsForMagentoModuleNames(
                        $package->getSuggests()
                    ),
                    self::KEY_PACKAGE_INSTALLEDPATH => $this->getComposer()->getInstallationManager()
                        ->getInstallPath($package)
                ];
            }
        }
        return $packages;
    }

    /**
     * Load locker
     *
     * @return \Composer\Package\Locker
     */
    private function getLocker()
    {
        if (!$this->locker) {
            $this->locker = $this->getComposer()->getLocker();
        }
        return $this->locker;
    }
}
