<?php

namespace App\Http\Controllers;

use App\Models\ProspectingSite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ProspectingController extends Controller
{
    public function index(Request $request)
    {
        $query = ProspectingSite::with('creator')->latest();
        if (!$request->user()->isAdmin()) {
            $query->where('created_by', $request->user()->id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('search')) {
            $term = '%' . trim($request->input('search')) . '%';
            $query->where(function ($builder) use ($term) {
                $builder->where('reference', 'like', $term)
                    ->orWhere('project_name', 'like', $term)
                    ->orWhere('village', 'like', $term)
                    ->orWhere('district', 'like', $term);
            });
        }

        $sites = $query->paginate(20)->withQueryString();
        return view('prospecting.index', compact('sites'));
    }

    public function create(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        return view('prospecting.create');
    }

    public function store(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        $data = $request->validate([
            'project_name' => 'nullable|string|max:255',
            'village' => 'required|string|max:255',
            'ward' => 'required|string|max:255',
            'district' => 'required|string|max:255',
            'province' => 'required|string|max:255',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'survey_date' => 'required|date',
            'siting_agency' => 'nullable|in:Government,PISP',
            'prospector_name' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:4000',
        ]);

        $data['reference'] = 'PR-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        $data['created_by'] = Auth::id();
        $site = ProspectingSite::create($data);

        return redirect()->route('prospecting.show', $site)
            ->with('success', 'Prospecting record submitted for review.');
    }

    public function show(Request $request, ProspectingSite $prospectingSite)
    {
        $this->authorizeSite($request, $prospectingSite);
        $prospectingSite->load(['creator', 'reviewer', 'borehole']);
        return view('prospecting.show', compact('prospectingSite'));
    }

    public function decide(Request $request, ProspectingSite $prospectingSite)
    {
        abort_unless($request->user()->isAdmin(), 403);
        $data = $request->validate([
            'status' => 'required|in:approved,rejected',
            'review_note' => 'nullable|string|max:2000',
        ]);

        $prospectingSite->update([
            'status' => $data['status'],
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
            'remarks' => trim(($prospectingSite->remarks ? $prospectingSite->remarks . "\n" : '') . ($data['review_note'] ?? '')) ?: $prospectingSite->remarks,
        ]);

        return redirect()->route('prospecting.show', $prospectingSite)
            ->with('success', 'Prospecting decision saved.');
    }

    public function markDrilled(Request $request, ProspectingSite $prospectingSite)
    {
        $this->authorizeSite($request, $prospectingSite);
        if ($prospectingSite->status !== 'approved') {
            throw ValidationException::withMessages(['status' => 'Only approved prospecting sites can be marked drilled.']);
        }

        $prospectingSite->update(['status' => 'drilled']);
        return redirect()->route('prospecting.show', $prospectingSite)
            ->with('success', 'Prospecting site marked as drilled.');
    }

    private function authorizeSite(Request $request, ProspectingSite $site): void
    {
        abort_unless($request->user()->isAdmin() || (int) $site->created_by === (int) $request->user()->id, 403);
    }
}