<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\Borehole;
use App\Models\Dam;
use App\Models\IrrigationScheme;
use App\Models\PipedWaterScheme;
use App\Models\ProspectingSite;
use App\Models\User;
use App\Models\WaterPoint;
use Illuminate\Support\Facades\DB;

class UatDataRepair
{
    public function run(string $keepAdmin): array
    {
        $keeper = User::where('email', $keepAdmin)->where('role', 'admin')->where('is_active', true)->firstOrFail();
        return DB::transaction(function () use ($keeper) {
            $counts = ['archived_users' => 0, 'repaired_samples' => 0, 'sites_returned_for_review' => 0, 'self_approvals_returned_for_review' => 0];
            auth()->onceUsingId($keeper->id);
            foreach (User::where('id', '!=', $keeper->id)->get() as $user) {
                $email = strtolower($user->email);
                $obviousTest = preg_match('/@example\\.(org|net|com)$/', $email) || $email === 'test@rida.gov.zw';
                $duplicateAdmin = $user->name === 'System Administrator' && in_array($email, ['admin@wims.local', 'admin@rida-wms.local'], true);
                if ($obviousTest || $duplicateAdmin) {
                    $user->forceFill(['is_active' => false, 'remember_token' => null])->save();
                    $user->delete();
                    DB::table('sessions')->where('user_id', $user->id)->delete();
                    $counts['archived_users']++;
                }
            }
            $locations = app(ZimbabweLocations::class);
            foreach (Borehole::whereIn('contractor', ['Zimbabwe Drilling Co.', 'Water Wells Ltd.', 'Aqua Drillers Zimbabwe'])->get() as $borehole) {
                if (!preg_match('/^BH-00(0[1-9]|1[0-9]|20)$/', $borehole->borehole_serial_no)
                    || !preg_match('/^Water Project [1-5]$/', $borehole->project_name ?? '')) {
                    continue;
                }
                $i = (int) substr($borehole->borehole_serial_no, 3);
                $location = $locations->sample($i);
                $location['admin_district'] = $location['district'];
                unset($location['district']);
                $this->repair($borehole, $location + [
                    'total_cost' => 1000 + (($i * 197) % 4001), 'cost_currency' => 'USD',
                    'water_quality_remarks' => 'UAT sample readings; assess measured parameters against screening limits.',
                ], $counts);
                $this->returnSelfApprovalForReview($borehole, $counts);
            }
            foreach (Dam::where('general_comments', 'Dam used for irrigation and livestock')->get() as $dam) {
                if (!preg_match('/^Dam (0[1-8])$/', $dam->name, $matches)) {
                    continue;
                }
                $i = (int) $matches[1];
                $this->repair($dam, $locations->sample($i) + [
                    'dam_code' => 'UAT-DAM-' . str_pad((string) $i, 3, '0', STR_PAD_LEFT),
                ], $counts);
                $this->returnSelfApprovalForReview($dam, $counts);
            }
            foreach (PipedWaterScheme::where('remarks', 'like', 'Scheme serving % community')->get() as $scheme) {
                if (!preg_match('/^(.+) Piped Water Scheme ([1-9]|1[0-5])$/', $scheme->name, $matches)) {
                    continue;
                }
                $location = $this->locationForDistrict($matches[1]);
                unset($location['latitude'], $location['longitude']);
                $this->repair($scheme, $location + [
                    'fund_amount' => $scheme->has_revolving_fund ? 500 + (int) $matches[2] * 100 : null,
                ], $counts);
            }
            foreach (IrrigationScheme::where('remarks', 'Irrigation scheme for smallholder farmers')->get() as $scheme) {
                if (!preg_match('/^Irrigation Scheme (0[1-9]|10)$/', $scheme->name, $matches)) {
                    continue;
                }
                $location = $locations->sample((int) $matches[1]);
                unset($location['latitude'], $location['longitude']);
                $this->repair($scheme, $location + [
                    'fund_amount_available' => $scheme->has_revolving_fund ? 500 + (int) $matches[1] * 100 : null,
                ], $counts);
            }
            $boreholes = Borehole::orderBy('borehole_serial_no')->get();
            foreach (WaterPoint::orderBy('name')->get() as $point) {
                if (!preg_match('/^Water Point (0[0-4][0-9]|050)$/', $point->name, $matches)) {
                    continue;
                }
                $i = (int) $matches[1];
                $borehole = $i <= 10 ? $boreholes->get($i - 1) : null;
                $location = $borehole ? [
                    'province' => $borehole->province, 'district' => $borehole->admin_district, 'ward' => $borehole->ward,
                    'latitude' => $borehole->latitude, 'longitude' => $borehole->longitude,
                ] : $locations->sample($i);
                $this->repair($point, $location + [
                    'borehole_id' => $borehole?->id,
                    'type_of_water_source' => $borehole ? 'Borehole' : ['Well', 'Spring', 'River'][$i % 3],
                ], $counts);
            }
            foreach (ProspectingSite::where('reference', 'not like', 'UAT-%')->get() as $site) {
                $validLocation = isset($locations->hierarchy()[$site->province][$site->district])
                    && in_array($site->ward, $locations->hierarchy()[$site->province][$site->district], true);
                if ($site->latitude !== null && $site->longitude !== null) {
                    $validLocation = $validLocation && $locations->contains($site->province, $site->district, (float) $site->latitude, (float) $site->longitude);
                }
                $validHistory = $site->status === 'submitted' || (
                    $site->reviewed_at && $site->reviewed_by && $site->reviewed_by !== $site->created_by
                    && ($site->status !== 'drilled' || ($site->drilled_at && $site->drilled_by && $site->drilled_at->gte($site->reviewed_at)))
                );
                $needsReset = $site->status !== 'submitted' || $site->reviewed_by || $site->reviewed_at || $site->drilled_by || $site->drilled_at;
                if ((!$validLocation || !$validHistory) && $needsReset) {
                    AuditLog::record($site, 'returned_for_review', [
                        'reason' => 'Invalid location or unverifiable workflow history; re-survey and independent approval required.',
                        'previous' => $site->only(['status', 'reviewed_by', 'reviewed_at', 'drilled_by', 'drilled_at']),
                    ]);
                    $site->update(['status' => 'submitted', 'reviewed_by' => null, 'reviewed_at' => null, 'drilled_at' => null, 'drilled_by' => null]);
                    $counts['sites_returned_for_review']++;
                }
            }
            return $counts;
        });
    }

