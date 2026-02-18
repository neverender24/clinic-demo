<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use Filament\Support\RawJs;
use Filament\Schemas\Schema;
use App\Trait\HasPeriodFilter;
use Filament\Facades\Filament;
use App\Models\HospitalAdmission;
use Filament\Forms\Components\Select;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;

class HospitalAdmissionChart extends ApexChartWidget
{
    use HasPeriodFilter, HasFiltersSchema;
    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'hospitalAdmissionChart';

    
    // protected static ?string $pollingInterval = '';
    
    protected static ?int $sort = 3;
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

    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components($this->getFormSchema());
    }

    protected function getFormSchema(): array
    {
        return [
            Select::make('hospital')
                ->options(function() {
                    $hospitals = HospitalAdmission::distinct('hospital')->pluck('hospital', 'hospital');

                    return ['All' => 'All'] + $hospitals->toArray();
                })
                ->label('Hospital')
                ->default('All')
                // ->default(HospitalAdmission::first()?->hospital)
                ,
            Select::make('period')
                ->options([
                    'Daily' => 'Daily',
                    'Weekly' => 'Weekly',
                    'Monthly' => 'Monthly',
                    'Yearly' => 'Yearly',
                ])
                ->default('Monthly'),
        ];
    }
    protected function getOptions(): array
    {
        $this->getData();

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
                'text' => "Showing {$this->filters['period']} Hospital Admission", // Chart title
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
        // dd($this->filters['clinic_id']);
        // dd();

        $data = HospitalAdmission::withPeriod($this->filters['period'])
                    ->when($this->filters['hospital'] != 'All' , fn($query) => $query->where('hospital', $this->filters['hospital']))
                    ->get()
                    ->each(function($item) {
                        $item->date = Carbon::parse($item->admission_date);
                        $item->year = $item->date->year;
                    });

        $filteredData = [];
        if ($this->filters['period'] === 'Daily') {
            $filteredData = $this->getDaily($data);
        } else if ($this->filters['period'] === 'Weekly') {
            $filteredData = $this->getWeekly($data);
        } else if ($this->filters['period'] === 'Monthly') {
            $filteredData = $this->getMonthly($data);
        } else if($this->filters['period'] === 'Yearly') {
            $filteredData = $this->getYearly($data);
        }

        // dd($filteredData);
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
