<?php

namespace App\Exports;

use App\Exports\Concerns\MapsLineTaxes;
use App\Exports\Support\ExportFormatter;
use App\Models\EstimateItem;
use App\Support\Csv\CsvResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class EstimateLineExport
{
    use MapsLineTaxes;

    public static function download(Request $request): StreamedResponse
    {
        $linePosition = 0;
        $lastEstimateId = null;

        return CsvResponse::stream(
            'estimate-lines-'.now()->format('Y-m-d').'.csv',
            self::headers(),
            self::query($request),
            function (EstimateItem $item) use (&$linePosition, &$lastEstimateId) {
                if ($lastEstimateId !== $item->estimate_id) {
                    $linePosition = 1;
                    $lastEstimateId = $item->estimate_id;
                } else {
                    $linePosition++;
                }

                return self::map($item, $linePosition);
            },
        );
    }

    /**
     * @return array<int, string>
     */
    public static function headers(): array
    {
        return [
            'estimate_id',
            'company_id',
            'estimate_number',
            'reference_number',
            'customer_id',
            'customer',
            'customer_email',
            'customer_tax_id',
            'estimate_date',
            'expiry_date',
            'status',
            'currency_id',
            'currency',
            'line_id',
            'line_position',
            'catalog_item_id',
            'line_name',
            'line_description',
            'line_quantity',
            'line_unit',
            'line_unit_price',
            'line_discount_type',
            'line_discount',
            'line_discount_val',
            'line_tax',
            'line_total',
            'line_taxes',
        ];
    }

    public static function query(Request $request): Builder
    {
        return EstimateItem::query()
            ->with(['estimate.customer', 'estimate.currency', 'taxes.taxType'])
            ->join('estimates', 'estimates.id', '=', 'estimate_items.estimate_id')
            ->where('estimate_items.company_id', request()->header('company'))
            ->whereHas('estimate', function ($query) use ($request) {
                $query->whereCompany()->applyFilters($request->all());
            })
            ->orderByDesc('estimates.sequence_number')
            ->orderBy('estimate_items.id')
            ->select('estimate_items.*');
    }

    /**
     * @return array<int, mixed>
     */
    public static function map(EstimateItem $item, int $linePosition): array
    {
        $estimate = $item->estimate;

        return [
            $estimate->id,
            $estimate->company_id,
            $estimate->estimate_number,
            $estimate->reference_number,
            $estimate->customer_id,
            $estimate->customer?->name ?? '',
            $estimate->customer?->email ?? '',
            $estimate->customer?->tax_id ?? '',
            ExportFormatter::date($estimate->estimate_date),
            ExportFormatter::date($estimate->expiry_date),
            $estimate->status,
            $estimate->currency_id,
            $estimate->currency?->code ?? '',
            $item->id,
            $linePosition,
            $item->item_id,
            $item->name,
            $item->description,
            (float) $item->quantity,
            $item->unit_name,
            ExportFormatter::money($item->price),
            $item->discount_type,
            $item->discount,
            ExportFormatter::money($item->discount_val),
            ExportFormatter::money($item->tax),
            ExportFormatter::money($item->total),
            ExportFormatter::json(self::lineTaxes($item)),
        ];
    }
}
