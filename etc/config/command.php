<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

require_once __DIR__ . '/../../../../app/bootstrap.php';

if (!empty($_POST['token']) && !empty($_POST['command'])) {
    $magentoObjectManagerFactory = \Magento\Framework\App\Bootstrap::createObjectManagerFactory(BP, $_SERVER);
    $magentoObjectManager = $magentoObjectManagerFactory->create($_SERVER);
    $tokenModel = $magentoObjectManager->get(\Magento\Integration\Model\Oauth\Token::class);

    $tokenPassedIn = urldecode($_POST['token'] ?? '');
    $command = urldecode($_POST['command'] ?? '');
    $arguments = urldecode($_POST['arguments'] ?? '');
    $timeout = floatval(urldecode($_POST['timeout'] ?? 60));

    // Token returned will be null if the token we passed in is invalid
    $tokenFromMagento = $tokenModel->loadByToken($tokenPassedIn)->getToken();
    if (!empty($tokenFromMagento) && ($tokenFromMagento === $tokenPassedIn)) {
        $php = PHP_BINDIR ? PHP_BINDIR . '/php' : 'php';
        $magentoBinary = $php . ' -f ../../../../bin/magento';
        $valid = validateCommand($magentoBinary, $command);
        if ($valid) {
            $fullCommand = escapeshellcmd($magentoBinary . " $command" . " $arguments");
            $process = Symfony\Component\Process\Process::fromShellCommandline($fullCommand);
            $process->setIdleTimeout($timeout);
            $process->setTimeout(0);
            $idleTimeout = false;
            try {
                $process->run();
                $output = $process->getOutput();
                if (!$process->isSuccessful()) {
                    $failureOutput = $process->getErrorOutput();
                    if (!empty($failureOutput)) {
                        $output = $failureOutput;
                    }
                }
                if (empty($output)) {
                    $output = "CLI did not return output.";
                }

            } catch (Symfony\Component\Process\Exception\ProcessTimedOutException $exception) {
                $output = "CLI command timed out, no output available.";
                $idleTimeout = true;
            }

            $exitCode = $process->getExitCode();

            if ($process->isSuccessful() || $idleTimeout) {
                http_response_code(202);
            } else {
                http_response_code(500);
            }

            // Suppress file paths from output
            echo suppressFilePaths($output);
        } else {
            http_response_code(403);
            echo "Given command not found valid in Magento CLI Command list.";
        }
    } else {
        http_response_code(401);
        echo("Command not unauthorized.");
    }
} else {
    http_response_code(412);
    echo("Required parameters are not set.");
}

/**
 * Returns escaped command.
 *
 * @param string $command
 * @return string
 */
function escapeCommand($command)
{
    $escapeExceptions = [
        '> /dev/null &' => '--dev-null-amp--'
    ];

    $command = escapeshellcmd(
        str_replace(array_keys($escapeExceptions), array_values($escapeExceptions), $command)
    );

    return str_replace(array_values($escapeExceptions), array_keys($escapeExceptions), $command);
}

/**
 * Checks magento list of CLI commands for given $command. Does not check command parameters, just base command.
 * @param string $magentoBinary
 * @param string $command
 * @return bool
 */
function validateCommand($magentoBinary, $command)
{
    exec($magentoBinary . ' list', $commandList);
    // Trim list of commands after first whitespace
    $commandList = array_map("trimAfterWhitespace", $commandList);
    return in_array(trimAfterWhitespace($command), $commandList);
}

/**
 * Returns given string trimmed of everything after the first found whitespace.
 * @param string $string
 * @return string
 */
function trimAfterWhitespace($string)
{
    return strtok($string, ' ');
}

/**
 * Suppress file paths in string.
 * @param string $string
 * @return string
 */
function suppressFilePaths(string $string): string
{
    // Match file paths on both *nix and Windows system
    $filePathPattern = '~(?:[A-Za-z]:[\\\/]|\\\\|\/)\S+~';
    $replacement = '[suppressed_path]';

    preg_match_all($filePathPattern, $string, $matches);
    if (!empty($matches)) {
        foreach ($matches[0] as $match) {
            $string = str_replace($match, $replacement, $string);
        }
    }
    return $string;
}
