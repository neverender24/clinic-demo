<?php

namespace App\Models;

use Filament\Facades\Filament;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClinicSetting extends Model
{
    protected $table = 'clinic_settings';

    protected $fillable = ['clinic_id', 'data'];

    protected $casts = ['data' => 'array'];

    /** Per-request cache to avoid repeated DB queries. */
    private static ?array $requestCache = null;

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    // -------------------------------------------------------------------------
    // Widget configuration
    // -------------------------------------------------------------------------

    public static function widgetDefaults(): array
    {
        return [
            ['key' => 'new_patients_this_month',      'label' => 'Stats Overview',                  'visible' => true],
            ['key' => 'consultation_chart',           'label' => 'Out-Patient Chart',               'visible' => true],
            ['key' => 'hospital_admission_chart',     'label' => 'Hospital Admission',              'visible' => true],
            ['key' => 'age_distribution_chart',       'label' => 'Age Group Distribution',          'visible' => true],
            ['key' => 'new_returning_patients_chart', 'label' => 'New vs Returning Patients',        'visible' => true],
            ['key' => 'follow_up_rate_chart',         'label' => 'Follow-up Rate (%)',              'visible' => true],
            ['key' => 'gender_distribution_chart',    'label' => 'Patient Gender Distribution',     'visible' => true],
            ['key' => 'most_prescribed_drugs',        'label' => 'Most Prescribed Drugs',           'visible' => true],
            ['key' => 'medical_conditions_chart',     'label' => 'Medical Conditions Distribution', 'visible' => true],
            ['key' => 'medicine_table_widget',        'label' => 'Medicine Usage Table',            'visible' => true],
        ];
    }

    // -------------------------------------------------------------------------
    // Defaults
    // -------------------------------------------------------------------------

    public static function defaults(): array
    {
        return [
            'consultation' => [
                // Form field visibility
                'show_dialysis'           => true,
                'show_consultation_fee'   => true,
                'show_attachments'        => true,
                'show_vital_signs'        => true,
                'show_patient_section'    => true,

                // SOAP labels
                'label_subjective'        => 'Subjective',
                'label_objective'         => 'Objective',
                'label_assessment'        => 'Assessment',
                'label_plan'              => 'Plan',

                // SOAP editor types: 'textarea' | 'richeditor'
                'editor_subjective'       => 'textarea',
                'editor_objective'        => 'textarea',
                'editor_assessment'       => 'textarea',
                'editor_plan'             => 'textarea',

                // Patient details sidebar toggles
                'show_smoker_type'        => true,
                'show_allergies'          => true,
                'show_surgeries'          => true,
                'show_medical_conditions' => true,
                'show_medications'        => true,
                'show_birthday'           => true,
                'show_age'                => true,
                'show_sex'                => true,
                'show_civil_status'       => true,
                'show_address'            => true,
                'show_occupation'         => true,
            ],
            'reports'  => [
                'med_cert' => [
                    'header_image' => null,
                    'paper_size' => 'letter',
                    'with_header' => true,
                    'header_fields' => ['name', 'date', 'age', 'address', 'sex'],
                    'content' => null,
                    'header_margin_top' => 5,
                    'header_width_percent' => 100,
                    'header_spacing' => 3,
                ],
            ],
            'clinical_orders' => [
                'lab_tests' => [
                    ['name' => 'CBC'],
                    ['name' => 'Creatinine'],
                    ['name' => 'Sodium'],
                    ['name' => 'Potassium'],
                    ['name' => 'Calcium'],
                    ['name' => 'Magnesium'],
                    ['name' => 'Uric acid'],
                    ['name' => 'Urine albumin-creatinine ratio'],
                    ['name' => 'Urinalysis'],
                    ['name' => 'FBS'],
                    ['name' => 'Lipid profile'],
                    ['name' => 'HBA1C'],
                    ['name' => 'Anti-nuclear antibodies'],
                    ['name' => 'ABG'],
                    ['name' => 'Ultrasound of whole abdomen with pre and post void scan'],
                    ['name' => 'Ultrasound KUB prostate'],
                    ['name' => 'CT stonogram'],
                    ['name' => 'CT-Scan of:'],
                ],
            ],
            'patients' => [
                'medical_condition_suggestions' => [
                    ['name' => 'Hypertension'],
                    ['name' => 'Diabetes Mellitus'],
                    ['name' => 'Diabetes Mellitus Type 1'],
                    ['name' => 'Diabetes Mellitus Type 2'],
                    ['name' => 'Stroke'],
                    ['name' => 'Heart Disease'],
                    ['name' => 'Coronary Artery Disease'],
                    ['name' => 'Chronic Kidney Disease'],
                    ['name' => 'Asthma'],
                    ['name' => 'COPD'],
                    ['name' => 'Thyroid Disease'],
                    ['name' => 'Hyperthyroidism'],
                    ['name' => 'Hypothyroidism'],
                    ['name' => 'Cancer'],
                    ['name' => 'Arthritis'],
                    ['name' => 'Epilepsy'],
                    ['name' => 'Hepatitis'],
                    ['name' => 'HIV/AIDS'],
                    ['name' => 'Tuberculosis'],
                    ['name' => 'Anemia'],
                    ['name' => 'Gout'],
                    ['name' => 'Psoriasis'],
                    ['name' => 'Lupus'],
                ],
            ],
            'dashboard' => [
                'widgets' => static::widgetDefaults(),
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Retrieval
    // -------------------------------------------------------------------------

    /**
     * Merge saved settings over defaults.
     *
     * - Associative arrays → merge recursively so new defaults fill missing keys.
     * - Sequential (indexed) lists → the saved value replaces the default entirely,
     *   because array_replace_recursive matches by numeric position and corrupts
     *   ordered lists like lab_tests and dashboard.widgets.
     */
    private static function mergeSettings(array $defaults, array $saved): array
    {
        $result = $defaults;

        foreach ($saved as $key => $value) {
            if (isset($result[$key]) && is_array($result[$key]) && is_array($value)) {
                // Indexed list → use saved data as-is, preserving order and content
                if (array_is_list($value)) {
                    $result[$key] = $value;
                } else {
                    // Associative → recurse so new default keys are back-filled
                    $result[$key] = static::mergeSettings($result[$key], $value);
                }
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    public static function getForCurrentClinic(): array
    {
        if (static::$requestCache !== null) {
            return static::$requestCache;
        }

        $clinic = Filament::getTenant();

        if (! $clinic) {
            return static::$requestCache = static::defaults();
        }

        $setting = static::where('clinic_id', $clinic->id)->first();

        if (! $setting || empty($setting->data)) {
            return static::$requestCache = static::defaults();
        }

        $merged = static::mergeSettings(static::defaults(), $setting->data);

        // Ensure any newly added widgets (not yet in saved data) are appended.
        $savedKeys = array_column($merged['dashboard']['widgets'] ?? [], 'key');
        foreach (static::widgetDefaults() as $default) {
            if (! in_array($default['key'], $savedKeys, true)) {
                $merged['dashboard']['widgets'][] = $default;
            }
        }

        return static::$requestCache = $merged;
    }

    /** Clear the in-request cache (called after saving settings). */
    public static function clearCache(): void
    {
        static::$requestCache = null;
    }

    // -------------------------------------------------------------------------
    // Consultation helpers
    // -------------------------------------------------------------------------

    public static function getConsultationSetting(string $key, bool $default = true): bool
    {
        $settings = static::getForCurrentClinic();

        return (bool) ($settings['consultation'][$key] ?? $default);
    }

    public static function getConsultationValue(string $key, mixed $default = null): mixed
    {
        $settings = static::getForCurrentClinic();

        return $settings['consultation'][$key] ?? $default;
    }

    // -------------------------------------------------------------------------
    // Reports helpers
    // -------------------------------------------------------------------------

    /**
     * Returns lab tests as a key-value array suitable for CheckboxList options.
     * e.g. ['CBC' => 'CBC', 'Creatinine' => 'Creatinine', ...]
     */
    public static function getLabTests(): array
    {
        $tests = static::getForCurrentClinic()['clinical_orders']['lab_tests'] ?? [];

        return collect($tests)
            ->pluck('name')
            ->filter()
            ->mapWithKeys(fn (string $name): array => [$name => $name])
            ->toArray();
    }

    // -------------------------------------------------------------------------
    // Medical Certificate helpers
    // -------------------------------------------------------------------------

    public static function getMedCertSettings(?int $clinicId = null): array
    {
        if ($clinicId) {
            $settings = static::getForClinic($clinicId);
        } else {
            $settings = static::getForCurrentClinic();
        }

        return $settings['reports']['med_cert'] ?? static::defaults()['reports']['med_cert'];
    }

    public static function getForClinic(int $clinicId): array
    {
        $setting = static::where('clinic_id', $clinicId)->first();

        if (! $setting || empty($setting->data)) {
            return static::defaults();
        }

        return static::mergeSettings(static::defaults(), $setting->data);
    }

    // -------------------------------------------------------------------------
    // Patients helpers
    // -------------------------------------------------------------------------

    /**
     * Returns medical condition suggestions as a flat array of strings.
     * e.g. ['Hypertension', 'Diabetes Mellitus', ...]
     */
    public static function getMedicalConditionSuggestions(): array
    {
        $items = static::getForCurrentClinic()['patients']['medical_condition_suggestions'] ?? [];

        return collect($items)
            ->pluck('name')
            ->filter()
            ->values()
            ->toArray();
    }

    // -------------------------------------------------------------------------
    // Dashboard / widget helpers
    // -------------------------------------------------------------------------

    public static function isWidgetVisible(string $key): bool
    {
        $widgets = static::getForCurrentClinic()['dashboard']['widgets'] ?? [];

        foreach ($widgets as $widget) {
            if ($widget['key'] === $key) {
                return (bool) ($widget['visible'] ?? true);
            }
        }

        return true;
    }

    public static function getWidgetSort(string $key): int
    {
        $widgets = static::getForCurrentClinic()['dashboard']['widgets'] ?? [];

        foreach ($widgets as $index => $widget) {
            if ($widget['key'] === $key) {
                return $index + 1;
            }
        }

        return 999;
    }
}
