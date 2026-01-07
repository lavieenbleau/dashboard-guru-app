<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Serial;
use App\Models\Mapel;
use App\Models\Lesson;
use App\Models\Theme;
use App\Models\Subtheme;
use App\Models\Exercise;
use App\Models\ExerciseItem;
use App\Models\ExerciseType;
use App\Models\Classroom;

class SoalController extends Controller
{
    public function index($serial)
    {
        $serial = Serial::findOrFail($serial);
        
        // Define soal categories (all from admin, can be shared)
        $categories = [
            ['id' => 'ulangan-harian', 'name' => 'Ulangan Harian', 'icon' => 'bx-edit', 'color' => 'primary', 'type_id' => 1],
            ['id' => 'pts', 'name' => 'Penilaian Tengah Semester', 'icon' => 'bx-file', 'color' => 'warning', 'type_id' => 2],
            ['id' => 'pas', 'name' => 'Penilaian Akhir Semester', 'icon' => 'bx-book', 'color' => 'danger', 'type_id' => 3],
            ['id' => 'tambahan', 'name' => 'Soal Tambahan', 'icon' => 'bx-plus-circle', 'color' => 'success', 'type_id' => null],
        ];

        return view('guru.soal.index', compact('serial', 'categories'));
    }

    public function listByCategory($serial, $category)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get category info
        $categoryMap = [
            'ulangan-harian' => ['name' => 'Ulangan Harian', 'color' => 'primary', 'type_id' => 1],
            'pts' => ['name' => 'Penilaian Tengah Semester', 'color' => 'warning', 'type_id' => 2],
            'pas' => ['name' => 'Penilaian Akhir Semester', 'color' => 'danger', 'type_id' => 3],
            'tambahan' => ['name' => 'Soal Tambahan', 'color' => 'success', 'type_id' => null],
        ];
        
        $categoryInfo = $categoryMap[$category] ?? ['name' => 'Soal', 'color' => 'info', 'type_id' => null];
        $exerciseTypeId = $categoryInfo['type_id'];
        
