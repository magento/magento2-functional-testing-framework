<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

if (isset($_POST['baseUrl']) && isset($_POST['username']) && isset($_POST['password']) && isset($_POST['command'])) {
    $baseUrl = urldecode($_POST['baseUrl']);
    $username = urldecode($_POST['username']);
    $password = urldecode($_POST['password']);
    $command = urldecode($_POST['command']);
    if (array_key_exists("arguments", $_POST)) {
        $arguments = urldecode($_POST['arguments']);
    } else {
        $arguments = null;
    }

    if (isAuthenticated($baseUrl, $username, $password)) {
        $php = PHP_BINDIR ? PHP_BINDIR . '/php' : 'php';
        $magentoBinary = $php . ' -f ../../../../bin/magento';
        $valid = validateCommand($magentoBinary, $command);
        if ($valid) {
            exec(
                escapeCommand($magentoBinary . ' ' . $command) . " $arguments" ." 2>&1",
                $output,
                $exitCode
            );
            if ($exitCode == 0) {
                http_response_code(202);
            } else {
                http_response_code(500);
            }
            echo implode("\n", $output);
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
 * Returns if credentials are successfully authenticated.
 *
 * @param string $baseUrl
 * @param string $username
 * @param string $password
 * @return bool
 */
function isAuthenticated($baseUrl, $username, $password)
{
    $userData = [
        "username" => $username,
        "password" => $password
    ];
    $ch = curl_init($baseUrl . "/index.php/rest/V1/integration/admin/token");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($userData));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_COOKIEFILE, '');
    curl_setopt(
        $ch,
        CURLOPT_HTTPHEADER,
        array("Content-Type: application/json", "Content-Lenght: " . strlen(json_encode($userData)))
    );

    $token = curl_exec($ch);

    if (!empty($token) && curl_getinfo($ch, CURLINFO_HTTP_CODE) === 200) {
        curl_close($ch);
        return true;
    } else {
        echo "Authentication error.";
        curl_close($ch);
        return false;
    }
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
