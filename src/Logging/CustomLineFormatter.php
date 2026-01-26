<?php

namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;

class CustomLineFormatter extends LineFormatter
{
    private const CUSTOM_FORMAT = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";

    public function __construct()
    {
        // Format + date au format lisible + on autorise les context/extra vides
        parent::__construct(self::CUSTOM_FORMAT, "Y-m-d H:i:s", true, true);
    }

    public function format(LogRecord $record): string
    {
        return parent::format($record);
    }
}
