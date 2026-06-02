<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Serial;
use App\Models\Mapel;
use App\Models\Lesson;
use App\Models\LessonItem;
use App\Models\Theme;
use App\Models\Subtheme;
use App\Models\Post;
use App\Models\Exercise;
use App\Models\ExerciseItem;
use App\Models\ExerciseType;
use App\Models\ExerciseModel;
use App\Models\Classroom;
use App\Services\OpenAIService;

class SoalController extends Controller
{
    public function index($serial)
    {
        $serial = Serial::with('product')->findOrFail($serial);
        $lessonIds = json_decode($serial->product->lesson_id ?? '[]', true) ?? [];
        
        $lessons = Lesson::whereIn('id', $lessonIds)
            ->where('category', Lesson::CATEGORY_MATERI)
            ->with('mapel')
            ->orderBy('name')
            ->get();

        return view('guru.soal.index-lesson', compact('serial', 'lessons'));
    }
    
    public function categories($serial, $lesson)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        
        $categories = collect();
        $exerciseTypes = ExerciseType::orderBy('id')->get();
        
        // Dynamic icons & colors based on type ID
        $icons = ['bx-edit', 'bx-file', 'bx-book', 'bx-check-shield', 'bx-award', 'bx-bar-chart-alt-2'];
        $colors = ['primary', 'warning', 'danger', 'info', 'secondary', 'dark'];
        
        foreach ($exerciseTypes as $index => $type) {
            $categories->push([
                'id' => $type->id,
                'name' => $type->name,
                'icon' => $icons[$index % count($icons)],
                'color' => $colors[$index % count($colors)],
                'type_id' => $type->id,
            ]);
        }
        
        // Append Soal Tambahan explicitly as requested
        $categories->push([
            'id' => 'tambahan', 
            'name' => 'Soal Tambahan', 
            'icon' => 'bx-plus-circle', 
            'color' => 'success', 
            'type_id' => null
        ]);

