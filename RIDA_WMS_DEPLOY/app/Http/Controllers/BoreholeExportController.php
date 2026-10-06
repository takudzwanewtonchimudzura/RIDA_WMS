<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\AuthorizesRecords;
use App\Models\Borehole;
use App\Exports\BoreholesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class BoreholeExportController extends Controller
{
    use AuthorizesRecords;

    public function exportExcel(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'province' => $request->province,
        ];
        if ($request->user()->isOperator()) {
            $filters['created_by'] = $request->user()->id;
        }
        
        return Excel::download(new BoreholesExport($filters), 'boreholes-' . now()->format('Y-m-d') . '.xlsx');
    }

    public function exportCSV(Request $request)
    {
        $filters = [
            'search' => $request->search,
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'province' => $request->province,
        ];
        if ($request->user()->isOperator()) {
            $filters['created_by'] = $request->user()->id;
        }
        
        return Excel::download(new BoreholesExport($filters), 'boreholes-' . now()->format('Y-m-d') . '.csv');
    }
}