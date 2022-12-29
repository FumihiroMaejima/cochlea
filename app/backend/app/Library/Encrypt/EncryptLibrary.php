<?php

namespace App\Library\Encrypt;

class EncryptLibrary
{
    // ブロック暗号:ある特定のビット数のまとまりを一度に処理する
    // ストリーム暗号:データの流れ（ストリーム）を順次処理していく
    // モード:ブロック暗号アルゴリズムの繰り返し方法
    // AES:通信データを区切り、置き換え・並べ替えのセットを複数回繰り返すアルゴリズム。(AESは128bitの平文をまとめて暗号化し、128bitの暗号文を作成する。)
    // 128:鍵長のこと。128bit
    // ECB:ECBモード。平文ブロックを暗号化したものが暗号文ブロックとなる。(非推奨)
    // CBC(Cipher Block Chaining):CBCモード。直前の暗号文ブロックと平文ブロックのXOR(排他的論理和)の値を暗号化。初期化ベクトル(IV。暗号化の度に異なるランダム値)が必須。
    /** @deprecated */
    private const MAIL_ENCRYPT_ALG_ECB = 'AES-128-ECB'; // ECBモード
    private const MAIL_ENCRYPT_ALG_CBC = 'AES-128-CBC'; // CBCモード
    private const MAIL_ENCRYPT_KEY = 'testXyZaBc159';

    /**
     * encrypt value
     *
     * @param string $value value
     * @param string $iv initialization vector 初期化ベクトル
     * @return string encrypt value
     */
    public static function encrypt(string $value, string $iv = self::MAIL_ENCRYPT_KEY): string
    {
        return openssl_encrypt($value, self::MAIL_ENCRYPT_ALG_CBC, $iv);
    }

    /**
     * decrypt value
     *
     * @param string $value value
     * @param string $iv initialization vector 初期化ベクトル
     * @return string encrypt value
     */
    public static function decrypt(string $value, string $iv = self::MAIL_ENCRYPT_KEY): string
    {
        return openssl_decrypt($value, self::MAIL_ENCRYPT_ALG_CBC, $iv);
    }
}
