<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Sniffs\Files;

/**
 * Line length sniff which ignores long lines in case they contain strings intended for translation.
 */
class LineLengthSniff extends \Generic_Sniffs_Files_LineLengthSniff
{
    /**
     * Having previous line content allows to ignore long lines in case of multi-line declaration.
     *
     * @var string
     */
    protected $previousLineContent = '';

    /**
     * {@inheritdoc}
     */
    protected function checkLineLength(\PHP_CodeSniffer_File $phpcsFile, $stackPtr, $lineContent)
    {
        $previousLineRegexp = '~__\($|\bPhrase\($~';
        $currentLineRegexp = '~__\(.+\)|\bPhrase\(.+\)~';
        $currentLineMatch = preg_match($currentLineRegexp, $lineContent) !== 0;
        $previousLineMatch = preg_match($previousLineRegexp, $this->previousLineContent) !== 0;
        $this->previousLineContent = $lineContent;
        if (! $currentLineMatch && !$previousLineMatch) {
            parent::checkLineLength($phpcsFile, $stackPtr, $lineContent);
        }
    }
}
