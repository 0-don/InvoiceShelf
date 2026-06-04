<?php

namespace App\Http\Controllers\V1\Admin\Export;

use App\Exports\ItemExport;
use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportItemsController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $this->authorize('viewAny', Item::class);

        return ItemExport::download($request);
    }
}
