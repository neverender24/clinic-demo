<?php

namespace App\Trait\Dashboard;

use App\Models\Scopes\TenantScope;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Illuminate\Database\Eloquent\Builder;

trait InteractsWithDashboardFilters
{
    use InteractsWithPageFilters;

    protected function getDashboardFilter(string $key, mixed $default = null): mixed
    {
        return data_get($this->pageFilters ?? [], $key, $default);
    }

    protected function getDashboardPeriod(): string
    {
        return (string) $this->getDashboardFilter('period', 'Monthly');
    }

    protected function getDashboardDateRange(): array
    {
        $startDate = $this->parseDashboardDate($this->getDashboardFilter('start_date'));
        $endDate = $this->parseDashboardDate($this->getDashboardFilter('end_date'), false);

        if ($startDate && $endDate && $startDate->gt($endDate)) {
            [$startDate, $endDate] = [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
        }

        if ($startDate || $endDate) {
            return [$startDate, $endDate];
        }

        return match ($this->getDashboardPeriod()) {
            'Daily', 'Weekly' => [now()->startOfMonth(), now()->endOfMonth()],
            'Monthly' => [now()->startOfYear(), now()->endOfDay()],
            default => [null, null],
        };
    }

    protected function applyDashboardDateFilter(mixed $query, string $column): mixed
    {
        [$startDate, $endDate] = $this->getDashboardDateRange();

        return $query
            ->when($startDate, fn ($query) => $query->where($column, '>=', $startDate))
            ->when($endDate, fn ($query) => $query->where($column, '<=', $endDate));
    }

    protected function applyDashboardClinicFilter(mixed $query, string $column = 'clinic_id'): mixed
    {
        $clinicId = $this->getDashboardFilter('clinic_id', 'All');

        return $query->when(
            filled($clinicId) && $clinicId !== 'All',
            fn ($query) => $query->where($column, $clinicId),
        );
    }

    protected function applyDashboardConsultationFilters(Builder $query, string $dateColumn = 'date', string $clinicColumn = 'clinic_id'): Builder
    {
        $query = $query->withoutGlobalScopes([TenantScope::class]);
        $query = $this->applyDashboardClinicFilter($query, $clinicColumn);

        return $this->applyDashboardDateFilter($query, $dateColumn);
    }

    protected function parseDashboardDate(mixed $value, bool $isStartOfDay = true): ?Carbon
    {
        if (blank($value)) {
            return null;
        }

        $date = $value instanceof Carbon ? $value->copy() : Carbon::parse($value);

        return $isStartOfDay ? $date->startOfDay() : $date->endOfDay();
    }
}
