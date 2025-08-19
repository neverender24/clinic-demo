<style>
    @page {
        size: A5;
        margin: 20mm;
    }

    body {
        font-family: Arial, sans-serif;
    }

    /* Remove fixed height so content can flow */
    .prescription-container {
        width: 148mm;
        /* height: 210mm; removed */
    }

    @media print {
        .prescription-container {
            width: 148mm;
        }
    }

    /* Fix underline gap for date fields */
    .underline-tight {
        display: inline-block;
        border-bottom: 2px solid #000;
        line-height: 1;
        padding-bottom: 0;
    }
</style>

<div class="prescription-container bg-white shadow text-[9pt] p-[10mm]" id="prescription">
    <!-- Header -->
    <header class="header">
        <img src="{{ asset('storage/'.$header_image) }}" alt="Clinic Logo" class="logo">
        <h1><hr></h1>
    </header>

    <!-- Patient Info -->
    <div class="text-content flex flex-col mt-2">
        <div class="flex justify-between">
            <div class="flex p-0">
                <strong>Name:</strong>
                <span class="ms-1">{{ $patient->full_name }}</span>
            </div>
            <div class="basis-1/4">
                <strong>Date:</strong>
                <span class="underline-tight">{{ now()->format('F j, Y') }}</span>
            </div>
        </div>
        <div class="flex justify-between">
            <div class="col-span-3">
                <strong>Address:</strong>
                <span>{{ $patient->address }}</span>
            </div>
            <div class="basis-1/4 flex justify-between">
                <div class="col-span-1">
                    <strong>Age:</strong>
                    <span>{{ \Carbon\Carbon::parse($patient->birthday)->age }}</span>
                </div>
                <div class="col-span-1">
                    <strong>Sex:</strong>
                    <span>{{ $patient->sex }}</span>
                </div>
            </div>
        </div>
        <div class="font-bold mt-5 text-center">
            @isset($title)
                {{ $title }}
            @else
                ADMITTING ORDERS
            @endisset
        </div>
    </div>

    <!-- Main Content -->
    <section class="content mt-[10px]">
        {!! $data !!}
    </section>

    <!-- Footer -->
    <footer class="footer mt-10">
        <div class="grid grid-cols-6 gap-x-5 mt-auto w-full">
            <div class="col-span-3"></div>
            <div class="col-span-3 physician-signature text-[7pt] flex justify-end">
                <div>
                    <p class="font-bold">BEN JAY C. PORCADILLA, RMT, MD, FPCP</p>
                    <p>License no: 0132066</p>
                    <p>PTR no: 2173419</p>
                    <p>S2 License no: _____________________</p>
                </div>
            </div>
        </div>
    </footer>
</div>
