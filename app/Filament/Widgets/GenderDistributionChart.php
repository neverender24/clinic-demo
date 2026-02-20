<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use App\Trait\Dashboard\HasWidgetStatsColumn;
use App\Trait\Dashboard\HasAgeDistributationColumn;
use App\Trait\Dashboard\HasDashboardSettings;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class GenderDistributionChart extends ApexChartWidget
{
    use HasDashboardSettings;
    use HasAgeDistributationColumn;

    protected static ?string $chartId = 'genderDistributionChart';

    protected static ?string $heading = 'Patient Gender Distribution';
    protected static ?int $contentHeight = 200; //px

    protected function getOptions(): array
    {
        // 🧠 Step 1: Count male & female patients
        $maleCount = Patient::where('sex', 'M')->count();
        $femaleCount = Patient::where('sex', 'F')->count();

        // 🧮 Step 2: Prepare chart data
        return [
            'chart' => [
                'type' => 'donut',
            ],
            'series' => [$maleCount, $femaleCount],
            'labels' => ['Male', 'Female'],

            'colors' => ['#3b82f6', '#ec4899'], // blue and pink

            'legend' => [
                'position' => 'bottom',
                'labels' => [
                    'colors' => '#6b7280',
                    'useSeriesColors' => false,
                ],
            ],


            'dataLabels' => [
                'enabled' => true,
                'style' => [
                    'fontSize' => '14px',
                ],
            ],
            'responsive' => [
                [
                    'breakpoint' => 640,
                    'options' => [
                        'dataLabels' => [
                            'style' => ['fontSize' => '11px'],
                        ],
                        'legend' => [
                            'fontSize' => '11px',
                        ],
                    ],
                ],
            ],
        ];
    }
}
