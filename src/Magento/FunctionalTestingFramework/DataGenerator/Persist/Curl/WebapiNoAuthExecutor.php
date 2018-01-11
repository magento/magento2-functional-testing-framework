<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Persist\Curl;

/**
 * Curl executor for Magento Web Api requests that do not require authorization.
 */
class WebapiNoAuthExecutor extends WebapiExecutor
{
    /**
     * No authorization is needed and just return.
     *
     * @return void
     */
    protected function authorize()
    {
        //NOP
    }
}