        // Get exercises based on category
        if ($category === 'tambahan') {
            // Soal Tambahan: custom exercises from teacher (is_admin = 0)
            $exercises = Exercise::where('serial_id', $serial->id)
                ->where('is_admin', 0)
                ->with(['lesson.mapel', 'exerciseItems', 'exerciseType'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Admin exercises (UH, PTS, PAS) - is_admin = 1 and serial_id is null
            $query = Exercise::where('is_admin', 1)
                ->whereNull('serial_id'); // Soal admin tidak punya serial_id
            
            if ($exerciseTypeId) {
                $query->where('exercise_type_id', $exerciseTypeId);
            }
            
            $exercises = $query->with(['lesson.mapel', 'exerciseItems', 'exerciseType', 'classrooms'])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('guru.soal.list-direct', compact('serial', 'category', 'categoryInfo', 'exercises'));
    }

    public function createCustom($serial)
    {
        $serial = Serial::findOrFail($serial);
        $category = 'tambahan'; // Fixed category for custom exercises
        
        // Get all mapels (mata pelajaran) untuk dipilih
        $mapels = Mapel::orderBy('name')->get();
        
        // Get exercise types
        $exerciseTypes = ExerciseType::all();
        
        // Get classrooms untuk dibagikan
        $classrooms = Classroom::where('serial_id', $serial->id)->get();
        
        $categoryInfo = ['name' => 'Soal Tambahan', 'color' => 'success'];

        return view('guru.soal.create-custom', compact('serial', 'category', 'categoryInfo', 'mapels', 'exerciseTypes', 'classrooms'));
    }

    public function storeCustom(Request $request, $serial)
    {
        $serial = Serial::findOrFail($serial);
        $category = 'tambahan';
        
        $request->validate([
            'exercise_type_id' => 'required|exists:exercise_types,id',
            'question_type' => 'required|in:pilihan_ganda,essai,jawaban_singkat',
            'mapel_id' => 'required|exists:mapels,id',
            'questions' => 'required|array|min:1',
            'questions.*.title' => 'required|max:255',
            'questions.*.question' => 'required',
            'questions.*.answer' => 'nullable',
            'questions.*.options' => 'nullable|array',
            'classrooms' => 'nullable|array',
        ]);

        // Find or create base lesson for this mapel
        $lesson = Lesson::firstOrCreate([
            'mapel_id' => $request->mapel_id,
            'category' => Lesson::CATEGORY_SOAL,
            'name' => 'Base Lesson',
        ], [
            'grade' => '1',
            'semester' => 1,
        ]);
        
        // Prepare shared_to_classes
        $sharedToClasses = null;
        if ($request->classrooms) {
            $sharedToClasses = json_encode($request->classrooms);
        }

        // Loop through all questions and create exercises
        $createdCount = 0;
        foreach ($request->questions as $index => $questionData) {
            // Create exercise header
            $exercise = Exercise::create([
                'lesson_id' => $lesson->id,
                'serial_id' => $serial->id,
                'exercise_type_id' => $request->exercise_type_id,
                'title' => $questionData['title'],
                'description' => null,
                'is_admin' => 0, // Custom dari guru
                'shared_to_classes' => $sharedToClasses,
            ]);

            // Create exercise item with question details
            // Map question_type to exercise_model_id
            $exerciseModelId = 1; // Default: Pilihan Ganda
            if ($request->question_type === 'essai') {
                $exerciseModelId = 2;
            } elseif ($request->question_type === 'jawaban_singkat') {
                $exerciseModelId = 3;
            }

            $exerciseItemData = [
                'exercise_id' => $exercise->id,
                'exercise_type_id' => $request->exercise_type_id,
                'exercise_model_id' => $exerciseModelId,
                'exercise_choice' => 1, // Default choice
                'exercise_number' => $index + 1,
                'question' => $questionData['question'],
                'answer' => $questionData['answer'] ?? null,
                'is_user' => 1, // Created by user (guru)
            ];

            // Add options if multiple choice
            if ($request->question_type === 'pilihan_ganda' && isset($questionData['options'])) {
                $options = array_filter($questionData['options']);
                $exerciseItemData['selection'] = json_encode([
                    'A' => $options[0] ?? null,
                    'B' => $options[1] ?? null,
                    'C' => $options[2] ?? null,
                    'D' => $options[3] ?? null,
                    'E' => $options[4] ?? null,
                ]);
            }

            ExerciseItem::create($exerciseItemData);
            
            $createdCount++;
        }

        return redirect()->route('guru.soal.list-direct', [$serial->id, $category])
            ->with('success', "Berhasil menambahkan {$createdCount} soal!");
    }

    public function editCustom($serial, $id)
    {
        $serial = Serial::findOrFail($serial);
        $category = 'tambahan';
        $exercise = Exercise::with('exerciseItems')->findOrFail($id);
        
        // Get all mapels
        $mapels = Mapel::all();
        $exerciseTypes = ExerciseType::all();
        $classrooms = Classroom::where('serial_id', $serial->id)->get();
        
        $categoryInfo = ['name' => 'Soal Tambahan', 'color' => 'success'];

        return view('guru.soal.edit-custom', compact('serial', 'category', 'categoryInfo', 'exercise', 'mapels', 'exerciseTypes', 'classrooms'));
    }

    public function updateCustom(Request $request, $serial, $id)
    {
        $exercise = Exercise::findOrFail($id);
        $serial = Serial::findOrFail($serial);
        $category = 'tambahan';
        
        $request->validate([
            'mapel_id' => 'required|exists:mapels,id',
            'exercise_type_id' => 'required|exists:exercise_types,id',
            'question_type' => 'required|in:pilihan_ganda,essai,jawaban_singkat',
            'title' => 'required|max:255',
            'question' => 'required',
            'answer' => 'nullable',
            'options' => 'nullable|array',
            'classrooms' => 'nullable|array',
        ]);

        // Update or create lesson
        $lesson = Lesson::firstOrCreate([
            'mapel_id' => $request->mapel_id,
            'category' => Lesson::CATEGORY_SOAL,
            'name' => 'Base Lesson',
        ], [
            'grade' => '1',
            'semester' => 1,
        ]);

        // Update exercise header
        $exercise->update([
            'lesson_id' => $lesson->id,
            'exercise_type_id' => $request->exercise_type_id,
            'title' => $request->title,
            'shared_to_classes' => $request->classrooms ? json_encode($request->classrooms) : null,
        ]);

        // Update or create exercise item
        $exerciseItem = $exercise->exerciseItems()->first();
        
        $exerciseItemData = [
            'question' => $request->question,
            'question_type' => $request->question_type,
            'correct_answer' => $request->answer,
        ];

        // Add options if multiple choice
        if ($request->question_type === 'pilihan_ganda' && $request->options) {
            $options = array_filter($request->options);
            $exerciseItemData['option_a'] = $options[0] ?? null;
            $exerciseItemData['option_b'] = $options[1] ?? null;
            $exerciseItemData['option_c'] = $options[2] ?? null;
            $exerciseItemData['option_d'] = $options[3] ?? null;
            $exerciseItemData['option_e'] = $options[4] ?? null;
        }

        if ($exerciseItem) {
            $exerciseItem->update($exerciseItemData);
        } else {
            $exerciseItemData['exercise_id'] = $exercise->id;
            $exerciseItemData['number'] = 1;
            ExerciseItem::create($exerciseItemData);
        }

        return redirect()->route('guru.soal.list-direct', [$serial->id, $category])
            ->with('success', 'Soal berhasil diupdate!');
    }

    public function destroyCustom($serial, $id)
    {
        $exercise = Exercise::findOrFail($id);
        $category = 'tambahan';
        
        // Only allow delete if not admin exercise
        if ($exercise->is_admin == 1) {
            return back()->with('error', 'Tidak dapat menghapus soal dari admin!');
        }
        
        $exercise->delete();

        return redirect()->route('guru.soal.list-direct', [$serial, $category])
            ->with('success', 'Soal berhasil dihapus!');
    }

    public function categorySelect($serial, $category)
    {
        // Redirect directly to list (no admin/custom selection needed)
        return redirect()->route('guru.soal.list-direct', [$serial, $category]);
    }

    public function category($serial, $type)
    {
        $serial = Serial::findOrFail($serial);
        
        // Get exercise types (UH, PTS, PAS)
        $exerciseTypes = ExerciseType::all();
        
        return view('guru.soal.category', compact('serial', 'type', 'exerciseTypes'));
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

    // NEW: List exercises by type with admin/custom filter
    public function listByType($serial, $type, $exerciseTypeId)
    {
        $serial = Serial::findOrFail($serial);
        $exerciseType = ExerciseType::findOrFail($exerciseTypeId);
        
        // Get exercises filtered by is_admin
        $exercises = Exercise::where('serial_id', $serial->id)
            ->where('exercise_type_id', $exerciseTypeId)
            ->where('is_admin', $type === 'admin' ? 1 : 0)
            ->with('lesson')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('guru.soal.list-by-type', compact('serial', 'type', 'exerciseType', 'exercises'));
    }

    // NEW: Share single exercise to all classes
    public function shareSingle($serial, $type, $exerciseTypeId, $id)
    {
        $exercise = Exercise::findOrFail($id);
        
        // Verify it's an admin exercise
        if ($exercise->is_admin != 1) {
            return back()->with('error', 'Hanya soal admin yang bisa di-share!');
        }

        // Toggle share status
        if ($exercise->shared_to_classes) {
            $exercise->shared_to_classes = null;
            $exercise->save();
            return back()->with('success', 'Soal dibatalkan dari semua kelas!');
        } else {
            $classrooms = \App\Models\Classroom::where('serial_id', $serial)->pluck('id')->toArray();
            
            if (empty($classrooms)) {
                return back()->with('error', 'Belum ada kelas yang dibuat!');
            }

            $exercise->shared_to_classes = json_encode($classrooms);
            $exercise->save();
            
            return back()->with('success', 'Soal berhasil di-share ke semua kelas (' . count($classrooms) . ' kelas)!');
        }
    }

    // NEW: Bulk share exercises to all classes
    public function bulkShare(Request $request, $serial, $type, $exerciseTypeId)
    {
        $exerciseIds = json_decode($request->input('exercise_ids', '[]'), true);
        
        if (empty($exerciseIds)) {
            return back()->with('error', 'Tidak ada soal yang dipilih!');
        }

        $classrooms = \App\Models\Classroom::where('serial_id', $serial)->pluck('id')->toArray();
        
        if (empty($classrooms)) {
            return back()->with('error', 'Belum ada kelas yang dibuat!');
        }

        $updated = 0;
        foreach ($exerciseIds as $exerciseId) {
            $exercise = Exercise::find($exerciseId);
            
            if ($exercise && $exercise->is_admin == 1) {
                $exercise->shared_to_classes = json_encode($classrooms);
                $exercise->save();
                $updated++;
            }
        }

        return back()->with('success', "$updated soal berhasil di-share ke semua kelas (" . count($classrooms) . " kelas)!");
    }

    // Share single exercise by category
    public function shareSingleCategory(Request $request, $serial, $category, $id)
    {
        $exercise = Exercise::findOrFail($id);
        
        if ($exercise->is_admin != 1) {
            return back()->with('error', 'Hanya soal admin yang bisa di-share!');
        }

        // Sync classrooms (remove old, add new)
        $classrooms = $request->classrooms ?? [];
        $exercise->classrooms()->sync($classrooms);
        
        return back()->with('success', 'Soal berhasil dibagikan ke ' . count($classrooms) . ' kelas!');
    }

    // Bulk share by category
    public function bulkShareCategory(Request $request, $serial, $category)
    {
        $exerciseIds = json_decode($request->input('exercise_ids', '[]'), true);
        
        if (empty($exerciseIds)) {
            return back()->with('error', 'Tidak ada soal yang dipilih!');
        }

        $classrooms = \App\Models\Classroom::where('serial_id', $serial)->pluck('id')->toArray();
        
        if (empty($classrooms)) {
            return back()->with('error', 'Belum ada kelas yang dibuat!');
        }

        $updated = 0;
        foreach ($exerciseIds as $exerciseId) {
            $exercise = Exercise::find($exerciseId);
            
            if ($exercise && $exercise->is_admin == 1) {
                $exercise->shared_to_classes = json_encode($classrooms);
                $exercise->save();
                $updated++;
            }
        }

        return back()->with('success', "$updated soal berhasil di-share ke semua kelas (" . count($classrooms) . " kelas)!");
    }
}
