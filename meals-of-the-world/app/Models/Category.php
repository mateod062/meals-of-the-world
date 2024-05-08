<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = ['slug'];

    public function meals()
    {
        return $this->hasMany(Meal::class);
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }
}
