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
        // 文字化けデータが含まれる為UTF-8へ変換をかける
        return mb_convert_encoding($output, 'UTF-8');
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
