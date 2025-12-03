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
        
        // Get unique mapels from lessons
        $mapelIds = Lesson::select('mapel_id')
            ->distinct()
            ->pluck('mapel_id');
        
        $mapels = Mapel::whereIn('id', $mapelIds)->get();

        return view('guru.soal.index', compact('serial', 'mapels'));
    }

    public function tema($serial, $mapel)
    {
        $serial = Serial::findOrFail($serial);
        $mapel  = Mapel::findOrFail($mapel);
        $themes = Theme::where('lesson_id', $mapel->id)->get();

        return view('guru.soal.tema', compact('serial', 'mapel', 'themes'));
    }

    public function subtema($serial, $mapel, $tema)
    {
        $serial = Serial::findOrFail($serial);
        $mapel  = Mapel::findOrFail($mapel);
        $tema   = Theme::findOrFail($tema);
        $subthemes = Subtheme::where('theme_id', $tema->id)->get();

        return view('guru.soal.subtema', compact('serial', 'mapel', 'tema', 'subthemes'));
    }

    public function list($serial, $mapel, $tema, $subtema)
    {
        $serial   = Serial::findOrFail($serial);
        $mapel    = Mapel::findOrFail($mapel);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);

        // Get soal (lessons with category=3) for this subtheme
        $lessons = Lesson::where('mapel_id', $mapel->id)
            ->where('category', Lesson::CATEGORY_SOAL)
            ->where('grade', $subtema->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru.soal.list', compact('serial', 'mapel', 'tema', 'subtema', 'lessons'));
    }

    public function create($serial, $mapel, $tema, $subtema)
    {
        $serial   = Serial::findOrFail($serial);
        $mapel    = Mapel::findOrFail($mapel);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);

        return view('guru.soal.create', compact('serial', 'mapel', 'tema', 'subtema'));
    }

    public function store(Request $request, $serial, $mapel, $tema, $subtema)
    {
        $request->validate([
            'name' => 'required|max:255',
            'semester' => 'nullable|integer',
        ]);

        Lesson::create([
            'mapel_id' => $mapel,
            'name' => $request->name,
            'grade' => $subtema,
            'semester' => $request->semester ?? 1,
            'category' => Lesson::CATEGORY_SOAL,
        ]);

        return redirect()->route('guru.soal.list', [$serial, $mapel, $tema, $subtema])
            ->with('success', 'Soal berhasil ditambahkan!');
    }

    public function edit($serial, $mapel, $tema, $subtema, $id)
    {
        $serial   = Serial::findOrFail($serial);
        $mapel    = Mapel::findOrFail($mapel);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);
        $lesson   = Lesson::findOrFail($id);

        return view('guru.soal.edit', compact('serial', 'mapel', 'tema', 'subtema', 'lesson'));
    }

    public function update(Request $request, $serial, $mapel, $tema, $subtema, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
            'semester' => 'nullable|integer',
        ]);

        $lesson = Lesson::findOrFail($id);
        $lesson->update([
            'name' => $request->name,
            'semester' => $request->semester ?? 1,
        ]);

        return redirect()->route('guru.soal.list', [$serial, $mapel, $tema, $subtema])
            ->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroy($serial, $mapel, $tema, $subtema, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        return redirect()->route('guru.soal.list', [$serial, $mapel, $tema, $subtema])
            ->with('success', 'Soal berhasil dihapus!');
    }
}
