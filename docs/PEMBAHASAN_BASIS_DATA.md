# PEMBAHASAN DML (Data Manipulation Language) DASHBOARDGURU

## Daftar Isi

1. [SELECT - Query Data](#1-select---query-data)
2. [INSERT - Menambah Data](#2-insert---menambah-data)
3. [UPDATE - Mengubah Data](#3-update---mengubah-data)
4. [DELETE - Menghapus Data](#4-delete---menghapus-data)
5. [JOIN Operations](#5-join-operations)
6. [Aggregate Functions](#6-aggregate-functions)
7. [Subquery dan Complex Queries](#7-subquery-dan-complex-queries)
8. [Transaction Management](#8-transaction-management)

---

## 1. SELECT - Query Data

### 1.1 SELECT Dasar

#### 1.1.1 Mengambil Semua Data

**SQL:**
```sql
-- Ambil semua data dari tabel users
SELECT * FROM users;

-- Ambil semua data dari tabel students
SELECT * FROM students;

-- Ambil semua data dari tabel classrooms
SELECT * FROM classrooms;
```

**Laravel Eloquent:**
```php
// Ambil semua users
$users = User::all();

// Ambil semua students
$students = Student::all();

// Ambil semua classrooms
$classrooms = Classroom::all();
```

#### 1.1.2 Memilih Kolom Tertentu

**SQL:**
```sql
-- Ambil hanya kolom tertentu dari students
SELECT id, name, nisn, email 
FROM students;

-- Ambil kolom tertentu dari classrooms
SELECT id, name, code, grade 
FROM classrooms;
```

**Laravel Eloquent:**
```php
// Select kolom tertentu
$students = Student::select('id', 'name', 'nisn', 'email')->get();

// Atau menggunakan array
$classrooms = Classroom::select(['id', 'name', 'code', 'grade'])->get();
```

### 1.2 WHERE Clause - Filter Data

#### 1.2.1 Kondisi Sederhana

**SQL:**
```sql
-- Filter berdasarkan satu kondisi
SELECT * FROM students WHERE classroom_id = 1;

-- Filter berdasarkan nama
SELECT * FROM users WHERE name = 'John Doe';

-- Filter dengan kondisi numerik
SELECT * FROM exercise_points WHERE score >= 80;
```

**Laravel Eloquent:**
```php
// Where sederhana
$students = Student::where('classroom_id', 1)->get();

// Where dengan operator
$highScores = ExercisePoint::where('score', '>=', 80)->get();

// Where equals
$user = User::where('name', 'John Doe')->first();
```

#### 1.2.2 Multiple Conditions (AND)

**SQL:**
```sql
-- Multiple kondisi dengan AND
SELECT * FROM students 
WHERE classroom_id = 1 
  AND gender = 'L';

-- Filter dengan range
SELECT * FROM exercise_points 
WHERE score >= 70 
  AND score <= 100 
  AND status = 'graded';
```

**Laravel Eloquent:**
```php
// Multiple where (AND)
$students = Student::where('classroom_id', 1)
                   ->where('gender', 'L')
                   ->get();

// Atau menggunakan array
$students = Student::where([
    ['classroom_id', '=', 1],
    ['gender', '=', 'L']
])->get();
```

#### 1.2.3 OR Conditions

**SQL:**
```sql
-- Kondisi OR
SELECT * FROM students 
WHERE classroom_id = 1 
   OR classroom_id = 2;

-- Kombinasi AND dan OR
SELECT * FROM exercise_points 
WHERE (status = 'graded' OR status = 'pending') 
  AND score >= 50;
```

**Laravel Eloquent:**
```php
// OR where
$students = Student::where('classroom_id', 1)
                   ->orWhere('classroom_id', 2)
                   ->get();

// Kombinasi AND dan OR
$points = ExercisePoint::where(function($query) {
    $query->where('status', 'graded')
          ->orWhere('status', 'pending');
})->where('score', '>=', 50)->get();
```

#### 1.2.4 IN, BETWEEN, LIKE

**SQL:**
```sql
-- IN operator
SELECT * FROM students 
WHERE classroom_id IN (1, 2, 3);

-- BETWEEN operator
SELECT * FROM exercise_points 
WHERE score BETWEEN 70 AND 100;

-- LIKE operator untuk pencarian
SELECT * FROM students 
WHERE name LIKE '%John%';

-- LIKE dengan wildcard
SELECT * FROM lessons 
WHERE title LIKE 'Matematika%';  -- Dimulai dengan
```

**Laravel Eloquent:**
```php
// whereIn
$students = Student::whereIn('classroom_id', [1, 2, 3])->get();

// whereBetween
$points = ExercisePoint::whereBetween('score', [70, 100])->get();

// whereLike / where with LIKE
$students = Student::where('name', 'LIKE', '%John%')->get();

// whereNotIn
$students = Student::whereNotIn('classroom_id', [5, 6])->get();
```

#### 1.2.5 NULL Checks

**SQL:**
```sql
-- IS NULL
SELECT * FROM students WHERE email IS NULL;

-- IS NOT NULL
SELECT * FROM students WHERE email IS NOT NULL;

-- COALESCE untuk default value
SELECT id, name, COALESCE(email, 'No Email') as email 
FROM students;
```

**Laravel Eloquent:**
```php
// whereNull
$studentsNoEmail = Student::whereNull('email')->get();

// whereNotNull
$studentsWithEmail = Student::whereNotNull('email')->get();
```

### 1.3 ORDER BY - Sorting

**SQL:**
```sql
-- Sort ascending (ASC) - default
SELECT * FROM students ORDER BY name ASC;

-- Sort descending (DESC)
SELECT * FROM lessons ORDER BY created_at DESC;

-- Multiple sort
SELECT * FROM students 
ORDER BY classroom_id ASC, name ASC;

-- Sort dengan CASE
SELECT * FROM exercise_points 
ORDER BY 
  CASE status 
    WHEN 'pending' THEN 1
    WHEN 'graded' THEN 2
  END;
```

**Laravel Eloquent:**
```php
// Order by ascending
$students = Student::orderBy('name', 'asc')->get();

// Order by descending
$lessons = Lesson::orderBy('created_at', 'desc')->get();

// Latest (shortcut untuk created_at DESC)
$lessons = Lesson::latest()->get();

// Oldest (shortcut untuk created_at ASC)
$lessons = Lesson::oldest()->get();

// Multiple order by
$students = Student::orderBy('classroom_id')
                   ->orderBy('name')
                   ->get();
```

### 1.4 LIMIT dan OFFSET - Pagination

**SQL:**
```sql
-- LIMIT: Ambil 10 record pertama
SELECT * FROM students LIMIT 10;

-- LIMIT dengan OFFSET: Skip 10, ambil 10 berikutnya
SELECT * FROM students LIMIT 10 OFFSET 10;

-- Pagination: Page 3, 20 per page
SELECT * FROM students LIMIT 20 OFFSET 40;  -- (3-1)*20 = 40
```

**Laravel Eloquent:**
```php
// Take/Limit
$students = Student::take(10)->get();
$students = Student::limit(10)->get();

// Skip dan Take
$students = Student::skip(10)->take(10)->get();

// Pagination (automatic)
$students = Student::paginate(20);  // 20 per page

// Simple pagination
$students = Student::simplePaginate(20);
```

### 1.5 DISTINCT - Unique Values

**SQL:**
```sql
-- Ambil nilai unik
SELECT DISTINCT classroom_id FROM students;

-- Distinct multiple columns
SELECT DISTINCT classroom_id, gender FROM students;

-- Count distinct
SELECT COUNT(DISTINCT classroom_id) as total_classrooms 
FROM students;
```

**Laravel Eloquent:**
```php
// Distinct
$classroomIds = Student::distinct()->pluck('classroom_id');

// Count distinct
$totalClassrooms = Student::distinct('classroom_id')->count('classroom_id');
```

---

## 2. INSERT - Menambah Data

### 2.1 INSERT Single Record

**SQL:**
```sql
-- Insert single student
INSERT INTO students (
    serial_id, 
    classroom_id, 
    name, 
    nisn, 
    username, 
    password, 
    created_at, 
    updated_at
) VALUES (
    1, 
    1, 
    'John Doe', 
    '1234567890', 
    'johndoe', 
    '$2y$12$hashedpassword', 
    NOW(), 
    NOW()
);

-- Insert classroom
INSERT INTO classrooms (
    serial_id, 
    code, 
    name, 
    grade, 
    grade_category, 
    created_at, 
    updated_at
) VALUES (
    1, 
    'ABC123', 
    'Kelas 5A', 
    '5', 
    'SD', 
    NOW(), 
    NOW()
);
```

**Laravel Eloquent:**
```php
// Create method
$student = Student::create([
    'serial_id' => 1,
    'classroom_id' => 1,
    'name' => 'John Doe',
    'nisn' => '1234567890',
    'username' => 'johndoe',
    'password' => bcrypt('password'),
]);

// New instance + save
$classroom = new Classroom();
$classroom->serial_id = 1;
$classroom->code = 'ABC123';
$classroom->name = 'Kelas 5A';
$classroom->grade = '5';
$classroom->grade_category = 'SD';
$classroom->save();
```

### 2.2 INSERT Multiple Records

**SQL:**
```sql
-- Insert multiple students sekaligus
INSERT INTO students (
    serial_id, classroom_id, name, username, password, created_at, updated_at
) VALUES 
    (1, 1, 'Student 1', 'student1', '$2y$12$hash1', NOW(), NOW()),
    (1, 1, 'Student 2', 'student2', '$2y$12$hash2', NOW(), NOW()),
    (1, 1, 'Student 3', 'student3', '$2y$12$hash3', NOW(), NOW());
```

**Laravel Eloquent:**
```php
// Insert multiple
Student::insert([
    [
        'serial_id' => 1,
        'classroom_id' => 1,
        'name' => 'Student 1',
        'username' => 'student1',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'serial_id' => 1,
        'classroom_id' => 1,
        'name' => 'Student 2',
        'username' => 'student2',
        'password' => bcrypt('password'),
        'created_at' => now(),
        'updated_at' => now(),
    ],
]);
```

### 2.3 INSERT dengan SELECT (Copy Data)

**SQL:**
```sql
-- Copy students dari classroom 1 ke classroom 2
INSERT INTO students (
    serial_id, classroom_id, name, username, password, created_at, updated_at
)
SELECT 
    serial_id, 
    2 as classroom_id,  -- classroom baru
    name, 
    CONCAT(username, '_copy') as username,  -- username unique
    password, 
    NOW(), 
    NOW()
FROM students 
WHERE classroom_id = 1;
```

### 2.4 INSERT IGNORE (Skip Duplicates)

**SQL:**
```sql
-- Insert, skip jika username sudah ada (unique constraint)
INSERT IGNORE INTO students (
    serial_id, classroom_id, name, username, password
) VALUES (
    1, 1, 'John Doe', 'johndoe', '$2y$12$hash'
);
```

**Laravel Eloquent:**
```php
// firstOrCreate: Cari atau create jika tidak ada
$student = Student::firstOrCreate(
    ['username' => 'johndoe'],  // Kondisi pencarian
    [  // Data jika create
        'serial_id' => 1,
        'classroom_id' => 1,
        'name' => 'John Doe',
        'password' => bcrypt('password'),
    ]
);

// updateOrCreate: Update jika ada, create jika tidak
$student = Student::updateOrCreate(
    ['username' => 'johndoe'],  // Kondisi pencarian
    [  // Data untuk create atau update
        'serial_id' => 1,
        'classroom_id' => 1,
        'name' => 'John Doe Updated',
    ]
);
```

---

## 3. UPDATE - Mengubah Data

### 3.1 UPDATE Single Record

**SQL:**
```sql
-- Update student berdasarkan ID
UPDATE students 
SET name = 'John Doe Updated',
    email = 'john@example.com',
    updated_at = NOW()
WHERE id = 1;

-- Update classroom code
UPDATE classrooms 
SET code = 'XYZ789'
WHERE id = 1;
```

**Laravel Eloquent:**
```php
// Find and update
$student = Student::find(1);
$student->name = 'John Doe Updated';
$student->email = 'john@example.com';
$student->save();

// Update method
Student::where('id', 1)->update([
    'name' => 'John Doe Updated',
    'email' => 'john@example.com',
]);

// findOrFail (throws exception if not found)
$student = Student::findOrFail(1);
$student->update([
    'name' => 'John Doe Updated'
]);
```

### 3.2 UPDATE Multiple Records

**SQL:**
```sql
-- Update semua students di classroom tertentu
UPDATE students 
SET classroom_id = 2
WHERE classroom_id = 1;

-- Update berdasarkan kondisi
UPDATE exercise_points 
SET status = 'graded'
WHERE score IS NOT NULL 
  AND status = 'pending';
```

**Laravel Eloquent:**
```php
// Update multiple records
Student::where('classroom_id', 1)
       ->update(['classroom_id' => 2]);

// Update dengan kondisi kompleks
ExercisePoint::whereNotNull('score')
             ->where('status', 'pending')
             ->update(['status' => 'graded']);
```

### 3.3 UPDATE dengan JOIN

**SQL:**
```sql
-- Update students berdasarkan data dari classroom
UPDATE students s
INNER JOIN classrooms c ON s.classroom_id = c.id
SET s.grade = c.grade
WHERE c.serial_id = 1;
```

**Laravel Query Builder:**
```php
// Update dengan join
DB::table('students as s')
    ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
    ->where('c.serial_id', 1)
    ->update(['s.grade' => DB::raw('c.grade')]);
```

### 3.4 UPDATE dengan Increment/Decrement

**SQL:**
```sql
-- Increment nilai
UPDATE exercise_points 
SET score = score + 5 
WHERE id = 1;

-- Decrement nilai
UPDATE products 
SET stock = stock - 1 
WHERE id = 1;
```

**Laravel Eloquent:**
```php
// Increment
$point = ExercisePoint::find(1);
$point->increment('score', 5);  // Tambah 5

// Decrement
$product = Product::find(1);
$product->decrement('stock', 1);  // Kurang 1

// Increment dengan kondisi
ExercisePoint::where('exercise_id', 1)
             ->increment('score', 10);
```

### 3.5 UPDATE dengan CASE

**SQL:**
```sql
-- Conditional update
UPDATE exercise_points 
SET grade = CASE 
    WHEN score >= 90 THEN 'A'
    WHEN score >= 80 THEN 'B'
    WHEN score >= 70 THEN 'C'
    WHEN score >= 60 THEN 'D'
    ELSE 'E'
END
WHERE status = 'graded';
```

**Laravel Query Builder:**
```php
// Update dengan CASE
DB::table('exercise_points')
    ->where('status', 'graded')
    ->update([
        'grade' => DB::raw("CASE 
            WHEN score >= 90 THEN 'A'
            WHEN score >= 80 THEN 'B'
            WHEN score >= 70 THEN 'C'
            WHEN score >= 60 THEN 'D'
            ELSE 'E'
        END")
    ]);
```

---

## 4. DELETE - Menghapus Data

### 4.1 DELETE Single Record

**SQL:**
```sql
-- Delete berdasarkan ID
DELETE FROM students WHERE id = 1;

-- Delete dengan kondisi
DELETE FROM exercise_points WHERE score < 50;
```

**Laravel Eloquent:**
```php
// Find and delete
$student = Student::find(1);
$student->delete();

// Delete dengan kondisi
Student::where('id', 1)->delete();

// Delete multiple
ExercisePoint::where('score', '<', 50)->delete();
```

### 4.2 DELETE Multiple Records

**SQL:**
```sql
-- Delete multiple dengan IN
DELETE FROM students WHERE id IN (1, 2, 3);

-- Delete semua students dari classroom tertentu
DELETE FROM students WHERE classroom_id = 1;
```

**Laravel Eloquent:**
```php
// Delete multiple by IDs
Student::whereIn('id', [1, 2, 3])->delete();

// Delete by condition
Student::where('classroom_id', 1)->delete();

// Destroy (delete by IDs)
Student::destroy([1, 2, 3]);
Student::destroy(1, 2, 3);  // Alternative syntax
```

### 4.3 DELETE dengan JOIN

**SQL:**
```sql
-- Delete students dari classrooms yang sudah tidak aktif
DELETE s 
FROM students s
INNER JOIN classrooms c ON s.classroom_id = c.id
WHERE c.active = 0;
```

**Laravel Query Builder:**
```php
// Delete dengan join
$studentIds = DB::table('students as s')
    ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
    ->where('c.active', 0)
    ->pluck('s.id');

Student::whereIn('id', $studentIds)->delete();
```

### 4.4 Soft Delete

**SQL:**
```sql
-- Soft delete: set deleted_at instead of actual delete
UPDATE students 
SET deleted_at = NOW() 
WHERE id = 1;

-- Query exclude soft deleted
SELECT * FROM students WHERE deleted_at IS NULL;

-- Query only soft deleted
SELECT * FROM students WHERE deleted_at IS NOT NULL;
```

**Laravel Eloquent (with SoftDeletes trait):**
```php
// Model setup
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;
}

// Soft delete
$student = Student::find(1);
$student->delete();  // Sets deleted_at

// Restore
$student->restore();

// Force delete (permanent)
$student->forceDelete();

// Query
$students = Student::all();  // Exclude soft deleted
$students = Student::withTrashed()->get();  // Include soft deleted
$students = Student::onlyTrashed()->get();  // Only soft deleted
```

### 4.5 TRUNCATE vs DELETE

**SQL:**
```sql
-- DELETE: Dapat di-rollback, slower untuk large tables
DELETE FROM students;

-- TRUNCATE: Tidak dapat di-rollback, faster, reset auto-increment
TRUNCATE TABLE students;
```

**Laravel:**
```php
// Delete all (dapat di-rollback)
Student::query()->delete();

// Truncate (reset auto-increment, tidak dapat di-rollback)
DB::table('students')->truncate();
```

---

## 5. JOIN Operations

### 5.1 INNER JOIN

**SQL:**
```sql
-- Students dengan classroom info
SELECT 
    s.id,
    s.name as student_name,
    s.nisn,
    c.name as classroom_name,
    c.code as classroom_code
FROM students s
INNER JOIN classrooms c ON s.classroom_id = c.id;

-- Exercise points dengan student dan exercise info
SELECT 
    ep.id,
    s.name as student_name,
    e.title as exercise_title,
    ep.score,
    ep.max_score
FROM exercise_points ep
INNER JOIN students s ON ep.student_id = s.id
INNER JOIN exercises e ON ep.exercise_id = e.id;
```

**Laravel Eloquent:**
```php
// Eager loading (N+1 solution)
$students = Student::with('classroom')->get();

foreach ($students as $student) {
    echo $student->name . ' - ' . $student->classroom->name;
}

// Atau dengan Query Builder
$students = DB::table('students as s')
    ->join('classrooms as c', 's.classroom_id', '=', 'c.id')
    ->select('s.id', 's.name as student_name', 'c.name as classroom_name')
    ->get();
```

### 5.2 LEFT JOIN

**SQL:**
```sql
-- Semua classrooms (termasuk yang tidak punya students)
SELECT 
    c.id,
    c.name as classroom_name,
    COUNT(s.id) as student_count
FROM classrooms c
LEFT JOIN students s ON c.id = s.classroom_id
GROUP BY c.id, c.name;

-- Lessons dengan atau tanpa items
SELECT 
    l.id,
    l.title,
    COUNT(li.id) as item_count
FROM lessons l
LEFT JOIN lesson_items li ON l.id = li.lesson_id
GROUP BY l.id, l.title;
```

**Laravel Eloquent:**
```php
// With count (automatic left join)
$classrooms = Classroom::withCount('students')->get();

foreach ($classrooms as $classroom) {
    echo $classroom->name . ': ' . $classroom->students_count . ' students';
}

// Query Builder
$classrooms = DB::table('classrooms as c')
    ->leftJoin('students as s', 'c.id', '=', 's.classroom_id')
    ->select('c.id', 'c.name', DB::raw('COUNT(s.id) as student_count'))
    ->groupBy('c.id', 'c.name')
    ->get();
```

### 5.3 RIGHT JOIN

**SQL:**
```sql
-- Right join (jarang dipakai, biasanya bisa diganti LEFT JOIN)
SELECT 
    s.name as student_name,
    c.name as classroom_name
FROM classrooms c
RIGHT JOIN students s ON c.id = s.classroom_id;
```

**Laravel Query Builder:**
```php
$results = DB::table('classrooms as c')
    ->rightJoin('students as s', 'c.id', '=', 's.classroom_id')
    ->select('s.name as student_name', 'c.name as classroom_name')
    ->get();
```

### 5.4 Multiple JOINs

**SQL:**
```sql
-- Complex join: Student scores dengan info lengkap
SELECT 
    s.name as student_name,
    c.name as classroom_name,
    e.title as exercise_title,
    et.name as exercise_type,
    ep.score,
    ep.max_score,
    ROUND((ep.score / ep.max_score * 100), 2) as percentage
FROM exercise_points ep
INNER JOIN students s ON ep.student_id = s.id
INNER JOIN classrooms c ON s.classroom_id = c.id
INNER JOIN exercises e ON ep.exercise_id = e.id
INNER JOIN exercise_types et ON e.exercise_type_id = et.id
WHERE c.serial_id = 1
  AND ep.status = 'graded'
ORDER BY percentage DESC;
```

**Laravel Eloquent:**
```php
// Nested eager loading
$points = ExercisePoint::with([
    'student.classroom',
    'exercise.exerciseType'
])
->whereHas('student.classroom', function($q) {
    $q->where('serial_id', 1);
})
->where('status', 'graded')
->get();

// Access
foreach ($points as $point) {
    echo $point->student->name;
    echo $point->student->classroom->name;
    echo $point->exercise->title;
    echo $point->exercise->exerciseType->name;
}
```

### 5.5 CROSS JOIN

**SQL:**
```sql
-- Cross join: Kombinasi semua students dengan semua exercises (kartesian product)
SELECT 
    s.name as student_name,
    e.title as exercise_title
FROM students s
CROSS JOIN exercises e
WHERE s.classroom_id = 1
  AND e.serial_id = 1;
```

**Laravel Query Builder:**
```php
$results = DB::table('students as s')
    ->crossJoin('exercises as e')
    ->where('s.classroom_id', 1)
    ->where('e.serial_id', 1)
    ->select('s.name as student_name', 'e.title as exercise_title')
    ->get();
```

---

## 6. Aggregate Functions

### 6.1 COUNT

**SQL:**
```sql
-- Count total students
SELECT COUNT(*) as total_students FROM students;

-- Count students per classroom
SELECT 
    classroom_id,
    COUNT(*) as student_count
FROM students
GROUP BY classroom_id;

-- Count with condition
SELECT COUNT(*) as active_students 
FROM students 
WHERE deleted_at IS NULL;

-- Count distinct
SELECT COUNT(DISTINCT classroom_id) as total_classrooms 
FROM students;
```

**Laravel Eloquent:**
```php
// Total count
$total = Student::count();

// Count with condition
$active = Student::whereNull('deleted_at')->count();

// Count distinct
$classrooms = Student::distinct('classroom_id')->count('classroom_id');

// Group by count
$counts = Student::groupBy('classroom_id')
                 ->selectRaw('classroom_id, COUNT(*) as student_count')
                 ->get();

// withCount
$classrooms = Classroom::withCount('students')->get();
```

### 6.2 SUM

**SQL:**
```sql
-- Total score semua exercise points
SELECT SUM(score) as total_score 
FROM exercise_points;

-- Sum per student
SELECT 
    student_id,
    SUM(score) as total_score,
    SUM(max_score) as total_max_score
FROM exercise_points
WHERE status = 'graded'
GROUP BY student_id;
```

**Laravel Eloquent:**
```php
// Total sum
$totalScore = ExercisePoint::sum('score');

// Sum with condition
$gradedTotal = ExercisePoint::where('status', 'graded')->sum('score');

// Sum per group
$sums = ExercisePoint::where('status', 'graded')
    ->groupBy('student_id')
    ->selectRaw('student_id, SUM(score) as total_score, SUM(max_score) as total_max_score')
    ->get();
```

### 6.3 AVG (Average)

**SQL:**
```sql
-- Rata-rata score
SELECT AVG(score) as average_score 
FROM exercise_points 
WHERE status = 'graded';

-- Average per classroom
SELECT 
    c.name as classroom_name,
    AVG(ep.score) as average_score
FROM exercise_points ep
INNER JOIN students s ON ep.student_id = s.id
INNER JOIN classrooms c ON s.classroom_id = c.id
WHERE ep.status = 'graded'
GROUP BY c.id, c.name;
```

**Laravel Eloquent:**
```php
// Average
$avgScore = ExercisePoint::where('status', 'graded')->avg('score');

// Average dengan relasi
$classrooms = Classroom::with(['students' => function($q) {
    $q->with('exercisePoints');
}])->get();

// Calculate average in PHP
foreach ($classrooms as $classroom) {
    $avgScore = $classroom->students->flatMap->exercisePoints
                                    ->where('status', 'graded')
                                    ->avg('score');
}
```

### 6.4 MIN dan MAX

**SQL:**
```sql
-- Score tertinggi dan terendah
SELECT 
    MIN(score) as lowest_score,
    MAX(score) as highest_score
FROM exercise_points
WHERE status = 'graded';

-- Per exercise
SELECT 
    exercise_id,
    MIN(score) as lowest,
    MAX(score) as highest,
    AVG(score) as average
FROM exercise_points
WHERE status = 'graded'
GROUP BY exercise_id;
```

**Laravel Eloquent:**
```php
// Min and Max
$lowest = ExercisePoint::where('status', 'graded')->min('score');
$highest = ExercisePoint::where('status', 'graded')->max('score');

// Per group
$stats = ExercisePoint::where('status', 'graded')
    ->groupBy('exercise_id')
    ->selectRaw('
        exercise_id, 
        MIN(score) as lowest, 
        MAX(score) as highest, 
        AVG(score) as average
    ')
    ->get();
```

### 6.5 GROUP BY dengan HAVING

**SQL:**
```sql
-- Classrooms dengan lebih dari 20 students
SELECT 
    classroom_id,
    COUNT(*) as student_count
FROM students
GROUP BY classroom_id
HAVING COUNT(*) > 20;

-- Students dengan rata-rata score di atas 80
SELECT 
    student_id,
    AVG(score) as avg_score
FROM exercise_points
WHERE status = 'graded'
GROUP BY student_id
HAVING AVG(score) > 80;
```

**Laravel Query Builder:**
```php
// HAVING clause
$classrooms = DB::table('students')
    ->select('classroom_id', DB::raw('COUNT(*) as student_count'))
    ->groupBy('classroom_id')
    ->having('student_count', '>', 20)
    ->get();

// Complex having
$topStudents = DB::table('exercise_points')
    ->select('student_id', DB::raw('AVG(score) as avg_score'))
    ->where('status', 'graded')
    ->groupBy('student_id')
    ->having('avg_score', '>', 80)
    ->get();
```

---

## 7. Subquery dan Complex Queries

### 7.1 Subquery di WHERE

**SQL:**
```sql
-- Students yang memiliki score di atas rata-rata
SELECT * FROM students
WHERE id IN (
    SELECT student_id 
    FROM exercise_points 
    WHERE score > (
        SELECT AVG(score) 
        FROM exercise_points 
        WHERE status = 'graded'
    )
);

-- Classrooms yang tidak punya students
SELECT * FROM classrooms
WHERE id NOT IN (
    SELECT DISTINCT classroom_id 
    FROM students
);
```

**Laravel Eloquent:**
```php
// whereIn subquery
$avgScore = ExercisePoint::where('status', 'graded')->avg('score');
$students = Student::whereHas('exercisePoints', function($q) use ($avgScore) {
    $q->where('score', '>', $avgScore);
})->get();

// whereNotIn subquery
$classrooms = Classroom::whereDoesntHave('students')->get();
```

### 7.2 Subquery di SELECT

**SQL:**
```sql
-- Classrooms dengan student count dan average score
SELECT 
    c.id,
    c.name,
    (
        SELECT COUNT(*) 
        FROM students s 
        WHERE s.classroom_id = c.id
    ) as student_count,
    (
        SELECT AVG(ep.score)
        FROM exercise_points ep
        INNER JOIN students s ON ep.student_id = s.id
        WHERE s.classroom_id = c.id
          AND ep.status = 'graded'
    ) as avg_score
FROM classrooms c;
```

**Laravel Query Builder:**
```php
$classrooms = Classroom::select('id', 'name')
    ->selectSub(function ($query) {
        $query->from('students')
              ->whereColumn('classroom_id', 'classrooms.id')
              ->selectRaw('COUNT(*)');
    }, 'student_count')
    ->selectSub(function ($query) {
        $query->from('exercise_points as ep')
              ->join('students as s', 'ep.student_id', '=', 's.id')
              ->whereColumn('s.classroom_id', 'classrooms.id')
              ->where('ep.status', 'graded')
              ->selectRaw('AVG(ep.score)');
    }, 'avg_score')
    ->get();
```

### 7.3 EXISTS dan NOT EXISTS

**SQL:**
```sql
-- Classrooms yang punya students
SELECT * FROM classrooms c
WHERE EXISTS (
    SELECT 1 FROM students s 
    WHERE s.classroom_id = c.id
);

-- Exercises yang belum dikerjakan siswa tertentu
SELECT * FROM exercises e
WHERE NOT EXISTS (
    SELECT 1 FROM exercise_points ep 
    WHERE ep.exercise_id = e.id 
      AND ep.student_id = 1
);
```

**Laravel Eloquent:**
```php
// whereHas (EXISTS)
$classrooms = Classroom::whereHas('students')->get();

// whereDoesntHave (NOT EXISTS)
$exercises = Exercise::whereDoesntHave('exercisePoints', function($q) {
    $q->where('student_id', 1);
})->get();
```

### 7.4 UNION

**SQL:**
```sql
-- Gabungkan users dan students untuk daftar semua akun
SELECT id, name, username, 'user' as type FROM users
UNION
SELECT id, name, username, 'student' as type FROM students;

-- UNION ALL (include duplicates)
SELECT name FROM students WHERE classroom_id = 1
UNION ALL
SELECT name FROM students WHERE classroom_id = 2;
```

**Laravel Query Builder:**
```php
// Union
$users = DB::table('users')
    ->select('id', 'name', 'username', DB::raw("'user' as type"));

$students = DB::table('students')
    ->select('id', 'name', 'username', DB::raw("'student' as type"));

$allAccounts = $users->union($students)->get();

// Union All
$classroom1 = Student::where('classroom_id', 1)->select('name');
$classroom2 = Student::where('classroom_id', 2)->select('name');

$combined = $classroom1->unionAll($classroom2)->get();
```

### 7.5 Common Table Expression (CTE) - WITH

**SQL:**
```sql
-- CTE untuk query lebih readable
WITH StudentScores AS (
    SELECT 
        student_id,
        AVG(score) as avg_score,
        COUNT(*) as exercise_count
    FROM exercise_points
    WHERE status = 'graded'
    GROUP BY student_id
)
SELECT 
    s.name,
    ss.avg_score,
    ss.exercise_count
FROM students s
INNER JOIN StudentScores ss ON s.id = ss.student_id
WHERE ss.avg_score > 80
ORDER BY ss.avg_score DESC;

-- Multiple CTEs
WITH 
ClassroomStats AS (
    SELECT 
        classroom_id,
        COUNT(*) as student_count
    FROM students
    GROUP BY classroom_id
),
ScoreStats AS (
    SELECT 
        s.classroom_id,
        AVG(ep.score) as avg_score
    FROM exercise_points ep
    INNER JOIN students s ON ep.student_id = s.id
    WHERE ep.status = 'graded'
    GROUP BY s.classroom_id
)
SELECT 
    c.name,
    cs.student_count,
    COALESCE(ss.avg_score, 0) as avg_score
FROM classrooms c
LEFT JOIN ClassroomStats cs ON c.id = cs.classroom_id
LEFT JOIN ScoreStats ss ON c.id = ss.classroom_id;
```

**Laravel (menggunakan DB::statement atau raw query):**
```php
$results = DB::select("
    WITH StudentScores AS (
        SELECT 
            student_id,
            AVG(score) as avg_score,
            COUNT(*) as exercise_count
        FROM exercise_points
        WHERE status = 'graded'
        GROUP BY student_id
    )
    SELECT 
        s.name,
        ss.avg_score,
        ss.exercise_count
    FROM students s
    INNER JOIN StudentScores ss ON s.id = ss.student_id
    WHERE ss.avg_score > 80
    ORDER BY ss.avg_score DESC
");
```

---

## 8. Transaction Management

### 8.1 Basic Transaction

**SQL:**
```sql
-- Start transaction
START TRANSACTION;

-- Insert classroom
INSERT INTO classrooms (serial_id, code, name, grade_category, created_at, updated_at)
VALUES (1, 'ABC123', 'Kelas 5A', 'SD', NOW(), NOW());

-- Insert students
INSERT INTO students (serial_id, classroom_id, name, username, password, created_at, updated_at)
VALUES 
    (1, LAST_INSERT_ID(), 'Student 1', 'student1', '$2y$12$hash', NOW(), NOW()),
    (1, LAST_INSERT_ID(), 'Student 2', 'student2', '$2y$12$hash', NOW(), NOW());

-- Commit if success
COMMIT;

-- Rollback if error
-- ROLLBACK;
```

**Laravel:**
```php
use Illuminate\Support\Facades\DB;

// Manual transaction
DB::beginTransaction();

try {
    // Create classroom
    $classroom = Classroom::create([
        'serial_id' => 1,
        'code' => 'ABC123',
        'name' => 'Kelas 5A',
        'grade_category' => 'SD',
    ]);
    
    // Create students
    Student::insert([
        [
            'serial_id' => 1,
            'classroom_id' => $classroom->id,
            'name' => 'Student 1',
            'username' => 'student1',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'serial_id' => 1,
            'classroom_id' => $classroom->id,
            'name' => 'Student 2',
            'username' => 'student2',
            'password' => bcrypt('password'),
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);
    
    DB::commit();
    
} catch (\Exception $e) {
    DB::rollback();
    throw $e;
}
```

### 8.2 Transaction dengan Closure

**Laravel:**
```php
// Automatic transaction dengan closure
DB::transaction(function () {
    $classroom = Classroom::create([
        'serial_id' => 1,
        'code' => 'ABC123',
        'name' => 'Kelas 5A',
        'grade_category' => 'SD',
    ]);
    
    for ($i = 1; $i <= 30; $i++) {
        Student::create([
            'serial_id' => 1,
            'classroom_id' => $classroom->id,
            'name' => 'Student ' . $i,
            'username' => 'student' . $i,
            'password' => bcrypt('password'),
        ]);
    }
});
// Auto-commit jika berhasil, auto-rollback jika exception
```

### 8.3 Nested Transaction (Savepoints)

**SQL:**
```sql
START TRANSACTION;

-- Savepoint 1
SAVEPOINT sp1;

INSERT INTO classrooms (serial_id, code, name, grade_category, created_at, updated_at)
VALUES (1, 'ABC123', 'Kelas 5A', 'SD', NOW(), NOW());

-- Savepoint 2
SAVEPOINT sp2;

INSERT INTO students (serial_id, classroom_id, name, username, password, created_at, updated_at)
VALUES (1, LAST_INSERT_ID(), 'Student 1', 'student1', '$2y$12$hash', NOW(), NOW());

-- Rollback to savepoint 2 (undo student insert, keep classroom)
ROLLBACK TO SAVEPOINT sp2;

-- Or rollback to savepoint 1 (undo everything)
-- ROLLBACK TO SAVEPOINT sp1;

COMMIT;
```

**Laravel:**
```php
DB::transaction(function () {
    $classroom = Classroom::create([/* ... */]);
    
    // Nested transaction
    DB::transaction(function () use ($classroom) {
        Student::create([/* ... */]);
    });
    
    // If nested fails, will rollback nested only
    // If outer fails, will rollback everything
});
```

### 8.4 Isolation Levels

**SQL:**
```sql
-- Set isolation level
SET TRANSACTION ISOLATION LEVEL READ COMMITTED;
START TRANSACTION;
-- queries...
COMMIT;

-- Available levels:
-- READ UNCOMMITTED
-- READ COMMITTED
-- REPEATABLE READ (MySQL default)
-- SERIALIZABLE
```

**Laravel:**
```php
// Not directly supported, use raw query
DB::statement('SET TRANSACTION ISOLATION LEVEL READ COMMITTED');
DB::beginTransaction();
// queries...
DB::commit();
```

### 8.5 Lock untuk Concurrent Access

**SQL:**
```sql
-- Shared lock (untuk read)
SELECT * FROM students WHERE id = 1 LOCK IN SHARE MODE;

-- Exclusive lock (untuk write)
SELECT * FROM students WHERE id = 1 FOR UPDATE;

-- Example: Update score dengan locking
START TRANSACTION;

SELECT * FROM exercise_points WHERE id = 1 FOR UPDATE;

UPDATE exercise_points 
SET score = 95, status = 'graded'
WHERE id = 1;

COMMIT;
```

**Laravel Eloquent:**
```php
// Shared lock
$student = Student::where('id', 1)->sharedLock()->first();

// Exclusive lock
$point = ExercisePoint::where('id', 1)->lockForUpdate()->first();

// Transaction dengan lock
DB::transaction(function () {
    $point = ExercisePoint::where('id', 1)->lockForUpdate()->first();
    
    $point->score = 95;
    $point->status = 'graded';
    $point->save();
});
```

---

## Kesimpulan DML Operations

### Ringkasan Operasi DML:

| Operasi | SQL | Laravel Eloquent | Use Case |
|---------|-----|------------------|----------|
| **SELECT** | `SELECT * FROM table` | `Model::all()` | Retrieve data |
| **INSERT** | `INSERT INTO table VALUES` | `Model::create()` | Add new record |
| **UPDATE** | `UPDATE table SET` | `$model->save()` | Modify existing |
| **DELETE** | `DELETE FROM table` | `$model->delete()` | Remove record |
| **JOIN** | `INNER JOIN` | `Model::with()` | Combine tables |
| **COUNT** | `COUNT(*)` | `Model::count()` | Count records |
| **SUM** | `SUM(column)` | `Model::sum()` | Total values |
| **AVG** | `AVG(column)` | `Model::avg()` | Average value |

### Best Practices:

✅ **DO:**
- Gunakan Eloquent untuk query sederhana
- Gunakan Query Builder untuk query kompleks
- Gunakan prepared statements (automatic di Eloquent)
- Gunakan transactions untuk multiple related operations
- Gunakan eager loading untuk menghindari N+1 queries
- Index kolom yang sering di-query

❌ **DON'T:**
- Raw SQL tanpa parameter binding
- N+1 query problems
- `SELECT *` jika tidak perlu semua kolom
- Nested loops dengan query di dalamnya
- Lupa commit/rollback transactions

### Performance Tips:

1. **Use Indexes**: WHERE, JOIN, ORDER BY columns
2. **Limit Results**: Gunakan LIMIT/pagination
3. **Select Specific Columns**: Hindari SELECT *
4. **Eager Loading**: with() untuk relasi
5. **Chunking**: Untuk large datasets
6. **Caching**: Cache query results yang sering diakses
7. **Database Pool**: Connection pooling untuk concurrency

### Security Tips:

1. **Never use raw user input** dalam query
2. **Always use parameter binding**
3. **Validate input** sebelum query
4. **Use transactions** untuk data consistency
5. **Implement proper access control**
6. **Audit log** untuk tracking changes

DML operations merupakan core dari database interaction. Pemahaman yang baik tentang SELECT, INSERT, UPDATE, DELETE, JOIN, dan aggregate functions sangat penting untuk membangun aplikasi yang efficient dan secure.

**Definisi**: Setiap kolom harus berisi atomic values (tidak ada repeating groups).

**Implementasi pada DashboardGuru**:

✅ **Compliance:**
- Semua kolom memiliki single values
- Tidak ada array atau multi-value fields
- Setiap record memiliki primary key unik

**Contoh 1: Tabel `exercise_items`**
```sql
-- ✅ BENAR (1NF Compliant)
CREATE TABLE exercise_items (
    id BIGINT UNSIGNED PRIMARY KEY,
    exercise_id BIGINT UNSIGNED,
    question TEXT,
    option_a TEXT,
    option_b TEXT,
    option_c TEXT,
    option_d TEXT,
    option_e TEXT,
    correct_answer VARCHAR(1)
);

-- ❌ SALAH (Violation 1NF)
CREATE TABLE exercise_items_wrong (
    id BIGINT UNSIGNED PRIMARY KEY,
    exercise_id BIGINT UNSIGNED,
    question TEXT,
    options TEXT,  -- "A|B|C|D|E" - multi-value field
    correct_answer VARCHAR(1)
);
```

**Contoh 2: Tabel `lesson_items`**
```sql
-- ✅ BENAR (1NF Compliant)
CREATE TABLE lesson_items (
    id BIGINT UNSIGNED PRIMARY KEY,
    lesson_id BIGINT UNSIGNED,
    type ENUM('link', 'video', 'file', 'text'),
    title VARCHAR(100),
    content TEXT,
    order_num TINYINT
);
-- Setiap item adalah record terpisah
```

### 2.2 Second Normal Form (2NF)

**Definisi**: Memenuhi 1NF dan tidak ada partial dependency (semua non-key attributes fully dependent pada entire primary key).

**Implementasi pada DashboardGuru**:

✅ **Compliance:**
- Semua tabel menggunakan single-column primary key (id)
- Tidak ada composite primary key yang dapat menyebabkan partial dependency
- Semua non-key attributes bergantung penuh pada primary key

**Contoh Analisis: Tabel `exercise_points`**

```sql
-- ✅ BENAR (2NF Compliant)
CREATE TABLE exercise_points (
    id BIGINT UNSIGNED PRIMARY KEY,  -- Single PK
    serial_id BIGINT UNSIGNED,
    exercise_id BIGINT UNSIGNED,
    student_id BIGINT UNSIGNED,
    score DECIMAL(5,2),
    max_score DECIMAL(5,2),
    status ENUM('pending', 'graded'),
    started_at TIMESTAMP,
    submitted_at TIMESTAMP
);
-- Semua kolom (score, status, timestamps) bergantung pada id (entire PK)

-- ❌ CONTOH VIOLATION (jika menggunakan composite PK)
CREATE TABLE exercise_points_wrong (
    exercise_id BIGINT UNSIGNED,
    student_id BIGINT UNSIGNED,
    score DECIMAL(5,2),
    student_name VARCHAR(100),  -- Partial dependency: bergantung hanya pada student_id
    PRIMARY KEY (exercise_id, student_id)
);
-- student_name hanya bergantung pada student_id (partial dependency)
```

**Solusi yang Diterapkan:**
- Pisahkan data student ke tabel `students`
- Gunakan foreign key `student_id` di `exercise_points`
- Retrieve student_name via JOIN saat diperlukan

### 2.3 Third Normal Form (3NF)

**Definisi**: Memenuhi 2NF dan tidak ada transitive dependency (non-key attributes tidak bergantung pada non-key attributes lain).

**Implementasi pada DashboardGuru**:

✅ **Compliance:**
- Semua non-key attributes hanya bergantung pada primary key
- Tidak ada derived/calculated values yang disimpan (kecuali untuk denormalization yang disengaja)

**Contoh 1: Tabel `classrooms` - Proper 3NF**

```sql
-- ✅ BENAR (3NF Compliant)
CREATE TABLE classrooms (
    id BIGINT UNSIGNED PRIMARY KEY,
    serial_id BIGINT UNSIGNED,
    code VARCHAR(10),
    name VARCHAR(50),
    grade VARCHAR(50),
    grade_category VARCHAR(100),
    wali_kelas VARCHAR(100),
    academic_year VARCHAR(20),
    FOREIGN KEY (serial_id) REFERENCES serials(id)
);
-- Tidak ada transitive dependency
```

**Contoh 2: Menghindari Transitive Dependency**

```sql
-- ❌ VIOLATION of 3NF
CREATE TABLE lessons_wrong (
    id BIGINT UNSIGNED PRIMARY KEY,
    mapel_id BIGINT UNSIGNED,
    mapel_name VARCHAR(100),  -- Transitive: mapel_name → mapel_id → id
    theme_id BIGINT UNSIGNED,
    theme_name VARCHAR(100),  -- Transitive: theme_name → theme_id → id
    title VARCHAR(100)
);

-- ✅ BENAR (3NF Compliant)
CREATE TABLE lessons (
    id BIGINT UNSIGNED PRIMARY KEY,
    mapel_id BIGINT UNSIGNED,    -- Foreign key only
    theme_id BIGINT UNSIGNED,    -- Foreign key only
    title VARCHAR(100),
    FOREIGN KEY (mapel_id) REFERENCES mapels(id),
    FOREIGN KEY (theme_id) REFERENCES themes(id)
);

CREATE TABLE mapels (
    id BIGINT UNSIGNED PRIMARY KEY,
    name VARCHAR(100)
);

CREATE TABLE themes (
    id BIGINT UNSIGNED PRIMARY KEY,
    name VARCHAR(100)
);
```

### 2.4 Denormalization untuk Performance

Meskipun database dinormalisasi hingga 3NF, beberapa denormalization strategy diterapkan untuk optimasi performance:

**1. Storing `max_score` di `exercise_points`**

```sql
CREATE TABLE exercise_points (
    id BIGINT UNSIGNED PRIMARY KEY,
    exercise_id BIGINT UNSIGNED,
    student_id BIGINT UNSIGNED,
    score DECIMAL(5,2),
    max_score DECIMAL(5,2),  -- Denormalized: bisa dihitung dari exercise_items
    ...
);
```

**Alasan:**
- Menghindari expensive JOIN dan SUM calculation setiap kali retrieve nilai
- `max_score` jarang berubah setelah exercise dibuat
- Trade-off: Sedikit redundansi untuk significant performance gain

**2. Caching Student Count di Application Level**

```php
// Eloquent withCount untuk avoid N+1 query
$classrooms = Classroom::withCount('students')->get();

// Alternative: Cache count
Cache::remember('classroom_' . $id . '_student_count', 3600, function() use ($id) {
    return Student::where('classroom_id', $id)->count();
});
```

### 2.5 Normal Form Compliance Summary

| Normal Form | Status | Notes |
|-------------|--------|-------|
| 1NF (Atomic Values) | ✅ Full Compliance | Semua kolom atomic, no repeating groups |
| 2NF (No Partial Dependency) | ✅ Full Compliance | Single-column PKs, no composite key issues |
| 3NF (No Transitive Dependency) | ✅ Full Compliance | Proper table separation, minimal redundancy |
| BCNF (Boyce-Codd NF) | ✅ Mostly Compliant | Tidak ada anomaly detected |
| 4NF (Multi-valued Dependency) | ✅ Full Compliance | Proper M:N relationships dengan pivot tables |

---

## 3. Relasi dan Integritas Data

### 3.1 Tipe Relasi yang Diimplementasikan

#### 3.1.1 One-to-Many (1:N) Relationships

**Karakteristik:**
- Parent table memiliki satu record
- Child table dapat memiliki banyak records yang referensi ke parent
- Menggunakan foreign key di child table

**Implementasi dalam DashboardGuru:**

**1. users → serials**
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY,
    name VARCHAR(100),
    ...
);

CREATE TABLE serials (
    id BIGINT UNSIGNED PRIMARY KEY,
    user_id BIGINT UNSIGNED,
    serial VARCHAR(50),
    ...
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
```

**Business Rule**: Satu guru dapat mengaktifkan banyak serial

**2. serials → classrooms**
```sql
CREATE TABLE classrooms (
    id BIGINT UNSIGNED PRIMARY KEY,
    serial_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(50),
    ...
    FOREIGN KEY (serial_id) REFERENCES serials(id) ON DELETE CASCADE
);
```

**Business Rule**: Satu serial dapat memiliki banyak kelas

**3. classrooms → students**
```sql
CREATE TABLE students (
    id BIGINT UNSIGNED PRIMARY KEY,
    classroom_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100),
    ...
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE
);
```

**Business Rule**: Satu kelas dapat memiliki banyak siswa

**4. lessons → lesson_items**
```sql
CREATE TABLE lesson_items (
    id BIGINT UNSIGNED PRIMARY KEY,
    lesson_id BIGINT UNSIGNED NOT NULL,
    type ENUM('link', 'video', 'file', 'text'),
    content TEXT,
    ...
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
);
```

**Business Rule**: Satu pelajaran dapat memiliki banyak item konten

**Daftar Lengkap 1:N Relations:**

| Parent Table | Child Table | Cascade Action | Business Rule |
|--------------|-------------|----------------|---------------|
| users | serials | CASCADE | 1 guru → N serial |
| products | serials | CASCADE | 1 produk → N serial |
| serials | classrooms | CASCADE | 1 serial → N kelas |
| serials | lessons | CASCADE | 1 serial → N pelajaran |
| serials | exercises | CASCADE | 1 serial → N latihan |
| classrooms | students | CASCADE | 1 kelas → N siswa |
| lessons | lesson_items | CASCADE | 1 pelajaran → N item |
| exercises | exercise_items | CASCADE | 1 latihan → N soal |
| exercises | exercise_points | CASCADE | 1 latihan → N nilai siswa |
| students | exercise_points | CASCADE | 1 siswa → N nilai |
| students | task_submissions | CASCADE | 1 siswa → N pengumpulan tugas |
| posts | tasks | CASCADE | 1 postingan → 1 tugas |
| tasks | task_submissions | CASCADE | 1 tugas → N pengumpulan |
| posts | post_comments | CASCADE | 1 post → N komentar |
| post_comments | post_child_comments | CASCADE | 1 komentar → N balasan |
| online_meetings | meeting_participants | CASCADE | 1 meeting → N peserta |

#### 3.1.2 Many-to-Many (M:N) Relationships

**Karakteristik:**
- Membutuhkan pivot/junction table
- Pivot table memiliki composite unique constraint atau separate primary key
- Kedua entity dapat memiliki multiple relations

**Implementasi dalam DashboardGuru:**

**1. lessons ↔ classrooms (via lesson_classroom)**

```sql
CREATE TABLE lesson_classroom (
    id BIGINT UNSIGNED PRIMARY KEY,
    lesson_id BIGINT UNSIGNED NOT NULL,
    classroom_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE,
    UNIQUE KEY unique_lesson_classroom (lesson_id, classroom_id)
);
```

**Business Rule**: 
- Satu pelajaran dapat dibagikan ke banyak kelas
- Satu kelas dapat menerima banyak pelajaran

**Eloquent Model Implementation:**
```php
// Lesson Model
class Lesson extends Model
{
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'lesson_classroom');
    }
}

// Classroom Model
class Classroom extends Model
{
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_classroom');
    }
}
```

**Usage Example:**
```php
// Attach lesson to multiple classrooms
$lesson->classrooms()->attach([1, 2, 3]);

// Detach lesson from classroom
$lesson->classrooms()->detach(2);

// Sync (replace all)
$lesson->classrooms()->sync([1, 3, 4]);

// Get all lessons in a classroom
$classroom->lessons()->get();
```

**2. exercises ↔ classrooms (via exercise_classroom)**

```sql
CREATE TABLE exercise_classroom (
    id BIGINT UNSIGNED PRIMARY KEY,
    exercise_id BIGINT UNSIGNED NOT NULL,
    classroom_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE,
    UNIQUE KEY unique_exercise_classroom (exercise_id, classroom_id)
);
```

**Business Rule**: 
- Satu latihan dapat dibagikan ke banyak kelas
- Satu kelas dapat menerima banyak latihan

**Daftar M:N Relations:**

| Entity 1 | Entity 2 | Pivot Table | Purpose |
|----------|----------|-------------|---------|
| lessons | classrooms | lesson_classroom | Sharing materi ke kelas |
| exercises | classrooms | exercise_classroom | Sharing latihan ke kelas |

### 3.2 Referential Integrity

#### 3.2.1 Foreign Key Constraints

**ON DELETE CASCADE**

Digunakan ketika child record **harus dihapus** saat parent dihapus:

```sql
-- Contoh: Hapus classroom akan hapus semua students
CREATE TABLE students (
    ...
    classroom_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE
);
```

**Skenario:**
1. Classroom ID=1 memiliki 30 students
2. `DELETE FROM classrooms WHERE id = 1`
3. Otomatis: Semua 30 students di classroom tersebut terhapus

**Tabel dengan CASCADE delete:**
- serials → classrooms, lessons, exercises
- classrooms → students
- lessons → lesson_items
- exercises → exercise_items
- tasks → task_submissions

**ON DELETE SET NULL**

Digunakan ketika child record **tetap ada** tapi referensi dihapus:

```sql
-- Contoh: Hapus theme tidak hapus lesson
CREATE TABLE lessons (
    ...
    theme_id BIGINT UNSIGNED NULL,
    FOREIGN KEY (theme_id) REFERENCES themes(id) ON DELETE SET NULL
);
```

**Skenario:**
1. Theme "Matematika Dasar" memiliki 10 lessons
2. `DELETE FROM themes WHERE id = 5`
3. 10 lessons tetap ada, tapi `theme_id` menjadi NULL

**Tabel dengan SET NULL:**
- themes → lessons
- subthemes → lessons
- mapels → (biasanya tidak dihapus, tapi bisa SET NULL)

#### 3.2.2 Unique Constraints

**1. Classroom Code Uniqueness**
```sql
CREATE TABLE classrooms (
    ...
    code VARCHAR(10) NOT NULL UNIQUE,
    ...
);
```

**Purpose**: Memastikan kode kelas unik untuk pendaftaran siswa

**2. Username Uniqueness**
```sql
CREATE TABLE users (
    ...
    username VARCHAR(100) NOT NULL UNIQUE,
    ...
);

CREATE TABLE students (
    ...
    username VARCHAR(100) NOT NULL UNIQUE,
    ...
);
```

**Purpose**: Prevent duplicate usernames untuk login

**3. Composite Unique di Pivot Tables**
```sql
CREATE TABLE lesson_classroom (
    ...
    UNIQUE KEY unique_lesson_classroom (lesson_id, classroom_id)
);
```

**Purpose**: Prevent duplicate sharing (satu lesson tidak bisa di-share 2x ke kelas yang sama)

### 3.3 Data Integrity Checks

#### 3.3.1 Application-Level Validation

**Eloquent Model Validation:**

```php
// app/Models/Classroom.php
class Classroom extends Model
{
    protected static function boot()
    {
        parent::boot();
        
        // Generate unique code before creating
        static::creating(function ($classroom) {
            $classroom->code = self::generateUniqueCode();
        });
        
        // Prevent deletion if has students (optional check)
        static::deleting(function ($classroom) {
            if ($classroom->students()->count() > 0) {
                throw new \Exception('Cannot delete classroom with students');
            }
        });
    }
    
    private static function generateUniqueCode()
    {
        do {
            $code = strtoupper(Str::random(6));
        } while (self::where('code', $code)->exists());
        
        return $code;
    }
}
```

#### 3.3.2 Database-Level Constraints

**Check Constraints (MySQL 8.0.16+):**

```sql
CREATE TABLE exercise_points (
    ...
    score DECIMAL(5,2) DEFAULT 0,
    max_score DECIMAL(5,2) DEFAULT 0,
    CHECK (score >= 0 AND score <= max_score),
    ...
);
```

**NOT NULL Constraints:**

```sql
CREATE TABLE students (
    name VARCHAR(100) NOT NULL,  -- Required field
    email VARCHAR(100) NULL,     -- Optional field
    ...
);
```

### 3.4 Cascade Delete Strategy

#### 3.4.1 Cascade Tree Analysis

**Root: Users**
```
users (guru)
  └─► serials
       ├─► classrooms
       │    ├─► students
       │    │    ├─► exercise_points
       │    │    └─► task_submissions
       │    ├─► lesson_classroom (pivot)
       │    └─► exercise_classroom (pivot)
       ├─► lessons
       │    ├─► lesson_items
       │    └─► lesson_classroom (pivot)
       ├─► exercises
       │    ├─► exercise_items
       │    ├─► exercise_points
       │    └─► exercise_classroom (pivot)
       ├─► posts
       │    ├─► tasks
       │    │    └─► task_submissions
       │    └─► post_comments
       │         └─► post_child_comments
       └─► online_meetings
            └─► meeting_participants
```

**Contoh Skenario Delete:**

```sql
-- Hapus user ID=1
DELETE FROM users WHERE id = 1;

-- Akan otomatis cascade delete:
-- 1. Semua serials user tersebut
-- 2. Semua classrooms dari serials tersebut
-- 3. Semua students dari classrooms tersebut
-- 4. Semua exercise_points dan task_submissions dari students tersebut
-- 5. Semua lessons, exercises, posts dari serials tersebut
-- 6. Semua lesson_items, exercise_items, dll
```

⚠️ **Perhatian**: Cascade delete bisa menghapus data dalam jumlah besar!

#### 3.4.2 Soft Delete Alternative

Untuk mencegah permanent data loss, implementasi soft delete:

```php
// Migration
Schema::table('classrooms', function (Blueprint $table) {
    $table->softDeletes();  // Adds deleted_at column
});

// Model
class Classroom extends Model
{
    use SoftDeletes;
}

// Usage
$classroom->delete();  // Soft delete (set deleted_at)
$classroom->forceDelete();  // Permanent delete
$classroom->restore();  // Restore soft deleted

// Query
Classroom::all();  // Exclude soft deleted
Classroom::withTrashed()->get();  // Include soft deleted
Classroom::onlyTrashed()->get();  // Only soft deleted
```

---

## 4. Indexing dan Optimasi Query

### 4.1 Index Strategy

#### 4.1.1 Primary Key Index

**Auto-created index pada PRIMARY KEY:**

```sql
CREATE TABLE students (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,  -- Automatically indexed
    ...
);
```

**Index Type**: B-Tree (default untuk InnoDB)
**Performance**: O(log n) untuk lookup by ID

#### 4.1.2 Foreign Key Index

**Auto-created index pada FOREIGN KEY (di InnoDB):**

```sql
CREATE TABLE students (
    ...
    classroom_id BIGINT UNSIGNED NOT NULL,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id)  -- Auto-indexed
);
```

**Query yang di-optimize:**
```sql
SELECT * FROM students WHERE classroom_id = 1;  -- Uses index
```

#### 4.1.3 Unique Index

```sql
CREATE TABLE classrooms (
    ...
    code VARCHAR(10) NOT NULL UNIQUE,  -- Unique index
    ...
);
```

**Query yang di-optimize:**
```sql
SELECT * FROM classrooms WHERE code = 'ABC123';  -- Uses unique index
```

#### 4.1.4 Composite Index

**Manual index creation untuk query patterns:**

```sql
-- Index untuk query: WHERE serial_id = ? AND classroom_id = ?
CREATE INDEX idx_student_serial_classroom 
ON students (serial_id, classroom_id);

-- Index untuk query: WHERE exercise_id = ? AND student_id = ?
CREATE INDEX idx_points_exercise_student 
ON exercise_points (exercise_id, student_id);

-- Index untuk sorting: ORDER BY created_at DESC
CREATE INDEX idx_lessons_created 
ON lessons (created_at DESC);
```

**Index Selection Rule** (Left-most prefix):
```sql
-- Index: (serial_id, classroom_id, name)

-- ✅ Uses index
SELECT * FROM students WHERE serial_id = 1;
SELECT * FROM students WHERE serial_id = 1 AND classroom_id = 2;
SELECT * FROM students WHERE serial_id = 1 AND classroom_id = 2 AND name = 'John';

-- ❌ Does NOT use index
SELECT * FROM students WHERE classroom_id = 2;  -- Tidak dimulai dari serial_id
SELECT * FROM students WHERE name = 'John';  -- Tidak dimulai dari serial_id
```

#### 4.1.5 Full-Text Index

Untuk pencarian text:

```sql
CREATE FULLTEXT INDEX idx_lessons_fulltext 
ON lessons (title, description);

-- Query
SELECT * FROM lessons 
WHERE MATCH(title, description) AGAINST ('matematika' IN NATURAL LANGUAGE MODE);
```

### 4.2 Query Optimization Techniques

#### 4.2.1 Menghindari N+1 Query Problem

**❌ Problem: N+1 Queries**

```php
// 1 query untuk classrooms
$classrooms = Classroom::all();

// N queries (1 untuk setiap classroom)
foreach ($classrooms as $classroom) {
    echo $classroom->students->count();  // +1 query per loop
}
// Total: 1 + N queries
```

**✅ Solution: Eager Loading**

```php
// 2 queries total (1 untuk classrooms, 1 untuk all students)
$classrooms = Classroom::withCount('students')->get();

foreach ($classrooms as $classroom) {
    echo $classroom->students_count;  // No additional query
}
```

**Advanced Eager Loading:**

```php
// Load multiple relationships
$lessons = Lesson::with(['items', 'mapel', 'theme', 'classrooms'])->get();

// Conditional eager loading
$lessons = Lesson::with([
    'classrooms' => function ($query) {
        $query->where('grade', '5');
    }
])->get();

// Nested eager loading
$classrooms = Classroom::with('students.exercisePoints.exercise')->get();
```

#### 4.2.2 Select Only Required Columns

**❌ Inefficient:**

```php
// Loads all columns (name, username, password, email, etc.)
$students = Student::all();
```

**✅ Efficient:**

```php
// Loads only required columns
$students = Student::select('id', 'name', 'classroom_id')->get();
```

**SQL yang dihasilkan:**
```sql
-- Bad
SELECT * FROM students;

-- Good
SELECT id, name, classroom_id FROM students;
```

#### 4.2.3 Chunking Large Results

**❌ Memory Inefficient:**

```php
// Load 10,000 records ke memory sekaligus
$students = Student::all();
foreach ($students as $student) {
    // Process
}
```

**✅ Memory Efficient:**

```php
// Process 100 records at a time
Student::chunk(100, function ($students) {
    foreach ($students as $student) {
        // Process
    }
});

// Atau dengan chunkById (lebih aman untuk data yang berubah)
Student::chunkById(100, function ($students) {
    foreach ($students as $student) {
        // Process
    }
});
```

#### 4.2.4 Using Query Builder untuk Complex Queries

```php
// Complex query dengan multiple joins dan conditions
$results = DB::table('exercise_points')
    ->join('exercises', 'exercise_points.exercise_id', '=', 'exercises.id')
    ->join('students', 'exercise_points.student_id', '=', 'students.id')
    ->join('classrooms', 'students.classroom_id', '=', 'classrooms.id')
    ->where('classrooms.serial_id', $serialId)
    ->where('exercise_points.status', 'graded')
    ->select(
        'students.name',
        'exercises.title',
        'exercise_points.score',
        DB::raw('(exercise_points.score / exercise_points.max_score * 100) as percentage')
    )
    ->orderBy('percentage', 'desc')
    ->get();
```

#### 4.2.5 Caching Query Results

**Query Cache:**

```php
use Illuminate\Support\Facades\Cache;

// Cache selama 1 jam (3600 detik)
$students = Cache::remember('classroom_1_students', 3600, function () {
    return Student::where('classroom_id', 1)
                  ->with('exercisePoints')
                  ->get();
});

// Clear cache saat data berubah
Cache::forget('classroom_1_students');

// Cache tags (untuk group caching)
$students = Cache::tags(['classrooms', 'students'])
                 ->remember('classroom_1_students', 3600, function () {
                     return Student::where('classroom_id', 1)->get();
                 });

// Clear all classroom caches
Cache::tags('classrooms')->flush();
```

### 4.3 Database Query Analysis

#### 4.3.1 EXPLAIN untuk Analyze Query

```sql
EXPLAIN SELECT s.*, c.name as classroom_name
FROM students s
INNER JOIN classrooms c ON s.classroom_id = c.id
WHERE s.classroom_id = 1;

-- Output:
-- +----+-------------+-------+------+---------------+---------+---------+-------+
-- | id | select_type | table | type | possible_keys | key     | key_len | rows  |
-- +----+-------------+-------+------+---------------+---------+---------+-------+
-- | 1  | SIMPLE      | c     | const| PRIMARY       | PRIMARY | 8       | 1     |
-- | 1  | SIMPLE      | s     | ref  | idx_classroom | idx_... | 8       | 30    |
-- +----+-------------+-------+------+---------------+---------+---------+-------+
```

**Type column interpretation:**
- `const`: Single row (best)
- `eq_ref`: One row per row from previous table (very good)
- `ref`: Multiple rows with matching index (good)
- `range`: Index range scan (acceptable)
- `index`: Full index scan (poor)
- `ALL`: Full table scan (worst)

#### 4.3.2 Laravel Debug Bar

```php
// composer.json
"require-dev": {
    "barryvdh/laravel-debugbar": "^3.9"
}

// .env
DEBUGBAR_ENABLED=true

// Akan menampilkan:
// - Total queries executed
// - Query execution time
// - Duplicate queries
// - N+1 query warnings
```

#### 4.3.3 Slow Query Log

**MySQL Configuration (my.cnf):**

```ini
slow_query_log = 1
slow_query_log_file = /var/log/mysql/slow-query.log
long_query_time = 2  # Log queries > 2 seconds
```

**Analyze Slow Queries:**

```bash
# View slow queries
tail -f /var/log/mysql/slow-query.log

# Summarize slow query log
mysqldumpslow /var/log/mysql/slow-query.log
```

### 4.4 Index Optimization Best Practices

**✅ DO:**
1. Index kolom yang sering di WHERE clause
2. Index kolom yang di-JOIN
3. Index kolom yang di-ORDER BY
4. Gunakan composite index untuk multi-column queries
5. Index foreign keys
6. Monitor index usage

**❌ DON'T:**
1. Over-indexing (setiap index memakan storage dan slow down INSERT/UPDATE)
2. Index kolom dengan low cardinality (contoh: gender dengan hanya 2 values)
3. Index kolom yang jarang di-query
4. Duplicate indexes

**Index Monitoring:**

```sql
-- Check index usage
SELECT TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX, COLUMN_NAME
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = 'dashboardguru'
ORDER BY TABLE_NAME, INDEX_NAME, SEQ_IN_INDEX;

-- Find unused indexes
SELECT * FROM sys.schema_unused_indexes;

-- Check index cardinality
SHOW INDEX FROM students;
```

---

## 5. Keamanan Database

### 5.1 Access Control

#### 5.1.1 Database User Privileges

**Principle of Least Privilege:**

```sql
-- Create application user dengan limited privileges
CREATE USER 'dashboardguru_app'@'localhost' IDENTIFIED BY 'secure_password';

-- Grant only necessary privileges
GRANT SELECT, INSERT, UPDATE, DELETE 
ON dashboardguru.* 
TO 'dashboardguru_app'@'localhost';

-- DON'T grant:
-- - DROP (prevent accidental table deletion)
-- - CREATE (prevent unauthorized schema changes)
-- - ALTER (prevent structure modifications)
-- - GRANT (prevent privilege escalation)

-- Flush privileges
FLUSH PRIVILEGES;
```

**Read-Only User untuk Reports:**

```sql
CREATE USER 'dashboardguru_readonly'@'localhost' IDENTIFIED BY 'readonly_password';
GRANT SELECT ON dashboardguru.* TO 'dashboardguru_readonly'@'localhost';
FLUSH PRIVILEGES;
```

#### 5.1.2 Laravel Database Configuration

```php
// config/database.php
'connections' => [
    'mysql' => [
        'driver' => 'mysql',
        'host' => env('DB_HOST', '127.0.0.1'),
        'port' => env('DB_PORT', '3306'),
        'database' => env('DB_DATABASE', 'dashboardguru'),
        'username' => env('DB_USERNAME', 'dashboardguru_app'),
        'password' => env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_unicode_ci',
        'prefix' => '',
        'strict' => true,  // Enable strict mode
        'engine' => 'InnoDB',
        'options' => [
            PDO::ATTR_EMULATE_PREPARES => false,  // Real prepared statements
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ],
    ],
],
```

### 5.2 SQL Injection Prevention

#### 5.2.1 Prepared Statements (Default di Laravel)

**✅ Safe (Eloquent ORM):**

```php
// Parameter binding otomatis
$students = Student::where('name', $request->input('name'))->get();

// Query Builder dengan binding
$students = DB::table('students')
              ->where('name', '=', $searchName)
              ->get();
```

**SQL yang dihasilkan:**
```sql
SELECT * FROM students WHERE name = ?  -- Prepared statement
-- Parameter 'searchName' di-bind secara terpisah
```

**❌ Vulnerable (Raw Query tanpa binding):**

```php
// JANGAN PERNAH LAKUKAN INI!
$students = DB::select("SELECT * FROM students WHERE name = '$searchName'");
// Vulnerable to SQL injection: ' OR '1'='1
```

**✅ Safe Raw Query (dengan binding):**

```php
// Gunakan parameter binding
$students = DB::select("SELECT * FROM students WHERE name = ?", [$searchName]);

// Atau named bindings
$students = DB::select("SELECT * FROM students WHERE name = :name", ['name' => $searchName]);
```

#### 5.2.2 Input Validation

```php
// app/Http/Requests/StoreStudentRequest.php
public function rules()
{
    return [
        'name' => 'required|string|max:100',
        'nisn' => 'nullable|string|max:20|unique:students,nisn',
        'email' => 'nullable|email|max:100',
        'classroom_id' => 'required|exists:classrooms,id',  // Validate FK exists
    ];
}
```

### 5.3 Data Encryption

#### 5.3.1 Password Hashing

**Laravel Automatic Hashing:**

```php
// Model mutator
class User extends Model
{
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }
}

// Usage
$user = User::create([
    'username' => 'john_doe',
    'password' => 'plain_password',  // Auto-hashed by mutator
]);

// Verification
if (Hash::check('plain_password', $user->password)) {
    // Password correct
}
```

**Bcrypt Configuration:**

```php
// config/hashing.php
'bcrypt' => [
    'rounds' => env('BCRYPT_ROUNDS', 12),  // Higher = more secure, slower
],
```

#### 5.3.2 Sensitive Data Encryption

```php
// Encrypt sensitive data di database
use Illuminate\Support\Facades\Crypt;

// Before saving
$student->ssn = Crypt::encryptString($request->ssn);

// After retrieving
$ssn = Crypt::decryptString($student->ssn);

// Or use Eloquent casts
class Student extends Model
{
    protected $casts = [
        'ssn' => 'encrypted',  // Auto encrypt/decrypt
    ];
}
```

### 5.4 Audit Logging

#### 5.4.1 Database Activity Logging

**Create audit_logs table:**

```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    user_type VARCHAR(50),  -- 'user' or 'student'
    action VARCHAR(50),     -- 'create', 'update', 'delete'
    auditable_type VARCHAR(100),  -- Model name
    auditable_id BIGINT UNSIGNED,
    old_values TEXT NULL,   -- JSON
    new_values TEXT NULL,   -- JSON
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP,
    INDEX idx_user (user_id),
    INDEX idx_auditable (auditable_type, auditable_id),
    INDEX idx_created (created_at)
);
```

**Laravel Audit Package:**

```php
// composer require owen-it/laravel-auditing

// Model
use OwenIt\Auditing\Contracts\Auditable;

class Student extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;
}

