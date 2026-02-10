<?php

namespace App\Trait;

use Filament\Forms\Components\RichEditor\RichContentRenderer;

trait HasHistoryAction
{
    // public function selectHistory($record)
    // {
    //     // dd($this->renderToHtml($this->data['test_results']));
    //     // dd($this->renderToHtml($this->data['test_results']));
    //     $this->data['test_results'] = $this->renderToHtml($this->data['test_results']).$record->test_results;
    //     $this->data['chief_complaint'] = $this->renderToHtml($this->data['chief_complaint']).$record->chief_complaint;
    //     $this->data['diagnosis'] = $this->renderToHtml($this->data['diagnosis']).$record->diagnosis;
    //     $this->data['management'] = $this->renderToHtml($this->data['management']).$record->management;
        
    // }

    public function selectHistory($record)
    {
        // dd($this->renderToHtml($this->data['test_results']));
        // dd($this->renderToHtml($this->data['test_results']));
        $this->data['test_results'] = $this->data['test_results']."\n".(strip_tags($record->test_results));
        $this->data['chief_complaint'] = $this->data['chief_complaint']."\n".(strip_tags($record->chief_complaint));
        $this->data['diagnosis'] = $this->data['diagnosis']."\n".(strip_tags($record->diagnosis));
        $this->data['management'] = $this->data['management']."\n".(strip_tags($record->management));
        
    }

    protected function renderToHtml($content): string 
    {
        if (empty($content)) {
            return '';
        }

        return RichContentRenderer::make($content)->toHtml() == '<p></p>' ? '' : RichContentRenderer::make($content)->toHtml();
    }

    public function copyPrescription($record)
    {
        // dd($record);   
        $previous_meds = $record->where('active', 1)->mapWithKeys(fn($item) => ['record-'.$item['pivot']['id'] => collect($item['pivot'])->except('consultation_id')])->toArray();

        // dd($previous_meds);
        $new_meds = array_merge($this->data['medicines'], $previous_meds);

        $this->data['medicines'] = $new_meds;

    }
}
