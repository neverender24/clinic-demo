<?php

namespace App\Trait;

use App\Models\ClinicSetting;
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
        $this->data['test_results'] = $this->appendHistoryText(
            $this->data['test_results'] ?? '',
            $record->test_results ?? '',
            'editor_objective',
        );
        $this->data['chief_complaint'] = $this->appendHistoryText(
            $this->data['chief_complaint'] ?? '',
            $record->chief_complaint ?? '',
            'editor_subjective',
        );
        $this->data['diagnosis'] = $this->appendHistoryText(
            $this->data['diagnosis'] ?? '',
            $record->diagnosis ?? '',
            'editor_assessment',
        );
        $this->data['management'] = $this->appendHistoryText(
            $this->data['management'] ?? '',
            $record->management ?? '',
            'editor_plan',
        );
    }

    protected function appendHistoryText(mixed $current, mixed $incoming, string $editorKey): string
    {
        if (ClinicSetting::getConsultationValue($editorKey, 'textarea') === 'richeditor') {
            return $this->appendHistoryRichText($current, $incoming);
        }

        return $this->appendHistoryPlainText($current, $incoming);
    }

    protected function appendHistoryPlainText(mixed $current, mixed $incoming): string
    {
        $currentText = $this->normalizeHistoryText($current);
        $incomingText = $this->normalizeHistoryText($incoming);

        return collect([$currentText, $incomingText])
            ->filter(fn (string $value) => $value !== '')
            ->implode("\n");
    }

    protected function appendHistoryRichText(mixed $current, mixed $incoming): string
    {
        $currentHtml = $this->normalizeHistoryHtml($current);
        $incomingHtml = $this->normalizeHistoryHtml($incoming);

        return collect([$currentHtml, $incomingHtml])
            ->filter(fn (string $value) => $value !== '')
            ->implode('');
    }

    protected function normalizeHistoryText(mixed $content): string
    {
        if (blank($content)) {
            return '';
        }

        if (is_array($content)) {
            $content = $this->renderToHtml($content);
        }

        return trim(strip_tags((string) $content));
    }

    protected function normalizeHistoryHtml(mixed $content): string
    {
        if (blank($content)) {
            return '';
        }

        if (is_array($content)) {
            $content = $this->renderToHtml($content);
        }

        $content = trim((string) $content);

        if ($content === '' || $content === '<p></p>') {
            return '';
        }

        if ($content !== strip_tags($content)) {
            return $content;
        }

        return collect(preg_split('/\R+/', $content) ?: [])
            ->map(fn (string $line) => trim($line))
            ->filter(fn (string $line) => $line !== '')
            ->map(fn (string $line) => '<p>'.e($line).'</p>')
            ->implode('');
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
        // Determine next batch number from existing medicines
        $existingBatches = collect($this->data['medicines'] ?? [])
            ->pluck('batch')
            ->filter()
            ->max() ?? 0;

        $nextBatch = $existingBatches + 1;

        $previous_meds = $record->where('active', 1)->mapWithKeys(fn ($item) => ['record-'.$item['pivot']['id'] => collect($item['pivot'])->except('consultation_id')->merge(['batch' => $nextBatch])])->toArray();

        $new_meds = array_merge($this->data['medicines'], $previous_meds);

        $this->data['medicines'] = $new_meds;

    }
}