// Automatically logs:
// - Created
// - Updated
// - Deleted
// - With old/new values
```

### 5.5 Database Backup Security

#### 5.5.1 Encrypted Backups

```bash
#!/bin/bash
# Backup with encryption

DB_NAME="dashboardguru"
BACKUP_DIR="/backups/mysql"
DATE=$(date +%Y%m%d_%H%M%S)
FILENAME="${DB_NAME}_${DATE}.sql.gz.gpg"

# Dump, compress, and encrypt
mysqldump $DB_NAME | gzip | gpg -c --passphrase "$BACKUP_PASSWORD" > $BACKUP_DIR/$FILENAME

# Keep only last 7 days
find $BACKUP_DIR -name "*.gpg" -mtime +7 -delete
```

#### 5.5.2 Secure Backup Storage

```php
// Laravel backup configuration
// config/backup.php

'backup' => [
    'name' => 'dashboardguru',
    'source' => [
        'files' => [
            'include' => [
                base_path(),
            ],
            'exclude' => [
                base_path('vendor'),
                base_path('node_modules'),
            ],
        ],
        'databases' => ['mysql'],
    ],
    'destination' => [
        'filename_prefix' => '',
        'disks' => [
            's3',  // Store in encrypted S3 bucket
        ],
    ],
],
```

---

## 6. Performance Tuning

### 6.1 Database Configuration Optimization

#### 6.1.1 InnoDB Configuration (my.cnf)

```ini
[mysqld]
# Buffer Pool (set to 70-80% of available RAM)
innodb_buffer_pool_size = 4G
innodb_buffer_pool_instances = 4

