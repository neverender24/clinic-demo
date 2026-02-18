<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use Illuminate\Support\Collection;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class MedicalConditionsChart extends ApexChartWidget
{
    protected static ?string $chartId = 'medicalConditionsChart';

    protected static ?string $heading = 'Medical Conditions Distribution';

    protected static ?int $sort = 9;

    protected static ?int $contentHeight = 200;

    protected int|string|array $columnSpan = 'full';

    public function getColumnSpan(): int | string | array
    {
        return ['default' => 'full'];
    }

    protected function getOptions(): array
    {
        $conditions = $this->getMedicalConditionsData();

        // Handle empty data gracefully
        if ($conditions->isEmpty()) {
            return [
                'chart' => [
                    'type' => 'donut',
                    'height' => 180,
                ],
                'series' => [1],
                'labels' => ['No data available'],
                'colors' => ['#e5e7eb'],
                'legend' => [
                    'show' => false,
                ],
                'dataLabels' => [
                    'enabled' => false,
                ],
            ];
        }

        // Create labels with counts included
        $labelsWithCounts = $conditions->map(function ($count, $label) {
            return "{$label} ({$count})";
        })->values()->toArray();

        return [
            'chart' => [
                'type' => 'donut',
                'height' => 180,
            ],
            'series' => $conditions->values()->toArray(),
            'labels' => $labelsWithCounts,
            'colors' => [
                '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6',
                '#ec4899', '#06b6d4', '#84cc16', '#f97316', '#6366f1',
            ],
            'legend' => [
                'position' => 'right',
                'fontSize' => '11px',
                'labels' => [
                    'colors' => '#6b7280',
                ],
                'itemMargin' => [
                    'horizontal' => 5,
                    'vertical' => 2,
                ],
            ],
            'plotOptions' => [
                'pie' => [
                    'donut' => [
                        'size' => '70%',
                        'labels' => [
                            'show' => true,
                            'total' => [
                                'show' => true,
                                'label' => 'Total',
                                'fontSize' => '12px',
                                'color' => '#6b7280',
                            ],
                        ],
                    ],
                ],
            ],
            'dataLabels' => [
                'enabled' => false,
            ],
            'responsive' => [
                [
                    'breakpoint' => 1024,
                    'options' => [
                        'legend' => [
                            'position' => 'bottom',
                            'fontSize' => '10px',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getMedicalConditionsData(): Collection
    {
        $patients = Patient::whereNotNull('medical_conditions')
            ->pluck('medical_conditions');

        $conditionCounts = collect();

        foreach ($patients as $conditions) {
            // Handle array (from JSON cast) or comma-separated strings
            if (is_array($conditions)) {
                $conditionsArray = $conditions;
            } elseif (is_string($conditions)) {
                $conditionsArray = array_map('trim', explode(',', $conditions));
            } else {
                continue;
            }

            foreach ($conditionsArray as $condition) {
                $condition = trim((string) $condition);
                if (!empty($condition)) {
                    // Normalize common condition names
                    $normalizedCondition = $this->normalizeConditionName($condition);
                    $conditionCounts[$normalizedCondition] = ($conditionCounts[$normalizedCondition] ?? 0) + 1;
                }
            }
        }

        return $conditionCounts->sortDesc()->take(10);
    }

    protected function normalizeConditionName(string $condition): string
    {
        $condition = ucwords(strtolower(trim($condition)));

        // Common normalization mappings
        $mappings = [
            'Hpn' => 'Hypertension',
            'Htn' => 'Hypertension',
            'High Blood' => 'Hypertension',
            'Highblood' => 'Hypertension',
            'High Blood Pressure' => 'Hypertension',
            'Dm' => 'Diabetes Mellitus',
            'Dm1' => 'Diabetes Mellitus Type 1',
            'Dm2' => 'Diabetes Mellitus Type 2',
            'Diabetes' => 'Diabetes Mellitus',
            'Ckd' => 'Chronic Kidney Disease',
            'Cad' => 'Coronary Artery Disease',
            'Copd' => 'COPD',
        ];

        return $mappings[$condition] ?? $condition;
    }
}
