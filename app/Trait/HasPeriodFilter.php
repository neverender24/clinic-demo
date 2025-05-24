<?php

namespace App\Trait;

use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;

trait HasPeriodFilter
{
    public function getDaily($data)
    {
        // dd($data->where('consultation_date', '2025-03-02'));
        $firstDayOfTheMonth = now()->startOfMonth();
        $lastDayOfTheMonth = now()->endOfMonth();
        // if (now()->day > 15) {
        //     $lastDayOfTheMonth = now();
        // } else {
        //     $lastDayOfTheMonth = $lastDayOfTheMonth->subDays(15);
        // }

        $dailyData = [];
        for ($day = $firstDayOfTheMonth; $day <= $lastDayOfTheMonth; $day->addDay()) {
            $dailyData[] = [
                'label' => $day->format('F - d'),
                'count' => $data->where('consultation_date', $day->format('Y-m-d'))->count()// Store each day as a string in 'Y-m-d' format
            ];
            
        }

        // dd($dailyData);

        return collect($dailyData);
        
    }

    public function getWeekly($data)
    {
        $data = $data->each(function($item) {
            $item->week = $item->date->weekOfMonth;
        })
        ->groupBy('week')
        ->map(fn($item, $key) => [
            'label' => "Week {$key}",
            'count' => $item->count()
        ])
        ->values();

        return $data;
    }

    public function getMonthly($data)
    {
        $firstDayOfTheYear = now()->startOfYear();
        $lastDayOfTheYear = now();
        $monthlyData = [];

        // Loop through all months in the current year and group consultations by month
        for ($month = $firstDayOfTheYear; $month <= $lastDayOfTheYear; $month->addMonth()) {
            $monthLabel = $month->format('F');  // Get the name of the month (e.g., "January")

            if (!isset($monthlyData[$monthLabel])) {
                $monthlyData[$monthLabel] = 0;
            }

            // Count consultations for this month
            $monthlyData[$monthLabel] = $data->filter(function($consultation) use ($month) {
                return $consultation->date->month == $month->month;  // Filter by month
            })->count();
        }

        // Convert the monthly data into a collection
        return collect($monthlyData)->map(function($count, $label) {
            return [
                'label' => $label,
                'count' => $count
            ];
        });

    }

    public function getYearly($data)
    {
        return $data->groupBy('year')->map(function($item, $key) {
            return [
                'label' => 'Year '.$key,
                'count' => $item->count()
            ];
        });
    }
}
