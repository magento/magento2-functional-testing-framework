<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataTransport;

/**
 * Curl executor for Magento Web Api requests that do not require authorization.
 */
class WebApiNoAuthExecutor extends WebApiExecutor
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
