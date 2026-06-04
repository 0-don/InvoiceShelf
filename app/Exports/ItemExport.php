<?php

namespace App\Exports;

use App\Exports\Support\ExportFormatter;
use App\Models\Item;
use App\Support\Csv\CsvResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemExport
{
    public static function download(Request $request): StreamedResponse
    {
        return CsvResponse::stream(
            'items-'.now()->format('Y-m-d').'.csv',
            self::headers(),
            self::query($request),
            fn (Item $item) => self::map($item),
        );
    }

    /**
     * @return array<int, string>
     */
    public static function headers(): array
    {
        return [
            'id',
            'item_id',
            'company_id',
            'name',
            'description',
            'price',
            'unit_id',
            'unit',
            'currency_id',
            'currency',
            'tax_per_item',
            'taxes',
            'created_at',
            'updated_at',
        ];
    }

    public static function query(Request $request): Builder
    {
        return Item::query()
            ->with(['currency', 'taxes.taxType'])
            ->leftJoin('units', 'units.id', '=', 'items.unit_id')
            ->whereCompany()
            ->applyFilters($request->all())
            ->select('items.*', 'units.name as unit_name')
            ->orderBy('items.name');
    }

    /**
     * @return array<int, mixed>
     */
    public static function map(Item $item): array
    {
        return [
            $item->id,
            $item->id,
            $item->company_id,
            $item->name,
            $item->description,
            ExportFormatter::money($item->price),
            $item->unit_id,
            $item->unit_name ?? $item->unit?->name ?? '',
            $item->currency_id,
            $item->currency?->code ?? '',
            ExportFormatter::yesNo($item->tax_per_item),
            self::taxesToJson($item),
            ExportFormatter::date($item->created_at),
            ExportFormatter::date($item->updated_at),
        ];
    }

    protected static function taxesToJson(Item $item): string
    {
        $taxes = $item->taxes
            ->map(fn ($tax) => array_filter([
                'tax_type_id' => $tax->tax_type_id,
                'name' => $tax->taxType?->name ?? $tax->name ?? null,
                'percent' => $tax->percent ?: null,
            ]))
            ->values()
            ->all();

        return ExportFormatter::json($taxes);
    }
}
