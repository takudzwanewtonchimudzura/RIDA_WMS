<?php
namespace App\Http\Controllers;

use App\Models\Borehole;
use App\Http\Controllers\Concerns\AuthorizesRecords;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class BoreholeReportController extends Controller
{
    use AuthorizesRecords;

    public function generatePDF(Request $request, $id)
    {
        $borehole = Borehole::with(['geologyLayers', 'casingDetails', 'consumableUsages'])->findOrFail($id);
        $this->authorizeRecordView($request, $borehole);
        
        $pdf = Pdf::loadView('reports.borehole', compact('borehole'))
            ->setPaper('a4', 'portrait');
        
        return $pdf->download("borehole-{$borehole->borehole_serial_no}.pdf");
    }

    public function showPDF(Request $request, $id)
    {
        $borehole = Borehole::with(['geologyLayers', 'casingDetails', 'consumableUsages'])->findOrFail($id);
        $this->authorizeRecordView($request, $borehole);
        
        $pdf = Pdf::loadView('reports.borehole', compact('borehole'))
            ->setPaper('a4', 'portrait');
        
        return $pdf->stream("borehole-{$borehole->borehole_serial_no}.pdf");
    }

    public function generateCertificate(Request $request, Borehole $borehole)
    {
        $this->authorizeRecordView($request, $borehole);
        $borehole->load(['geologyLayers', 'casingDetails', 'consumableUsages']);
        $pdf = Pdf::loadView('reports.borehole-certificate', compact('borehole'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("drilling-certificate-{$borehole->borehole_serial_no}.pdf");
    }
}