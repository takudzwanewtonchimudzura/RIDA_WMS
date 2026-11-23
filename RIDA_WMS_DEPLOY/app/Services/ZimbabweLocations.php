<?php

namespace App\Services;

use App\Rules\DistrictCoordinates;
use Illuminate\Validation\Rule;

class ZimbabweLocations
{
    private ?array $data = null;
    private ?array $samples = null;

    public function data(): array
    {
        return $this->data ??= json_decode(file_get_contents(resource_path('data/zimbabwe.json')), true, 512, JSON_THROW_ON_ERROR);
    }

    public function hierarchy(): array
    {
        $hierarchy = [];
        foreach ($this->data() as $province => $districts) {
            foreach ($districts as $district => $data) {
                $hierarchy[$province][$district] = array_keys($data['wards']);
            }
        }
        return $hierarchy;
    }

    public function rules(string $districtField = 'district', bool $coordinates = true): array
    {
        $data = $this->data();
        $province = is_string(request()->input('province')) ? request()->input('province') : '';
        $district = is_string(request()->input($districtField)) ? request()->input($districtField) : '';
        $rules = [
            'province' => ['required', 'string', Rule::in(array_keys($data))],
            $districtField => ['required', 'string', Rule::in(array_keys($data[$province] ?? []))],
            'ward' => ['required', 'string', Rule::in(array_keys($data[$province][$district]['wards'] ?? []))],
        ];
        if ($coordinates) {
            $rules['latitude'] = ['nullable', 'required_with:longitude', 'numeric', 'between:-22.5,-15.5', new DistrictCoordinates($districtField)];
            $rules['longitude'] = ['nullable', 'required_with:latitude', 'numeric', 'between:25,33.2'];
        }
        return $rules;
    }

    public function contains(string $province, string $district, float $lat, float $lng): bool
    {
        $geometry = $this->data()[$province][$district]['geometry'] ?? null;
        if (!$geometry) {
            return false;
        }
        $polygons = $geometry['type'] === 'Polygon' ? [$geometry['coordinates']] : $geometry['coordinates'];
        foreach ($polygons as $rings) {
            if (!$this->inRing($lng, $lat, $rings[0])) {
                continue;
            }
            foreach (array_slice($rings, 1) as $hole) {
                if ($this->inRing($lng, $lat, $hole)) {
                    continue 2;
                }
            }
            return true;
        }
        return false;
    }

    private function inRing(float $x, float $y, array $ring): bool
    {
        $inside = false;
        for ($i = 0, $j = count($ring) - 1; $i < count($ring); $j = $i++) {
            [$xi, $yi] = $ring[$i];
            [$xj, $yj] = $ring[$j];
            $cross = ($x - $xi) * ($yj - $yi) - ($y - $yi) * ($xj - $xi);
            if (abs($cross) < 1e-12 && $x >= min($xi, $xj) && $x <= max($xi, $xj) && $y >= min($yi, $yj) && $y <= max($yi, $yj)) {
                return true;
            }
            if (($yi > $y) !== ($yj > $y) && $x < ($xj - $xi) * ($y - $yi) / ($yj - $yi) + $xi) {
                $inside = !$inside;
            }
        }
        return $inside;
    }

    public function sample(int $index = 0): array
    {
        if ($this->samples !== null) {
            return $this->samples[$index % count($this->samples)];
        }
        $locations = [];
        foreach ($this->data() as $province => $districts) {
            foreach ($districts as $district => $data) {
                foreach ($data['wards'] as $ward => [$lat, $lng]) {
                    if ($this->contains($province, $district, (float) $lat, (float) $lng)) {
                        $locations[] = compact('province', 'district', 'ward') + ['latitude' => $lat, 'longitude' => $lng];
                        break;
                    }
                }
            }
        }
        $this->samples = $locations;
        return $locations[$index % count($locations)];
    }
}
