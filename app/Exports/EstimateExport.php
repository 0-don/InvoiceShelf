<?php

namespace App\Exports;

use App\Exports\Concerns\MapsCustomFields;
use App\Exports\Concerns\MapsDocumentTaxes;
use App\Exports\Concerns\MapsLineTaxes;
use App\Exports\Support\ExportFormatter;
use App\Models\Estimate;
use App\Models\EstimateItem;
use App\Support\Csv\CsvResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EstimateExport
{
    use MapsCustomFields;
    use MapsDocumentTaxes;
    use MapsLineTaxes;

    public static function download(Request $request): StreamedResponse
    {
        return CsvResponse::stream(
            'estimates-'.now()->format('Y-m-d').'.csv',
            self::headers(),
            self::query($request),
            fn (Estimate $estimate) => self::map($estimate),
        );
    }

    /**
     * @return array<int, string>
     */
    public static function headers(): array
    {
        return [
            'id',
            'company_id',
            'estimate_number',
            'reference_number',
            'sequence_number',
            'unique_hash',
            'customer_id',
            'customer',
            'customer_email',
            'customer_tax_id',
            'customer_company_name',
            'estimate_date',
            'expiry_date',
            'status',
            'currency_id',
            'currency',
            'exchange_rate',
            'tax_per_item',
            'tax_included',
            'discount_per_item',
            'sub_total',
            'discount',
            'discount_type',
            'tax',
            'total',
            'base_sub_total',
            'base_discount_val',
            'base_tax',
            'base_total',
            'notes',
            'document_taxes',
            'custom_fields',
            'created_at',
            'updated_at',
            'items',
        ];
    }

    public static function query(Request $request): Builder
    {
        return Estimate::query()
            ->with([
                'customer',
                'currency',
                'fields.customField',
                'taxes.taxType',
                'items' => fn ($query) => $query->orderBy('id'),
                'items.taxes.taxType',
            ])
            ->whereCompany()
            ->applyFilters($request->all())
            ->orderByDesc('sequence_number');
    }

    /**
     * @return array<int, mixed>
     */
    public static function map(Estimate $estimate): array
    {
        return [
            $estimate->id,
            $estimate->company_id,
            $estimate->estimate_number,
            $estimate->reference_number,
            $estimate->sequence_number,
            $estimate->unique_hash,
            $estimate->customer_id,
            $estimate->customer?->name ?? '',
            $estimate->customer?->email ?? '',
            $estimate->customer?->tax_id ?? '',
            $estimate->customer?->company_name ?? '',
            ExportFormatter::date($estimate->estimate_date),
            ExportFormatter::date($estimate->expiry_date),
            $estimate->status,
            $estimate->currency_id,
            $estimate->currency?->code ?? '',
            $estimate->exchange_rate,
            ExportFormatter::yesNo($estimate->tax_per_item),
            ExportFormatter::yesNo($estimate->tax_included),
            ExportFormatter::yesNo($estimate->discount_per_item),
            ExportFormatter::money($estimate->sub_total),
            ExportFormatter::money($estimate->discount_val),
            $estimate->discount_type,
            ExportFormatter::money($estimate->tax),
            ExportFormatter::money($estimate->total),
            ExportFormatter::money($estimate->base_sub_total),
            ExportFormatter::money($estimate->base_discount_val),
            ExportFormatter::money($estimate->base_tax),
            ExportFormatter::money($estimate->base_total),
            $estimate->getNotes(),
            self::estimateDocumentTaxesToJson($estimate),
            self::customFieldsToJson($estimate),
            ExportFormatter::date($estimate->created_at),
            ExportFormatter::date($estimate->updated_at),
            self::itemsToJson($estimate),
        ];
    }

    protected static function itemsToJson(Estimate $estimate): string
    {
        $lines = $estimate->items
            ->values()
            ->map(fn (EstimateItem $item, int $index) => [
                'id' => $item->id,
                'position' => $index + 1,
                'item_id' => $item->item_id,
                'name' => $item->name,
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit' => $item->unit_name,
                'unit_price' => ExportFormatter::money($item->price),
                'discount_type' => $item->discount_type,
                'discount' => $item->discount,
                'discount_val' => ExportFormatter::money($item->discount_val),
                'tax' => ExportFormatter::money($item->tax),
                'total' => ExportFormatter::money($item->total),
                'base_price' => ExportFormatter::money($item->base_price),
                'base_discount_val' => ExportFormatter::money($item->base_discount_val),
                'base_tax' => ExportFormatter::money($item->base_tax),
                'base_total' => ExportFormatter::money($item->base_total),
                'taxes' => self::lineTaxes($item),
            ])
            ->all();

        return ExportFormatter::json($lines);
    }
}
