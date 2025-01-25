<?php

namespace App\Filament\Widgets;

use App\Models\Clinic;
use App\Models\Consultation;
use App\Models\Scopes\TenantScope;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Leandrocfe\FilamentApexCharts\Widgets\ApexChartWidget;

class ConsultationChart extends ApexChartWidget
{

    protected static ?int $sort = 2;

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
    protected static ?string $heading = 'ConsultationChart';

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
                ->options(Clinic::all()->pluck('name', 'id'))
                ->default(Filament::getTenant()->id),
            Select::make('period')
                ->options([
                    'Daily' => 'Daily',
                    'Weekly' => 'Weekly',
                    'Monthly' => 'Monthly',
                    'Yearly' => 'Yearly',
                ])
                ->default('Weekly'),
        ];
    }

    protected function getOptions(): array
    {
        $this->getData();

        return [
            'chart' => [
                'type' => 'bar',
                'height' => 300,
            ],
            'series' => [
                [
                    'name' => 'Consultations',
                    'data' => $this->chartData->toArray(),
                ],
            ],
            'xaxis' => [
                'categories' => $this->chartLabel->toArray(),
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'yaxis' => [
                'labels' => [
                    'style' => [
                        'fontFamily' => 'inherit',
                    ],
                ],
            ],
            'colors' => ['#f59e0b'],
            'plotOptions' => [
                'bar' => [
                    'borderRadius' => 3,
                    'horizontal' => false,
                ],
            ],
        ];
    }

    protected function getData(): void
    {
        // dd($this->filterFormData['clinic_id']);
        // dd();
        $data = Consultation::withoutGlobalScope(TenantScope::class)
                    ->where('clinic_id', $this->filterFormData['clinic_id'])
                    ->get()
                    ->map(fn($item) => [
                        'Weekly' => Carbon::parse($item->date)->weekOfMonth,
                        'Daily' => Carbon::parse($item->date)->dayOfMonth,
                        'Monthly' => Carbon::parse($item->date)->month,
                        'Yearly' => Carbon::parse($item->date)->year
                    ])
                    ->groupBy($this->filterFormData['period'])
                    ->map(function($item, $key) {
                        
                        if ($this->filterFormData['period'] == 'Weekly') {

                            $label = "Week $key";

                        } else if($this->filterFormData['period'] == 'Monthly') {
                            
                            $label = date("F", mktime(0, 0, 0, intval($key), 1));

                        } else {

                            $label = $key;

                        }

                        return [
                            'label' => $label,
                            'count' => $item->count()
                        ];
                    });

        $this->chartData = $data->pluck('count');

        $this->chartLabel = $data->pluck('label');
    }
}
