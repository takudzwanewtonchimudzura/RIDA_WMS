<div class="row mb-3">
    @foreach(['province' => 'Province', 'district' => 'District'] as $field => $label)
    <div class="col-md-6">
        <label for="{{ $field }}" class="form-label">{{ $label }} *</label>
        <input id="{{ $field }}" name="{{ $field }}" class="form-control" value="{{ old($field, $locationRecord?->{$field} ?? '') }}" required>
        @error($field)<div class="text-danger small">{{ $message }}</div>@enderror
    </div>
    @endforeach
</div>
