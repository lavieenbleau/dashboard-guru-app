<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Serial;
use Illuminate\Support\Facades\Schema;

class GuruDashboardController extends Controller
{
 public function index()
    {
        // If the serials table doesn't exist (development state), return an empty collection
        if (! Schema::hasTable('serials')) {
            $serials = collect();
        } else {
            $serials = Serial::with(['product','classrooms'])
                        ->where('user_id', auth()->id())
                        ->get()
                        ->groupBy('product_id')
                        ->map(function ($group) {
                            return $group->sortByDesc('expired_at')->first();
                        })
                        ->values();
        }

        return view('guru.dashboard', compact('serials'));
    }
}