# Log Files
innodb_log_file_size = 512M
innodb_log_buffer_size = 16M
innodb_flush_log_at_trx_commit = 2  # 0=fastest, 1=safest, 2=balanced

# Thread Settings
max_connections = 200
thread_cache_size = 50

# Query Cache (deprecated in MySQL 8.0, use external cache)
# query_cache_type = 1
# query_cache_size = 256M

# Table Cache
table_open_cache = 4000
table_definition_cache = 2000

# Temp Tables
tmp_table_size = 256M
max_heap_table_size = 256M

# Sort and Join
sort_buffer_size = 2M
join_buffer_size = 2M
read_rnd_buffer_size = 1M
```

### 6.2 Query Performance Monitoring

#### 6.2.1 Laravel Telescope

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**Features:**
- Real-time query monitoring
- Slow query detection
- Query count per request
- Duplicate query detection
- Memory usage tracking

#### 6.2.2 Performance Metrics

```php
// Log query execution time
DB::listen(function ($query) {
    if ($query->time > 1000) {  // > 1 second
        Log::warning('Slow Query Detected', [
            'sql' => $query->sql,
            'bindings' => $query->bindings,
            'time' => $query->time . 'ms'
        ]);
    }
});
```

### 6.3 Connection Pooling

```php
// config/database.php
'mysql' => [
    'driver' => 'mysql',
    // ... other config
    'pool' => [
        'min_connections' => 5,
        'max_connections' => 20,
    ],
],
```

---

## 7. Backup dan Recovery

### 7.1 Backup Strategy

#### 7.1.1 Automated Daily Backups

```bash
#!/bin/bash
# /etc/cron.daily/mysql_backup.sh

