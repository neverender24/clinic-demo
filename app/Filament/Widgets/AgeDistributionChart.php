<?php

namespace App\Filament\Widgets;

use Illuminate\Support\Facades\DB;
use App\Trait\Dashboard\HasAgeDistributationColumn;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class AgeDistributionChart extends ApexChartWidget
{
    use HasAgeDistributationColumn;

    protected static ?string $chartId = 'ageDistributionChart';
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Age Group Distribution';
    protected static ?int $contentHeight = 200; //px

    protected function getOptions(): array
    {
        // 🧠 Calculate age dynamically from birthday and group by range
        $ageGroups = DB::table('consultations')
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->selectRaw("
                CASE
                    WHEN TIMESTAMPDIFF(YEAR, patients.birthday, CURDATE()) BETWEEN 0 AND 12 THEN '0–12 (Children)'
                    WHEN TIMESTAMPDIFF(YEAR, patients.birthday, CURDATE()) BETWEEN 13 AND 19 THEN '13–19 (Teens)'
                    WHEN TIMESTAMPDIFF(YEAR, patients.birthday, CURDATE()) BETWEEN 20 AND 39 THEN '20–39 (Adults)'
                    WHEN TIMESTAMPDIFF(YEAR, patients.birthday, CURDATE()) BETWEEN 40 AND 59 THEN '40–59 (Middle-aged)'
                    WHEN TIMESTAMPDIFF(YEAR, patients.birthday, CURDATE()) >= 60 THEN '60+ (Seniors)'
                    ELSE 'Unknown'
                END AS age_group,
                COUNT(DISTINCT patients.id) AS total
            ")
            ->groupBy('age_group')
            ->orderByRaw('MIN(TIMESTAMPDIFF(YEAR, patients.birthday, CURDATE()))')
            ->get();

        // 📊 Prepare chart data
        $labels = $ageGroups->pluck('age_group')->toArray();
        $series = $ageGroups->pluck('total')->toArray();

        return [
            'chart' => [
                'type' => 'pie',
            ],
            'series' => $series,
            'labels' => $labels,
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
