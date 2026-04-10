<?php

namespace App\Filament\Widgets;

use App\Models\Patient;
use App\Models\Consultation;
use App\Trait\Dashboard\HasWidgetStatsColumn;
use App\Trait\Dashboard\HasDashboardSettings;
use App\Trait\Dashboard\InteractsWithDashboardFilters;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewPatientsThisMonth extends StatsOverviewWidget
{
    use HasDashboardSettings;
    use HasWidgetStatsColumn;
    use InteractsWithDashboardFilters;

    public function getColumnSpan(): int | string | array
    {
        return ['default' => 'full'];
    }

    protected function getStats(): array
    {
        [$startDate, $endDate] = $this->getDashboardDateRange();

        $totalPatients = Patient::query()
            ->whereHas('consultations', fn ($query) => $this->applyDashboardConsultationFilters($query))
            ->distinct()
            ->count('patients.id');

        $consultations = $this->applyDashboardConsultationFilters(Consultation::query())->count();

        $newPatients = Patient::query()
            ->when($startDate, fn ($query) => $query->where('created_at', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->where('created_at', '<=', $endDate))
            ->when(
                $this->getDashboardFilter('clinic_id', 'All') !== 'All',
                fn ($query) => $query->whereHas('consultations', fn ($consultations) => $this->applyDashboardConsultationFilters($consultations)),
            )
            ->count();

        $returningPatients = $this->applyDashboardConsultationFilters(Consultation::query())
            ->select('patient_id')
            ->groupBy('patient_id')
            ->havingRaw('COUNT(*) > 1')
            ->count();

        return [
            Stat::make('Total Patients', number_format($totalPatients))
                ->description('Patients matching the dashboard filters')
                ->icon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Consultations', number_format($consultations))
                ->description('Consultations matching the dashboard filters')
                ->icon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make('New Patients', number_format($newPatients))
                ->description('Patient registrations in the selected range')
                ->icon('heroicon-m-user-plus')
                ->color('info'),

            Stat::make('Returning Patients', number_format($returningPatients))
                ->description('Patients with more than one consultation in range')
                ->icon('heroicon-m-arrow-path')
                ->color('warning'),
        ];
    }
}