        return view('guru.soal.index', compact('serial', 'lesson', 'categories'));
    }

    public function listByCategory($serial, $lesson, $category)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        
        // Handle Soal Tambahan explicitly
        if ($category === 'tambahan') {
            $categoryInfo = ['name' => 'Soal Tambahan', 'color' => 'success', 'type_id' => null];
            $exerciseTypeId = null;
            
            // Soal Tambahan: custom exercises from teacher (is_admin = 0)
            $exercises = Exercise::where('serial_id', $serial->id)
                ->where('lesson_id', $lesson->id)
                ->where('is_admin', 0)
                ->with(['lesson.mapel', 'exerciseItems', 'exerciseType'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // It's a dynamic Exercise Type ID
            $exerciseType = ExerciseType::findOrFail($category);
            
            // Dynamic color determination based on ID (to match index view)
            $colors = ['primary', 'warning', 'danger', 'info', 'secondary', 'dark'];
            $color = $colors[($exerciseType->id - 1) % count($colors)] ?? 'primary';
            
            $categoryInfo = [
                'name' => $exerciseType->name,
                'color' => $color,
                'type_id' => $exerciseType->id
            ];
            
            $exerciseTypeId = $exerciseType->id;
            
            // All exercises (Admin & Guru) for this type
            $query = Exercise::where('lesson_id', $lesson->id)
                ->where('serial_id', $serial->id);
            
            if ($exerciseTypeId) {
                $query->where('exercise_type_id', $exerciseTypeId);
            }
            
            $exercises = $query->with(['lesson.mapel', 'exerciseItems', 'exerciseType', 'sharedSerials'])
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        return view('guru.soal.list-direct', compact('serial', 'lesson', 'category', 'categoryInfo', 'exercises'));
    }

    public function createCustom($serial, $lesson)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $category = 'tambahan'; // Fixed category for custom exercises
        
        // Get exercise models (jenis soal)
        $exerciseModels = ExerciseModel::orderBy('name')->get();
        
        // Get ALL exercise types (not filtered)
        $exerciseTypes = ExerciseType::orderBy('name')->get();
        
        // Get all lessons (paket materi) untuk dipilih
        $lessons = Lesson::where('category', Lesson::CATEGORY_MATERI)->with('mapel')->orderBy('name')->get();
        
        // Get classrooms untuk dibagikan
        $classrooms = Classroom::where('serial_id', $serial->id)->get();
        
        $categoryInfo = ['name' => 'Soal Tambahan', 'color' => 'success'];

        return view('guru.soal.create-custom', compact('serial', 'lesson', 'category', 'categoryInfo', 'exerciseModels', 'exerciseTypes', 'lessons', 'classrooms'));
    }

    public function storeCustom(Request $request, $serial, $lesson)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $category = 'tambahan';
        
        $request->validate([
            'exercise_type_id' => 'required|exists:exercise_types,id',
            'question_type' => 'required|exists:exercise_models,id',
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|max:255',
            'time_limit' => 'required|integer|min:1|max:480',
            'questions' => 'required|array|min:1',
            'questions.*.question' => 'required',
            'questions.*.answer' => 'nullable',
            'questions.*.options' => 'nullable|array',
            'classrooms' => 'nullable|array',
        ]);

        // VALIDATE BUSINESS RULES: Check if selected model is allowed for this type
        $exerciseType = ExerciseType::findOrFail($request->exercise_type_id);
        $exerciseModel = ExerciseModel::findOrFail($request->question_type);
        $allowedModels = $this->getAllowedExerciseModelIds($request->exercise_type_id);
        
        if (!in_array($request->question_type, $allowedModels)) {
            return back()
                ->withInput()
                ->with('error', "Jenis Soal \"{$exerciseModel->name}\" tidak diizinkan untuk Tipe Soal \"{$exerciseType->name}\"");
        }

        // Get selected lesson
        $lesson = Lesson::findOrFail($request->lesson_id);
        
        // Determine question type from exercise_model_id
        // Models 1-2: Pilihan Ganda, Models 3-7: Essai
        $exerciseModelId = (int) $request->question_type;
        $questionType = in_array($exerciseModelId, [1, 2]) ? 'pilihan_ganda' : 'essai';
        
        // Create single exercise header for the whole package
        $exercise = Exercise::create([
            'lesson_id' => $lesson->id,
            'serial_id' => $serial->id,
            'exercise_type_id' => $request->exercise_type_id,
            'title' => $request->title,
            'time_limit' => $request->time_limit,
            'is_admin' => 0, // Custom dari guru
        ]);

        // Loop through all questions and create items
        $createdCount = 0;
        foreach ($request->questions as $index => $questionData) {
            $exerciseItemData = [
                'exercise_id' => $exercise->id,
                'exercise_type_id' => $request->exercise_type_id,
                'exercise_model_id' => $exerciseModelId,
                'exercise_choice' => 1, // Default choice
                'exercise_number' => $index + 1,
                'question' => $questionData['question'],
                'answer' => isset($questionData['answer']) 
                    ? (str_contains($questionData['answer'], ',') 
                        ? array_values(array_filter(array_map('trim', explode(',', $questionData['answer'])))) 
                        : [$questionData['answer']])
                    : null,
                'is_user' => 1, // Created by user (guru)
            ];

            // Add options if multiple choice
            if ($questionType === 'pilihan_ganda' && isset($questionData['options'])) {
                $options = array_filter($questionData['options']);
                $newOptions = [];
                $labels = ['A', 'B', 'C', 'D', 'E'];
                $i = 0;
                foreach ($options as $opt) {
                    if ($i < count($labels) && trim($opt) !== '') {
                        $newOptions[] = ['key' => $labels[$i], 'text' => $opt];
                    }
                    $i++;
                }
                $exerciseItemData['options'] = empty($newOptions) ? null : $newOptions;
            }

            ExerciseItem::create($exerciseItemData);
            
            $createdCount++;
        }

        return redirect()->route('guru.soal.list-direct', [$serial->id, $lesson->id, $category])
            ->with('success', 'Soal tambahan berhasil dibuat!');
    }

    public function editCustom($serial, $lesson, $id)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $category = 'tambahan';
        $exercise = Exercise::with('exerciseItems')->findOrFail($id);
        
        // Get all mapels
        $mapels = Mapel::all();
        // Get all lessons (Paket Materi)
        $lessons = Lesson::where('category', Lesson::CATEGORY_MATERI)->with('mapel')->orderBy('name')->get();
        $exerciseTypes = ExerciseType::all();
        $exerciseModels = ExerciseModel::orderBy('name')->get();
        $classrooms = Classroom::where('serial_id', $serial->id)->get();
        
        $categoryInfo = ['name' => 'Soal Tambahan', 'color' => 'success'];

        return view('guru.soal.edit-custom', compact('serial', 'lesson', 'category', 'categoryInfo', 'exercise', 'exerciseModels', 'exerciseTypes', 'lessons', 'classrooms'));
    }

    public function updateCustom(Request $request, $serial, $lesson, $id)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $exercise = Exercise::findOrFail($id);
        $category = 'tambahan';
        
        $request->validate([
            'lesson_id' => 'required|exists:lessons,id',
            'exercise_type_id' => 'required|exists:exercise_types,id',
            'title' => 'required|max:255',
            'time_limit' => 'required|integer|min:1|max:480',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:exercise_items,id',
            'items.*.question_type' => 'required|in:pilihan_ganda,essai,jawaban_singkat',
            'items.*.question' => 'required',
            'items.*.answer' => 'nullable',
            'items.*.selection' => 'nullable|array',
            'classrooms' => 'nullable|array',
        ]);

        // Update exercise header
        $exercise->update([
            'lesson_id' => $request->lesson_id,
            'exercise_type_id' => $request->exercise_type_id,
            'title' => $request->title,
            'time_limit' => $request->time_limit,
        ]);

        // Update each exercise item
        foreach ($request->items as $itemData) {
            $exerciseItem = ExerciseItem::findOrFail($itemData['id']);
            
            // Map question_type to exercise_model_id
            $exerciseModelId = 1; // Default: Pilihan Ganda
            if ($itemData['question_type'] === 'essai') {
                $exerciseModelId = 2;
            } elseif ($itemData['question_type'] === 'jawaban_singkat') {
                $exerciseModelId = 3;
            }

            $updateData = [
                'exercise_type_id' => $request->exercise_type_id,
                'exercise_model_id' => $exerciseModelId,
                'question' => $itemData['question'],
                'answer' => isset($itemData['answer']) 
                    ? (str_contains($itemData['answer'], ',') 
                        ? array_values(array_filter(array_map('trim', explode(',', $itemData['answer'])))) 
                        : [$itemData['answer']])
                    : null,
            ];

            // Add options if pilihan ganda
            if ($itemData['question_type'] === 'pilihan_ganda' && isset($itemData['selection'])) {
                $options = array_filter($itemData['selection']);
                $newOptions = [];
                $labels = ['A', 'B', 'C', 'D', 'E'];
                $i = 0;
                foreach ($options as $opt) {
                    if ($i < count($labels) && trim($opt) !== '') {
                        $newOptions[] = ['key' => $labels[$i], 'text' => $opt];
                    }
                    $i++;
                }
                $updateData['options'] = empty($newOptions) ? null : $newOptions;
            } else {
                $updateData['options'] = null;
            }

            $exerciseItem->update($updateData);
        }

        // Share to classrooms
        if ($request->classrooms && is_array($request->classrooms)) {
            $shareSerialIds = [];
            foreach ($request->classrooms as $classroomId) {
                $classroom = Classroom::find($classroomId);
                if ($classroom && $classroom->serial_id) {
                    $shareSerialIds[] = $classroom->serial_id;
                }
            }
            
            // Sync share_exercises
            $exercise->sharedSerials()->sync($shareSerialIds);
        } else {
            $exercise->sharedSerials()->sync([]);
        }

        return redirect()->route('guru.soal.list-direct', [$serial->id, $lesson->id, $category])
            ->with('success', 'Soal tambahan berhasil diupdate!');
    }

    public function destroyCustom($serial, $lesson, $id)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $exercise = Exercise::findOrFail($id);
        $category = 'tambahan';
        
        // Only allow delete if not admin exercise
        if ($exercise->is_admin == 1) {
            return back()->with('error', 'Tidak dapat menghapus soal dari admin!');
        }
        
        $exercise->delete();

        return redirect()->route('guru.soal.list-direct', [$serial->id, $lesson->id, 'tambahan'])
            ->with('success', 'Soal tambahan berhasil dihapus!');
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
        
        // Get exercise models (jenis soal)
        $exerciseModels = ExerciseModel::orderBy('name')->get();
        
        // Get all exercise types (tipe soal)
        $exerciseTypes = ExerciseType::orderBy('name')->get();
        
        $categories = [
            'ulangan-harian' => ['name' => 'Ulangan Harian', 'color' => 'primary', 'type_id' => 1],
            'pts' => ['name' => 'Penilaian Tengah Semester', 'color' => 'warning', 'type_id' => 2],
            'pas' => ['name' => 'Penilaian Akhir Semester', 'color' => 'danger', 'type_id' => 3],
            'tambahan' => ['name' => 'Soal Tambahan', 'color' => 'success', 'type_id' => null],
        ];
        
        $categoryInfo = $categories[$category] ?? ['name' => 'Soal', 'color' => 'info', 'type_id' => null];

        return view('guru.soal.create', compact('serial', 'category', 'categoryInfo', 'tema', 'exerciseModels', 'exerciseTypes'));
    }

    public function store(Request $request, $serial, $category, $tema)
    {
        $request->validate([
            'name' => 'required|max:255',
            'exercise_model_id' => 'required|exists:exercise_models,id',
            'exercise_type_id' => 'required|exists:exercise_types,id',
            'description' => 'nullable|string',
            'link' => 'nullable|url',
            'semester' => 'required|in:1,2',
            'questions' => 'nullable|array',
        ]);

        $serial = Serial::findOrFail($serial);
        
        // Find or create lesson for this tema/subthema
        $lesson = Lesson::firstOrCreate([
            'mapel_id' => null,
            'name' => $tema,
            'category' => Lesson::CATEGORY_SOAL,
        ], [
            'grade' => $tema,
            'semester' => $request->semester,
        ]);

        // Create exercise
        $exercise = Exercise::create([
            'lesson_id' => $lesson->id,
            'serial_id' => $serial->id,
            'exercise_type_id' => $request->exercise_type_id,
            'title' => $request->name,
            'description' => $request->description,
            'time_limit' => null,
            'is_admin' => 0,
        ]);

        // Create exercise items (individual questions) if provided
        if (!empty($request->questions)) {
            foreach ($request->questions as $index => $questionText) {
                if (!empty($questionText)) {
                    ExerciseItem::create([
                        'exercise_id' => $exercise->id,
                        'exercise_type_id' => $request->exercise_type_id,
                        'exercise_model_id' => $request->exercise_model_id,
                        'exercise_choice' => 1,
                        'exercise_number' => $index + 1,
                        'question' => $questionText,
                        'answer' => null,
                        'is_user' => 1,
                    ]);
                }
            }
        }

        return redirect()->route('guru.soal.list', [$serial->id, $category, $tema])
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

        $classrooms = \App\Models\Classroom::where('serial_id', $serial)->pluck('id')->toArray();

        if (empty($classrooms)) {
            return back()->with('error', 'Belum ada kelas yang dibuat!');
        }

        return back()->with('success', 'Soal berhasil diproses untuk serial ini!');
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
                $updated++;
            }
        }

        return back()->with('success', "$updated soal berhasil diproses untuk serial ini!");
    }

    // Share single exercise in a category to all classes
    public function shareSingleCategory(Request $request, $serial, $lesson, $category, $id)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $exercise = Exercise::findOrFail($id);

        $classrooms = $request->classrooms ?? [];
        
        // Only keep classrooms that are selected
        // We do this by inserting/updating for selected ones and we can remove unselected ones
        
        // Remove existing shares for this exercise in this serial
        DB::table('share_exercises')
            ->where('exercise_id', $exercise->id)
            ->whereIn('classroom_id', function($query) use ($serial) {
                $query->select('id')
                      ->from('classrooms')
                      ->where('serial_id', $serial->id);
            })
            ->delete();
            
        // Insert new shares
        foreach ($classrooms as $classroomId) {
            DB::table('share_exercises')->insert([
                'exercise_id' => $exercise->id,
                'classroom_id' => $classroomId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        
        return back()->with('success', 'Pembagian kuis berhasil diperbarui. Saat ini kuis dibagikan ke ' . count($classrooms) . ' kelas.');
    }

    // Bulk share by category
    public function bulkShareCategory(Request $request, $serial, $lesson, $category)
    {
        $serialModel = Serial::findOrFail($serial);
        $lessonModel = Lesson::findOrFail($lesson);
        $exerciseIds = json_decode($request->input('exercise_ids', '[]'), true);
        
        if (empty($exerciseIds)) {
            return back()->with('error', 'Tidak ada kuis yang dipilih!');
        }

        $classrooms = \App\Models\Classroom::where('serial_id', $serial)->pluck('id')->toArray();
        
        if (empty($classrooms)) {
            return back()->with('error', 'Belum ada kelas yang dibuat!');
        }

        $updated = 0;
        foreach ($exerciseIds as $exerciseId) {
            $exercise = Exercise::find($exerciseId);

            if ($exercise) {
                // Remove existing shares for this exercise in this serial
                DB::table('share_exercises')
                    ->where('exercise_id', $exercise->id)
                    ->whereIn('classroom_id', $classrooms)
                    ->delete();
                    
                // Insert new shares
                foreach ($classrooms as $classroomId) {
                    DB::table('share_exercises')->insert([
                        'exercise_id' => $exercise->id,
                        'classroom_id' => $classroomId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
                $updated++;
            }
        }

        return back()->with('success', "$updated kuis berhasil dibagikan ke semua kelas (" . count($classrooms) . " kelas)!");
    }

    /**
     * Show AI Question Generator form
     */
    public function aiGenerator($serial, $lesson)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::with('mapel')->findOrFail($lesson);
        $category = 'tambahan';
        
        // Get exercise models (jenis soal)
        $exerciseModels = ExerciseModel::orderBy('name')->get();
        
        // Get ALL exercise types (not filtered)
        $exerciseTypes = ExerciseType::orderBy('name')->get();
        
        // Get classrooms
        $classrooms = Classroom::where('serial_id', $serial->id)->get();

        // Get uploaded materials (custom materi) for dropdown
        $uploadedMaterials = Post::where('serial_id', $serial->id)
            ->where('category', 'like', '%"lesson_id":' . $lesson->id . '%')
            ->where('is_task', 0)
            ->where(function ($query) {
                $query->whereNotNull('description')
                    ->orWhereNotNull('attachment')
                    ->orWhereNotNull('link');
            })
            ->latest()
            ->get();

        // Get admin materials (lesson items) for this specific lesson
        $adminMaterials = LessonItem::where('lesson_id', $lesson->id)
            ->latest()
            ->get();

        $materials = $uploadedMaterials->map(function ($material) {
            $material->source_type = 'post';
            $material->source_label = 'Materi Guru';
            $material->display_name = $material->title;

            return $material;
        })->merge($adminMaterials->map(function ($material) {
            $material->source_type = 'lesson_item';
            $material->source_label = 'Materi Admin';
            $material->display_name = $material->title;

            return $material;
        }))->sortByDesc('created_at')->values();
        
        $categoryInfo = ['name' => 'Generate Soal dengan AI', 'color' => 'success'];

        return view('guru.soal.ai-generator', compact('serial', 'lesson', 'category', 'categoryInfo', 'exerciseModels', 'exerciseTypes', 'classrooms', 'materials'));
    }

    /**
     * Read uploaded material content for AI generation.
     */
    public function readUploadedMaterial($serial, $lesson, $materialId)
    {
        $serial = Serial::findOrFail($serial);

        $materialType = 'post';
        $materialKey = $materialId;

        if (str_contains((string) $materialId, ':')) {
            [$materialType, $materialKey] = array_pad(explode(':', (string) $materialId, 2), 2, null);
        }

        if ($materialType === 'lesson_item') {
            $material = LessonItem::where('lesson_id', $lesson)->findOrFail($materialKey);
        } else {
            $material = Post::where('serial_id', $serial->id)
                ->where('category', 'like', '%"lesson_id":' . $lesson . '%')
                ->where('is_task', 0)
                ->findOrFail($materialKey);
            $materialType = 'post';
        }

        $parts = [];
        $parts[] = 'Judul materi: ' . ($material->title ?? $material->name ?? '');

        if ($materialType === 'lesson_item') {
            $parts[] = 'Sumber materi: Materi Admin';

            if (!empty($material->embed)) {
                $parts[] = 'Konten materi:';
                $parts[] = trim(strip_tags($material->embed));
            }
        } else {
            $parts[] = 'Sumber materi: Materi Guru';
        }

        if ($materialType === 'post' && !empty($material->description)) {
            $parts[] = 'Deskripsi materi:';
            $parts[] = trim(strip_tags($material->description));
        }

        if ($materialType === 'post' && !empty($material->link)) {
            $parts[] = 'Referensi link: ' . $material->link;
        }

        if (!empty($material->file) || !empty($material->attachment)) {
            $attachmentPath = $material->file ?? $material->attachment;
            $parts[] = 'Nama file lampiran: ' . basename($attachmentPath);

            $attachmentText = $this->extractAttachmentText($attachmentPath);
            if (!empty($attachmentText)) {
                $parts[] = 'Cuplikan isi lampiran:';
                $parts[] = $attachmentText;
            }
        }

        return response()->json([
            'material_id' => $material->id,
            'source_type' => $materialType,
            'title' => $material->title ?? $material->name,
            'mapel_id' => $material->mapel_id,
            'mapel_name' => optional($material->mapel)->name,
            'illustration' => trim(implode("\n\n", $parts)),
        ]);
    }

    /**
     * Generate questions with AI
     */
    public function generateWithAI(Request $request, $serial, $lesson)
    {
        $request->validate([
            'illustration' => 'required|string|min:20',
            'exercise_model_id' => 'required|exists:exercise_models,id',
            'difficulty' => 'required|in:mudah,sedang,sulit',
            'count' => 'required|integer|min:1|max:10',
            'exercise_type_id' => 'required|exists:exercise_types,id',
            'time_limit' => 'required|integer|min:1|max:480',
        ]);

        try {
            // Get exercise type & model
            $exerciseType = ExerciseType::findOrFail($request->exercise_type_id);
            $exerciseModel = ExerciseModel::findOrFail($request->exercise_model_id);
            
            // VALIDATE BUSINESS RULES: Check if selected model is allowed for this type
            $allowedModels = $this->getAllowedExerciseModelIds($request->exercise_type_id);
            if (!in_array($request->exercise_model_id, $allowedModels)) {
                return back()
                    ->withInput()
                    ->with('error', "Jenis Soal \"{$exerciseModel->name}\" tidak diizinkan untuk Tipe Soal \"{$exerciseType->name}\"");
            }
            
            // Map exercise model to question type for AI generation
            $questionType = $this->mapExerciseModelToQuestionType($exerciseModel->id);
            
            $openAIService = new OpenAIService();
            
            $questions = $openAIService->generateQuestions(
                $request->illustration,
                $questionType,
                $request->difficulty,
                $request->count
            );

            // Store questions in session for preview
            session(['ai_generated_questions' => [
                'questions' => $questions,
                'exercise_model_id' => $request->exercise_model_id,
                'exercise_model_name' => $exerciseModel->name,
                'lesson_id' => $lesson,
                'exercise_type_id' => $request->exercise_type_id,
                'time_limit' => $request->time_limit,
                'question_type' => $questionType,
                'classrooms' => $request->classrooms,
            ]]);

            return redirect()->route('guru.soal.ai-preview', ['serial' => $serial, 'lesson' => $lesson]);

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal generate soal: ' . $e->getMessage());
        }
    }

    /**
     * Map exercise model ID to question type for AI generation
     */
    private function mapExerciseModelToQuestionType($exerciseModelId)
    {
        return match($exerciseModelId) {
            1, 2 => 'pilihan_ganda',  // Pilihan Ganda, Pilihan Ganda Banyak
            3, 4, 5, 6, 7 => 'essai',  // Pernyataan, Isian, Uraian, Iya Tidak, Argumen
            default => 'pilihan_ganda',
        };
    }
    /**
     * Preview AI generated questions
     */
    public function aiPreview($serial, $lesson)
    {
        $serialModel = Serial::findOrFail($serial);
        $lessonModel = Lesson::with('mapel')->findOrFail($lesson);
        $aiData = session('ai_generated_questions');
        
        if (!$aiData) {
            return redirect()->route('guru.soal.ai-generator', [$serialModel->id, $lessonModel->id])
                ->with('error', 'Tidak ada soal yang di-generate. Silakan generate soal terlebih dahulu.');
        }

        $exerciseTypes = ExerciseType::whereIn('kode', ['UH', 'SL'])->get();
        $classrooms = Classroom::where('serial_id', $serialModel->id)->get();
        
        $category = 'tambahan';
        $categoryInfo = ['name' => 'Preview Soal AI', 'color' => 'info'];

        return view('guru.soal.ai-preview', compact('serialModel', 'lessonModel', 'category', 'categoryInfo', 'aiData', 'exerciseTypes', 'classrooms'));
    }

    /**
     * Save AI generated questions
     */
    public function saveAIQuestions(Request $request, $serial, $lesson)
    {
        $serialModel = Serial::findOrFail($serial);
        $lessonModel = Lesson::findOrFail($lesson);
        
        $request->validate([
            'exercise_title' => 'required|string|max:255',
            'exercise_type_id' => 'required|exists:exercise_types,id',
            'exercise_model_id' => 'required|exists:exercise_models,id',
            'time_limit' => 'required|integer|min:1|max:480',
            'questions' => 'required|array|min:1',
            'questions.*.title' => 'required|max:255',
            'questions.*.question' => 'required',
            'questions.*.answer' => 'nullable',
            'questions.*.options' => 'nullable|array',
            'classrooms' => 'nullable|array',
        ]);

        // Get the exercise model
        $exerciseModel = ExerciseModel::findOrFail($request->exercise_model_id);
        $exerciseType = ExerciseType::findOrFail($request->exercise_type_id);

        // VALIDATE BUSINESS RULES: Check if selected model is allowed for this type
        $allowedModels = $this->getAllowedExerciseModelIds($request->exercise_type_id);
        if (!in_array((int) $request->exercise_model_id, array_map('intval', $allowedModels), true)) {
            return back()
                ->withInput()
                ->with('error', "Jenis Soal \"{$exerciseModel->name}\" tidak diizinkan untuk Tipe Soal \"{$exerciseType->name}\"");
        }

        // Collect valid questions
        $validQuestions = [];
        foreach ($request->questions as $questionData) {
            if (!(isset($questionData['deleted']) && $questionData['deleted'] == 'true')) {
                $validQuestions[] = $questionData;
            }
        }

        if (empty($validQuestions)) {
            return redirect()->route('guru.soal.list-direct', [$serialModel->id, $lessonModel->id, 'tambahan'])
                ->with('error', 'Tidak ada soal yang tersimpan!');
        }

        // Use transaction for consistency
        DB::beginTransaction();
        try {
            // Create ONE exercise for all AI-generated questions
            $exercise = Exercise::create([
                'lesson_id' => $lessonModel->id,
                'serial_id' => $serialModel->id,
                'exercise_type_id' => $request->exercise_type_id,
                'title' => $request->exercise_title,
                'time_limit' => $request->time_limit,
                'description' => null,
                'is_admin' => 0,
            ]);

            // Create exercise items (questions) for this single exercise
            foreach ($validQuestions as $index => $questionData) {
                $itemData = [
                    'exercise_id' => $exercise->id,
                    'exercise_type_id' => $request->exercise_type_id,
                    'exercise_model_id' => $request->exercise_model_id,
                    'exercise_choice' => 1,
                    'exercise_number' => $index + 1,
                    'question' => $questionData['question'],
                    'answer' => isset($questionData['answer']) 
                        ? (str_contains($questionData['answer'], ',') 
                            ? array_values(array_filter(array_map('trim', explode(',', $questionData['answer'])))) 
                            : [$questionData['answer']])
                        : null,
                    'is_user' => 1,
                ];

                // Add options if multiple choice (Model 1 or 2)
                if (in_array($exerciseModel->id, [1, 2]) && isset($questionData['options'])) {
                    $newOptions = [];
                    $labels = ['A', 'B', 'C', 'D', 'E'];
                    $i = 0;
                    foreach ($questionData['options'] as $opt) {
                        if ($i < count($labels) && trim($opt) !== '') {
                            $newOptions[] = ['key' => $labels[$i], 'text' => $opt];
                        }
                        $i++;
                    }
                    $itemData['options'] = empty($newOptions) ? null : $newOptions;
                }

                ExerciseItem::create($itemData);
            }

            DB::commit();

            // Clear session data
            session()->forget('ai_generated_questions');

            return redirect()->route('guru.soal.list-direct', [$serialModel->id, $lessonModel->id, 'tambahan'])
                ->with('success', "Berhasil menyimpan " . count($validQuestions) . " soal dengan jenis '" . $exerciseModel->name . "'!");
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan soal: ' . $e->getMessage());
        }
    }

    /**
     * View single exercise with questions (read-only)
     */
    public function viewExercise($serial, $lesson, $exerciseId)
    {
        $serial = Serial::findOrFail($serial);
        $lesson = Lesson::findOrFail($lesson);
        $exercise = Exercise::findOrFail($exerciseId);

        $exercise->load(['lesson.mapel', 'exerciseItems', 'exerciseType']);

        return view('guru.soal.view-exercise', compact('serial', 'lesson', 'exercise'));
    }

    /**
     * Extract plain text from supported attachment formats.
     */
    private function getAllowedExerciseModelIds($exerciseTypeId): array
    {
        $exerciseType = ExerciseType::find($exerciseTypeId);
        if (!$exerciseType) {
            return ExerciseModel::pluck('id')->all();
        }

        // AKM: all models allowed; others: only "Pilihan Ganda".
        if (strtoupper((string) $exerciseType->kode) === 'AKM') {
            return ExerciseModel::pluck('id')->all();
        }

        $pilihanGandaId = ExerciseModel::whereRaw('LOWER(name) = ?', ['pilihan ganda'])->value('id');
        return $pilihanGandaId ? [(int) $pilihanGandaId] : [];
    }

    private function extractAttachmentText(?string $attachmentPath): ?string
    {
        if (empty($attachmentPath) || !Storage::disk('public')->exists($attachmentPath)) {
            return null;
        }

        $extension = strtolower(pathinfo($attachmentPath, PATHINFO_EXTENSION));
        $supportedTextExtensions = ['txt', 'md', 'csv', 'json', 'html', 'htm', 'xml'];

        if (!in_array($extension, $supportedTextExtensions, true)) {
            return null;
        }

        $raw = Storage::disk('public')->get($attachmentPath);
        $clean = trim(strip_tags($raw));

        if ($clean === '') {
            return null;
        }

        // Keep prompt size reasonable.
        return mb_substr($clean, 0, 2000);
    }
}

