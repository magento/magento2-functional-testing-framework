<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sniffs\Arrays;

use PHP_CodeSniffer\Sniffs\Sniff;
use PHP_CodeSniffer\Files\File;

class ShortArraySyntaxSniff implements Sniff
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        return [T_ARRAY];
    }

    /**
     * {@inheritdoc}
     */
    public function process(File $sourceFile, $stackPtr)
    {
        $sourceFile->addError(
            'Short array syntax must be used; expected "[]" but found "array()"',
            $stackPtr,
            'ShortArraySyntax'
        );
    }
}
