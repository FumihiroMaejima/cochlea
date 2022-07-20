<?php

namespace App\Library\Array;

use stdClass;
use App\Exceptions\MyApplicationHttpException;
use App\Exceptions\ExceptionStatusCodeMessages;

class ArrayLibrary
{
    /**
     * convert stdClass to array
     *
     * @param stdClass $object
     * @return array converted value
     */
    public static function toArray(stdClass $object): array
    {
        $value = json_decode(json_encode($object), true);

        if (!is_array($value)) {
            throw new MyApplicationHttpException(
                ExceptionStatusCodeMessages::STATUS_CODE_500,
                'failed converting to array.'
            );
        }

        return $value;
    }
}
