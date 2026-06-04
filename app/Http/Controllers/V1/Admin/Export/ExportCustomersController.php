<?php

namespace App\Http\Controllers\V1\Admin\Export;

use App\Exports\CustomerExport;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportCustomersController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Customer::class);

        return CustomerExport::download($request);
    }
}
