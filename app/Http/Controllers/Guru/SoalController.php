<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\Serial;
use App\Models\Mapel;
use App\Models\Lesson;
use App\Models\Theme;
use App\Models\Subtheme;
use App\Models\Post;
use App\Models\Exercise;
use App\Models\ExerciseItem;
use App\Models\ExerciseType;
use App\Models\Classroom;
use App\Services\OpenAIService;

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
            
            $exercises = $query->with(['lesson.mapel', 'exerciseItems', 'exerciseType', 'sharedSerials'])
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
        
        // Get exercise types - only Ulangan Harian and Soal Latihan
        $exerciseTypes = ExerciseType::whereIn('kode', ['UH', 'SL'])->get();
        
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
            'time_limit' => 'required|integer|min:1|max:480',
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
        
        // Loop through all questions and create exercises
        $createdCount = 0;
        foreach ($request->questions as $index => $questionData) {
            // Create exercise header
            $exercise = Exercise::create([
                'lesson_id' => $lesson->id,
                'serial_id' => $serial->id,
                'exercise_type_id' => $request->exercise_type_id,
                'title' => $questionData['title'],
                'time_limit' => $request->time_limit,
                'is_admin' => 0, // Custom dari guru
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
                'answer' => ($request->question_type === 'pilihan_ganda') 
                    ? json_encode([$questionData['answer'] ?? null])
                    : ($questionData['answer'] ?? null),
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
                'answer' => ($itemData['question_type'] === 'pilihan_ganda')
                    ? json_encode([$itemData['answer'] ?? null])
                    : ($itemData['answer'] ?? null),
            ];

            // Add selection if pilihan ganda
            if ($itemData['question_type'] === 'pilihan_ganda' && isset($itemData['selection'])) {
                $updateData['selection'] = json_encode($itemData['selection']);
            } else {
                $updateData['selection'] = null;
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

        return redirect()->route('guru.soal.list-direct', [$serial->id, $category])
            ->with('success', 'Semua soal berhasil diupdate!');
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

    // Share single exercise by category
    public function shareSingleCategory(Request $request, $serial, $category, $id)
    {
        $exercise = Exercise::findOrFail($id);
        
        if ($exercise->is_admin != 1) {
            return back()->with('error', 'Hanya soal admin yang bisa di-share!');
        }

        $classrooms = $request->classrooms ?? [];

        if (empty($classrooms)) {
            return back()->with('error', 'Belum ada kelas yang dibuat!');
        }

        return back()->with('success', 'Soal berhasil diproses untuk serial ini!');
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
                $updated++;
            }
        }

        return back()->with('success', "$updated soal berhasil diproses untuk serial ini!");
    }

    /**
     * Show AI Question Generator form
     */
    public function aiGenerator($serial)
    {
        $serial = Serial::findOrFail($serial);
        $category = 'tambahan';
        
        // Get all mapels (mata pelajaran)
        $mapels = Mapel::orderBy('name')->get();
        
        // Get exercise types
        $exerciseTypes = ExerciseType::whereIn('kode', ['UH', 'SL'])->get();
        
        // Get classrooms
        $classrooms = Classroom::where('serial_id', $serial->id)->get();

        // Get uploaded materials (custom materi) for dropdown
        $uploadedMaterials = Post::where('serial_id', $serial->id)
            ->where('is_task', 0)
            ->where(function ($query) {
                $query->whereNotNull('description')
                    ->orWhereNotNull('attachment')
                    ->orWhereNotNull('link');
            })
            ->with('mapel')
            ->latest()
            ->get();

        // Get admin materials (lessons) so they can also be used as AI source
        $adminMaterials = Lesson::where('category', Lesson::CATEGORY_MATERI)
            ->with('mapel')
            ->latest()
            ->get();

        $materials = $uploadedMaterials->map(function ($material) {
            $material->source_type = 'post';
            $material->source_label = 'Materi Guru';

            return $material;
        })->merge($adminMaterials->map(function ($material) {
            $material->source_type = 'lesson';
            $material->source_label = 'Materi Admin';

            return $material;
        }))->sortByDesc('created_at')->values();
        
        $categoryInfo = ['name' => 'Generate Soal dengan AI', 'color' => 'success'];

        return view('guru.soal.ai-generator', compact('serial', 'category', 'categoryInfo', 'mapels', 'exerciseTypes', 'classrooms', 'materials'));
    }

    /**
     * Read uploaded material content for AI generation.
     */
    public function readUploadedMaterial($serial, $materialId)
    {
        $serial = Serial::findOrFail($serial);

        $materialType = 'post';
        $materialKey = $materialId;

        if (str_contains((string) $materialId, ':')) {
            [$materialType, $materialKey] = array_pad(explode(':', (string) $materialId, 2), 2, null);
        }

        if ($materialType === 'lesson') {
            $material = Lesson::where('category', Lesson::CATEGORY_MATERI)
                ->with('mapel')
                ->findOrFail($materialKey);
        } else {
            $material = Post::where('serial_id', $serial->id)
                ->where('is_task', 0)
                ->with('mapel')
                ->findOrFail($materialKey);
            $materialType = 'post';
        }

        $parts = [];
        $parts[] = 'Judul materi: ' . ($material->title ?? $material->name);

        if ($materialType === 'lesson') {
            $parts[] = 'Sumber materi: Materi Admin';

            if (!empty($material->grade) || !empty($material->semester)) {
                $grade = $material->grade ?? '-';
                $semester = $material->semester ?? '-';
                $parts[] = 'Kelas / Semester: ' . $grade . ' / ' . $semester;
            }
        } else {
            $parts[] = 'Sumber materi: Materi Guru';
        }

        if (!empty($material->description)) {
            $parts[] = 'Deskripsi materi:';
            $parts[] = trim($material->description);
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
     * Generate questions using OpenAI API
     */
    public function generateWithAI(Request $request, $serial)
    {
        $request->validate([
            'illustration' => 'required|string|min:20',
            'question_type' => 'required|in:pilihan_ganda,essai',
            'difficulty' => 'required|in:mudah,sedang,sulit',
            'count' => 'required|integer|min:1|max:10',
        ]);

        try {
            $openAIService = new OpenAIService();
            
            $questions = $openAIService->generateQuestions(
                $request->illustration,
                $request->question_type,
                $request->difficulty,
                $request->count
            );

            // Store questions in session for preview
            session(['ai_generated_questions' => [
                'questions' => $questions,
                'question_type' => $request->question_type,
                'mapel_id' => $request->mapel_id,
                'exercise_type_id' => $request->exercise_type_id,
                'classrooms' => $request->classrooms,
            ]]);

            return redirect()->route('guru.soal.ai-preview', $serial)
                ->with('success', 'Berhasil menghasilkan ' . count($questions) . ' soal!');

        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Gagal generate soal: ' . $e->getMessage());
        }
    }

    /**
     * Preview AI-generated questions before saving
     */
    public function aiPreview($serial)
    {
        $serial = Serial::findOrFail($serial);
        
        $aiData = session('ai_generated_questions');
        
        if (!$aiData) {
            return redirect()->route('guru.soal.ai-generator', $serial->id)
                ->with('error', 'Tidak ada soal yang di-generate. Silakan generate soal terlebih dahulu.');
        }

        $mapels = Mapel::all();
        $exerciseTypes = ExerciseType::whereIn('kode', ['UH', 'SL'])->get();
        $classrooms = Classroom::where('serial_id', $serial->id)->get();
        
        $categoryInfo = ['name' => 'Preview Soal AI', 'color' => 'info'];

        return view('guru.soal.ai-preview', compact('serial', 'aiData', 'mapels', 'exerciseTypes', 'classrooms', 'categoryInfo'));
    }

    /**
     * Save AI-generated questions to database
     */
    public function saveAIQuestions(Request $request, $serial)
    {
        $serial = Serial::findOrFail($serial);
        
        $request->validate([
            'exercise_title' => 'required|string|max:255',
            'exercise_type_id' => 'required|exists:exercise_types,id',
            'mapel_id' => 'required|exists:mapels,id',
            'time_limit' => 'required|integer|min:1|max:480',
            'questions' => 'required|array|min:1',
            'questions.*.title' => 'required|max:255',
            'questions.*.question' => 'required',
            'questions.*.answer' => 'nullable',
            'questions.*.options' => 'nullable|array',
            'question_type' => 'required|in:pilihan_ganda,essai',
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
        
        // Collect valid questions
        $validQuestions = [];
        foreach ($request->questions as $questionData) {
            if (!(isset($questionData['deleted']) && $questionData['deleted'] == 'true')) {
                $validQuestions[] = $questionData;
            }
        }

        if (empty($validQuestions)) {
            return redirect()->route('guru.soal.list-direct', [$serial->id, 'tambahan'])
                ->with('error', 'Tidak ada soal yang tersimpan!');
        }

        // Use transaction for consistency
        DB::beginTransaction();
        try {
            $now = now();

            // Map question_type to exercise_model_id
            $exerciseModelId = 1; // Default: Pilihan Ganda
            if ($request->question_type === 'essai') {
                $exerciseModelId = 2;
            }

            // Create ONE exercise for all AI-generated questions
            $exercise = Exercise::create([
                'lesson_id' => $lesson->id,
                'serial_id' => $serial->id,
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
                    'exercise_model_id' => $exerciseModelId,
                    'exercise_choice' => 1,
                    'exercise_number' => $index + 1,
                    'question' => $questionData['question'],
                    'answer' => ($request->question_type === 'pilihan_ganda')
                        ? json_encode([$questionData['answer'] ?? null])
                        : ($questionData['answer'] ?? null),
                    'is_user' => 1,
                ];

                // Add options if multiple choice
                if ($request->question_type === 'pilihan_ganda' && isset($questionData['options'])) {
                    $options = array_filter($questionData['options']);
                    $itemData['selection'] = json_encode([
                        'A' => $options[0] ?? null,
                        'B' => $options[1] ?? null,
                        'C' => $options[2] ?? null,
                        'D' => $options[3] ?? null,
                        'E' => $options[4] ?? null,
                    ]);
                }

                ExerciseItem::create($itemData);
            }

            DB::commit();

            // Clear session data
            session()->forget('ai_generated_questions');

            return redirect()->route('guru.soal.list-direct', [$serial->id, 'tambahan'])
                ->with('success', "Berhasil menyimpan " . count($validQuestions) . " soal yang di-generate AI!");
                
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
    public function viewExercise($serial, $exerciseId)
    {
        $serial = Serial::findOrFail($serial);
        $exercise = Exercise::findOrFail($exerciseId);

        $exercise->load(['lesson.mapel', 'exerciseItems', 'exerciseType']);

        return view('guru.soal.view-exercise', compact('serial', 'exercise'));
    }

    /**
     * Extract plain text from supported attachment formats.
     */
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
