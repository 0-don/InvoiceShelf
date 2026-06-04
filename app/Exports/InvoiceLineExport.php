<?php

namespace App\Exports;

use App\Exports\Concerns\MapsLineTaxes;
use App\Exports\Support\ExportFormatter;
use App\Models\InvoiceItem;
use App\Support\Csv\CsvResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceLineExport
{
    use MapsLineTaxes;

    public static function download(Request $request): StreamedResponse
    {
        $linePosition = 0;
        $lastInvoiceId = null;

        return CsvResponse::stream(
            'invoice-lines-'.now()->format('Y-m-d').'.csv',
            self::headers(),
            self::query($request),
            function (InvoiceItem $item) use (&$linePosition, &$lastInvoiceId) {
                if ($lastInvoiceId !== $item->invoice_id) {
                    $linePosition = 1;
                    $lastInvoiceId = $item->invoice_id;
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
            'invoice_id',
            'company_id',
            'invoice_number',
            'reference_number',
            'customer_id',
            'customer',
            'customer_email',
            'customer_tax_id',
            'invoice_date',
            'due_date',
            'status',
            'paid_status',
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
        return InvoiceItem::query()
            ->with(['invoice.customer', 'invoice.currency', 'taxes.taxType'])
            ->join('invoices', 'invoices.id', '=', 'invoice_items.invoice_id')
            ->where('invoice_items.company_id', request()->header('company'))
            ->whereHas('invoice', function ($query) use ($request) {
                $query->whereCompany()->applyFilters($request->all());
            })
            ->orderByDesc('invoices.sequence_number')
            ->orderBy('invoice_items.id')
            ->select('invoice_items.*');
    }

    /**
     * @return array<int, mixed>
     */
    public static function map(InvoiceItem $item, int $linePosition): array
    {
        $invoice = $item->invoice;

        return [
            $invoice->id,
            $invoice->company_id,
            $invoice->invoice_number,
            $invoice->reference_number,
            $invoice->customer_id,
            $invoice->customer?->name ?? '',
            $invoice->customer?->email ?? '',
            $invoice->customer?->tax_id ?? '',
            ExportFormatter::date($invoice->invoice_date),
            ExportFormatter::date($invoice->due_date),
            $invoice->status,
            $invoice->paid_status,
            $invoice->currency_id,
            $invoice->currency?->code ?? '',
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
