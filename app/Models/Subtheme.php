<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subtheme extends Model
{
    protected $table = 'subthemes';

    public function materi()
    {
        return $this->hasMany(Lesson::class, 'subtheme_id');
    }
}