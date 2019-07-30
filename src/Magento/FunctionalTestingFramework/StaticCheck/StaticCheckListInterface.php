<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\FunctionalTestingFramework\StaticCheck;

/**
 * Contains a list of Static Check Scripts
 * @api
 */
interface StaticCheckListInterface
{
    /**
     * Gets list of static check script instances
     *
     * @return StaticCheckInterface[]
     */
    public function getStaticChecks();
}
