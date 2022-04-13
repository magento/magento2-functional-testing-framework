<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Module\Util;

class ModuleUtils
{
    /**
     * Module util function that returns UTF-8 encoding string with control/invisible characters removed,
     * and it returns the original string when on error.
     *
     * @param string $input
     * @return string
     */
    public function utf8SafeControlCharacterTrim(string $input): string
    {
        // Convert $input string to UTF-8 encoding
        $convInput = iconv("ISO-8859-1", "UTF-8//IGNORE", $input);
        if ($convInput !== false) {
            // Remove invisible control characters, unused code points and replacement character
            // so that they don't break xml test results for Allure
            $cleanInput = preg_replace('/[^\PC\s]|\x{FFFD}/u', '', $convInput);
            if ($cleanInput !== null) {
                return $cleanInput;
            } else {
                $err = preg_last_error_msg();
                print("MagentoCLI response preg_replace() with error $err.\n");
            }
        }

        return $input;
    }
}
