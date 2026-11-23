<?php

namespace App\Rules;

use App\Services\ZimbabweLocations;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class DistrictCoordinates implements DataAwareRule, ValidationRule
{
    private array $data = [];

    public function __construct(private string $districtField) {}

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_numeric($value) || !is_numeric($this->data['longitude'] ?? null)) {
            return;
        }
        if (!app(ZimbabweLocations::class)->contains(
            (string) ($this->data['province'] ?? ''),
            (string) ($this->data[$this->districtField] ?? ''),
            (float) $value,
            (float) $this->data['longitude']
        )) {
            $fail('Coordinates must be inside Zimbabwe and the selected district. Check the province, district and GPS reading.');
        }
    }
}
