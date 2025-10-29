<?php
/**
 * Copyright 2017 Adobe
 * All Rights Reserved.
 */

namespace Magento\Sniffs\MicroOptimizations;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class IsNullSniff implements Sniff
{
    /**
     * @var string
     */
    protected $blocklist = 'is_null';

    /**
     * @inheritdoc
     */
    public function register()
    {
        return [T_STRING];
    }

    /**
     * @inheritdoc
     */
    public function process(File $sourceFile, $stackPtr)
    {
        $tokens = $sourceFile->getTokens();
        if ($tokens[$stackPtr]['content'] === $this->blocklist) {
            $sourceFile->addError(
                "is_null must be avoided. Use strict comparison instead.",
                $stackPtr,
                'IsNullUsage'
            );
        }
    }
}
