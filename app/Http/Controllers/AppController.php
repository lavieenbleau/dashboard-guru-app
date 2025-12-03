<?php

namespace App\Http\Controllers;

use App\Models\Serial;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function index($serial)
    {
        $serial = Serial::with(['product','classrooms'])
                    ->where('user_id', auth()->id())
                    ->findOrFail($serial);

        return view('guru.app.index', compact('serial'));
    }
}