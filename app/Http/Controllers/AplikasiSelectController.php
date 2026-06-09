<?php

namespace App\Http\Controllers;

use App\Models\Serial;

class AplikasiSelectController extends Controller
{
    public function index()
    {
        $aplikasi = Serial::with('product')
            ->where('user_id', auth()->id())
            ->get()
            ->groupBy('product_id')
            ->map(function ($group) {
                return $group->sortByDesc('expired_at')->first();
            })
            ->values();

        return view('guru.aplikasi.select', compact('aplikasi'));
    }
}