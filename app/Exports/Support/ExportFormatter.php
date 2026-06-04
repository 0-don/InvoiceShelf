<?php

namespace App\Exports\Support;

use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;

final class ExportFormatter
{
    public static function money(mixed $amount): string
    {
        if ($amount === null) {
            return '';
        }

        return number_format(((int) $amount) / 100, 2, '.', '');
    }

    public static function date(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($value instanceof CarbonInterface) {
            return $value->format('Y-m-d');
        }

        return Carbon::parse($value)->format('Y-m-d');
    }

    public static function bool(bool $value): string
    {
        return $value ? '1' : '0';
    }

    public static function yesNo(mixed $value): string
    {
        return in_array($value, [true, 1, '1', 'YES', 'yes'], true) ? '1' : '0';
    }

    /**
     * @param  array<int, mixed>  $data
     */
    public static function json(array $data): string
    {
        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR);
    }
}