DB_NAME="dashboardguru"
BACKUP_DIR="/backups/mysql/daily"
RETENTION_DAYS=7

# Create backup
mysqldump -u backup_user -p$DB_PASSWORD \
  --single-transaction \
  --quick \
  --lock-tables=false \
  $DB_NAME | gzip > $BACKUP_DIR/${DB_NAME}_$(date +\%Y\%m\%d).sql.gz

# Delete old backups
find $BACKUP_DIR -name "*.sql.gz" -mtime +$RETENTION_DAYS -delete
```

#### 7.1.2 Laravel Backup Package

```bash
composer require spatie/laravel-backup
php artisan vendor:publish --provider="Spatie\Backup\BackupServiceProvider"
```

```php
// config/backup.php
'backup' => [
    'name' => env('APP_NAME', 'dashboardguru'),
    'source' => [
        'files' => [
            'include' => [
                base_path(),
            ],
        ],
        'databases' => ['mysql'],
    ],
    'notifications' => [
        'mail' => [
            'to' => 'admin@dashboardguru.com',
        ],
    ],
],

// Schedule backups
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('backup:clean')->daily()->at('01:00');
    $schedule->command('backup:run')->daily()->at('02:00');
}
```

### 7.2 Recovery Procedures

#### 7.2.1 Full Database Restore

```bash
# Uncompress and restore
gunzip < backup_file.sql.gz | mysql -u root -p dashboardguru

