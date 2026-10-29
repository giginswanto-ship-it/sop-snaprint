<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sop extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'sop_code',
        'title',
        'slug',
        'category',
        'purpose',
        'scope',
        'responsibilities',
        'indicators',
        'reference_forms',
        'content_html',
        'order',
        'status',
    ];

    public function module()
    {
        return $this->belongsTo(SopModule::class, 'module_id');
    }
}
