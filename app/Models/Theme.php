<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Theme extends Model
{
    protected $table = 'themes';
    
    protected $fillable = ['lesson_id', 'theme', 'name'];

    public function subthemes()
    {
        return $this->hasMany(Subtheme::class, 'theme_id');
    }
}