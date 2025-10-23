<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Facades\DB;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class NewReturningPatientsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'newReturningPatientsChart';
    protected static ?string $heading = 'New vs Returning Patients';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 3;
    protected static ?int $contentHeight = 200; //px

    protected function getOptions(): array
    {
        // 🧠 Query: Count patients by number of consultations
        $patientStats = DB::table('consultations')
            ->select('patient_id', DB::raw('COUNT(*) as total_consultations'))
            ->groupBy('patient_id')
            ->get();

        $newPatients = $patientStats->where('total_consultations', 1)->count();
        $returningPatients = $patientStats->where('total_consultations', '>', 1)->count();

        $labels = ['New Patients', 'Returning Patients'];
        $series = [$newPatients, $returningPatients];

        return [
            'chart' => [
                'type' => 'donut'
            ],
            'series' => $series,
            'labels' => $labels,
            'colors' => ['#60a5fa', '#34d399'], // blue & green tones
            'legend' => [
                'position' => 'bottom',
                'labels' => [
                    'fontFamily' => 'inherit',
                ],
            ],
            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'fontSize' => '13px',
                ],
            ],
        ];
    }
}
