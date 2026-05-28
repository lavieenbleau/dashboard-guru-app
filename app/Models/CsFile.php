<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CsFile extends Model
{
    protected $table = 'cs_files';

    protected $fillable = [
        'room_id',
        'file_path',
    ];

    public function room()
    {
        return $this->belongsTo(CsRoom::class, 'room_id');
    }
}