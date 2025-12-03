<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Serial;

class AplikasiDashboardController extends Controller
{
    public function index($id)
    {
        $serial = Serial::with('product')->findOrFail($id);

        return view('guru.aplikasi.dashboard', compact('serial'));
    }
}