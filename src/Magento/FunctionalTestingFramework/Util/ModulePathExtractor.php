<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util;

/**
 * Class ModulePathExtractor, resolve module reference based on path
 */
class ModulePathExtractor
{
    const MAGENTO = 'Magento';

    /**
     * Extracts module name from the path given
     * @param string $path
     * @return string
     */
    public function extractModuleName($path)
    {
        if (empty($path)) {
            return "NO MODULE DETECTED";
        }
        $paths = explode(DIRECTORY_SEPARATOR, $path);
        if (count($paths) < 3) {
            return "NO MODULE DETECTED";
        } elseif ($paths[count($paths)-3] == "Mftf") {
            // app/code/Magento/[Analytics]/Test/Mftf/Test/SomeText.xml
            return $paths[count($paths)-5];
        }
        // dev/tests/acceptance/tests/functional/Magento/FunctionalTest/[Analytics]/Test/SomeText.xml
        return $paths[count($paths)-3];
    }

    /**
     * Extracts the extension form the path, Magento for dev/tests/acceptance, [name] before module otherwise
     * @param string $path
     * @return string
     */
    public function getExtensionPath($path)
    {
        $paths = explode(DIRECTORY_SEPARATOR, $path);
        if ($paths[count($paths)-3] == "Mftf") {
            // app/code/[Magento]/Analytics/Test/Mftf/Test/SomeText.xml
            return $paths[count($paths)-6];
        }
        return self::MAGENTO;
    }
}
