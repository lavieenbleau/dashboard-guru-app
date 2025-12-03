<?php

namespace App\Http\Controllers;

use App\Models\Classroom;

class ClassroomDashboardController extends Controller
{
    public function index($id)
    {
        $classroom = Classroom::with('students')->findOrFail($id);

        return view('guru.dashboard-kelas', compact('classroom'));
    }
}