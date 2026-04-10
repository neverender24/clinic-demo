<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Facades\DB;
use App\Trait\Dashboard\HasAgeDistributationColumn;
use App\Trait\Dashboard\HasDashboardSettings;
use App\Trait\Dashboard\InteractsWithDashboardFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class NewReturningPatientsChart extends ApexChartWidget
{
    use HasDashboardSettings;
    use HasAgeDistributationColumn;
    use InteractsWithDashboardFilters;

    protected static ?string $chartId = 'newReturningPatientsChart';
    protected static ?string $heading = 'New vs Returning Patients';
    protected static ?int $contentHeight = 200; //px

    protected function getOptions(): array
    {
        $patientStats = DB::table('consultations')
            ->select('patient_id', DB::raw('COUNT(*) as total_consultations'))
            ->when(
                $this->getDashboardFilter('clinic_id', 'All') !== 'All',
                fn ($query) => $query->where('clinic_id', $this->getDashboardFilter('clinic_id')),
            );

        $patientStats = $this->applyDashboardDateFilter($patientStats, 'date')
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
            'responsive' => [
                [
                    'breakpoint' => 640,
                    'options' => [
                        'dataLabels' => [
                            'style' => ['fontSize' => '10px'],
                        ],
                        'legend' => [
                            'fontSize' => '10px',
                        ],
                    ],
                ],
            ],
        ];
    }
}
