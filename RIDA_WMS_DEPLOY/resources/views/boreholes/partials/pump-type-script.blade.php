<script>
    document.addEventListener('DOMContentLoaded', () => {
        const pumpTypeInput = document.getElementById('pump_type');
        const technicalFields = document.querySelector('[data-pump-technical]');
        const handpumpField = document.querySelector('[data-pump-field="handpump"]');

        if (!pumpTypeInput || !technicalFields || !handpumpField) return;

        const cylinderInput = handpumpField.querySelector('input');
        const updatePumpFields = () => {
            const pumpType = pumpTypeInput.value.trim().toLowerCase();
            const isHandpump = pumpType === 'handpump';

            handpumpField.hidden = !isHandpump;
            technicalFields.querySelectorAll('input').forEach((input) => {
                input.disabled = pumpType === '' || (input === cylinderInput && !isHandpump);
            });
        };

        pumpTypeInput.addEventListener('input', updatePumpFields);
        pumpTypeInput.addEventListener('change', updatePumpFields);
        updatePumpFields();
    });
</script>