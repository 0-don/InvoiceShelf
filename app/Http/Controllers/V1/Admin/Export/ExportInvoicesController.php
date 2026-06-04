<?php

namespace App\Http\Controllers\V1\Admin\Export;

use App\Exports\InvoiceExport;
use App\Exports\InvoiceLineExport;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportInvoicesController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Invoice::class);

        if ($request->query('format') === 'lines') {
            return InvoiceLineExport::download($request);
        }

        return InvoiceExport::download($request);
    }
}
