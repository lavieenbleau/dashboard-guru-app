<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    public function posts()
{
    return $this->hasMany(Post::class, 'mapel_id');
}

}