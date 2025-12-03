<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Serial;
use App\Models\Mapel;
use App\Models\Lesson;
use App\Models\Theme;
use App\Models\Subtheme;
use App\Models\Post;
use Illuminate\Support\Str;

class TugasController extends Controller
{
    public function index($serial)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get unique themes (mata pelajaran) as main menu
        $themes = Theme::select('id', 'name')
            ->distinct()
            ->get()
            ->unique('name');

        return view('guru.tugas.index', compact('serial', 'themes'));
    }

    public function subtema($serial, $tema)
    {
        $serial = Serial::findOrFail($serial);
        $tema = Theme::findOrFail($tema);
        
        // Get subthemes for this theme
        $subthemes = Subtheme::where('theme_id', $tema->id)->get();
        
        // Get tugas for each subtheme
        $tugasData = [];
        foreach ($subthemes as $subtheme) {
            $lessons = Lesson::where('category', Lesson::CATEGORY_TUGAS)
                ->where('grade', $subtheme->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            $tugasData[$subtheme->id] = $lessons;
        }

        return view('guru.tugas.subtema', compact('serial', 'tema', 'subthemes', 'tugasData'));
    }

    public function list($serial, $tema, $subtema)
    {
        $serial   = Serial::findOrFail($serial);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);

        // Get tugas (lessons with category=2) for this subtheme
        $lessons = Lesson::where('category', Lesson::CATEGORY_TUGAS)
            ->where('grade', $subtema->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guru.tugas.list', compact('serial', 'tema', 'subtema', 'lessons'));
    }

    public function show($serial, $tema, $subtema, $id)
    {
        $serial   = Serial::findOrFail($serial);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);
        $lesson   = Lesson::findOrFail($id);
        
        // Get items/questions for this tugas from posts table using category JSON
        $items = Post::where('serial_id', $serial->id)
            ->where('is_task', 1)
            ->orderBy('created_at', 'asc')
            ->get()
            ->filter(function($post) use ($lesson) {
                $category = json_decode($post->category, true);
                return isset($category['lesson_id']) && $category['lesson_id'] == $lesson->id;
            });

        return view('guru.tugas.show', compact('serial', 'tema', 'subtema', 'lesson', 'items'));
    }

    public function create($serial, $tema, $subtema)
    {
        $serial   = Serial::findOrFail($serial);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);

        return view('guru.tugas.create', compact('serial', 'tema', 'subtema'));
    }

    public function store(Request $request, $serial, $tema, $subtema)
    {
        $request->validate([
            'name' => 'required|max:255',
            'semester' => 'nullable|integer',
            'description' => 'nullable',
            'questions.*' => 'nullable|string',
            'link' => 'nullable|url',
        ]);

        $lesson = Lesson::create([
            'mapel_id' => null,
            'name' => $request->name,
            'grade' => $subtema,
            'semester' => $request->semester ?? 1,
            'category' => Lesson::CATEGORY_TUGAS,
        ]);
        
        // Save description and link as posts
        if ($request->description || $request->link) {
            $category = json_encode([
                'lesson_id' => $lesson->id,
                'type' => 'description',
            ]);
            
            Post::create([
                'serial_id' => $serial,
                'user_id' => auth()->id(),
                'mapel_id' => null,
                'title' => 'Deskripsi Tugas',
                'description' => $request->description,
                'slug' => Str::slug($request->name) . '-desc-' . time(),
                'link' => $request->link,
                'category' => $category,
                'is_task' => 1,
            ]);
        }
        
        // Save questions
        if ($request->questions) {
            foreach ($request->questions as $index => $question) {
                if (!empty($question)) {
                    $category = json_encode([
                        'lesson_id' => $lesson->id,
                        'type' => 'question',
                        'number' => $index + 1,
                    ]);
                    
                    Post::create([
                        'serial_id' => $serial,
                        'user_id' => auth()->id(),
                        'mapel_id' => null,
                        'title' => 'Soal ' . ($index + 1),
                        'description' => $question,
                        'slug' => Str::slug($request->name) . '-q' . ($index + 1) . '-' . time(),
                        'category' => $category,
                        'is_task' => 1,
                    ]);
                }
            }
        }

        return redirect()->route('guru.tugas.list', [$serial, $tema, $subtema])
            ->with('success', 'Tugas berhasil ditambahkan!');
    }

    public function edit($serial, $tema, $subtema, $id)
    {
        $serial   = Serial::findOrFail($serial);
        $tema     = Theme::findOrFail($tema);
        $subtema  = Subtheme::findOrFail($subtema);
        $lesson   = Lesson::findOrFail($id);

        return view('guru.tugas.edit', compact('serial', 'tema', 'subtema', 'lesson'));
    }

    public function update(Request $request, $serial, $tema, $subtema, $id)
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

        return redirect()->route('guru.tugas.list', [$serial, $tema, $subtema])
            ->with('success', 'Tugas berhasil diperbarui!');
    }

    public function destroy($serial, $tema, $subtema, $id)
    {
        $lesson = Lesson::findOrFail($id);
        $lesson->delete();

        return redirect()->route('guru.tugas.list', [$serial, $tema, $subtema])
            ->with('success', 'Tugas berhasil dihapus!');
    }
}