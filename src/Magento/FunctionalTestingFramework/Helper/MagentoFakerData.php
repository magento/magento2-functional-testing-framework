<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Helper;

/**
 * Class MagentoFakerData
 */
class MagentoFakerData extends \Codeception\Module
{
    /**
     * Get Customer data.
     *
     * @param array $additional
     * @return array
     */
    public function getCustomerData(array $additional = [])
    {
        return [$additional];
    }

    /**
     * Get category data.
     *
     * @return array
     */
    public function getCategoryData()
    {
        return [];
    }

    /**
     * Get simple product data.
     *
     * @return array
     */
    public function getProductData()
    {
        return [];
    }

    /**
     * Get Content Page Data.
     *
     * @return array
     */
    public function getContentPage()
    {
        return [];
    }
}
