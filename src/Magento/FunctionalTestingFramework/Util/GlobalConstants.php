<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\Util;

final class GlobalConstants
{
    /**
     * A new id attribute that needs special handling to maintain backward compatibility.
     */
    const TEST_ID_ATTRIBUTE = 'stepKey';

    /**
     * The old attribute that should still work as an id attribute.
     */
    const DEPRECATED_TEST_ID_ATTRIBUTE = 'mergeKey';

    /**
     * GlobalConstants constructor.
     *
     * Make this private so it cannot be instantiated.
     */
    private function __construct()
    {
    }
}
