<?php

namespace App\Trait;

trait HasHistoryAction
{
    public function selectHistory($record)
    {
        dd($this->data);
        $this->data['test_results'].=$record->test_results;
        $this->data['chief_complaint'].=$record->chief_complaint;
        $this->data['diagnosis'].=$record->diagnosis;
        $this->data['management'].=$record->management;
        
    }

    public function copyPrescription($record)
    {
        
        $previous_meds = $record->where('active', 1)->map(fn($item) => collect($item->pivot)->except('consultation_id'))->values()->toArray();

        $new_meds = array_merge($this->data['medicines'], $previous_meds);

        $this->data['medicines'] = $new_meds;

    }
}
