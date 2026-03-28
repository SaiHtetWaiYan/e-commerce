@props([
    'name',
    'options' => [],
    'value' => null,
    'placeholder' => 'Select an option',
    'required' => false
])

<div x-data="{
        open: false,
        value: '{{ old($name, $value) }}',
        options: {{ json_encode($options) }},
        get selectedLabel() {
            let selected = this.options.find(opt => opt.value == this.value);
            return selected ? selected.label : '{{ $placeholder }}';
        },
        selectValue(val) {
            this.value = val;
            this.open = false;
        }
    }"
    @click.away="open = false"
    class="relative w-full text-sm"
>
    <!-- Hidden input for form submission -->
    <input type="hidden" name="{{ $name }}" x-model="value" {{ $required ? 'required' : '' }}>

    <!-- Dropdown Button -->
    <button type="button" 
        @click="open = !open" 
        class="w-full flex items-center justify-between rounded-xl border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 outline-none text-left"
        :class="{'text-gray-500 dark:text-gray-400': !value}"
    >
        <span x-text="selectedLabel" class="block truncate"></span>
        <svg class="h-4 w-4 text-gray-400 dark:text-gray-500 flex-shrink-0 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

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
        <ul tabindex="-1" role="listbox" class="py-1">
            <template x-for="option in options" :key="option.value">
                <li @click="selectValue(option.value)"
                    class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-gray-50 dark:hover:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                    :class="{'bg-primary-50 dark:bg-primary-900/20 text-primary-900 dark:text-primary-100': value == option.value}"
                    role="option"
                >
                    <span x-text="option.label" class="block truncate font-medium" :class="{'font-bold': value == option.value}"></span>
                    
                    <span x-show="value == option.value" class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary-600 dark:text-primary-400">
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </span>
                </li>
            </template>
            <li x-show="options.length === 0" class="text-gray-500 dark:text-gray-400 italic px-3 py-2 border-b border-white top-2 left-6">No options available</li>
        </ul>
    </div>
</div>
