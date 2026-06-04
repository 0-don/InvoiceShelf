<?php

namespace App\Exports;

use App\Exports\Concerns\MapsCustomFields;
use App\Exports\Support\ExportFormatter;
use App\Models\Expense;
use App\Support\Csv\CsvResponse;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExpenseExport
{
    use MapsCustomFields;

    public static function download(Request $request): StreamedResponse
    {
        return CsvResponse::stream(
            'expenses-'.now()->format('Y-m-d').'.csv',
            self::headers(),
            self::query($request),
            fn (Expense $expense) => self::map($expense),
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
            'expense_number',
            'expense_date',
            'expense_category_id',
            'category',
            'customer_id',
            'customer',
            'customer_email',
            'payment_method_id',
            'payment_method',
            'currency_id',
            'currency',
            'exchange_rate',
            'amount',
            'base_amount',
            'notes',
            'custom_fields',
            'created_at',
            'updated_at',
        ];
    }

    public static function query(Request $request): Builder
    {
        return Expense::query()
            ->with(['category', 'customer', 'currency', 'paymentMethod', 'fields.customField'])
            ->whereCompany()
            ->applyFilters($request->all())
            ->orderByDesc('expense_date');
    }

    /**
     * @return array<int, mixed>
     */
    public static function map(Expense $expense): array
    {
        return [
            $expense->id,
            $expense->company_id,
            $expense->expense_number,
            ExportFormatter::date($expense->expense_date),
            $expense->expense_category_id,
            $expense->category?->name ?? '',
            $expense->customer_id,
            $expense->customer?->name ?? '',
            $expense->customer?->email ?? '',
            $expense->payment_method_id,
            $expense->paymentMethod?->name ?? '',
            $expense->currency_id,
            $expense->currency?->code ?? '',
            $expense->exchange_rate,
            ExportFormatter::money($expense->amount),
            ExportFormatter::money($expense->base_amount),
            $expense->notes,
            self::customFieldsToJson($expense),
            ExportFormatter::date($expense->created_at),
            ExportFormatter::date($expense->updated_at),
        ];
    }
}
