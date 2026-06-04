<?php

namespace App\Support\Csv;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvResponse
{
    /**
     * @param  Closure(mixed): array<int, mixed>|array<int, array<int, mixed>>  $mapRow
     */
    public static function stream(
        string $filename,
        array $headers,
        Builder $query,
        Closure $mapRow,
    ): StreamedResponse {
        return response()->streamDownload(function () use ($headers, $query, $mapRow) {
            $handle = fopen('php://output', 'w');

            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, $headers);

            foreach ($query->cursor() as $model) {
                $mapped = $mapRow($model);
                $rows = is_array($mapped[0] ?? null) ? $mapped : [$mapped];

                foreach ($rows as $row) {
                    fputcsv($handle, $row);
                }
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
