<?php

declare(strict_types=1);

namespace App\Library\Fiber;

use stdClass;
use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use Fiber;
use FiberError;
use ReflectionFiber;

class FiberLibrary
{
    /**
     * get fiber.
     * @param int $value
     * @return Fiber
     */
    public static function getFiber(int $value): Fiber
    {
        $fiber = new Fiber(
            function($value): int {
                $two = Fiber::suspend($value);
                $three = Fiber::suspend($two);
                $four = Fiber::suspend($three);
                $five = Fiber::suspend($four);
                $six = Fiber::suspend($five);
                return $six;
            }
        );

        return $fiber;
    }

    /**
     * sampel suspend.
     * @return Fiber
     */
    public static function sampleSuspend(): Fiber
    {
        $fiber = new Fiber(function (): string {
            $value = Fiber::suspend('saple value');
            return "catch resume $value";
        });

        return $fiber;
    }
}
