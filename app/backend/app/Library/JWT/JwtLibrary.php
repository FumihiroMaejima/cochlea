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
        /* exec("echo \$SHELL", $shell);
        if (preg_match('/ash/', current($shell))) {
            exec("echo $value | base64 -d", $output);
        } else {
            exec("echo $value | base64 -D", $output);
        } */
        // ashだと小文字のdしか適用出来ない
        exec("echo $value | base64 -d", $output);
        // exec("echo $value | base64 -D", $output);
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
        // exec("echo $value | base64 -D", $output);
        exec("echo $value | base64 -d", $output);
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
