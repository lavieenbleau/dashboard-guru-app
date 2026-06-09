<script>
window.applyExerciseModelRule = function(config) {
    const typeSelect = document.getElementById(config.typeSelectId);
    const modelSelect = document.getElementById(config.modelSelectId);
    const hintEl = config.hintId ? document.getElementById(config.hintId) : null;
    const formEl = typeSelect ? typeSelect.closest('form') : null;

    if (!typeSelect || !modelSelect) return;

    // Save original options
    const originalOptions = Array.from(modelSelect.querySelectorAll('option')).map(opt => ({
        value: opt.value,
        text: opt.textContent,
        clone: opt.cloneNode(true)
    }));

    const getTypeCode = () => {
        const opt = typeSelect.options[typeSelect.selectedIndex];
        const kode = (opt && opt.dataset && opt.dataset.kode) ? String(opt.dataset.kode).trim().toUpperCase() : '';
        if (kode) return kode;
        const label = (opt && opt.textContent) ? opt.textContent.trim().toUpperCase() : '';
        return label.includes('AKM') ? 'AKM' : '';
    };

    const filterOptions = () => {
        if (!typeSelect.value) {
            // Restore all options if type not selected
            modelSelect.innerHTML = '';
            originalOptions.forEach(o => modelSelect.appendChild(o.clone.cloneNode(true)));
            modelSelect.disabled = false;
            modelSelect.value = '';
            if (hintEl) {
                hintEl.style.display = 'none';
                hintEl.innerHTML = '';
            }
            
            // Remove hidden input if any
            const hiddenInput = document.getElementById(config.modelSelectId + '_hidden');
            if (hiddenInput) hiddenInput.remove();
            
            return;
        }

        const isAkm = getTypeCode() === 'AKM';
        const currentVal = modelSelect.value;
        
        modelSelect.innerHTML = '';
        
        let allowedCount = 0;
        let singleAllowedValue = '';
        let singleAllowedText = '';

        originalOptions.forEach(o => {
            if (!o.value) {
                // Placeholder option
                modelSelect.appendChild(o.clone.cloneNode(true));
                return;
            }

            const t = String(o.text).trim().toLowerCase();
            const isPg = (t === 'pilihan ganda');

            let allowed = false;
            if (isAkm) {
                // AKM allows all options
                allowed = true; 
            } else {
                // Non-AKM ONLY allows Pilihan Ganda
                allowed = isPg;
            }

            if (allowed) {
                modelSelect.appendChild(o.clone.cloneNode(true));
                allowedCount++;
                singleAllowedValue = o.value;
                singleAllowedText = o.text.trim();
            }
        });

        if (allowedCount === 1) {
            modelSelect.value = singleAllowedValue;
            modelSelect.disabled = true;
            
            // Add hidden input so form submits the value since select is disabled
            let hiddenInput = document.getElementById(config.modelSelectId + '_hidden');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.id = config.modelSelectId + '_hidden';
                hiddenInput.name = modelSelect.name;
                modelSelect.parentNode.appendChild(hiddenInput);
            }
            hiddenInput.value = singleAllowedValue;

            if (hintEl) {
                hintEl.style.display = 'inline-block';
                hintEl.className = 'badge bg-label-primary mt-2';
                hintEl.innerHTML = '<i class="bx bx-check-circle me-1"></i>Hanya mendukung ' + singleAllowedText;
            }
            
        } else {
            modelSelect.disabled = false;
            const hiddenInput = document.getElementById(config.modelSelectId + '_hidden');
            if (hiddenInput) hiddenInput.remove();

            // Try to restore previous value if it's still valid
            const stillValid = Array.from(modelSelect.options).some(opt => opt.value === currentVal && currentVal !== '');
            if (stillValid) {
                modelSelect.value = currentVal;
            } else {
                modelSelect.value = '';
            }

            if (hintEl) {
                hintEl.style.display = 'none';
                hintEl.className = 'form-text text-danger mt-1';
                hintEl.innerHTML = '';
            }
        }
        
        // Always trigger change so main form logic can run
        if (typeof config.onChange === 'function') {
            config.onChange(modelSelect.value);
        } else {
            const evt = new Event('change');
            modelSelect.dispatchEvent(evt);
        }
    };

    typeSelect.addEventListener('change', function() {
        filterOptions();
    });

    if (typeSelect.value) {
        filterOptions();
    }
};
</script>
