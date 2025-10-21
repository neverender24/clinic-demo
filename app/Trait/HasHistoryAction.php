<?php

namespace App\Trait;

use Filament\Forms\Components\RichEditor\RichContentRenderer;

trait HasHistoryAction
{
    public function selectHistory($record)
    {
        // dd($this->renderToHtml($this->data['test_results']));
        // dd($this->renderToHtml($this->data['test_results']));
        $this->data['test_results'] = $this->renderToHtml($this->data['test_results']).$record->test_results;
        $this->data['chief_complaint'] = $this->renderToHtml($this->data['chief_complaint']).$record->chief_complaint;
        $this->data['diagnosis'] = $this->renderToHtml($this->data['diagnosis']).$record->diagnosis;
        $this->data['management'] = $this->renderToHtml($this->data['management']).$record->management;
        
    }

    protected function renderToHtml($content): string 
    {
        return RichContentRenderer::make($content)->toHtml() == '<p></p>' ? '' : RichContentRenderer::make($content)->toHtml();
    }

    public function copyPrescription($record)
    {
        
        $previous_meds = $record->where('active', 1)->map(fn($item) => collect($item->pivot)->except('consultation_id'))->values()->toArray();

        $new_meds = array_merge($this->data['medicines'], $previous_meds);

        $this->data['medicines'] = $new_meds;

    }
}
