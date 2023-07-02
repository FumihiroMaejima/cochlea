<?php

namespace App\Library\Math;

class PrimeNumberLibrary
{
    private const _EVEN_VALUE_BASE_NUMBER = 2; // 偶数の基数

    /**
     * either parameter is prime value (1とその数以外に約数が無い正の整数(自然数))
     *
     * @param int $value value
     * @return bool
     */
    public static function isPrimeNumber(int $value): bool
    {
        // 2より小さい値は素数ではない
        if ($value < self::_EVEN_VALUE_BASE_NUMBER) {
            return false;
        }

        // 2は素数
        if ($value === self::_EVEN_VALUE_BASE_NUMBER) {
            return true;
        }

        // 2以外の偶数は素数ではない
        if (($value % self::_EVEN_VALUE_BASE_NUMBER) === 0) {
            return false;
        }

        // 奇数で割り切れるかを判定
        // 自然数Nが合成数(1より大きい素数ではない自然数)の時は必ず√N以下の約数がある
        // (奇数だけを参照する為2ずつ増加)
        $squareRoot = sqrt($value);
        for ($i = 3; $i <= $squareRoot; $i += 2) {
            if ($value % $i === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * get max prime number in parameter
     *
     * @param int $value value
     * @return int
     */
    public static function getMaxPrimeNumber(int $value): int
    {
        $largestPrime = 0;
        // 最大値の為パラメーターから減算して確認
        for ($i = $value; 0 < $i; $i--) {
            if (self::isPrimeNumber($i)) {
                $largestPrime = $i;
                break;
            }
        }

        return $largestPrime;
    }

    /**
     * get greater prime number in parameter
     *
     * @param int $value value
     * @param int $maxCount get count
     * @return array
     */
    public static function getGreaterPrimeNumbers(int $value, int $maxCount = 1): array
    {
        $primeNumberList = [];
        $count = 0;
        // 最大値の為パラメーターから減算して確認
        for ($i = $value; 0 < $i; $i--) {
            if (self::isPrimeNumber($i)) {
                $primeNumberList[] = $i;
                $count++;
                if ($count === $maxCount) {
                    break;
                }
            }

        }

        return $primeNumberList;
    }

    /**
     * get prime factorization values (素因数分解)
     *
     * @param int $value value
     * @return array
     */
    public static function getPrimeFactorization(int $value): array
    {
        $factors = [];
        $divisor = 2;
        $number = $value;

        while ($number > 1) {
            // 割り切れる場合
            if ($number % $divisor === 0) {
                // 素因数を格納
                $factors[] = $divisor;
                $number = $number / $divisor;
            } else {
                $divisor++;
            }
        }

        return $factors;
    }

    /**
     * get greatest common divisor value (GCD=最大公約数)
     *
     * @param int $value1 value
     * @param int $value2 compair value
     * @return int
     */
    public static function getGreatestCommonDivisor(int $value1, int $value2): int
    {
        // 割り切れたら終了
        while ($value2 != 0) {
            $remainder = $value1 % $value2;
            $value1 = $value2;
            $value2 = $remainder;
        }
        // 絶対値に変換
        return abs($value1);
    }

    /**
     * check is greatest common divisor value is 1
     *
     * @param int $value1 value
     * @param int $value2 compair value
     * @return int
     */
    public static function isGcdIsOne(int $value1, int $value2): int
    {
        // 最大公約数が1=互いに素な値
        return self::getGreatestCommonDivisor($value1, $value2) === 1;
    }
}
