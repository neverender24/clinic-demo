<?php

namespace App\Filament\Widgets;

use Throwable;
use App\Models\Medicine;
use App\Models\Scopes\ConsultationScope;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class MostPrescribedDrugs extends BaseWidget
{
    protected static ?int $sort = 9;
    protected $medicines;

    protected function getHeading(): ?string
    {
        try {
            if ($this->medicines->count() > 0) {

                return 'Most Prescribed Drugs';

            }
        } catch (Throwable $th) {
            //throw $th;
        }

        return null;
    }

    protected function getStats(): array
    {
        $this->medicines = Medicine::with(['consultations'])
                            ->whereHas('consultations')
                            ->where('active',1)
                            ->get();

//                             dd($this->medicines);
        $data = $this->medicines->map(fn($item) => [
                                'stat' => Stat::make(
                                                new HtmlString($item->full_name_of_medicine),
                                                $item->consultations->count()
                                            ),
                                'count' => $item->consultations->count()
                            ])
                            ->sortByDesc('count')
                            ->take(3)
                            ->pluck('stat')
                            ->toArray();

        return $data;

        // return [
        //     Stat::make('Unique views', '192.1k')
        //         ->description('32k increase')
        //         ->descriptionIcon('heroicon-m-arrow-trending-up')
        //         ->color('success'),
        //     Stat::make('Bounce rate', '21%')
        //         ->description('7% increase')
        //         ->descriptionIcon('heroicon-m-arrow-trending-down')
        //         ->color('danger'),
        //     Stat::make('Average time on page', '3:12')
        //         ->description('3% increase')
        //         ->descriptionIcon('heroicon-m-arrow-trending-up')
        //         ->color('success'),
        // ];
    }
}
