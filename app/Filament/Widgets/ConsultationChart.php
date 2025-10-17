<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Clinic;
use App\Models\Consultation;
use Filament\Facades\Filament;
use App\Models\Scopes\TenantScope;
use Filament\Forms\Components\Select;
use App\Models\Scopes\ConsultationScope;
use App\Trait\HasPeriodFilter;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Support\RawJs;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ConsultationChart extends ApexChartWidget
{

    use HasPeriodFilter;

    // protected static ?string $pollingInterval = '';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 1;

    /**
     * Chart Id
     *
     * @var string
     */
    protected static ?string $chartId = 'consultationChart';

    /**
     * Widget Title
     *
     * @var string|null
     */
    protected static ?string $heading = 'Out-Patient Chart';

    /**
     * Chart options (series, labels, types, size, animations...)
     * https://apexcharts.com/docs/options
     *
     * @return array
     */

     protected $chartData;

     protected $chartLabel;

     protected function getFormSchema(): array
    {
        return [

            Select::make('clinic_id')
                ->options(function() {

                    $hospital = Clinic::all()->pluck('name', 'id');

                    return ['All' => 'All'] + $hospital->toArray();
                })
                ->label('Clinic')
                ->default('All')
                // ->default(Filament::getTenant()->id)
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
                'text' => "Showing {$this->filterFormData['period']} Consultations", // Chart title
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
                //
            ],
        ];

    }

    protected function getData(): void
    {
        // dd($this->filterFormData['clinic_id']);
        // dd();

        $data = Consultation::withoutGlobalScopes([TenantScope::class])
                    ->when($this->filterFormData['clinic_id'] != 'All', fn($query) => $query->where('clinic_id', $this->filterFormData['clinic_id']))
                    ->withPeriod($this->filterFormData['period'])
                    ->get()
                    ->each(function($item) {
                        $item->consultation_date = $item->date->format('Y-m-d');
                    });

        $filteredData = [];
        if ($this->filterFormData['period'] === 'Daily') {
            $filteredData = $this->getDaily($data);
        } else if ($this->filterFormData['period'] === 'Weekly') {
            $filteredData = $this->getWeekly($data);
        } else if ($this->filterFormData['period'] === 'Monthly') {
            $filteredData = $this->getMonthly($data);
        } else if ($this->filterFormData['period'] === 'Yearly') {
            $filteredData = $this->getYearly($data);
        }

//        dd($filteredData);
        $this->chartData = $filteredData->pluck('count');
        $this->chartLabel = $filteredData->pluck('label');

    }

    protected function extraJsOptions(): RawJs
    {
        return RawJs::make(<<<'JS'
        {
            annotations: {
                points: [{
                    x: 'Bananas',
                    seriesIndex: 0,
                    label: {
                    borderColor: '#775DD0',
                    offsetY: 0,
                    style: {
                        color: '#fff',
                        background: '#775DD0',
                    },
                    text: 'Bananas are good',
                    }
                }]
            },
            yaxis: {
                labels: {
                    formatter: function (val, index) {
                        return val.toFixed(0)
                    }
                }
            },
            xaxis: {
                labels: {
                    rotate: -45
                },
                tickPlacement: 'on'
            },
            stroke: {
                curve: "smooth"
            },
            dataLabels: {
                enabled: true,
                formatter: function (val, opt) {
                    if (val) {
                        return val;
                    }
                    return ''
                },
                dropShadow: {
                    enabled: true
                },
            }
            // zoom: {
            //     type: "x",
            //     enabled: true,
            //     autoScaleYaxis: true
            // },
            // toolbar: {
            //     autoSelected: "zoom"
            // }

        }
        JS);
    }
}
