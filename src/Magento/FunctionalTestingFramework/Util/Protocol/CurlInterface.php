<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\FunctionalTestingFramework\Util\Protocol;

/**
 * Curl protocol interface.
 */
interface CurlInterface
{
    /**
     * HTTP request methods.
     */
    const GET = 'GET';
    const PUT = 'PUT';
    const POST = 'POST';
    const DELETE = 'DELETE';

    /**
     * Add additional option to cURL.
     *
     * @param int $option
     * @param mixed $value
     * @return $this
     */
    public function addOption($option, $value);

    /**
     * Send request to the remote server.
     *
     * @param string $url
     * @param mixed $body
     * @param string $method
     * @param mixed $headers
     * @return void
     */
    public function write($url, $body = [], $method = CurlInterface::POST, $headers = []);

    /**
     * Read response from server.
     *
     * @param string $successRegex
     * @param string $returnRegex
     * @return mixed
     */
    public function read($successRegex = null, $returnRegex = null);

    /**
     * Close the connection to the server.
     *
     * @return void
     */
    public function close();
}
