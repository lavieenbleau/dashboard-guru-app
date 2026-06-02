<script>
window.applyExerciseModelRule = function(config) {
    const typeSelect = document.getElementById(config.typeSelectId);
    const modelSelect = document.getElementById(config.modelSelectId);
    const hintEl = config.hintId ? document.getElementById(config.hintId) : null;

    if (!typeSelect || !modelSelect) return;

    const allOptions = Array.from(modelSelect.querySelectorAll('option')).map(option => ({
        value: option.value,
        text: option.textContent,
        dataType: option.dataset ? option.dataset.type : null,
    }));

    const getTypeCode = () => {
        const opt = typeSelect.options[typeSelect.selectedIndex];
        const kode = (opt && opt.dataset && opt.dataset.kode) ? String(opt.dataset.kode) : '';
        if (kode) return kode.toUpperCase();
        const label = (opt && opt.textContent) ? opt.textContent : '';
        return String(label).toUpperCase().includes('AKM') ? 'AKM' : '';
    };

    const findPilihanGandaId = () => {
        const options = allOptions.filter(o => o.value !== '');
        const pilihanGanda = options.find(o => (o.text || '').trim().toLowerCase() === 'pilihan ganda');
        return pilihanGanda ? String(pilihanGanda.value) : '';
    };

    const rebuildOptions = (restricted) => {
        const pgId = findPilihanGandaId();
        const allowed = restricted
            ? allOptions.filter(o => o.value === '' || String(o.value) === pgId)
            : allOptions;

        modelSelect.innerHTML = '';
        allowed.forEach(o => {
            const option = document.createElement('option');
            option.value = o.value;
            option.textContent = o.text;
            if (o.dataType) option.dataset.type = o.dataType;
            modelSelect.appendChild(option);
        });

        return pgId;
    };

    const applyRule = () => {
        // Rule: only AKM may use all models.
        const restricted = getTypeCode() !== 'AKM';
        const pgId = rebuildOptions(restricted);

        if (restricted) {
            modelSelect.value = pgId || '';
            if (hintEl) {
                hintEl.style.display = 'block';
                hintEl.textContent = 'Tipe soal selain AKM hanya memperbolehkan model soal "Pilihan Ganda".';
            }
        } else if (hintEl) {
            hintEl.style.display = 'none';
            hintEl.textContent = '';
        }
    };

    typeSelect.addEventListener('change', function() {
        if (!this.value) {
            modelSelect.innerHTML = '';
            allOptions.forEach(o => {
                const option = document.createElement('option');
                option.value = o.value;
                option.textContent = o.text;
                if (o.dataType) option.dataset.type = o.dataType;
                modelSelect.appendChild(option);
            });
            modelSelect.value = '';
            if (hintEl) {
                hintEl.style.display = 'none';
                hintEl.textContent = '';
            }
            return;
        }
        applyRule();
        if (typeof config.onChange === 'function') config.onChange(modelSelect.value);
    });

    if (typeSelect.value) {
        applyRule();
        if (typeof config.onChange === 'function') config.onChange(modelSelect.value);
    }
};
</script>
