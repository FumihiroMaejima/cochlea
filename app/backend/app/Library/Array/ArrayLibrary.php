<?php

namespace App\Library\Array;

use stdClass;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;

class ArrayLibrary
{
    /**
     * convert stdClass (& stdClasses in array) to array
     *
     * @param array<int|string, mixed>|stdClass $object
     * @return array converted value
     */
    public static function toArray(stdClass|array $object): array
    {
        $value = json_decode(json_encode($object), true);

        if (!is_array($value)) {
            throw new MyApplicationHttpException(
                StatusCodeMessages::STATUS_CODE_500,
                'failed converting to array.'
            );
        }

        return $value;
    }

    /**
     * get array item first index value.
     *
     * @param array $items
     * @return array<int|string, mixed>|stdClass first index value
     */
    public static function getFirst(array $items): array|stdClass
    {
        return current($items);
    }
}
