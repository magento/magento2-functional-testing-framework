<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Composer;

/**
 * Class ComposerPackage contains composer json information in a MFTF test package
 */
class ComposerPackage extends AbstractComposer
{
    /**
     * @var \Composer\Package\CompletePackage
     */
    private $rootPackage;

    /**
     * Retrieve package name from composer json
     *
     * @return string
     */
    public function getName()
    {
        /** @var \Composer\Package\CompletePackage $package */
        $package = $this->getRootPackage();
        return $package->getPrettyName();
    }

    /**
     * Retrieve package type from composer json
     *
     * @return string
     */
    public function getType()
    {
        /** @var \Composer\Package\CompletePackage $package */
        $package = $this->getRootPackage();
        return $package->getType();
    }

    /**
     * Retrieve package version from composer json
     *
     * @return string
     */
    public function getVersion()
    {
        /** @var \Composer\Package\CompletePackage $package */
        $package = $this->getRootPackage();
        return $package->getPrettyVersion();
    }

    /**
     * Retrieve package description from composer json
     *
     * @return string
     */
    public function getDescription()
    {
        /** @var \Composer\Package\CompletePackage $package */
        $package = $this->getRootPackage();
        return $package->getDescription();
    }

    /**
     * Retrieve package require from composer json
     *
     * @return array
     */
    public function getRequires()
    {
        /** @var \Composer\Package\CompletePackage $package */
        $package = $this->getRootPackage();
        return $package->getRequires();
    }

    /**
     * Retrieve package dev require from composer json
     *
     * @return array
     */
    public function getDevRequires()
    {
        /** @var \Composer\Package\CompletePackage $package */
        $package = $this->getRootPackage();
        return $package->getDevRequires();
    }

    /**
     * Retrieve package suggest from composer json
     *
     * @return array
     */
    public function getSuggests()
    {
        /** @var \Composer\Package\CompletePackage $package */
        $package = $this->getRootPackage();
        return $package->getSuggests();
    }

    /**
     * Retrieve magento module names in package's suggest
     *
     * @return array
     */
    public function getSuggestedMagentoModules()
    {
        return $this->parseSuggestsForMagentoModuleNames($this->getSuggests());
    }

    /**
     * Determines if package is a mftf test package
     *
     * @return boolean
     */
    public function isMftfTestPackage()
    {
        return ($this->getType() == self::TEST_MODULE_PACKAGE_TYPE) ? true : false;
    }

    /**
     * Retrieve packages require for given package name and version
     *
     * @param string $name
     * @param string $version
     * @return array
     */
    public function getRequiresForPackage($name, $version)
    {
        /** @var \Composer\Package\CompletePackage $package */
        $package = $this->getComposer()->getRepositoryManager()->findPackage($name, $version);
        return $package->getRequires();
    }

    /**
     * Check if a package is required in composer json
     *
     * @param string $packageName
     * @return boolean
     */
    public function isPackageRequiredInComposerJson($packageName)
    {
        return (in_array($packageName, array_keys($this->getRequires()))
            || in_array($packageName, array_keys($this->getDevRequires()))
        );
    }

    /**
     * Get root package
     *
     * @return \Composer\Package\RootPackageInterface
     */
    public function getRootPackage()
    {
        if (!$this->rootPackage) {
            $this->rootPackage = $this->getComposer()->getPackage();
        }
        return $this->rootPackage;
    }
}
