<?php

namespace App\Exports;

use App\Exports\Concerns\MapsAddressColumns;
use App\Exports\Concerns\MapsCustomFields;
use App\Exports\Support\ExportFormatter;
use App\Models\Customer;
use App\Support\Csv\CsvResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CustomerExport
{
    use MapsAddressColumns;
    use MapsCustomFields;

    public static function download(Request $request): StreamedResponse
    {
        return CsvResponse::stream(
            'customers-'.now()->format('Y-m-d').'.csv',
            self::headers(),
            self::query($request),
            fn (Customer $customer) => self::map($customer),
        );
    }

    /**
     * @return array<int, string>
     */
    public static function headers(): array
    {
        return array_merge([
            'id',
            'customer_id',
            'company_id',
            'type',
            'name',
            'contact_name',
            'company_name',
            'email',
            'phone',
            'website',
            'prefix',
            'tax_id',
            'currency_id',
            'currency',
            'enable_portal',
            'due_amount',
            'base_due_amount',
            'created_at',
            'custom_fields',
        ], self::addressHeaders('billing'), self::addressHeaders('shipping'));
    }

    public static function query(Request $request): Builder
    {
        return Customer::query()
            ->with(['currency', 'fields.customField', 'billingAddress.country', 'shippingAddress.country'])
            ->whereCompany()
            ->applyFilters($request->all())
            ->withSum('invoices as due_amount', 'due_amount')
            ->withSum('invoices as base_due_amount', 'base_due_amount')
            ->orderBy('name');
    }

    /**
     * @return array<int, mixed>
     */
    public static function map(Customer $customer): array
    {
        return array_merge([
            $customer->id,
            $customer->id,
            $customer->company_id,
            self::customerType($customer),
            $customer->name,
            $customer->contact_name,
            $customer->company_name,
            $customer->email,
            $customer->phone,
            $customer->website,
            $customer->prefix,
            $customer->tax_id,
            $customer->currency_id,
            $customer->currency?->code ?? '',
            ExportFormatter::bool($customer->enable_portal),
            ExportFormatter::money($customer->due_amount),
            ExportFormatter::money($customer->base_due_amount),
            ExportFormatter::date($customer->created_at),
            self::customFieldsToJson($customer),
        ], self::addressRow($customer->billingAddress), self::addressRow($customer->shippingAddress));
    }

    protected static function customerType(Customer $customer): string
    {
        return filled($customer->company_name) ? 'company' : 'individual';
    }
}
