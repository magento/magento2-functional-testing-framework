<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

// @codingStandardsIgnoreFile
namespace Magento\FunctionalTestingFramework\Module;

use Magento\FunctionalTestingFramework\Codeception\Module\Sequence;
use Codeception\Exception\ModuleException;

/**
 * MagentoSequence module.
 *
 */
class MagentoSequence extends Sequence
{
    protected array $config = ['prefix' => ''];
}
if (!function_exists('msq') && !function_exists('msqs')) {
    require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'Util' . DIRECTORY_SEPARATOR . 'msq.php';
} else {
    throw new ModuleException('Magento\FunctionalTestingFramework\Module\MagentoSequence', "function 'msq' and 'msqs' already defined");
}
