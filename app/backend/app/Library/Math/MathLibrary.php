<?php

namespace App\Library\Math;

use App\Library\Math\PrimeNumberLibrary;

class MathLibrary
{
    // フェルマーの小定理
    // pが素数で，aがpの倍数でない正の整数のとき 下記の式が成り立つ
    // a**(p-1)≡1(modp)

    // フェルマーの小定理
    // pが素数で，aがpの倍数でない正の整数のとき 下記の式が成り立つ
    // a^(p-1)≡1(modp)

    // 合同式=割り算の余りに注目した式
    // 7 ≡ 4 (mod3) 7と4は割った余りが等しい(両方余り1)
    // aとbをnで割った余りが等しいとき，合同式では a≡b(modn),a≡b(modn) と書く

    /**
     * get Fermat's Little Theorem value
     *
     * @param int $value
     * @param int $light light value
     * @return array
     */
    public static function getFermatsLittleTheorem(int $value, int $lightValue): array
    {
        // a**(p-1)≡1(mod p)の検証
        $squared = ($value - 1);
        $mod = $lightValue % $value;

        $result = false;
        $leftValue = 0;
        // $valueより小さい値からループ開始
        for($i = ($value - 1); $i >= 0; $i--) {
            $checkValue = ($i ** $squared);
            // 余りが一致する場合
            if ($checkValue % $value === $mod) {
                $result = true;
                $leftValue = $i;
                break;
            }
        }

        return [
            'mod' => $mod,
            'leftValue' => $leftValue,
            'result' => $result,
        ];
    }

    // モジュラ逆数の取得
    // ax≡1(mod m)の時,
    // 両辺 aの逆数 a**(-1)=1/a をかけ、x=1/aとするとxが求められる
    // (整数aのモジュラ逆数)=a**(m−2) mod m
    // a = 3, m = 11, mod_inverse = 4
    // a = 4, m = 11, mod_inverse = 3

    // 互いに素な自然数=最大公約数が1の値

    /**
     * get mod inverse of parameter by $mod
     *
     * @param int $value
     * @param int $mod be prime value;
     * @return int
     */
    public static function getModInverse(int $value, int $mod): int
    {
        // ax≡1 mod p が成立する様な数xをモジュラ逆数と言う。(a,pが互いに素な素数であるのが前提)
        // x= a ** -1 と表記される事もある。

        // フェルマーの小定理を使う事で、a**(p-1)≡1(mod p)が成り立つ事が想定出来る(pが素数)
        // pが素数かつp>=3 素数の場合、(a**-1)を導く為に両辺に(a**(p-2))をかけると下記の通りになる。
        // (a**-1) ≡ (a**p-2) mod p
        // 故にモジュラ逆数は(a**-1)となりaの逆数と言える。

        // a **((p-1)(q-1)n + 1) ≡ a mod pq
        return ($value ** ($mod - 2)) % $mod;
    }
}