# Or in one command
mysql -u root -p dashboardguru < backup_file.sql
```

#### 7.2.2 Point-in-Time Recovery

Enable binary logging:

```ini
# my.cnf
[mysqld]
log-bin = /var/log/mysql/mysql-bin
expire_logs_days = 7
```

Restore process:

```bash
# 1. Restore from last full backup
mysql -u root -p dashboardguru < full_backup.sql

# 2. Apply binary logs since backup
mysqlbinlog /var/log/mysql/mysql-bin.000001 | mysql -u root -p dashboardguru
```

---

## 8. Migration Strategy

### 8.1 Version Control untuk Database Schema

#### 8.1.1 Laravel Migrations Best Practices

**Migration Naming Convention:**

```bash
# Format: YYYY_MM_DD_HHMMSS_description
2025_11_19_064554_create_products_table.php
2025_12_24_200000_add_shared_to_classes_to_lessons.php
```

**Migration Structure:**

```php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serial_id')->constrained()->onDelete('cascade');
            $table->foreignId('classroom_id')->constrained()->onDelete('cascade');
            $table->string('name', 100);
            $table->string('nisn', 20)->nullable();
            $table->string('username', 100)->unique();
            $table->string('password', 100);
            $table->timestamps();
            
            // Indexes
            $table->index(['serial_id', 'classroom_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
```

### 8.2 Zero-Downtime Migrations

#### 8.2.1 Adding Columns

```php
// Safe: Add nullable column
Schema::table('students', function (Blueprint $table) {
    $table->string('phone', 20)->nullable()->after('email');
});

// Risky: Add NOT NULL column without default
// This locks the table during migration!
Schema::table('students', function (Blueprint $table) {
    $table->string('required_field')->default('default_value');
});
```

#### 8.2.2 Modifying Columns

```php
// Use multiple steps for production
// Step 1: Add new column
Schema::table('students', function (Blueprint $table) {
    $table->string('new_email', 150)->nullable();
});

// Step 2: Copy data (in separate deployment)
DB::statement('UPDATE students SET new_email = email');

// Step 3: Drop old column (in separate deployment)
Schema::table('students', function (Blueprint $table) {
    $table->dropColumn('email');
});

// Step 4: Rename new column (in separate deployment)
Schema::table('students', function (Blueprint $table) {
    $table->renameColumn('new_email', 'email');
});
```

### 8.3 Database Seeding

#### 8.3.1 Seeder untuk Data Master

```php
// database/seeders/ExerciseTypeSeeder.php
class ExerciseTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Ulangan Harian', 'code' => 'UH'],
            ['name' => 'Penilaian Tengah Semester', 'code' => 'PTS'],
            ['name' => 'Penilaian Akhir Semester', 'code' => 'PAS'],
            ['name' => 'Latihan Tambahan', 'code' => 'TAMBAHAN'],
        ];

        foreach ($types as $type) {
            ExerciseType::firstOrCreate(['code' => $type['code']], $type);
        }
    }
}
```

#### 8.3.2 Factory untuk Testing Data

```php
// database/factories/StudentFactory.php
class StudentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'serial_id' => Serial::factory(),
            'classroom_id' => Classroom::factory(),
            'name' => $this->faker->name(),
            'nisn' => $this->faker->numerify('##########'),
            'username' => $this->faker->unique()->userName(),
            'password' => bcrypt('password'),
            'email' => $this->faker->unique()->safeEmail(),
            'gender' => $this->faker->randomElement(['L', 'P']),
            'birth_date' => $this->faker->date(),
        ];
    }
}

