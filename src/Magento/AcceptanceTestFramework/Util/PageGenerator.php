<?php

umask(0);

require_once '../../../../bootstrap.php';

$objectManager = \Magento\AcceptanceTestFramework\ObjectManagerFactory::getObjectManager();

$generatorPool = $objectManager->get('Magento\AcceptanceTestFramework\Generate\Pool');
foreach ($generatorPool->getGenerators() as $generator) {
    if (!$generator instanceof \Magento\AcceptanceTestFramework\Generate\LauncherInterface) {
        throw new \InvalidArgumentException(
            'Generator ' . get_class($generator) . ' should implement LauncherInterface'
        );
    }
    $generator->launch();
}

\Magento\AcceptanceTestFramework\Generate\GenerateResult::displayResults();
