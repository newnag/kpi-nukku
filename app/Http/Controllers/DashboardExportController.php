<?php

// app/Http/Controllers/DashboardExportController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IndicatorsExport;

class DashboardExportController extends Controller
{
    public function export(Request $request)
    {
        $filters = [
            'year'        => $request->query('year'),
            'standard_id' => $request->query('standard'),
            'category_id' => $request->query('dimension'),
            'status'      => $request->query('status'),
            'dept_id'     => $request->query('dept_id'),
            'code'        => $request->query('code'),
        ];

        $fileName = 'dashboard_data_' . now()->format('Ymd_His') . '.xlsx';
        return Excel::download(new IndicatorsExport($filters), $fileName);
    }
}

