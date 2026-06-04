<?php

namespace App\Exports;

use App\Exports\Concerns\MapsCustomFields;
use App\Exports\Concerns\MapsDocumentTaxes;
use App\Exports\Concerns\MapsLineTaxes;
use App\Exports\Support\ExportFormatter;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Support\Csv\CsvResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InvoiceExport
{
    use MapsCustomFields;
    use MapsDocumentTaxes;
    use MapsLineTaxes;

    public static function download(Request $request): StreamedResponse
    {
        return CsvResponse::stream(
            'invoices-'.now()->format('Y-m-d').'.csv',
            self::headers(),
            self::query($request),
            fn (Invoice $invoice) => self::map($invoice),
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
            'invoice_number',
            'reference_number',
            'sequence_number',
            'unique_hash',
            'customer_id',
            'customer',
            'customer_email',
            'customer_tax_id',
            'customer_company_name',
            'invoice_date',
            'due_date',
            'status',
            'paid_status',
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
            'due_amount',
            'base_sub_total',
            'base_discount_val',
            'base_tax',
            'base_total',
            'base_due_amount',
            'sent',
            'viewed',
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
        return Invoice::query()
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
    public static function map(Invoice $invoice): array
    {
        return [
            $invoice->id,
            $invoice->company_id,
            $invoice->invoice_number,
            $invoice->reference_number,
            $invoice->sequence_number,
            $invoice->unique_hash,
            $invoice->customer_id,
            $invoice->customer?->name ?? '',
            $invoice->customer?->email ?? '',
            $invoice->customer?->tax_id ?? '',
            $invoice->customer?->company_name ?? '',
            ExportFormatter::date($invoice->invoice_date),
            ExportFormatter::date($invoice->due_date),
            $invoice->status,
            $invoice->paid_status,
            $invoice->currency_id,
            $invoice->currency?->code ?? '',
            $invoice->exchange_rate,
            ExportFormatter::yesNo($invoice->tax_per_item),
            ExportFormatter::yesNo($invoice->tax_included),
            ExportFormatter::yesNo($invoice->discount_per_item),
            ExportFormatter::money($invoice->sub_total),
            ExportFormatter::money($invoice->discount_val),
            $invoice->discount_type,
            ExportFormatter::money($invoice->tax),
            ExportFormatter::money($invoice->total),
            ExportFormatter::money($invoice->due_amount),
            ExportFormatter::money($invoice->base_sub_total),
            ExportFormatter::money($invoice->base_discount_val),
            ExportFormatter::money($invoice->base_tax),
            ExportFormatter::money($invoice->base_total),
            ExportFormatter::money($invoice->base_due_amount),
            ExportFormatter::yesNo($invoice->sent),
            ExportFormatter::yesNo($invoice->viewed),
            $invoice->notes,
            self::invoiceDocumentTaxesToJson($invoice),
            self::customFieldsToJson($invoice),
            ExportFormatter::date($invoice->created_at),
            ExportFormatter::date($invoice->updated_at),
            self::itemsToJson($invoice),
        ];
    }

    protected static function itemsToJson(Invoice $invoice): string
    {
        $lines = $invoice->items
            ->values()
            ->map(fn (InvoiceItem $item, int $index) => [
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
