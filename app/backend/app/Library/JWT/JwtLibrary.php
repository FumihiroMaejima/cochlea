<?php

namespace App\Library\JWT;

class JwtLibrary
{
    /**
     * decode token header.
     *
     * @param string $value value
     * @return array
     */
    public static function decodeTokenHeader(string $value): array
    {
        exec("echo $value | base64 -D", $output);
        return $output;
    }

    /**
     * decode token payload.
     *
     * @param string $value value
     * @return array
     */
    public static function decodeTokenPayload(string $value): array
    {
        exec("echo $value | base64 -D", $output);
        return $output;
    }

    /**
     * decode token header.
     *
     * @param string $value value
     * @return array
     */
    public static function encodeTokenHeader(string $value = '{"typ":"JWT","alg":"none"}'): array
    {
        exec("echo $value | base64", $output);
        return $output;
    }
}
