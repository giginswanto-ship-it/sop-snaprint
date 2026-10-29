<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'form_code',
        'title',
        'slug',
        'reference_sop',
        'category',
        'description',
        'template_html',
        'fields_schema',
        'order',
    ];

    protected $casts = [
        'fields_schema' => 'array',
    ];

    public function module()
    {
        return $this->belongsTo(SopModule::class, 'module_id');
    }

    public function submissions()
    {
        return $this->hasMany(FormSubmission::class, 'document_form_id')->orderBy('created_at', 'desc');
    }
}
