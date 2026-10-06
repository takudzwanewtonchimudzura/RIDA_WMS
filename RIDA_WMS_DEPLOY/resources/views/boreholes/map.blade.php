@extends('layouts.app')

@section('title', 'Borehole Map')

@section('content')
<div class="bg-white shadow-sm rounded-lg p-6">
    <h2 class="text-xl font-semibold mb-4">Borehole Locations</h2>
    <div id="map" class="h-96 w-full rounded-lg border border-gray-300"></div>
</div>

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    // Initialize map
    var map = L.map('map').setView([-17.825, 31.049], 6);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);
    
    // Add borehole markers
    @foreach($boreholes as $borehole)
        @php
            // For demo - in real app, store actual coordinates
            $lat = -17.8 + (rand(-50, 50) / 100);
            $lng = 31.0 + (rand(-50, 50) / 100);
        @endphp
        
        L.marker([{{ $lat }}, {{ $lng }}]).addTo(map)
            .bindPopup(`
                <div class="p-2">
                    <h4 class="font-bold">{{ $borehole->borehole_serial_no }}</h4>
                    <p><strong>Location:</strong> {{ $borehole->locality }}, {{ $borehole->admin_district }}</p>
                    <p><strong>Depth:</strong> {{ $borehole->final_depth_m }} m</p>
                    <p><strong>Blowing Yield:</strong> {{ $borehole->blowing_yield ?? $borehole->yield_lps }} l/s</p>
                    <a href="/boreholes/{{ $borehole->id }}" class="text-blue-600 hover:underline">View Details</a>
                </div>
            `);
    @endforeach
</script>
@endpush
@endsection