<?php

namespace App\Library\Encrypt;

use App\Library\Math\PrimeNumberLibrary;

class RSAEncryptLibrary
{
    // RSA暗号=公開鍵暗号(非対称鍵暗号)方式

    // 基準値
    private const MAX_PRIME_BASE_NUMBER = 300000000; // 3億

    private const BASE_N_VALUE = 299999999;
    private const BASE_E_VALUE = 17477;
    private const BASE_D_VALUE = 15845;

    /**
     * max prime number value
     *
     * @return int
     */
    public static function getMaxPrimeNumberForRSAEncrypt(): int
    {
        return PrimeNumberLibrary::getMaxPrimeNumber(self::MAX_PRIME_BASE_NUMBER);
    }

    /**
     * get encrypt base values
     *
     * @param int $value
     * @return array
     */
    public static function getEncryptBaseValueList(int $value): array
    {
        // パラメーター以下でもっとも大きい$p,$qの値を素因数分解結果から取得
        // ケースによって$nの値を柔軟に取得出来る為最大値から＊個目を取得するか指定すると良い
        $n = PrimeNumberLibrary::getMaxTwoPairPrimeFactorization($value);
        [$p, $q] = PrimeNumberLibrary::getPrimeFactorization($n);

        // E,Dの取得
        $ed = self::getEAndD($p, $q);

        return [
            'p' => $p,
            'q' => $q,
            'N' => $n,
            'E' => $ed['E'],
            'D' => $ed['D']
        ];
    }

    /**
     * get
     *
     * @param int $p prime number p
     * @param int $q prime number q
     * @return array
     */
    public static function getEAndD(int $p, int $q): array
    {
        $result['ED'] = ($p-1) * ($q-1) * 1 + 1;
        $ed = $result['ED'];
        $e = 0;
        $d = 0;

        // 大きい値を基準値にする
        $base = $p >= $q ? $p : $q;

        // 最大値の為パラメーターから減算して確認
        for ($i = $base - 1; 0 < $i; $i--) {
            // 同じ値は参照しない
            if (($i === ($p - 1)) ||($i === ($q - 1))) {
                continue;
            }
            // 素数かつ割り切れる値
            if (
                PrimeNumberLibrary::isPrimeNumber($i) &&
                ($ed % $i === 0)
            ) {
                $e = $i;
                $d = $ed / $i;
                break;
            }

        }
        $result['E'] = $e;
        $result['D'] = $d;

        return $result;
    }

    /**
     * encrypt value
     *
     * @param int $value value
     * @return int encrypt value
     */
    public static function encrypt(string $value): int
    {
        return ($value ** self::BASE_E_VALUE) % self::BASE_N_VALUE;
    }

    /**
     * decrypt value
     *
     * @param int $value value
     * @return int decrypt value
     */
    public static function decrypt(string $value): int
    {
        return ($value ** self::BASE_D_VALUE) % self::BASE_N_VALUE;
    }
}
