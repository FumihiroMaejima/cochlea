<?php

namespace App\Library\Performance;

class LeibnizFormulaLibrary
{
    /**
     * ライプニッツ級数を計算する
     *
     * @return int value
     */
    public static function leibnizFormula()
    {
      $s = 0;
      for ($i = 0; $i < 10**8; $i++)
      {
        $s += ((-1)**$i)/(2*$i + 1);
      }
      return $s;
    }
}

LeibnizFormulaLibrary::leibnizFormula();
