<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\DataGenerator\Util;

interface DataReferenceResolverInterface
{
    const REFERENCE_REGEX_PATTERN = "/(?<reference>{{[\w]+\..+}})/";

    /**
     * @param string $data
     * @param string $originalDataEntity
     * @return mixed
     */
    public function getDataReference(string $data, string $originalDataEntity);

    /**
     * @param string $data
     * @param string $originalDataEntity
     * @return mixed
     */
    public function getDataUniqueness(string $data, string $originalDataEntity);
}