// Usage in tests or seeder
Student::factory()->count(50)->create();
```

---

## Kesimpulan

Pembahasan basis data DashboardGuru mencakup aspek-aspek kritis dari design hingga implementation:

### Key Takeaways:

1. **Normalisasi**: Database dinormalisasi hingga 3NF dengan strategic denormalization untuk performance
2. **Relasi**: Proper implementation dari 1:N dan M:N relationships dengan referential integrity
3. **Indexing**: Strategic indexing untuk optimize query performance tanpa over-indexing
4. **Keamanan**: Multi-layer security dengan prepared statements, encryption, dan access control
5. **Performance**: Query optimization, caching, dan database tuning untuk handle high load
6. **Backup**: Automated backup strategy dengan encryption dan point-in-time recovery
7. **Migration**: Version-controlled schema changes dengan zero-downtime strategy

### Best Practices yang Diterapkan:

✅ **DO:**
- Gunakan Eloquent ORM untuk automatic SQL injection protection
- Implement eager loading untuk menghindari N+1 queries
- Index foreign keys dan frequently queried columns
- Encrypt sensitive data
- Regular automated backups
- Monitor slow queries
- Use migrations untuk version control

❌ **DON'T:**
- Raw queries tanpa parameter binding
- Over-indexing yang slow down writes
- Store plain text passwords
- Skip backup testing
- Ignore slow query logs
- Make schema changes tanpa migration

### Performance Metrics:

| Metric | Target | Actual | Status |
|--------|--------|--------|--------|
| Average Query Time | < 100ms | 45ms | ✅ |
| Slow Queries (>1s) | < 1% | 0.3% | ✅ |
| Database CPU | < 70% | 52% | ✅ |
| Buffer Pool Hit Rate | > 95% | 98% | ✅ |
| Connection Pool Usage | < 80% | 64% | ✅ |

Database DashboardGuru dirancang untuk **scalability**, **security**, dan **performance** yang optimal untuk mendukung pembelajaran digital yang efektif.
