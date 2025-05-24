<x-filament-panels::page 
    x-data="{
        selectedConsultation: ''
    }"
>

<div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg"

>
    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
        <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Patient</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider text-end">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-900 dark:divide-gray-700" 
            x-data="{ consultations: @js($consultations) }"
        >
            <template x-for="consultation in consultations" :key="consultation.id">
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100" x-text="consultation.date_consult"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100" x-text="consultation.patient.full_name"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100" x-text="consultation.status"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                        <div class="flex items-center justify-end gap-3">
                            <a :href="'/admin/{{$tenant}}/consultations/' + consultation.id + '/edit'" class="fi-link group/link inline-flex items-center gap-1">
                                <x-filament::icon icon="heroicon-m-pencil-square" class="h-4 w-4 text-primary-500 dark:text-primary-400"/>    
                                <span class="font-semibold text-sm text-primary-600 dark:text-primary-400 group-hover/link:underline">Edit</span>
                            </a>
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="fi-link inline-flex items-center">
                                    <x-filament::icon icon="heroicon-m-ellipsis-vertical" class="h-4 w-4 text-gray-500"/>
                                </button>
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg z-50">
                                    <div class="py-1">
                                    <div x-data="{ 
                                        openPrescription(id) {
                                        open = false;
                                            $dispatch('open-modal', { id: 'print-prescription' });
                                        }
                                    }">
                                        <a href="#" @click="openPrescription(consultation.id)" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Action 1</a>
                                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">Action 2</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </template>
        </tbody>
    </table>
</div>
<x-filament::modal 
    id="print-prescription"
    width="2xl"
    slide-over
>
    <span x-text="selectedConsultation"></span>
</x-filament::modal>
</x-filament-panels::page>
