<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SopModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'title',
        'category',
        'description',
        'order',
    ];

    public function sops()
    {
        return $this->hasMany(Sop::class, 'module_id')->orderBy('order', 'asc');
    }

    public function forms()
    {
        return $this->hasMany(DocumentForm::class, 'module_id')->orderBy('order', 'asc');
    }
}
