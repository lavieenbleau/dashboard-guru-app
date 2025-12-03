<?php

namespace App\Http\Controllers;

use App\Models\Serial;

class AplikasiSelectController extends Controller
{
    public function index()
    {
        $aplikasi = Serial::with('product')
            ->where('user_id', auth()->id())
            ->get();

        return view('guru.aplikasi.select', compact('aplikasi'));
    }
}