<?php

namespace App\Exports\Concerns;

use App\Exports\Support\ExportFormatter;
use App\Models\EstimateItem;
use App\Models\InvoiceItem;

trait MapsLineTaxes
{
    /**
     * @return array<int, array<string, mixed>>
     */
    protected static function lineTaxes(InvoiceItem|EstimateItem $item): array
    {
        return $item->taxes
            ->map(fn ($tax) => array_filter([
                'tax_type_id' => $tax->tax_type_id,
                'name' => $tax->taxType?->name ?? $tax->name ?? null,
                'percent' => $tax->percent ?: null,
                'amount' => $tax->amount ? ExportFormatter::money($tax->amount) : null,
            ]))
            ->values()
            ->all();
    }
}
