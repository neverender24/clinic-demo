<?php

namespace App\Filament\Widgets;

use App\Models\Consultation;
use App\Trait\Dashboard\HasAgeDistributationColumn;
use App\Trait\Dashboard\HasDashboardSettings;
use App\Trait\Dashboard\InteractsWithDashboardFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class FollowUpRateChart extends ApexChartWidget
{
    use HasDashboardSettings;
    use HasAgeDistributationColumn;
    use InteractsWithDashboardFilters;

    protected static ?string $chartId = 'followUpRateChart';
    protected static ?string $heading = 'Follow-up Rate (%)';
    protected static ?int $contentHeight = 200; //px
 
    protected function getOptions(): array
    {
        $scheduledFollowUps = $this->applyDashboardConsultationFilters(
            Consultation::query()->whereNotNull('next_follow_up_schedule')
        )->get();

        $totalScheduled = $scheduledFollowUps->count();
        $totalReturned = 0;

        foreach ($scheduledFollowUps as $consult) {
            $hasFollowedUp = $this->applyDashboardConsultationFilters(
                Consultation::query()->where('patient_id', $consult->patient_id)
                ->where('date', '>=', $consult->next_follow_up_schedule) // include same date
                ->where('id', '!=', $consult->id)
            )->exists();

            if ($hasFollowedUp) {
                $totalReturned++;
            }
        }

        $followUpRate = $totalScheduled > 0
            ? round(($totalReturned / $totalScheduled) * 100, 1)
            : 0;

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
