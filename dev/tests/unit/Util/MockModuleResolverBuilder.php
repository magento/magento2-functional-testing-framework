<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace tests\unit\Util;

use AspectMock\Test as AspectMock;
use Magento\FunctionalTestingFramework\ObjectManager;
use Magento\FunctionalTestingFramework\ObjectManagerFactory;
use Magento\FunctionalTestingFramework\Util\ModuleResolver;
use Magento\FunctionalTestingFramework\Config\MftfApplicationConfig;

class MockModuleResolverBuilder
{
    /**
     * Default paths for mock ModuleResolver
     *
     * @var array
     */
    private $defaultPaths = ['Magento_Module' => '/base/path/some/other/path/Magento/Module'];

    /**
     * Mock ModuleResolver builder
     *
     * @param array $paths
     * @return void
     * @throws \Exception
     */
    public function setup($paths = null)
    {
        if (empty($paths)) {
            $paths = $this->defaultPaths;
        }

        $mockConfig = AspectMock::double(MftfApplicationConfig::class, ['forceGenerateEnabled' => false]);
        $instance = AspectMock::double(ObjectManager::class, ['create' => $mockConfig->make(), 'get' => null])->make();
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);

        $property = new \ReflectionProperty(ModuleResolver::class, 'instance');
        $property->setAccessible(true);
        $property->setValue(null);

        $mockResolver = AspectMock::double(
            ModuleResolver::class,
            [
                'getAdminToken' => false,
                'globRelevantPaths' => [],
                'getEnabledModules' => []
            ]
        );
        $instance = AspectMock::double(ObjectManager::class, ['create' => $mockResolver->make(), 'get' => null])
            ->make();
        AspectMock::double(ObjectManagerFactory::class, ['getObjectManager' => $instance]);

        $resolver = ModuleResolver::getInstance();
        $property = new \ReflectionProperty(ModuleResolver::class, 'enabledModuleNameAndPaths');
        $property->setAccessible(true);
        $property->setValue($resolver, $paths);
    }
}
