<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConsultationMedicine extends Model
{
    protected $guarded = [];

    protected $table = 'consultation_medicine';

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function medicine()
    {
        return $this->belongsTo(Medicine::class);
    }
}
