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
        if ($request->user()->isOperator()) {
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

        $sites = $query->paginate(config('rwims.default_page_size'))->withQueryString();
        return view('prospecting.index', compact('sites'));
    }

    public function create(Request $request)
    {
        abort_if($request->user()->isViewer(), 403);
        return view('prospecting.create');
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->canCapture(), 403);
        $data = $this->validatedData($request);

        $data['reference'] = 'PR-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        $data['created_by'] = Auth::id();
        $data['submitted_at'] = now();
        $site = ProspectingSite::create($data);

        return redirect()->route('prospecting.show', $site)
            ->with('success', 'Prospecting record submitted for review.');
    }

    public function edit(Request $request, ProspectingSite $prospectingSite)
    {
        $this->authorizeSite($request, $prospectingSite);
        abort_unless($request->user()->canCapture() && $prospectingSite->status === 'submitted', 403);
        return view('prospecting.create', compact('prospectingSite'));
    }

    public function update(Request $request, ProspectingSite $prospectingSite)
    {
        $this->authorizeSite($request, $prospectingSite);
        abort_unless($request->user()->canCapture() && $prospectingSite->status === 'submitted', 403);
        $data = $this->validatedData($request);
        \Illuminate\Support\Facades\DB::transaction(function () use ($prospectingSite, $data) {
            $site = ProspectingSite::query()->lockForUpdate()->findOrFail($prospectingSite->id);
            abort_unless($site->status === 'submitted', 422, 'Reviewed sites cannot be edited.');
            $site->update($data);
            \App\Models\AuditLog::record($site, 'survey_corrected');
        });
        return redirect()->route('prospecting.show', $prospectingSite)->with('success', 'Survey corrected and awaiting independent review.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'project_name' => 'nullable|string|max:255',
            'village' => 'required|string|max:255',
            ...app(\App\Services\ZimbabweLocations::class)->rules(),
            'survey_date' => 'required|date|before_or_equal:today',
            'siting_agency' => 'nullable|in:Government,PISP',
            'prospector_name' => 'nullable|string|max:255',
            'remarks' => 'nullable|string|max:4000',
        ]);
    }

    public function show(Request $request, ProspectingSite $prospectingSite)
    {
        $this->authorizeSite($request, $prospectingSite);
        $prospectingSite->load(['creator', 'reviewer', 'borehole']);
        return view('prospecting.show', compact('prospectingSite'));
    }

    public function decide(Request $request, ProspectingSite $prospectingSite)
    {
        abort_unless($request->user()->canApprove(), 403);
        abort_if((int) $prospectingSite->created_by === (int) $request->user()->id, 403, 'A different supervisor must review this site.');
        $data = $request->validate([
            'status' => 'required|in:approved,rejected',
            'review_note' => 'nullable|string|max:2000',
        ]);

        \Illuminate\Support\Facades\DB::transaction(function () use ($prospectingSite, $data) {
            $site = ProspectingSite::query()->lockForUpdate()->findOrFail($prospectingSite->id);
            $locations = app(\App\Services\ZimbabweLocations::class);
            $validWard = in_array($site->ward, $locations->hierarchy()[$site->province][$site->district] ?? [], true);
            $validCoordinates = $site->latitude === null && $site->longitude === null
                || ($site->latitude !== null && $site->longitude !== null && $locations->contains($site->province, $site->district, (float) $site->latitude, (float) $site->longitude));
            if ($site->status !== 'submitted' || $site->survey_date->isFuture() || !$validWard || !$validCoordinates) {
                throw ValidationException::withMessages(['status' => 'Only submitted, already surveyed sites with a valid location may be reviewed. Correct the survey first.']);
            }
            $site->update([
                'status' => $data['status'],
                'reviewed_by' => Auth::id(),
                'reviewed_at' => now(),
                'remarks' => trim(($site->remarks ? $site->remarks . "\n" : '') . ($data['review_note'] ?? '')) ?: $site->remarks,
            ]);
            \App\Models\AuditLog::record($site, $data['status'], ['review_note' => $data['review_note'] ?? null]);
        });

        return redirect()->route('prospecting.show', $prospectingSite)
            ->with('success', 'Prospecting decision saved.');
    }

    public function markDrilled(Request $request, ProspectingSite $prospectingSite)
    {
        $this->authorizeSite($request, $prospectingSite);
        abort_unless($request->user()->canCapture(), 403);
        $data = $request->validate(['drilled_at' => 'required|date|before_or_equal:now']);
        $prospectingSite->markDrilled($request->user(), $data['drilled_at']);
        return redirect()->route('prospecting.show', $prospectingSite)
            ->with('success', 'Prospecting site marked as drilled.');
    }

    private function authorizeSite(Request $request, ProspectingSite $site): void
    {
        abort_unless(!$request->user()->isOperator() || (int) $site->created_by === (int) $request->user()->id, 403);
    }
}