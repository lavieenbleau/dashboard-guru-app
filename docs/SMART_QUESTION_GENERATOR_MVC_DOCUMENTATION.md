# Smart Question Generator - MVC Documentation

## 📋 Table of Contents
1. [Overview](#overview)
2. [System Architecture](#system-architecture)
3. [Database Schema](#database-schema)
4. [Models](#models)
5. [Controllers](#controllers)
6. [Services](#services)
7. [Important Database Queries](#important-database-queries)
8. [Routes](#routes)
9. [Data Flow & Workflow](#data-flow--workflow)
10. [Configuration](#configuration)
11. [Error Handling](#error-handling)

---

## Overview

**Smart Question Generator** adalah fitur generasi soal otomatis menggunakan AI (OpenAI GPT-4o-mini atau OpenRouter). Guru dapat membuat soal berkualitas hanya dengan memberikan deskripsi materi, tanpa perlu mengetik soal satu per satu.

### Keunggulan Fitur
- ✅ Menghemat waktu guru dalam membuat soal
- ✅ AI menghasilkan soal dengan struktur yang baik
- ✅ Mendukung Pilihan Ganda dan Essay
- ✅ Dapat diedit sebelum disimpan
- ✅ Terintegrasi langsung ke Bank Soal
- ✅ Dapat dibagikan ke kelas dengan sekali klik

---

## System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                      GURU INTERFACE                         │
│  (Blade Template: ai-generator.blade.php)                   │
└─────────────────────┬───────────────────────────────────────┘
                      │ POST /ai-generate
                      ▼
┌─────────────────────────────────────────────────────────────┐
│         SOAL CONTROLLER (Guru/SoalController)                │
│  • aiGenerator() - Tampilkan form                           │
│  • generateWithAI() - Process form & call service           │
│  • aiPreview() - Preview & edit soal                        │
│  • saveAIQuestions() - Simpan ke database                   │
└─────────────────────┬───────────────────────────────────────┘
                      │ Call OpenAIService
                      ▼
┌─────────────────────────────────────────────────────────────┐
│           OPEN AI SERVICE (Services)                         │
│  • generateQuestions() - Call OpenAI API                    │
│  • buildPrompt() - Build AI Prompt                          │
│  • formatQuestions() - Format Response                      │
└─────────────────────┬───────────────────────────────────────┘
                      │ API Request
                      ▼
┌─────────────────────────────────────────────────────────────┐
│        OPENAI API / OPENROUTER API                          │
│  • GPT-4o-mini Model                                        │
│  • Generate structured JSON response                        │
└─────────────────────────────────────────────────────────────┘
                      │ Response
                      ▼
┌─────────────────────────────────────────────────────────────┐
│              SESSION / DATABASE                             │
│  • exercises table - Header soal                           │
│  • exercise_items table - Items (pertanyaan)               │
│  • lessons table - Kategori materi                         │
│  • share_exercises pivot - Sharing ke kelas                │
└─────────────────────────────────────────────────────────────┘
```

---

## Database Schema

### Tabel: `exercises`
Header/induk dari satu set soal

```sql
CREATE TABLE exercises (
    id                  BIGINT PRIMARY KEY AUTO_INCREMENT,
    lesson_id          BIGINT NOT NULL,           -- FK ke lessons
    serial_id          BIGINT NULL,               -- FK ke serials (guru punya serial)
    exercise_type_id   BIGINT NOT NULL,           -- FK ke exercise_types (UH/SL/PTS/PAS)
    title              VARCHAR(200) NULL,         -- Judul set soal
    time_limit         SMALLINT UNSIGNED NULL,    -- Durasi pengerjaan dalam menit (NULL = no limit)
    is_admin           TINYINT DEFAULT 1,         -- 0=guru custom, 1=admin
    shared_to_classes  JSON NULL,                 -- Array class IDs untuk sharing
    created_at         TIMESTAMP,
    updated_at         TIMESTAMP,
    
    -- Foreign keys
    CONSTRAINT fk_exercises_lesson_id 
        FOREIGN KEY (lesson_id) REFERENCES lessons(id),
    CONSTRAINT fk_exercises_serial_id 
        FOREIGN KEY (serial_id) REFERENCES serials(id),
    CONSTRAINT fk_exercises_exercise_type_id 
        FOREIGN KEY (exercise_type_id) REFERENCES exercise_types(id)
);
```

### Tabel: `exercise_items`
Item/pertanyaan individual dalam satu exercise

```sql
CREATE TABLE exercise_items (
    id                  BIGINT PRIMARY KEY AUTO_INCREMENT,
    admin_id           BIGINT NULL,               -- FK ke admins
    user_id            BIGINT NULL,               -- FK ke users
    competence_id      BIGINT NULL,               -- FK ke competencies
    exercise_id        BIGINT NOT NULL,           -- FK ke exercises
    exercise_type_id   BIGINT NOT NULL,           -- FK ke exercise_types
    exercise_model_id  BIGINT NOT NULL,           -- FK ke exercise_models (1=PG, 2=Essay, 3=Isian)
    exercise_choice    TINYINT,                   -- Pilihan (default 1)
    exercise_number    INT,                       -- Nomor urut soal (1, 2, 3, ...)
    question           TEXT NOT NULL,             -- Teks pertanyaan
    selection          TEXT NULL,                 -- JSON array opsi jawaban (untuk pilihan ganda)
    answer             TEXT NULL,                 -- Kunci jawaban
    is_user            TINYINT DEFAULT 0,         -- 1=user created, 0=admin
    created_at         TIMESTAMP,
    updated_at         TIMESTAMP,
    
    -- Foreign keys
    CONSTRAINT fk_exercise_items_exercise_id 
        FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE,
    CONSTRAINT fk_exercise_items_exercise_type_id 
        FOREIGN KEY (exercise_type_id) REFERENCES exercise_types(id),
    CONSTRAINT fk_exercise_items_exercise_model_id 
        FOREIGN KEY (exercise_model_id) REFERENCES exercise_models(id)
);
```

### Tabel: `lessons`
Kategori/materi tempat soal disimpan

```sql
CREATE TABLE lessons (
    id              BIGINT PRIMARY KEY AUTO_INCREMENT,
    mapel_id        BIGINT NOT NULL,             -- FK ke mapels (mata pelajaran)
    name            VARCHAR(255),                -- Nama materi
    description     TEXT NULL,
    category        VARCHAR(50),                 -- 'materi' atau 'soal'
    grade           VARCHAR(10),                 -- Kelas (1, 2, ..., 12)
    semester        INT,                         -- Semester (1 atau 2)
    created_at      TIMESTAMP,
    updated_at      TIMESTAMP
);
```

### Tabel: `exercise_types`
Tipe soal: Ulangan Harian, PTS, PAS, Soal Latihan

```sql
CREATE TABLE exercise_types (
    id          BIGINT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100),                  -- 'Ulangan Harian', 'PTS', dll
    kode        VARCHAR(10),                   -- 'UH', 'SL', 'PTS', 'PAS'
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP
);
```

### Tabel: `exercise_models`
Model/tipe format soal

```sql
CREATE TABLE exercise_models (
    id          BIGINT PRIMARY KEY AUTO_INCREMENT,
    name        VARCHAR(100),                  -- 'Pilihan Ganda', 'Essay', 'Isian'
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP
);
```

### Tabel: `share_exercises`
Pivot table untuk sharing soal ke serial/kelas

```sql
CREATE TABLE share_exercises (
    id          BIGINT PRIMARY KEY AUTO_INCREMENT,
    exercise_id BIGINT NOT NULL,
    serial_id   BIGINT NOT NULL,
    created_at  TIMESTAMP,
    updated_at  TIMESTAMP,
    
    UNIQUE KEY unique_exercise_serial (exercise_id, serial_id),
    CONSTRAINT fk_share_exercises_exercise_id 
        FOREIGN KEY (exercise_id) REFERENCES exercises(id),
    CONSTRAINT fk_share_exercises_serial_id 
        FOREIGN KEY (serial_id) REFERENCES serials(id)
);
```

---

## Models

### 1. Exercise Model
**File:** `app/Models/Exercise.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exercise extends Model
{
    protected $table = 'exercises';
    protected $fillable = [
        'lesson_id',
        'serial_id',
        'exercise_type_id',
        'title',
        'time_limit',
        'is_admin',
    ];

    // ===== RELATIONSHIPS =====
    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function exerciseType()
    {
        return $this->belongsTo(ExerciseType::class);
    }

    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }

    public function exerciseItems()
    {
        return $this->hasMany(ExerciseItem::class, 'exercise_id');
    }

    public function sharedSerials()
    {
        return $this->belongsToMany(Serial::class, 'share_exercises', 'exercise_id', 'serial_id');
    }

    // ===== ACCESSORS =====
    public function getSharedToClassesAttribute()
    {
        return $this->sharedSerials()->pluck('serial_id')->toJson();
    }
}
```

### 2. ExerciseItem Model
**File:** `app/Models/ExerciseItem.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExerciseItem extends Model
{
    protected $fillable = [
        'admin_id',
        'user_id',
        'competence_id',
        'exercise_id',
        'exercise_type_id',
        'exercise_model_id',
        'exercise_choice',
        'exercise_number',
        'question',
        'selection',
        'answer',
        'is_user'
    ];

    protected $casts = [
        'selection' => 'array',  // Auto-cast JSON to array
    ];

    // ===== RELATIONSHIPS =====
    public function exercise()
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    public function exerciseType()
    {
        return $this->belongsTo(ExerciseType::class, 'exercise_type_id');
    }

    public function exerciseModel()
    {
        return $this->belongsTo(ExerciseModel::class, 'exercise_model_id');
    }
}
```

### 3. Lesson Model
**File:** `app/Models/Lesson.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lesson extends Model
{
    const CATEGORY_MATERI = 'materi';
    const CATEGORY_SOAL = 'soal';

    protected $fillable = [
        'mapel_id',
        'name',
        'category',
        'grade',
        'semester',
    ];

    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    public function exercises()
    {
        return $this->hasMany(Exercise::class);
    }
}
```

---

## Controllers

### SoalController (Guru)
**File:** `app/Http/Controllers/Guru/SoalController.php`

#### 1. `aiGenerator($serial)`
Tampilkan form AI Generator

```php
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

    // Merge materials
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
```

#### 2. `generateWithAI(Request $request, $serial)`
Process form, call OpenAI Service, store in session

```php
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
        
        // Call OpenAI API to generate questions
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
```

#### 3. `aiPreview($serial)`
Show preview & edit form

```php
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
```

#### 4. `saveAIQuestions(Request $request, $serial)`
Save AI-generated questions to database

```php
public function saveAIQuestions(Request $request, $serial)
{
    $serial = Serial::findOrFail($serial);
    
    $request->validate([
        'exercise_title' => 'required|string|max:255',
        'exercise_type_id' => 'required|exists:exercise_types,id',
        'mapel_id' => 'required|exists:mapels,id',
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
    
    // Collect valid questions (exclude deleted)
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
                'answer' => $questionData['answer'] ?? null,
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
                ]);
            }

            ExerciseItem::create($itemData);
        }

        // Share to classrooms if selected
        if ($request->classrooms && is_array($request->classrooms)) {
            foreach ($request->classrooms as $classroomId) {
                $classroom = Classroom::find($classroomId);
                if ($classroom && $classroom->serial_id) {
                    $exercise->sharedSerials()->attach($classroom->serial_id);
                }
            }
        }

        DB::commit();

        return redirect()->route('guru.soal.list-direct', [$serial->id, 'tambahan'])
            ->with('success', count($validQuestions) . ' soal berhasil disimpan ke Bank Soal!');

    } catch (\Exception $e) {
        DB::rollBack();
        
        return back()
            ->withInput()
            ->with('error', 'Gagal menyimpan soal: ' . $e->getMessage());
    }
}
```

---

## Services

### OpenAIService
**File:** `app/Services/OpenAIService.php`

```php
<?php

namespace App\Services;

use OpenAI;
use Exception;

class OpenAIService
{
    protected $client;
    protected $maxRetries = 3;
    protected $retryDelay = 2; // seconds

    public function __construct()
    {
        $apiKey = config('services.openai.api_key');
        
        if (!$apiKey) {
            throw new Exception('AI API key belum dikonfigurasi. Silakan tambahkan OpenRouter API key ke file .env Anda.');
        }

        // Support for custom base URL (e.g., OpenRouter)
        $baseUrl = config('services.openai.base_url');
        
        if ($baseUrl) {
            $this->client = OpenAI::factory()
                ->withApiKey($apiKey)
                ->withBaseUri($baseUrl)
                ->withHttpHeader('HTTP-Referer', config('app.url'))
                ->withHttpHeader('X-Title', config('app.name'))
                ->make();
        } else {
            $this->client = OpenAI::client($apiKey);
        }
    }

    /**
     * Generate questions based on material illustration
     *
     * @param string $illustration Material description from teacher
     * @param string $questionType Type of question: 'pilihan_ganda' or 'essai'
     * @param string $difficulty Difficulty level: 'mudah', 'sedang', or 'sulit'
     * @param int $count Number of questions to generate
     * @return array Array of generated questions
     * @throws Exception
     */
    public function generateQuestions(string $illustration, string $questionType, string $difficulty, int $count): array
    {
        $attempt = 0;
        $lastException = null;

        while ($attempt < $this->maxRetries) {
            try {
                $prompt = $this->buildPrompt($illustration, $questionType, $difficulty, $count);

                // Use best available model based on provider
                $model = config('services.openai.base_url') 
                    ? 'openai/gpt-4o-mini'  // OpenRouter format
                    : 'gpt-4o-mini';         // OpenAI direct format

                // Call OpenAI API
                $response = $this->client->chat()->create([
                    'model' => $model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'Anda adalah seorang guru berpengalaman yang ahli dalam membuat soal-soal berkualitas untuk siswa. Anda harus menghasilkan soal dalam format JSON yang valid.'
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt
                        ]
                    ],
                    'temperature' => 0.7,
                    'response_format' => ['type' => 'json_object'],
                ]);

                // Extract and parse response
                $content = $response->choices[0]->message->content;
                $data = json_decode($content, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new Exception('Failed to parse OpenAI response: ' . json_last_error_msg());
                }

                return $this->formatQuestions($data, $questionType);

            } catch (Exception $e) {
                $lastException = $e;
                $attempt++;

                // Check if it's a rate limit error
                if ($this->isRateLimitError($e)) {
                    if ($attempt < $this->maxRetries) {
                        // Exponential backoff: wait longer with each retry
                        $delay = $this->retryDelay * pow(2, $attempt - 1);
                        sleep($delay);
                        continue;
                    }
                }

                // If not rate limit error or max retries reached, throw immediately
                throw new Exception('AI API Error: ' . $e->getMessage());
            }
        }

        // If all retries failed
        throw new Exception('AI API Error setelah ' . $this->maxRetries . ' percobaan: ' . ($lastException ? $lastException->getMessage() : 'Unknown error'));
    }

    /**
     * Check if the error is a rate limit error
     */
    private function isRateLimitError(Exception $e): bool
    {
        $message = strtolower($e->getMessage());
        return str_contains($message, 'rate limit') || 
               str_contains($message, 'too many requests') ||
               str_contains($message, '429');
    }

    /**
     * Build prompt for OpenAI based on parameters
     */
    private function buildPrompt(string $illustration, string $questionType, string $difficulty, int $count): string
    {
        $difficultyMap = [
            'mudah' => 'mudah (cocok untuk pemula)',
            'sedang' => 'sedang (tingkat menengah)',
            'sulit' => 'sulit (tingkat lanjut dengan analisis mendalam)'
        ];

        $difficultyText = $difficultyMap[$difficulty] ?? 'sedang';

        if ($questionType === 'pilihan_ganda') {
            return <<<PROMPT
Buatlah {$count} soal pilihan ganda berkualitas dengan tingkat kesulitan {$difficultyText} berdasarkan materi berikut:

{$illustration}

Format output harus dalam JSON dengan struktur:
{
  "questions": [
    {
      "title": "Judul singkat soal",
      "question": "Teks soal yang lengkap dan jelas",
      "options": {
        "A": "Pilihan A",
        "B": "Pilihan B",
        "C": "Pilihan C",
        "D": "Pilihan D"
      },
      "correct_answer": "A",
      "explanation": "Penjelasan mengapa jawaban tersebut benar"
    }
  ]
}

Pastikan:
- Setiap soal memiliki 4 pilihan jawaban (A, B, C, D)
- Hanya satu jawaban yang benar
- Question dan options menggunakan bahasa Indonesia yang baik dan benar
- Explanation memberikan penjelasan yang jelas dan edukatif
PROMPT;
        } else {
            return <<<PROMPT
Buatlah {$count} soal essay berkualitas dengan tingkat kesulitan {$difficultyText} berdasarkan materi berikut:

{$illustration}

Format output harus dalam JSON dengan struktur:
{
  "questions": [
    {
      "title": "Judul singkat soal",
      "question": "Teks soal yang mendorong analisis dan pemikiran kritis",
      "correct_answer": "Poin-poin kunci yang harus ada dalam jawaban",
      "explanation": "Penjelasan lengkap tentang jawaban yang diharapkan"
    }
  ]
}

Pastikan:
- Soal mendorong siswa untuk berpikir kritis dan analitis
- Question menggunakan bahasa Indonesia yang baik dan benar
- Correct_answer berisi poin-poin kunci yang harus ada dalam jawaban siswa
- Explanation memberikan panduan untuk penilaian
PROMPT;
        }
    }

    /**
     * Format questions from OpenAI response to match our application structure
     */
    private function formatQuestions(array $data, string $questionType): array
    {
        if (!isset($data['questions']) || !is_array($data['questions'])) {
            throw new Exception('Format response dari AI tidak valid. Silakan coba lagi.');
        }

        $formatted = [];

        foreach ($data['questions'] as $question) {
            $formattedQuestion = [
                'title' => $question['title'] ?? 'Soal',
                'question' => $question['question'] ?? '',
                'correct_answer' => $question['correct_answer'] ?? '',
                'explanation' => $question['explanation'] ?? '',
            ];

            if ($questionType === 'pilihan_ganda' && isset($question['options'])) {
                $formattedQuestion['options'] = [
                    $question['options']['A'] ?? '',
                    $question['options']['B'] ?? '',
                    $question['options']['C'] ?? '',
                    $question['options']['D'] ?? '',
                ];
            }

            $formatted[] = $formattedQuestion;
        }

        return $formatted;
    }
}
```

---

## Important Database Queries

### 1. Get AI-Generated Questions for Preview
```php
// Get all exercise items from a specific exercise
$exercise = Exercise::with('exerciseItems')->findOrFail($exerciseId);

$questions = $exercise->exerciseItems()
    ->orderBy('exercise_number')
    ->get();
```

### 2. Save AI-Generated Questions
```php
DB::beginTransaction();

try {
    // 1. Create Exercise header
    $exercise = Exercise::create([
        'lesson_id' => $lesson->id,
        'serial_id' => $serial->id,
        'exercise_type_id' => $request->exercise_type_id,
        'title' => $request->exercise_title,
        'is_admin' => 0,  // Custom guru
    ]);

    // 2. Create Exercise Items (questions)
    foreach ($validQuestions as $index => $questionData) {
        $itemData = [
            'exercise_id' => $exercise->id,
            'exercise_type_id' => $request->exercise_type_id,
            'exercise_model_id' => $exerciseModelId,
            'exercise_choice' => 1,
            'exercise_number' => $index + 1,
            'question' => $questionData['question'],
            'answer' => $questionData['answer'] ?? null,
            'is_user' => 1,
        ];

        // For multiple choice, add options
        if ($request->question_type === 'pilihan_ganda' && isset($questionData['options'])) {
            $itemData['selection'] = json_encode([
                'A' => $questionData['options'][0] ?? null,
                'B' => $questionData['options'][1] ?? null,
                'C' => $questionData['options'][2] ?? null,
                'D' => $questionData['options'][3] ?? null,
            ]);
        }

        ExerciseItem::create($itemData);
    }

    // 3. Share to classrooms
    if ($request->classrooms && is_array($request->classrooms)) {
        foreach ($request->classrooms as $classroomId) {
            $classroom = Classroom::find($classroomId);
            if ($classroom && $classroom->serial_id) {
                $exercise->sharedSerials()->attach($classroom->serial_id);
            }
        }
    }

    DB::commit();
    
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;
}
```

### 3. Get Teacher's Custom Questions (Soal Tambahan)
```php
// Get all custom questions (AI-generated or manually created by teacher)
$customQuestions = Exercise::where('serial_id', $serial->id)
    ->where('is_admin', 0)
    ->with(['lesson.mapel', 'exerciseItems', 'exerciseType'])
    ->orderBy('created_at', 'desc')
    ->get();

// Query menggunakan SQL:
SELECT 
    e.id,
    e.title,
    e.lesson_id,
    e.serial_id,
    e.exercise_type_id,
    e.is_admin,
    l.name AS lesson_name,
    m.name AS mapel_name,
    et.name AS exercise_type_name,
    COUNT(ei.id) AS question_count
FROM exercises e
LEFT JOIN lessons l ON e.lesson_id = l.id
LEFT JOIN mapels m ON l.mapel_id = m.id
LEFT JOIN exercise_types et ON e.exercise_type_id = et.id
LEFT JOIN exercise_items ei ON e.id = ei.exercise_id
WHERE e.serial_id = ? AND e.is_admin = 0
GROUP BY e.id
ORDER BY e.created_at DESC;
```

### 4. Get Lesson for Material Category
```php
// Find or create base lesson for mapel
$lesson = Lesson::firstOrCreate(
    [
        'mapel_id' => $mapel_id,
        'category' => Lesson::CATEGORY_SOAL,
        'name' => 'Base Lesson',
    ],
    [
        'grade' => '1',
        'semester' => 1,
    ]
);

// Query menggunakan SQL:
SELECT * FROM lessons 
WHERE mapel_id = ? AND category = 'soal' AND name = 'Base Lesson'
LIMIT 1;
```

### 5. Get All Mapels
```php
$mapels = Mapel::orderBy('name')->get();

// Query menggunakan SQL:
SELECT * FROM mapels ORDER BY name ASC;
```

### 6. Get Exercise Types for Teacher
```php
// Only UH (Ulangan Harian) and SL (Soal Latihan)
$exerciseTypes = ExerciseType::whereIn('kode', ['UH', 'SL'])->get();

// Query menggunakan SQL:
SELECT * FROM exercise_types WHERE kode IN ('UH', 'SL');
```

### 7. Get Classrooms for Sharing
```php
$classrooms = Classroom::where('serial_id', $serial->id)->get();

// Query menggunakan SQL:
SELECT * FROM classrooms WHERE serial_id = ?;
```

### 8. Share Exercise to Classrooms
```php
// Attach exercise to multiple classrooms' serials
if ($request->classrooms && is_array($request->classrooms)) {
    foreach ($request->classrooms as $classroomId) {
        $classroom = Classroom::find($classroomId);
        if ($classroom && $classroom->serial_id) {
            // Insert into share_exercises pivot table
            $exercise->sharedSerials()->attach($classroom->serial_id);
        }
    }
}

// Query menggunakan SQL:
INSERT INTO share_exercises (exercise_id, serial_id, created_at, updated_at)
VALUES (?, ?, NOW(), NOW());
```

### 9. Get AI Materials Sources (Posts & Lessons)
```php
// Get teacher's upload materials
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

// Get admin materials
$adminMaterials = Lesson::where('category', Lesson::CATEGORY_MATERI)
    ->with('mapel')
    ->latest()
    ->get();

// Query menggunakan SQL (Materials):
SELECT 
    p.id,
    p.title,
    p.serial_id,
    p.description,
    p.file,
    p.attachment,
    p.link,
    m.name AS mapel_name,
    p.created_at
FROM posts p
LEFT JOIN mapels m ON p.mapel_id = m.id
WHERE p.serial_id = ? AND p.is_task = 0
    AND (p.description IS NOT NULL 
         OR p.attachment IS NOT NULL 
         OR p.link IS NOT NULL)
ORDER BY p.created_at DESC;

// Query untuk admin materials:
SELECT 
    l.id,
    l.name,
    l.mapel_id,
    l.category,
    l.grade,
    l.semester,
    m.name AS mapel_name,
    l.created_at
FROM lessons l
LEFT JOIN mapels m ON l.mapel_id = m.id
WHERE l.category = 'materi'
ORDER BY l.created_at DESC;
```

### 10. Count AI-Generated Questions by Type
```php
// Count exercise items by question type (exercise_model_id)
$questionStats = ExerciseItem::where('exercise_id', $exerciseId)
    ->select('exercise_model_id', DB::raw('COUNT(*) as count'))
    ->groupBy('exercise_model_id')
    ->get();

// Query menggunakan SQL:
SELECT 
    exercise_model_id,
    COUNT(*) as count
FROM exercise_items
WHERE exercise_id = ?
GROUP BY exercise_model_id;
```

---

## Routes

### Web Routes Configuration
**File:** `routes/web.php`

```php
// AI Question Generator Routes (Inside Guru Routes)
Route::middleware(['auth', 'role:guru'])->group(function () {
    Route::prefix('guru/soal/{serial}')->group(function () {
        
        // AI Generator Form
        Route::get('/ai-generator', [SoalController::class, 'aiGenerator'])
            ->name('guru.soal.ai-generator');
        
        // Read material content (AJAX)
        Route::get('/read-material/{materialId}', [SoalController::class, 'readUploadedMaterial'])
            ->name('guru.soal.read-material');
        
        // Generate with AI (Process form)
        Route::post('/ai-generate', [SoalController::class, 'generateWithAI'])
            ->name('guru.soal.ai-generate');
        
        // Preview generated questions
        Route::get('/ai-preview', [SoalController::class, 'aiPreview'])
            ->name('guru.soal.ai-preview');
        
        // Save AI questions to database
        Route::post('/ai-save', [SoalController::class, 'saveAIQuestions'])
            ->name('guru.soal.ai-save');
    });
});
```

---

## Data Flow & Workflow

### Complete Workflow

```
┌─────────────────────────────────────────────────────────────────┐
│ 1. GURU AKSES FORM GENERATOR                                   │
│    GET /guru/soal/{serial}/ai-generator                        │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│ 2. FORM RENDER                                                 │
│    • Load mapels, exercise_types, classrooms, materials        │
│    • Show ai-generator.blade.php                               │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│ 3. GURU INPUT DATA                                             │
│    • Pilih atau paste materi deskripsi                        │
│    • Pilih jenis soal (Pilgan/Essay)                          │
│    • Pilih tingkat kesulitan                                  │
│    • Isi jumlah soal (1-10)                                   │
│    • Pilih mata pelajaran & tipe soal                         │
│    • (Optional) Pilih kelas untuk sharing                     │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│ 4. SUBMIT FORM                                                 │
│    POST /guru/soal/{serial}/ai-generate                        │
│    Validation:                                                 │
│    • illustration: min 20 char                                 │
│    • question_type: pilihan_ganda|essai                        │
│    • difficulty: mudah|sedang|sulit                            │
│    • count: 1-10                                               │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│ 5. CALL OPENAI SERVICE                                         │
│    OpenAIService::generateQuestions()                          │
│    • buildPrompt() - Prepare structured prompt                 │
│    • Call OpenAI API with GPT-4o-mini                         │
│    • Handle rate limit with exponential backoff                │
│    • formatQuestions() - Parse & format response               │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│ 6. STORE IN SESSION                                            │
│    session['ai_generated_questions'] = [                       │
│        'questions' => [...],                                   │
│        'question_type' => 'pilihan_ganda',                     │
│        'mapel_id' => $id,                                      │
│        'exercise_type_id' => $id,                              │
│        'classrooms' => [...]                                   │
│    ]                                                            │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│ 7. REDIRECT TO PREVIEW                                         │
│    GET /guru/soal/{serial}/ai-preview                          │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│ 8. PREVIEW & EDIT                                              │
│    • Show generated questions                                  │
│    • Allow edit each question                                  │
│    • Allow delete individual questions                         │
│    • Show answer & explanation                                 │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│ 9. SUBMIT TO SAVE                                              │
│    POST /guru/soal/{serial}/ai-save                            │
│    Data:                                                       │
│    • exercise_title                                            │
│    • questions[] (array of questions)                          │
│    • mapel_id, exercise_type_id                                │
│    • classrooms[] (optional)                                   │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│ 10. DATABASE TRANSACTION                                       │
│     DB::beginTransaction()                                     │
│     • Create Exercise record                                   │
│     • Create ExerciseItem records (each question)             │
│     • Attach to classrooms (share_exercises)                  │
│     DB::commit()                                               │
└────────────────────┬────────────────────────────────────────────┘
                     │
                     ▼
┌─────────────────────────────────────────────────────────────────┐
│ 11. REDIRECT SUCCESS                                           │
│     GET /guru/soal/{serial}/list-direct/tambahan               │
│     Show success message with count of saved questions         │
└─────────────────────────────────────────────────────────────────┘
```

---

## Configuration

### Environment Variables (.env)
```env
# OpenRouter (Recommended - Free tier available)
OPENAI_API_KEY=sk-or-v1-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_BASE_URL=https://openrouter.ai/api/v1
OPENAI_ORGANIZATION=

# OR OpenAI Direct
# OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
# OPENAI_BASE_URL=
# OPENAI_ORGANIZATION=org-xxxxxxxxxxxxxxxxxxxxxxxx
```

### Config File (config/services.php)
```php
'openai' => [
    'api_key' => env('OPENAI_API_KEY'),
    'base_url' => env('OPENAI_BASE_URL'),
    'organization' => env('OPENAI_ORGANIZATION'),
],
```

### Key Configuration Attributes

| Attribute | Value | Meaning |
|-----------|-------|---------|
| `exercise_model_id` | 1 | Pilihan Ganda (Multiple Choice) |
| `exercise_model_id` | 2 | Essay |
| `exercise_model_id` | 3 | Isian Singkat (Short Answer) |
| `is_admin` | 0 | Custom dari guru (AI-generated atau manual) |
| `is_admin` | 1 | Admin/Kurikulum |
| `is_user` | 1 | Created by user/teacher |
| `is_user` | 0 | Default/admin |
| `lesson_category` | 'materi' | Learning material |
| `lesson_category` | 'soal' | Questions/exercises |

---

## Error Handling

### Common Errors & Solutions

#### 1. Rate Limit Error
```php
// Problem: Terlalu banyak request ke OpenAI dalam waktu singkat
// Solution: Service sudah implement exponential backoff

// Retry logic:
while ($attempt < 3) {
    try {
        // API call
    } catch (Exception $e) {
        if (str_contains($e->getMessage(), 'rate limit')) {
            sleep(2 * pow(2, $attempt - 1));  // 2s, 4s, 8s
            $attempt++;
        } else {
            throw $e;
        }
    }
}
```

#### 2. Invalid Response Format
```php
// Problem: AI response tidak sesuai format JSON yang diharapkan
// Solution: 
if (json_last_error() !== JSON_ERROR_NONE) {
    throw new Exception('Format response dari AI tidak valid. Silakan coba lagi.');
}
```

#### 3. API Key Not Configured
```php
// Problem: OPENAI_API_KEY tidak ada di .env
// Solution:
if (!$apiKey) {
    throw new Exception('AI API key belum dikonfigurasi. Silakan tambahkan OpenRouter API key ke file .env');
}
```

#### 4. No Lesson Found
```php
// Problem: Lesson belum ada untuk mapel tertentu
// Solution: Auto-create using firstOrCreate
$lesson = Lesson::firstOrCreate(
    [
        'mapel_id' => $request->mapel_id,
        'category' => Lesson::CATEGORY_SOAL,
        'name' => 'Base Lesson',
    ],
    [
        'grade' => '1',
        'semester' => 1,
    ]
);
```

---

## Summary

Fitur Smart Question Generator mengintegrasikan:

1. **Frontend**: User-friendly form untuk input materi & parameter
2. **Controller**: Orchestrate flow dari form → service → database
3. **Service**: Handle API call ke OpenAI dengan retry logic & rate limit handling
4. **Database**: Structured tables untuk exercises, items, dan relationships
5. **Session**: Temporary storage untuk preview sebelum save

Workflow yang clean dan transaction-based memastikan data consistency dan user experience yang baik.

---

## Summary

Fitur Smart Question Generator mengintegrasikan:

1. **Frontend**: User-friendly form untuk input materi & parameter
2. **Controller**: Orchestrate flow dari form → service → database
3. **Service**: Handle API call ke OpenAI dengan retry logic & rate limit handling
4. **Database**: Structured tables untuk exercises, items, dan relationships
5. **Session**: Temporary storage untuk preview sebelum save

Workflow yang clean dan transaction-based memastikan data consistency dan user experience yang baik.

---

## Fitur: Time Limit (Waktu Pengerjaan Kuis)

### Overview
Fitur time_limit memungkinkan guru mengatur durasi maksimal pengerjaan kuis ketika membuat soal. Field ini nullable, sehingga guru dapat memilih untuk tidak menetapkan batas waktu.

### Database Field
```sql
ALTER TABLE exercises ADD COLUMN time_limit UNSIGNEDSMALLINT NULL AFTER title;
```

**Tipe Data**: `unsignedSmallInteger`  
**Default**: NULL (tidak ada batas waktu)  
**Range**: 1-480 menit (1 menit hingga 8 jam)

### Validation Rules
```php
// Di storeCustom() method
'time_limit' => 'nullable|integer|min:1|max:480'

// Di saveAIQuestions() method
'time_limit' => 'nullable|integer|min:1|max:480'
```

### Form Input
Tersedia di tiga form:
1. **ai-generator.blade.php** - Saat generate soal dengan AI
2. **ai-preview.blade.php** - Saat preview soal sebelum save
3. **create-custom.blade.php** - Saat membuat soal manual

Input field:
```blade
<input 
    type="number" 
    class="form-control" 
    name="time_limit" 
    min="1" 
    max="480" 
    placeholder="Kosongkan untuk tidak ada batas waktu">
```

### Controller Implementation

#### Di `storeCustom()`:
```php
$exercise = Exercise::create([
    'lesson_id' => $lesson->id,
    'serial_id' => $serial->id,
    'exercise_type_id' => $request->exercise_type_id,
    'title' => $questionData['title'],
    'time_limit' => $request->time_limit,  // <-- Tambahan
    'is_admin' => 0,
]);
```

#### Di `saveAIQuestions()`:
```php
$exercise = Exercise::create([
    'lesson_id' => $lesson->id,
    'serial_id' => $serial->id,
    'exercise_type_id' => $request->exercise_type_id,
    'title' => $request->exercise_title,
    'time_limit' => $request->time_limit,  // <-- Tambahan
    'description' => null,
    'is_admin' => 0,
]);
```

### Usage Example
- Guru input: `45 menit` → Siswa dapat mengerjakan kuis max 45 menit
- Guru input: Kosong → Tidak ada batas waktu, siswa bisa unlimited
- Default: Kosong (NULL)

### Related Migrations
File: `database/migrations/2026_05_28_add_time_limit_to_exercises_table.php`

```php
public function up(): void
{
    Schema::table('exercises', function (Blueprint $table) {
        $table->unsignedSmallInteger('time_limit')
            ->nullable()
            ->default(null)
            ->comment('Waktu pengerjaan dalam menit. NULL = tidak ada batas waktu')
            ->after('title');
    });
}
```

---

**Documentation Last Updated**: 2026-05-28
**Version**: 1.1 (Added Time Limit Feature)
**System**: Dashboard Guru - AI Question Generator Module
