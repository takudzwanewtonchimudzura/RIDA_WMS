<!DOCTYPE html>
<html>
<head>
    <title>Borehole Report - {{ $borehole->borehole_serial_no }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
        .section { margin-bottom: 25px; }
        .section-title { background: #f5f5f5; padding: 8px; font-weight: bold; border-left: 4px solid #333; }
        .table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        .table th, .table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .table th { background-color: #f2f2f2; }
        .signature-section { margin-top: 50px; }
        .signature-line { width: 300px; border-top: 1px solid #000; margin-top: 40px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>DISTRICT DEVELOPMENT FUND - WATER DIVISION</h1>
        <h2>BOREHOLE TECHNICAL REPORT</h2>
    </div>

    <!-- General Information -->
    <div class="section">
        <div class="section-title">GENERAL INFORMATION</div>
        <table class="table">
            <tr>
                <td><strong>Borehole Serial No:</strong> {{ $borehole->borehole_serial_no }}</td>
                <td><strong>Province:</strong> {{ $borehole->province }}</td>
            </tr>
            <tr>
                <td><strong>District:</strong> {{ $borehole->admin_district }}</td>
                <td><strong>Locality:</strong> {{ $borehole->locality }}</td>
            </tr>
            <tr>
                <td><strong>Ward:</strong> {{ $borehole->ward }}</td>
                <td><strong>Village:</strong> {{ $borehole->village }}</td>
            </tr>
            <tr>
                <td><strong>Date:</strong> {{ $borehole->date->format('d/m/Y') }}</td>
                <td><strong>Usage:</strong> {{ $borehole->usage }}</td>
            </tr>
        </table>
    </div>

    <!-- Geology Information -->
    @if($borehole->geologyLayers->isNotEmpty())
    <div class="section">
        <div class="section-title">GEOLOGY INFORMATION</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Depth From (m)</th>
                    <th>Depth To (m)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($borehole->geologyLayers as $layer)
                <tr>
                    <td>{{ $layer->sample_description ?? 'N/A' }}</td>
                    <td>{{ $layer->description_layer_depth_from ?? 'N/A' }}</td>
                    <td>{{ $layer->description_layer_depth_to ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <!-- Water Quality -->
    <div class="section">
        <div class="section-title">WATER QUALITY</div>
        <table class="table">
            <tr>
                <td><strong>TDS:</strong> {{ $borehole->tds_mg_l }} mg/l</td>
                <td><strong>pH:</strong> {{ $borehole->ph }}</td>
            </tr>
        </table>
    </div>

    <!-- Signatures -->
    <div class="signature-section">
        <table width="100%">
            <tr>
                <td width="33%">
                    <div class="signature-line"></div>
                    <div>Operator: {{ $borehole->operator }}</div>
                </td>
                <td width="33%">
                    <div class="signature-line"></div>
                    <div>Hydrogeologist: {{ $borehole->hydrogeologist }}</div>
                </td>
                <td width="33%">
                    <div class="signature-line"></div>
                    <div>Date: {{ $borehole->date->format('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>