<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $primaryKey = ['translatable_id', 'language_code'];

    public $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'translatable_type',
        'translatable_id',
        'language_code',
        'title'
    ];

    public function translatable()
    {
        return $this->morphTo();
    }
}
