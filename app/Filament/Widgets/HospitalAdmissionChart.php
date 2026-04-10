<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Trait\HasPeriodFilter;
use App\Trait\Dashboard\HasDashboardSettings;
use App\Models\HospitalAdmission;
use App\Trait\Dashboard\InteractsWithDashboardFilters;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class HospitalAdmissionChart extends ApexChartWidget
{
    use HasDashboardSettings;
    use HasPeriodFilter;
    use InteractsWithDashboardFilters;
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'hospitalAdmissionChart';


    // protected static ?string $pollingInterval = '';

    protected int | string | array $columnSpan = 6;

    public function getColumnSpan(): int | string | array
    {
        return [
            'default' => 'full',
            'lg' => 6,
        ];
    }

    protected static ?string $heading = 'Hospital Admission';
    protected $chartData;

    protected $chartLabel;

    protected function getOptions(): array
    {
        $this->getData();
        $period = $this->getDashboardPeriod();

        return [
            'chart' => [
                'type' => 'area', // Change the chart type to 'area'
                'stacked' => false, // Not stacking the areas
                'height' => 350,
                'zoom' => [
                    'type' => 'x',
                    'enabled' => true,
                    'autoScaleYaxis' => true,
                ],
                'toolbar' => [
                    'autoSelected' => 'zoom',
                ],
            ],
            'series' => [
                [
                    'name' => 'Consultations',
                    'data' => $this->chartData->toArray(),
                ],
            ],
            // 'dataLabels' => [
            //     'enabled' => true, // Disable data labels
            //     'formatter' => function($value) {
            //         dd($value);
            //     }
            // ],
            'markers' => [
                'size' => 0, // Remove markers
            ],
            'title' => [
                'text' => "Showing {$period} Hospital Admission",
                'align' => 'left',
            ],
            'fill' => [
                'type' => 'gradient', // Set gradient fill
                'gradient' => [
                    'inverseColors' => false, // Keep the gradient in order
                    'opacityFrom' => 0.5, // Gradient from full opacity
                    'opacityTo' => 0, // Gradient to no opacity
                    'stops' => [0, 90, 100], // Define where the gradient stops
                ],
            ],
            'yaxis' => [
                'title' => [
                    'text' => 'Consultations', // Y-axis title
                ],
            ],
            'xaxis' => [
                // 'type' => 'datetime', // Set the x-axis type to datetime
                'categories' => $this->chartLabel->toArray(), // X-axis categories
                'labels' => [
                    'rotate' => -45, // Slant labels by -45 degrees (slanted)
                    'style' => [
                        'fontSize' => '12px', // Adjust font size for better readability
                        'fontFamily' => 'inherit'
                    ],
                ],
            ],
            'tooltip' => [
                'shared' => false, // Disable shared tooltips
            ],
            'responsive' => [
                [
                    'breakpoint' => 640,
                    'options' => [
                        'chart' => ['height' => 250],
                        'xaxis' => [
                            'labels' => [
                                'style' => ['fontSize' => '9px'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected function getData(): void
    {
        $period = $this->getDashboardPeriod();

        $data = $this->applyDashboardDateFilter(
            $this->applyDashboardClinicFilter(HospitalAdmission::query()),
            'admission_date',
        )
            ->get()
            ->each(function ($item) {
                $item->date = Carbon::parse($item->admission_date);
                $item->consultation_date = $item->date->format('Y-m-d');
                $item->year = $item->date->year;
            });

        $filteredData = [];
        if ($period === 'Daily') {
            $filteredData = $this->getDaily($data);
        } elseif ($period === 'Weekly') {
            $filteredData = $this->getWeekly($data);
        } elseif ($period === 'Monthly') {
            $filteredData = $this->getMonthly($data);
        } elseif ($period === 'Yearly') {
            $filteredData = $this->getYearly($data);
        }
        $this->chartData = $filteredData->pluck('count');
        $this->chartLabel = $filteredData->pluck('label');
    }

    // protected function extraJsOptions(): RawJs
    // {
    //     return RawJs::make(<<<'JS'
    //     {
    //         annotations: {
    //             points: [{
    //                 x: 'Bananas',
    //                 seriesIndex: 0,
    //                 label: {
    //                 borderColor: '#775DD0',
    //                 offsetY: 0,
    //                 style: {
    //                     color: '#fff',
    //                     background: '#775DD0',
    //                 },
    //                 text: 'Bananas are good',
    //                 }
    //             }]
    //         },
    //         yaxis: {
    //             labels: {
    //                 formatter: function (val, index) {
    //                     return val.toFixed(0)
    //                 }
    //             }
    //         },
    //         xaxis: {
    //             labels: {
    //                 rotate: -45
    //             },
    //             tickPlacement: 'on'
    //         },
    //         stroke: {
    //             curve: "smooth"
    //         },
    //         dataLabels: {
    //             enabled: true,
    //             formatter: function (val, opt) {
    //                 if (val) {
    //                     return val;
    //                 }
    //                 return ''
    //             },
    //             dropShadow: {
    //                 enabled: true
    //             },
    //         }
    //         // zoom: {
    //         //     type: "x",
    //         //     enabled: true,
    //         //     autoScaleYaxis: true
    //         // },
    //         // toolbar: {
    //         //     autoSelected: "zoom"
    //         // }

    //     }
    //     JS);
    // }
}
