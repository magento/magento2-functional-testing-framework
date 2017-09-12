<?php
namespace Magento\FunctionalTestingFramework\Module;

use Codeception\Module\Sequence;
use Codeception\Exception\ModuleException;

/**
 * MagentoSequence module.
 *
 * @codingStandardsIgnoreFile
 */
class MagentoSequence extends Sequence
{
    protected $config = ['prefix' => ''];
}

if (!function_exists('msq') && !function_exists('msqs')) {
    require_once __DIR__ . '/../Util/msq.php';
} else {
    throw new ModuleException('Magento\FunctionalTestingFramework\Module\MagentoSequence', "function 'msq' and 'msqs' already defined");
}
