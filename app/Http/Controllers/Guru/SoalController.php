<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Serial;
use App\Models\Mapel;
use App\Models\Lesson;
use App\Models\Theme;
use App\Models\Subtheme;

class SoalController extends Controller
{
    public function index($serial)
    {
        $serial = Serial::findOrFail($serial);
        
        // Define soal categories
        $categories = [
            ['id' => 'ulangan-harian', 'name' => 'Ulangan Harian', 'icon' => 'bx-edit', 'color' => 'primary'],
            ['id' => 'pts', 'name' => 'Penilaian Tengah Semester', 'icon' => 'bx-file', 'color' => 'warning'],
            ['id' => 'pas', 'name' => 'Penilaian Akhir Semester', 'icon' => 'bx-book', 'color' => 'danger'],
            ['id' => 'tambahan', 'name' => 'Soal Tambahan', 'icon' => 'bx-plus-circle', 'color' => 'success'],
        ];

        return view('guru.soal.index', compact('serial', 'categories'));
    }

    public function subtema($serial, $category)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get category info
        $categories = [
            'ulangan-harian' => ['name' => 'Ulangan Harian', 'color' => 'primary'],
            'pts' => ['name' => 'Penilaian Tengah Semester', 'color' => 'warning'],
            'pas' => ['name' => 'Penilaian Akhir Semester', 'color' => 'danger'],
            'tambahan' => ['name' => 'Soal Tambahan', 'color' => 'success'],
        ];
        
        $categoryInfo = $categories[$category] ?? ['name' => 'Soal', 'color' => 'info'];
        
        // Get unique themes (mata pelajaran)
        $themes = Theme::select('id', 'name')
            ->distinct()
            ->get()
            ->unique('name');

        return view('guru.soal.subtema', compact('serial', 'category', 'categoryInfo', 'themes'));
    }

    public function list($serial, $category, $tema)
    {
        $serial = Serial::findOrFail($serial);
        $tema = Theme::findOrFail($tema);
        
        // Get category info
        $categories = [
            'ulangan-harian' => ['name' => 'Ulangan Harian', 'color' => 'primary'],
            'pts' => ['name' => 'Penilaian Tengah Semester', 'color' => 'warning'],
            'pas' => ['name' => 'Penilaian Akhir Semester', 'color' => 'danger'],
            'tambahan' => ['name' => 'Soal Tambahan', 'color' => 'success'],
        ];
        
        $categoryInfo = $categories[$category] ?? ['name' => 'Soal', 'color' => 'info'];

        // Get soal for this category and tema - store category in semester field
        $lessons = Lesson::where('category', Lesson::CATEGORY_SOAL)
            ->where('grade', $tema->id)
            ->where('semester', $this->getCategoryId($category))
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru.soal.list', compact('serial', 'category', 'categoryInfo', 'tema', 'lessons'));
    }

    public function show($serial, $category, $tema, $id)
    {
        $serial = Serial::findOrFail($serial);
        $tema = Theme::findOrFail($tema);
        $lesson = Lesson::findOrFail($id);
        
        // Get category info
        $categories = [
            'ulangan-harian' => ['name' => 'Ulangan Harian', 'color' => 'primary'],
            'pts' => ['name' => 'Penilaian Tengah Semester', 'color' => 'warning'],
            'pas' => ['name' => 'Penilaian Akhir Semester', 'color' => 'danger'],
            'tambahan' => ['name' => 'Soal Tambahan', 'color' => 'success'],
        ];
        
        $categoryInfo = $categories[$category] ?? ['name' => 'Soal', 'color' => 'info'];
        
        // Get student submissions for this lesson (via posts with lesson_id in category)
        $submissions = \App\Models\Task::with(['student', 'post'])
            ->whereHas('post', function($q) use ($lesson) {
                $q->where('category', 'like', '%"lesson_id":' . $lesson->id . '%');
            })
            ->where('serial_id', $serial->id)
            ->latest()
            ->get();
        
        // Calculate statistics
        $totalSubmissions = $submissions->count();
        $averageScore = $submissions->whereNotNull('point')->avg('point');
        $highestScore = $submissions->whereNotNull('point')->max('point');

        return view('guru.soal.show', compact('serial', 'category', 'categoryInfo', 'tema', 'lesson', 'submissions', 'totalSubmissions', 'averageScore', 'highestScore'));
    }

    private function getCategoryId($category)
    {
        return match($category) {
            'ulangan-harian' => 1,
            'pts' => 2,
            'pas' => 3,
            'tambahan' => 4,
            default => 1,
        };
    }

    public function create($serial, $category, $tema)
    {
        $serial = Serial::findOrFail($serial);
        $tema = Theme::findOrFail($tema);
        
        $categories = [
            'ulangan-harian' => ['name' => 'Ulangan Harian', 'color' => 'primary'],
            'pts' => ['name' => 'Penilaian Tengah Semester', 'color' => 'warning'],
            'pas' => ['name' => 'Penilaian Akhir Semester', 'color' => 'danger'],
            'tambahan' => ['name' => 'Soal Tambahan', 'color' => 'success'],
        ];
        
        $categoryInfo = $categories[$category] ?? ['name' => 'Soal', 'color' => 'info'];

        return view('guru.soal.create', compact('serial', 'category', 'categoryInfo', 'tema'));
    }

    public function store(Request $request, $serial, $category, $tema)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        Lesson::create([
            'mapel_id' => null,
            'name' => $request->name,
            'grade' => $tema,
            'semester' => $this->getCategoryId($category),
            'category' => Lesson::CATEGORY_SOAL,
        ]);

        return redirect()->route('guru.soal.list', [$serial, $category, $tema])
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    public function edit($serial, $category, $tema, $id)
    {
        $serial = Serial::findOrFail($serial);
        $tema = Theme::findOrFail($tema);
        $lesson = Lesson::findOrFail($id);
        
        $categories = [
            'ulangan-harian' => ['name' => 'Ulangan Harian', 'color' => 'primary'],
            'pts' => ['name' => 'Penilaian Tengah Semester', 'color' => 'warning'],
            'pas' => ['name' => 'Penilaian Akhir Semester', 'color' => 'danger'],
            'tambahan' => ['name' => 'Soal Tambahan', 'color' => 'success'],
        ];
        
        $categoryInfo = $categories[$category] ?? ['name' => 'Soal', 'color' => 'info'];

        return view('guru.soal.edit', compact('serial', 'category', 'categoryInfo', 'tema', 'lesson'));
    }

    public function update(Request $request, $serial, $category, $tema, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $lesson = Lesson::findOrFail($id);
        $lesson->update([
            'name' => $request->name,
        ]);

        return redirect()->route('guru.soal.list', [$serial, $category, $tema])
            ->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroy($serial, $category, $tema, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        return redirect()->route('guru.soal.list', [$serial, $category, $tema])
            ->with('success', 'Soal berhasil dihapus!');
    }

    public function grade(Request $request, $serial, $taskId)
    {
        $request->validate([
            'point' => 'required|numeric|min:0|max:100',
        ]);

        $task = \App\Models\Task::findOrFail($taskId);
        $task->update([
            'point' => $request->point,
        ]);

        return back()->with('success', 'Nilai berhasil disimpan!');
    }
}
