<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;

class TugasController extends Controller
{
    public function index($serial, $classroom)
    {
        return view('guru.tugas.index', compact('serial', 'classroom'));
    }
}