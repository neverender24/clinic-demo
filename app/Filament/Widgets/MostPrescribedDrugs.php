<?php

namespace App\Filament\Widgets;

use Throwable;
use App\Models\Medicine;
use App\Trait\Dashboard\HasDashboardSettings;
use App\Trait\Dashboard\InteractsWithDashboardFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\HtmlString;

class MostPrescribedDrugs extends BaseWidget
{
    use HasDashboardSettings;
    use InteractsWithDashboardFilters;

    protected $medicines;

    public function getColumnSpan(): int | string | array
    {
        return ['default' => 'full'];
    }

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
        $this->medicines = Medicine::query()
            ->withCount([
                'consultations as filtered_consultations_count' => fn ($query) => $this->applyDashboardConsultationFilters($query),
            ])
            ->whereHas('consultations', fn ($query) => $this->applyDashboardConsultationFilters($query))
            ->where('active', 1)
            ->get();

        $data = $this->medicines->map(fn($item) => [
                                'stat' => Stat::make(
                                                new HtmlString($item->name . ($item->brand ? "- (".ucwords($item->brand).")" : '')),
                                                $item->filtered_consultations_count
                                            ),
                                'count' => $item->filtered_consultations_count
                            ])
                            ->sortByDesc('count')
                            ->take(3)
                            ->pluck('stat')
                            ->toArray();

        return $data;
    }
}
