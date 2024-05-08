<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealTranslation extends Translation
{
    use HasFactory;

    protected $table = 'translations';

    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'language_code',
        'title',
        'description'
    ];
}
