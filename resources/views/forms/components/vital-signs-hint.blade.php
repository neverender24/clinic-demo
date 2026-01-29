<div x-data="{
    vitalSigns: [
        { label: 'BP', checked: false },
        { label: 'HR', checked: false },
        { label: 'RR', checked: false },
        { label: 'Temp', checked: false },
        { label: 'O2', checked: false },
        { label: 'Pulse', checked: false },
        { label: 'Height', checked: false },
        { label: 'Weight', checked: false }
    ],
    toggleVitalSign(sign) {
        const textarea = document.querySelector('textarea[wire\\:model\\.live=\'data.chief_complaint\']')
            || document.querySelector('textarea[id$=\'chief_complaint\']');
        if (!textarea) return;

        const text = sign.label + ': ';
        let currentValue = textarea.value || '';

        if (sign.checked) {
            // Append the vital sign
            if (currentValue && !currentValue.endsWith('\n') && currentValue.length > 0) {
                currentValue += '\n';
            }
            currentValue += text;
            textarea.value = currentValue;
        } else {
            // Remove the vital sign line
            const lines = currentValue.split('\n');
            const filtered = lines.filter(line => !line.startsWith(text));
            textarea.value = filtered.join('\n');
        }

        // Trigger input event for Livewire
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    }
}" class="flex flex-wrap gap-2">
    <template x-for="(sign, index) in vitalSigns" :key="index">
        <label class="inline-flex items-center gap-1 text-xs cursor-pointer">
            <input
                type="checkbox"
                x-model="sign.checked"
                @change="toggleVitalSign(sign)"
                class="rounded border-gray-300 text-primary-600 shadow-sm focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-700 h-3 w-3"
            >
            <span x-text="sign.label" class="text-gray-500 dark:text-gray-400"></span>
        </label>
    </template>
</div>
