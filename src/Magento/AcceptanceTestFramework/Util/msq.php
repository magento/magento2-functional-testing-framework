<?php
use Magento\AcceptanceTestFramework\Module\MagentoSequence;

if (!function_exists('msq')) {
    function msq($id = null)
    {
        if ($id and isset(MagentoSequence::$hash[$id])) {
            return MagentoSequence::$hash[$id];
        }
        $prefix = MagentoSequence::$prefix;
        $sequence = $prefix . uniqid($id);
        if ($id) {
            MagentoSequence::$hash[$id] = $sequence;
        }
        return $sequence;
    }
}

if (!function_exists('msqs')) {
    function msqs($id = null)
    {
        if ($id and isset(MagentoSequence::$suiteHash[$id])) {
            return MagentoSequence::$suiteHash[$id];
        }
        $prefix = MagentoSequence::$prefix;
        $sequence = $prefix . uniqid($id);
        if ($id) {
            MagentoSequence::$suiteHash[$id] = $sequence;
        }
        return $sequence;
    }
}
