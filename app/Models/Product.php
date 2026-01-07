<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'lesson_id',
        'name',
        'grade',
        'grade_category',
        'semester'
    ];

    public function serials()
    {
        return $this->hasMany(Serial::class);
    }
}