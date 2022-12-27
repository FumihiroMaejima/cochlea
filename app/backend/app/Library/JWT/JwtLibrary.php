<?php

namespace App\Library\JWT;

class JwtLibrary
{
    /**
     * decode token header.
     *
     * @param string $value value
     * @return string
     */
    public static function decodeTokenHeader(string $value): string
    {
        return exec("echo $value | base64 -D");
    }

    /**
     * decode token payload.
     *
     * @param string $value value
     * @return string
     */
    public static function decodeTokenPayload(string $value): string
    {
        return exec("echo $value | base64 -D");
    }

    /**
     * decode token header.
     *
     * @param string $value value
     * @return string
     */
    public static function encodeTokenHeader(string $value = '{"typ":"JWT","alg":"none"}'): string
    {
        return exec("echo $value | base64");
    }
}
