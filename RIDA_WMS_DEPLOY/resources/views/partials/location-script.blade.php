<script>
document.addEventListener('DOMContentLoaded', () => {
    const locations = @json(app(\App\Services\ZimbabweLocations::class)->hierarchy());
    for (const form of document.querySelectorAll('form')) {
        const provinceInput = form.querySelector('[name="province"]');
        const districtInput = form.querySelector('[name="district"], [name="admin_district"]');
        const wardInput = form.querySelector('[name="ward"]');
        if (!provinceInput || !districtInput || !wardInput) continue;

        const asSelect = (input) => {
            const select = document.createElement('select');
            for (const attr of input.attributes) {
                if (!['type', 'value', 'placeholder'].includes(attr.name)) select.setAttribute(attr.name, attr.value);
            }
            select.classList.add('form-select');
            select.required = true;
            input.replaceWith(select);
            return select;
        };
        const previous = [provinceInput.value, districtInput.value, wardInput.value];
        const [province, district, ward] = [provinceInput, districtInput, wardInput].map(asSelect);
        const populate = (select, options, value, label) => {
            select.replaceChildren(new Option(`Select ${label}`, ''));
            for (const option of options.sort((a, b) => a.localeCompare(b, undefined, {numeric: true}))) {
                select.add(new Option(option, option, false, option === value));
            }
            if (value && !options.includes(value)) {
                const warning = document.createElement('div');
                warning.className = 'text-danger small';
                warning.textContent = `Previous ${label} "${value}" is not in the reference data. Select a valid value.`;
                select.after(warning);
            }
            select.disabled = options.length === 0;
        };
        const updateWard = (value = '') => populate(ward, locations[province.value]?.[district.value] ?? [], value, 'ward');
        const updateDistrict = (value = '', wardValue = '') => {
            populate(district, Object.keys(locations[province.value] ?? {}), value, 'district');
            updateWard(wardValue);
        };
        populate(province, Object.keys(locations), previous[0], 'province');
        updateDistrict(previous[1], previous[2]);
        province.addEventListener('change', () => updateDistrict());
        district.addEventListener('change', () => updateWard());
    }
});
</script>
