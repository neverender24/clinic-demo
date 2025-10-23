<?php

namespace App\Filament\Widgets;

use App\Models\Consultation;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class FollowUpRateChart extends ApexChartWidget
{
    protected static ?string $chartId = 'followUpRateChart';
    protected static ?string $heading = 'Follow-up Rate (%)';
    protected static ?int $sort = 6;
    protected int | string | array $columnSpan = 3;
    protected static ?int $contentHeight = 200; //px

    protected function getOptions(): array
    {
        // 🧠 Step 1: Get consultations that have a follow-up scheduled
        $scheduledFollowUps = Consultation::whereNotNull('next_follow_up_schedule')->get();

        $totalScheduled = $scheduledFollowUps->count();
        $totalReturned = 0;

        // 🧩 Step 2: For each scheduled patient, check if they had another consultation
        foreach ($scheduledFollowUps as $consult) {
            $hasFollowedUp = Consultation::where('patient_id', $consult->patient_id)
                ->where('date', '>=', $consult->next_follow_up_schedule) // include same date
                ->where('id', '!=', $consult->id) // avoid counting the same record
                ->exists();

            if ($hasFollowedUp) {
                $totalReturned++;
            }
        }

        // 📊 Step 3: Compute rate
        $followUpRate = $totalScheduled > 0
            ? round(($totalReturned / $totalScheduled) * 100, 1)
            : 0;

        // ✅ Step 4: Return chart options

        return [
            'chart' => [
                'type' => 'radialBar',
            ],
            'series' => [$followUpRate],
            'labels' => ['Follow-up Rate'],
            'plotOptions' => [
                'radialBar' => [
                    'hollow' => ['size' => '65%'],
                    'dataLabels' => [
                        'name' => ['fontSize' => '16px'],
                        'value' => ['fontSize' => '22px'],
                    ],
                ],
            ],
            'colors' => ['#10b981'],
        ];
    }
}
