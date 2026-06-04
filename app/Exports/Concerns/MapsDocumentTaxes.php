<?php

namespace App\Exports\Concerns;

use App\Exports\Support\ExportFormatter;
use App\Models\Estimate;
use App\Models\Invoice;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait MapsDocumentTaxes
{
    protected static function invoiceDocumentTaxesToJson(Invoice $invoice): string
    {
        return self::documentTaxesToJson($invoice->taxes->whereNull('invoice_item_id'));
    }

    protected static function estimateDocumentTaxesToJson(Estimate $estimate): string
    {
        return self::documentTaxesToJson($estimate->taxes->whereNull('estimate_item_id'));
    }

    /**
     * @param  Collection<int, Model>  $taxes
     */
    protected static function documentTaxesToJson(Collection $taxes): string
    {
        $payload = $taxes
            ->map(fn ($tax) => array_filter([
                'tax_type_id' => $tax->tax_type_id,
                'name' => $tax->taxType?->name ?? $tax->name ?? null,
                'percent' => $tax->percent ?: null,
                'amount' => $tax->amount ? ExportFormatter::money($tax->amount) : null,
            ]))
            ->values()
            ->all();

        return ExportFormatter::json($payload);
    }
}
