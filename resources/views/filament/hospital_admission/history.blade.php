<div x-data="myData">
    <div>
        <div x-load="" x-load-src="http://localhost:8000/js/filament/tables/components/table.js?v=3.3.14.0" class="fi-ta">
            <div class="fi-ta-ctn divide-y divide-gray-200 overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:divide-white/10 dark:bg-gray-900 dark:ring-white/10">
                <div class="fi-ta-header-ctn divide-y divide-gray-200 dark:divide-white/10">


                    <!--[if BLOCK]><![endif]-->
                    <div class="border-b border-gray-200 bg-white dark:bg-gray-900 px-4 py-5 sm:px-6">
                        <div class="-ml-4 -mt-2 flex flex-wrap items-center justify-between sm:flex-nowrap">
                            <div class="ml-4 mt-2">
                                <h3 class="text-base font-semibold text-gray-900 dark:text-white">Hospital Admissions</h3>
                            </div>
                            <div class="ml-4 mt-2 shrink-0">
                                <legend class="grid grid-cols-1">
                                    <span class="font-bold text-xs">
                                        Legend:
                                    </span>
                                    <span class="inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium dark:text-white text-gray-900 ">
                                        <svg class="size-1.5 fill-warning-400" viewBox="0 0 6 6" aria-hidden="true">
                                            <circle cx="3" cy="3" r="3"></circle>
                                        </svg>
                                        Admission Date
                                    </span>
                                    <span class="inline-flex items-center gap-x-1.5 rounded-md px-2 py-1 text-xs font-medium dark:text-white text-gray-900 ">
                                        <svg class="size-1.5 fill-success-400" viewBox="0 0 6 6" aria-hidden="true">
                                            <circle cx="3" cy="3" r="3"></circle>
                                        </svg>
                                        Discharge Date
                                    </span>
                                </legend>
                            </div>
                        </div>
                    </div>

                    <div x-show="false" class="fi-ta-header-toolbar flex items-center justify-between gap-x-4 px-4 py-3 sm:px-6" style="display: none;">

                        <div class="flex shrink-0 items-center gap-x-4">

                        </div>

                    </div>


                </div>
                <div class="fi-ta-content relative divide-y divide-gray-200 overflow-x-auto dark:divide-white/10 dark:border-t-white/10">
                    @foreach($hospital_admissions as $row)
                    <!--[if BLOCK]><![endif]-->
                    <div style="--cols-default: repeat(1, minmax(0, 1fr));" class="grid grid-cols-[--cols-default] gap-y-px bg-gray-200 dark:bg-white/5">
                        <!--[if BLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->

                        <div class="fi-ta-record relative h-full bg-white transition duration-75 dark:bg-gray-900">

                            <div class="flex items-center">
                                <!--[if BLOCK]><![endif]--><!--[if ENDBLOCK]><![endif]-->


                                <div class="flex w-full flex-col gap-y-3 py-4 md:flex-row md:items-center">
                                    <div class="flex-1">
                                        <!--[if BLOCK]><![endif]-->
                                        <div class="ps-4 sm:ps-6 pe-4 sm:pe-6 block w-full">
                                            <!--[if BLOCK]><![endif]-->
                                            <!--[if BLOCK]><![endif]-->
                                            <div style="--col-span-default: span 1 / span 1;" class="col-[--col-span-default] flex-1 w-full">
                                                <!--[if BLOCK]><![endif]-->
                                                <div class="fi-ta-split flex items-center gap-3">
                                                    <!--[if BLOCK]><![endif]-->
                                                    <!--[if BLOCK]><![endif]-->
                                                    <div style="--col-span-default: span 1 / span 1;" class="col-[--col-span-default] flex-1 w-full">
                                                        <!--[if BLOCK]><![endif]-->
                                                        <div class="fi-ta-col-wrp">
                                                            <!--[if BLOCK]><![endif]-->
                                                            <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                                                <div class="fi-ta-text grid w-full gap-y-1">
                                                                    <div class="flex ">
                                                                        <div class="flex max-w-max">
                                                                            <div class="fi-ta-text-item inline-flex items-center gap-1.5  ">
                                                                                <span class="fi-ta-text-item-label text-sm leading-6 text-gray-950 dark:text-white  ">
                                                                                    {{$row->hospital}}
                                                                                </span>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div style="--col-span-default: span 1 / span 1;" class="col-[--col-span-default] flex-1 w-full">
                                                        <div class="flex flex-col items-start">
                                                            <div style="--col-span-default: span 1 / span 1;" class="col-[--col-span-default] flex-1 w-full">
                                                                <div class="fi-ta-col-wrp">
                                                                    <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                                                        <div class="fi-ta-text grid w-full gap-y-1">
                                                                            <div class="flex ">
                                                                                <div class="flex max-w-max" style="">
                                                                                    <div class="fi-ta-text-item inline-flex items-center gap-1.5 fi-color-custom fi-color-warning">
                                                                                        <svg style="--c-500:var(--warning-500);" class="fi-ta-text-item-icon h-5 w-5 text-custom-500" width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M22.8353 10.0654C22.1878 9.84955 21.5191 10.3315 21.5191 11.0141V12.5819H15V35.3986H21.5191V37.2128C21.5191 37.8953 22.1878 38.3773 22.8353 38.1614L31.7004 35.2064C32.1087 35.0703 32.3842 34.6882 32.3842 34.2577V13.9691C32.3842 13.5387 32.1087 13.1565 31.7004 13.0204L22.8353 10.0654ZM25.8651 23.3891C25.8651 24.1892 25.5408 24.8378 25.1408 24.8378C24.7407 24.8378 24.4164 24.1892 24.4164 23.3891C24.4164 22.589 24.7407 21.9404 25.1408 21.9404C25.5408 21.9404 25.8651 22.589 25.8651 23.3891ZM21.5191 14.5819H17V33.3986H21.5191V14.5819Z" fill="currentColor"></path>
                                                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9 6C7.34315 6 6 7.34315 6 9V39C6 40.6569 7.34315 42 9 42H39C40.6569 42 42 40.6569 42 39V9C42 7.34315 40.6569 6 39 6H9ZM40 9C40 8.44771 39.5523 8 39 8H9C8.44771 8 8 8.44772 8 9V39C8 39.5523 8.44772 40 9 40H39C39.5523 40 40 39.5523 40 39V9Z" fill="currentColor"></path>
                                                                                        </svg>
                                                                                        <span class="fi-ta-text-item-label text-sm leading-6 text-custom-600 dark:text-custom-400  " style="--c-400:var(--warning-400);--c-600:var(--warning-600);">
                                                                                            {{Carbon\Carbon::parse($row->admission_date)->format('F j, Y')}}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div style="--col-span-default: span 1 / span 1;" class="col-[--col-span-default] flex-1 w-full">
                                                                <div class="fi-ta-col-wrp">
                                                                    <div class="flex w-full disabled:pointer-events-none justify-start text-start">
                                                                        <div class="fi-ta-text grid w-full gap-y-1">
                                                                            <div class="flex ">
                                                                                <div class="flex max-w-max" style="">
                                                                                    <div class="fi-ta-text-item inline-flex items-center gap-1.5 fi-color-custom fi-color-success">
                                                                                        <svg style="--c-500:var(--success-500);" class="fi-ta-text-item-icon h-5 w-5 text-custom-500" width="48" height="48" viewBox="0 0 48 48" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.6489 20.0637C4.13178 20.2576 3.86978 20.834 4.0637 21.3511L6.95436 29.0596L5.21925 36H5.00003C4.44774 36 4.00003 36.4477 4.00003 37C4.00003 37.5523 4.44774 38 5.00003 38H5.97911C5.9936 38.0003 6.00805 38.0003 6.02245 38H17.9769C17.9919 38.0003 18.007 38.0003 18.0222 38H29.9779C29.993 38.0003 30.0081 38.0003 30.0232 38H41.9776C41.9867 38.0002 41.9959 38.0003 42.0051 38.0002L42.0209 38H43C43.5523 38 44 37.5523 44 37C44 36.4477 43.5523 36 43 36H42.7808L41.0457 29.0596L43.9364 21.3511C44.1303 20.834 43.8683 20.2576 43.3512 20.0637C42.834 19.8698 42.2576 20.1318 42.0637 20.6489L40.432 25H30V27H32V29H31C30.4477 29 30 29.4477 30 30C30 30.4435 30.2887 30.8196 30.6884 30.9505L29.2457 36H18.7543L17.3116 30.9505C17.7113 30.8196 18 30.4435 18 30C18 29.4477 17.5523 29 17 29H16V27H18V25H7.56803L5.93636 20.6489C5.74244 20.1318 5.16602 19.8698 4.6489 20.0637ZM39.0637 28.6489C39.0214 28.7618 39 28.8807 39 29H34V27H39.682L39.0637 28.6489ZM40.7192 36L39.4692 31H32.7543L31.3258 36H40.7192ZM8.93636 28.6489L8.31803 27H14V29H9.00003C9.00003 28.8807 8.97869 28.7618 8.93636 28.6489ZM16.6743 36L15.2457 31H8.5308L7.2808 36H16.6743Z" fill="currentColor"></path>
                                                                                            <path d="M23 13V10H25V13H28V15H25V18H23V15H20V13H23Z" fill="currentColor"></path>
                                                                                        </svg>
                                                                                        <span class="fi-ta-text-item-label text-sm leading-6 text-custom-600 dark:text-custom-400  " style="--c-400:var(--success-400);--c-600:var(--success-600);">
                                                                                            {{Carbon\Carbon::parse($row->discharge_date)->format('F j, Y')}}
                                                                                        </span>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="fi-ta-actions flex shrink-0 items-center gap-3 flex-wrap sm:flex-nowrap justify-start md:justify-end md:ps-3 ps-4 sm:ps-6 pe-4 sm:pe-6">
                                        <button @click="showModal({{$row}})" type="button" class="fi-link group/link relative inline-flex items-center justify-center outline-none fi-size-sm fi-link-size-sm gap-1 fi-color-custom fi-color-primary fi-ac-action fi-ac-link-action">

                                            <svg fill="none" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" class="animate-spin fi-link-icon h-4 w-4 text-custom-600 dark:text-custom-400" style="--c-400:var(--primary-400);--c-600:var(--primary-600);" wire:loading.delay.default="" wire:target="mountTableAction('view', '2')">
                                                <path clip-rule="evenodd" d="M12 19C15.866 19 19 15.866 19 12C19 8.13401 15.866 5 12 5C8.13401 5 5 8.13401 5 12C5 15.866 8.13401 19 12 19ZM12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z" fill-rule="evenodd" fill="currentColor" opacity="0.2"></path>
                                                <path d="M2 12C2 6.47715 6.47715 2 12 2V5C8.13401 5 5 8.13401 5 12H2Z" fill="currentColor"></path>
                                            </svg>
                                            <span class="font-semibold text-sm text-custom-600 dark:text-custom-400 group-hover/link:underline group-focus-visible/link:underline" style="--c-400:var(--primary-400);--c-600:var(--primary-600);">
                                                View
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <section id="modal-hospital-admission" x-show="modal">
        <div class="relative z-10" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <!--
    Background backdrop, show/hide based on modal state.

    Entering: "ease-out duration-300"
      From: "opacity-0"
      To: "opacity-100"
    Leaving: "ease-in duration-200"
      From: "opacity-100"
      To: "opacity-0"
  -->
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/90 transition-opacity" aria-hidden="true"></div>

            <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <!--
        Modal panel, show/hide based on modal state.

        Entering: "ease-out duration-300"
          From: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
          To: "opacity-100 translate-y-0 sm:scale-100"
        Leaving: "ease-in duration-200"
          From: "opacity-100 translate-y-0 sm:scale-100"
          To: "opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
      -->
                    <div class="relative transform overflow-hidden rounded-lg bg-white dark:bg-gray-800/80 px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                        <div class=" flex flex-col text-md">
                            <div class="text-lg uppercase border-b border-b-1 bdc-gray-500/60 dark:bdc-white/60 mb-3 pb-2">
                                🏥 Hospital Admission Details
                            </div>
                            <div>
                                <div class="mt-6 border-t border-white/10">
                                    <dl class="divide-y divide-white/10">
                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0" >
                                        <dt class="text-sm/6 font-medium text-white">Record Created:</dt>
                                        <dd class="mt-1 text-sm/6 text-gray-400 sm:col-span-2 sm:mt-0" x-text="admission.created_at"></dd>
                                    </div>
                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm/6 font-medium text-white">Hospital:</dt>
                                        <dd class="mt-1 text-sm/6 text-gray-400 sm:col-span-2 sm:mt-0" x-text="admission.hospital"></dd>
                                    </div>
                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0" >
                                        <dt class="text-sm/6 font-medium text-white">Admission Date:</dt>
                                        <dd class="mt-1 text-sm/6 text-gray-400 sm:col-span-2 sm:mt-0" x-text="admission.admission_date"></dd>
                                    </div>
                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm/6 font-medium text-white">Discharged Date:</dt>
                                        <dd class="mt-1 text-sm/6 text-gray-400 sm:col-span-2 sm:mt-0" x-text="admission.discharge_date"></dd>
                                    </div>
                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm/6 font-medium text-white">Diagnosis:</dt>
                                        <dd class="mt-1 text-sm/6 text-gray-400 sm:col-span-2 sm:mt-0" x-html="admission.final_diagnosis"></dd>
                                    </div>
                                    <div class="px-4 py-6 sm:grid sm:grid-cols-3 sm:gap-4 sm:px-0">
                                        <dt class="text-sm/6 font-medium text-white">Remarks:</dt>
                                        <dd class="mt-1 text-sm/6 text-gray-400 sm:col-span-2 sm:mt-0" x-html="admission.remarks"></dd>
                                    </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                            <button type="button" @click="$dispatch('update-from-admission', {data: admission})" class="inline-flex w-full justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 sm:col-start-2">Copy</button>
                            <button type="button" @click="closeModal" class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
</div>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('myData', () => ({
            modal: false,
            admission: [],
            showModal(data) {
                this.admission = data;
                this.modal = true;
            },
            closeModal() {
                this.admission = [];
                this.modal = false;
            }
        }))
    })
</script>