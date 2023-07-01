<?php

namespace App\Library\Math;

use App\Library\Math\PrimeNumberLibrary;

class MathLibrary
{
    // フェルマーの小定理
    // pが素数で，aがpの倍数でない正の整数のとき 下記の式が成り立つ
    // a^(p-1)≡1(modp)

    /**
     * get Fermat's Little Theorem value
     *
     * @param int $value
     * @param int $light light value
     * @return array
     */
    public static function getFermatsLittleTheorem(int $value, int $lightValue): array
    {
        // a^(p-1)≡1(modp)の検証
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
}
