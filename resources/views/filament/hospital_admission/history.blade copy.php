<div>
    <div class=" ">
        <div class="mx-auto max-w-7xl">
            <div class="bg-gray-900 py-5 rounded-xl">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="-ml-4 -mt-2 flex flex-wrap items-center justify-between sm:flex-nowrap border-b bdc-white">
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
                            <!-- <button type="button" class="relative inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Create new job</button> -->
                        </div>
                    </div>
                    <div class="mt-8 flow-root">
                        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                                @if($hospital_admissions->count() > 0)
                                <table class="min-w-full divide-y divide-gray-700">
                                    <!-- <thead>
                                        <tr>
                                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-white sm:pl-0">Name</th>
                                        </tr>
                                    </thead> -->
                                    <tbody class="divide-y divide-gray-800">
                                        @foreach($hospital_admissions as $row)
                                        <tr>
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 dark:text-gray-200 sm:pl-0 w-20
                                            ">{{ $row->clinic->name }}</td>
                                            <td>
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
                                                                                        April 3, 2025
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
                                                                                        April 15, 2025
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
                                            </td>
                                        </tr>
                                        @endforeach

                                        <!-- More people... -->
                                    </tbody>
                                </table>
                                @else
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>