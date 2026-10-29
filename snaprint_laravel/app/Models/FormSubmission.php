<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'document_form_id',
        'form_code',
        'outlet_name',
        'submitted_by',
        'submission_date',
        'form_data',
        'status',
        'notes',
    ];

    protected $casts = [
        'form_data' => 'array',
        'submission_date' => 'date',
    ];

    public function documentForm()
    {
        return $this->belongsTo(DocumentForm::class, 'document_form_id');
    }
}
