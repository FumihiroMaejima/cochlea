<?php

declare(strict_types=1);

namespace App\Library\Log;

use App\Library\Time\TimeLibrary;
use Monolog\Formatter\LineFormatter;

class LogFormatterLibrary extends LineFormatter
{
    public const DATE_FORMAT = TimeLibrary::DEFAULT_DATE_TIME_MILLI_SECOND_FORMAT;

    // ログのフォーマットはMonologのLineFormatterを継承している為、以下の定数は使用可能。
    // 変更したい場合は、親クラスの定数を上書きする形で定義する。
    public const SIMPLE_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context%\n";

    /**
     * @param string|null $format                The format of the message
     * @param string|null $dateFormat            The format of the timestamp: one supported by DateTime::format
     * @param bool        $allowInlineLineBreaks Whether to allow inline line breaks in log entries
     */
    public function __construct(
        ?string $format = null,
        ?string $dateFormat = self::DATE_FORMAT,
        bool $allowInlineLineBreaks = false,
        bool $ignoreEmptyContextAndExtra = false,
        bool $includeStacktraces = false)
    {
        // format設定は固定
        parent::__construct($format, self::DATE_FORMAT, $allowInlineLineBreaks, $ignoreEmptyContextAndExtra, $includeStacktraces);
    }
}
