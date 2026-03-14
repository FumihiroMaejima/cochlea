<?php

declare(strict_types=1);

namespace App\Library\Log;

use App\Exceptions\MyApplicationHttpException;
use App\Library\Message\StatusCodeMessages;
use App\Library\Array\ArrayLibrary;
use App\Library\Time\TimeLibrary;
use Monolog\Formatter\LineFormatter;
use Exception;

class LogFormatterLibrary extends LineFormatter
{
        /**
        * @param string|null $format                The format of the message
        * @param string|null $dateFormat            The format of the timestamp: one supported by DateTime::format
        * @param bool        $allowInlineLineBreaks Whether to allow inline line breaks in log entries
        */
}
