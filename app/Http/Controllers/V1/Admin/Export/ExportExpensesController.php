<?php

namespace App\Http\Controllers\V1\Admin\Export;

use App\Exports\ExpenseExport;
use App\Http\Controllers\Controller;
use App\Models\Expense;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportExpensesController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Expense::class);

        return ExpenseExport::download($request);
    }
}
