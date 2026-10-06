<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'RIDA RWIMS Reports')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2c7fb8;
            --secondary-color: #7fcdbb;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc;
        }

        .report-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a5276 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
        }

        .kpi-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease;
            border-left: 4px solid var(--primary-color);
        }

        .kpi-card:hover {
            transform: translateY(-5px);
        }

        .chart-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .export-buttons {
            margin-bottom: 1rem;
        }

        .filter-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        @media print {
            .no-print {
                display: none !important;
            }
            
            .report-header {
                background: #2c7fb8 !important;
                -webkit-print-color-adjust: exact;
            }
            
            .kpi-card {
                box-shadow: none;
                border: 1px solid #dee2e6;
            }
        }
    </style>
    @stack('styles')
</head>
<body>
    <!-- Report Header -->
    <div class="report-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-tint fa-3x text-white-50"></i>
                        </div>
                        <div>
                            <h1 class="h3 mb-1">RIDA RWIMS</h1>
                            <p class="mb-0 text-white-50">Rural Water Infrastructure Management System</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <h2 class="h4 mb-1">@yield('report-title', 'Management Report')</h2>
                    <p class="mb-0 text-white-50" id="reportPeriod">@yield('report-period', 'All Time')</p>
                    <small class="text-white-50">Generated on: {{ now()->format('M d, Y H:i') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid">
        <!-- Export Buttons -->
        <div class="row no-print">
            <div class="col-12">
                <div class="export-buttons d-flex justify-content-end gap-2">
                    <button class="btn btn-outline-primary btn-sm" onclick="window.print()">
                        <i class="fas fa-print me-1"></i>Print
                    </button>
                    <button class="btn btn-outline-success btn-sm" onclick="exportToPDF()">
                        <i class="fas fa-file-pdf me-1"></i>PDF
                    </button>
                    <button class="btn btn-outline-info btn-sm" onclick="exportToExcel()">
                        <i class="fas fa-file-excel me-1"></i>Excel
                    </button>
                </div>
            </div>
        </div>

        @yield('content')
    </div>

    <!-- Footer -->
    <footer class="mt-5 py-4 text-center text-muted no-print">
        <div class="container">
            <p class="mb-1">RIDA RWIMS - Confidential Report</p>
            <small>This report contains sensitive information. Distribution is restricted to authorized personnel only.</small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function exportToPDF() {
            // Implementation for PDF export
            alert('PDF export functionality will be implemented');
        }

        function exportToExcel() {
            // Implementation for Excel export
            alert('Excel export functionality will be implemented');
        }

        // Auto-update report period
        document.getElementById('reportPeriod').textContent = new Date().toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'long', 
            day: 'numeric' 
        });
    </script>
    @stack('scripts')
</body>
</html>