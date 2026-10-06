<?php

namespace App\Http\Controllers;

use App\Models\PipedWaterScheme;
use App\Models\WaterQualityTest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WaterQualityTestController extends Controller
{
    public function store(Request $request, PipedWaterScheme $pipedWaterScheme): RedirectResponse
    {
        abort_if($request->user()->isViewer(), 403);

        $this->authorizeScheme($request, $pipedWaterScheme);

        $data = $request->validate([
            'tested_at' => ['required', 'date'],
            'ph' => ['nullable', 'numeric', 'between:0,14'],
            'turbidity_ntu' => ['nullable', 'numeric', 'min:0'],
            'ecoli_cfu_100ml' => ['nullable', 'integer', 'min:0'],
            'tds_mg_l' => ['nullable', 'numeric', 'min:0'],
            'electrical_conductivity' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['tested_by'] = $request->user()->id;
        $pipedWaterScheme->qualityTests()->create($data);

        return back()->with('success', 'Water quality test recorded successfully.');
    }

    public function history(Request $request, PipedWaterScheme $pipedWaterScheme): View
    {
        $this->authorizeScheme($request, $pipedWaterScheme);

        $tests = $pipedWaterScheme->qualityTests()
            ->with('tester')
            ->latest('tested_at')
            ->paginate(20)
            ->withQueryString();

        return view('piped-water-schemes.quality-history', compact('pipedWaterScheme', 'tests'));
    }

    private function authorizeScheme(Request $request, PipedWaterScheme $scheme): void
    {
        abort_unless(
            $request->user()->isAdmin() || $scheme->created_by === $request->user()->id,
            403
        );
    }
}
