@props([
    'name',
    'options' => [],
    'values' => [],
    'placeholder' => 'Select options',
    'required' => false
])

<div x-data="{
        open: false,
        values: {{ json_encode(is_array(old(str_replace('[]', '', $name), $values)) ? array_map('strval', old(str_replace('[]', '', $name), $values)) : (is_object($values) ? $values->map(fn($id) => (string)$id)->toArray() : array_map('strval', (array)($values ?: [])))) }},
        options: {{ json_encode($options) }},
        get selectedLabels() {
            if (this.values.length === 0) return '{{ $placeholder }}';
            return this.options.filter(opt => this.values.includes(String(opt.value))).map(opt => opt.label).join(', ');
        },
        toggleValue(val) {
            val = String(val);
            if (this.values.includes(val)) {
                this.values = this.values.filter(v => v !== val);
            } else {
                this.values.push(val);
            }
        },
        removeValue(val, event) {
            event.stopPropagation();
            this.values = this.values.filter(v => v !== String(val));
        }
    }"
    @click.away="open = false"
    class="relative w-full text-sm sm:col-span-2"
>
    <!-- Dropdown Button -->
    <button type="button" 
        @click="open = !open" 
        class="w-full flex items-center justify-between rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-1.5 min-h-[42px] focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none text-left"
        :class="{'text-gray-500 dark:text-gray-400': values.length === 0}"
    >
        <div class="flex flex-wrap gap-1.5 items-center flex-1 pr-2">
            <span x-show="values.length === 0" class="py-1">{{ $placeholder }}</span>
            <template x-for="val in values" :key="val">
                <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg bg-primary-50 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 text-xs font-semibold border border-primary-200 dark:border-primary-800/50">
                    <span x-text="options.find(opt => String(opt.value) === val)?.label || val"></span>
                    <svg @click="removeValue(val, $event)" class="w-3.5 h-3.5 hover:text-primary-900 dark:hover:text-primary-100 cursor-pointer transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </span>
            </template>
        </div>
        
        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500 flex-shrink-0 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <!-- Hidden inputs for form submission -->
    <template x-for="val in values" :key="val">
        <input type="hidden" name="{{ $name }}" :value="val">
    </template>
    
    <!-- Required validation fallback -->
    <template x-if="{{ $required ? 'true' : 'false' }} && values.length === 0">
        <input type="text" style="opacity:0; position:absolute; z-index:-1; width:1px; height:1px;" required>
    </template>

    <!-- Dropdown Menu -->
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="transform opacity-0 scale-95"
        x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95"
        class="absolute z-10 mt-1 w-full rounded-xl bg-white dark:bg-gray-800 shadow-lg border border-gray-200 dark:border-gray-700 max-h-60 overflow-auto focus:outline-none"
        style="display: none;"
    >
        <ul tabindex="-1" role="listbox" class="py-2 px-1">
            <template x-for="option in options" :key="option.value">
                <li @click="toggleValue(option.value)"
                    class="cursor-pointer select-none rounded-lg px-3 py-2 mx-1 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white flex items-center gap-3 transition-colors"
                >
                    <div class="w-4 h-4 rounded border flex items-center justify-center transition-colors shadow-sm"
                         :class="values.includes(String(option.value)) ? 'bg-primary-600 border-primary-600' : 'bg-white dark:bg-gray-900 border-gray-300 dark:border-gray-600'">
                        <svg x-show="values.includes(String(option.value))" class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span x-text="option.label" class="block truncate font-medium"></span>
                </li>
            </template>
            <li x-show="options.length === 0" class="text-gray-500 dark:text-gray-400 italic px-4 py-2">No options available</li>
        </ul>
    </div>
</div>
