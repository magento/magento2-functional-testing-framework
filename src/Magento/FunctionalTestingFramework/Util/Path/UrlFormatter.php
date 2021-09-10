<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magento\FunctionalTestingFramework\Util\Path;

use Magento\FunctionalTestingFramework\Exceptions\TestFrameworkException;

class UrlFormatter implements FormatterInterface
{
    /**
     * Return formatted url path from input string.
     *
     * @param string  $url
     * @param boolean $withTrailingSeparator
     *
     * @return string
     * @throws TestFrameworkException
     */
    public static function format(string $url, bool $withTrailingSeparator = true): string
    {
        $sanitizedUrl = rtrim($url, '/');

        // Remove all characters except letters, digits and $-_.+!*'(),{}|\\^~[]`<>#%";/?:@&=
        $sanitizedUrl = filter_var($sanitizedUrl, FILTER_SANITIZE_URL);

        if (false === $sanitizedUrl) {
            throw new TestFrameworkException("Invalid url: $url\n");
        }

        // Validate URL according to http://www.faqs.org/rfcs/rfc2396
        $validUrl = filter_var($sanitizedUrl, FILTER_VALIDATE_URL);

        if (false !== $validUrl) {
            return $withTrailingSeparator ? $validUrl . '/' : $validUrl;
        }

        // Validation might be failed due to missing URL scheme or host, attempt to build them and re-validate
        $validUrl = filter_var(self::buildUrl($sanitizedUrl), FILTER_VALIDATE_URL);

        if (false !== $validUrl) {
            return $withTrailingSeparator ? $validUrl . '/' : $validUrl;
        }

        throw new TestFrameworkException("Invalid url: $url\n");
    }

    /**
     * Try to build missing url scheme and host.
     *
     * @param string $url
     *
     * @return string
     */
    private static function buildUrl(string $url): string
    {
        $urlParts = parse_url($url);

        if (!isset($urlParts['scheme'])) {
            $urlParts['scheme'] = 'http';
        }

        if (!isset($urlParts['host'])) {
            $urlParts['host'] = rtrim($urlParts['path'], '/');
            $urlParts['host'] = str_replace("//", '/', $urlParts['host']);
            unset($urlParts['path']);
        }

        if (isset($urlParts['path'])) {
            $urlParts['path'] = rtrim($urlParts['path'], '/');
        }

        return str_replace("///", "//", self::merge($urlParts));
    }

    /**
     * Returns url from $parts given, used with parse_url output for convenience.
     * This only exists because of deprecation of http_build_url, which does the exact same thing as the code below.
     *
     * @param array $parts
     *
     * @return string
     */
    private static function merge(array $parts): string
    {
        $get = function ($key) use ($parts) {
            return $parts[$key] ?? '';
        };

        $pass = $get('pass');
        $user = $get('user');
        $userinfo = $pass !== '' ? "$user:$pass" : $user;
        $port = $get('port');
        $scheme = $get('scheme');
        $query = $get('query');
        $fragment = $get('fragment');
        $authority = ($userinfo !== '' ? "$userinfo@" : '') . $get('host') . ($port ? ":$port" : '');

        return str_replace(
            [
                '%scheme',
                '%authority',
                '%path',
                '%query',
                '%fragment'
            ],
            [
                strlen($scheme) ? "$scheme:" : '',
                strlen($authority) ? "//$authority" : '',
                $get('path'),
                strlen($query) ? "?$query" : '',
                strlen($fragment) ? "#$fragment" : ''
            ],
            '%scheme%authority%path%query%fragment'
        );
    }
}