    private function returnSelfApprovalForReview(Borehole|Dam $record, array &$counts): void
    {
        if (!$record->approved_at || !$record->created_by || (int) $record->created_by !== (int) $record->approved_by) {
            return;
        }
        AuditLog::record($record, 'approval_returned_for_review', [
            'reason' => 'Historical sample self-approval requires an independent reviewer.',
            'previous' => $record->only(['created_by', 'approved_by', 'approved_at']),
        ]);
        $record->update(['approved_by' => null, 'approved_at' => null]);
        $counts['self_approvals_returned_for_review']++;
    }

    private function locationForDistrict(string $districtName): array
    {
        $districtName = ['Kadoma' => 'Kadoma Urban'][$districtName] ?? $districtName;
        $locations = app(ZimbabweLocations::class);
        foreach ($locations->data() as $province => $districts) {
            if (isset($districts[$districtName])) {
                foreach ($districts[$districtName]['wards'] as $ward => [$latitude, $longitude]) {
                    if ($locations->contains($province, $districtName, $latitude, $longitude)) {
                        return ['province' => $province, 'district' => $districtName, 'ward' => $ward, 'latitude' => $latitude, 'longitude' => $longitude];
                    }
                }
            }
        }
        throw new \RuntimeException("No reference location found for seeded district {$districtName}; no substitute has been guessed.");
    }

    private function repair($record, array $values, array &$counts): void
    {
        $record->fill($values);
        if ($record->isDirty()) {
            AuditLog::record($record, 'uat_sample_repaired', ['before' => array_intersect_key($record->getOriginal(), $record->getDirty()), 'after' => $record->getDirty()]);
            $record->save();
            $counts['repaired_samples']++;
        }
    }
}
