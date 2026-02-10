<?php

namespace App\Filament\Widgets;

use Carbon\Carbon;
use App\Models\Patient;
use App\Models\Consultation;
use App\Trait\Dashboard\HasWidgetStatsColumn;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class NewPatientsThisMonth extends StatsOverviewWidget
{
    use HasWidgetStatsColumn;

    protected function getStats(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        // 🧍 Total patients
        $totalPatients = Patient::count();

        // 🩺 Consultations today
        $consultationsToday = Consultation::whereDate('date', Carbon::today())->count();

        // 🆕 New patients this month (first-time consultations)
        $newPatients = Patient::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();

        // 🔁 Returning patients (patients with >1 consultations this month)
        $returningPatients = Consultation::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->select('patient_id')
            ->groupBy('patient_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        // 📈 Growth rates (vs last month)
        $lastMonthRange = [
            Carbon::now()->subMonth()->startOfMonth(),
            Carbon::now()->subMonth()->endOfMonth(),
        ];

        $lastMonthNew = Patient::whereBetween('created_at', $lastMonthRange)->count();

        $lastMonthReturning = Consultation::whereBetween('date', $lastMonthRange)
            ->select('patient_id')
            ->groupBy('patient_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $newGrowth = $lastMonthNew > 0
            ? round((($newPatients - $lastMonthNew) / $lastMonthNew) * 100, 1)
            : 0;

        $returningGrowth = $lastMonthReturning > 0
            ? round((($returningPatients - $lastMonthReturning) / $lastMonthReturning) * 100, 1)
            : 0;

        // 📊 Stats Overview Cards
        return [
            Stat::make('Total Patients', number_format($totalPatients))
                ->description('Registered patients in the system')
                ->icon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Consultations Today', number_format($consultationsToday))
                ->description('Patients seen today')
                ->icon('heroicon-m-calendar-days')
                ->color('success'),

            Stat::make('New Patients (This Month)', number_format($newPatients))
                ->description(
                    $newGrowth >= 0
                        ? "+{$newGrowth}% vs last month"
                        : "{$newGrowth}% vs last month"
                )
                ->descriptionIcon($newGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($newGrowth >= 0 ? 'success' : 'danger'),

            Stat::make('Returning Patients (This Month)', number_format($returningPatients))
                ->description(
                    $returningGrowth >= 0
                        ? "+{$returningGrowth}% vs last month"
                        : "{$returningGrowth}% vs last month"
                )
                ->descriptionIcon($returningGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->icon('heroicon-m-arrow-path')
                ->color($returningGrowth >= 0 ? 'success' : 'danger'),
        ];
    }
}
