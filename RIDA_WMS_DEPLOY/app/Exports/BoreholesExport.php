<?php

namespace App\Exports;

use App\Models\Borehole;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BoreholesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $filters;

    public function __construct(array $filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Borehole::with(['creator', 'approver', 'geologyLayers', 'casingDetails']);

        // Apply filters
        if (!empty($this->filters['date_from'])) {
            $query->where('date', '>=', $this->filters['date_from']);
        }
        
        if (!empty($this->filters['date_to'])) {
            $query->where('date', '<=', $this->filters['date_to']);
        }
        
        if (!empty($this->filters['province'])) {
            $query->where('province', $this->filters['province']);
        }

        if (!empty($this->filters['created_by'])) {
            $query->where('created_by', $this->filters['created_by']);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Serial No',
            'Province',
            'District',
            'Locality',
            'Ward',
            'Coordinates',
            'Depth (m)',
            'Yield (L/s)',
            'Water Quality (pH)',
            'Total Cost (USD)',
            'Beneficiaries',
            'Funding Agency',
            'Contractor',
            'Status',
            'Completion Date'
        ];
    }

    public function map($borehole): array
    {
        return [
            $borehole->borehole_serial_no,
            $borehole->province,
            $borehole->admin_district,
            $borehole->locality,
            $borehole->ward,
            $borehole->latitude && $borehole->longitude ? 
                $borehole->latitude . ', ' . $borehole->longitude : 'N/A',
            $borehole->final_borehole_depth,
            $borehole->yield_lps,
            $borehole->ph ?? 'N/A',
            $borehole->total_cost ?? 0,
            $borehole->number_of_beneficiaries ?? 0,
            $borehole->funding_agency ?? 'N/A',
            $borehole->contractor ?? 'N/A',
            $borehole->approved_at ? 'Approved' : 'Pending',
            $borehole->date->format('Y-m-d')
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text
            1 => ['font' => ['bold' => true]],
            
            // Set column widths
            'A' => ['width' => 15],
            'B' => ['width' => 12],
            'C' => ['width' => 15],
            'D' => ['width' => 15],
            'E' => ['width' => 12],
            'F' => ['width' => 20],
        ];
    }
}