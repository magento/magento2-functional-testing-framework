<?php
/**
 * Copyright 2020 Adobe
 * All Rights Reserved.
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
