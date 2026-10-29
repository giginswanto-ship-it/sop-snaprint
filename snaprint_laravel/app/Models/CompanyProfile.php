<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'brand_name',
        'operator_name',
        'operator_url',
        'document_title',
        'document_subtitle',
        'document_version',
        'effective_date',
        'status',
        'confidentiality_statement',
    ];
}
