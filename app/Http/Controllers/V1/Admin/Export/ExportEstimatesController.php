<?php

namespace App\Http\Controllers\V1\Admin\Export;

use App\Exports\EstimateExport;
use App\Exports\EstimateLineExport;
use App\Http\Controllers\Controller;
use App\Models\Estimate;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportEstimatesController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Estimate::class);

        if ($request->query('format') === 'lines') {
            return EstimateLineExport::download($request);
        }

        return EstimateExport::download($request);
    }
}
