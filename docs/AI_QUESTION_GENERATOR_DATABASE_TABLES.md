# AI Question Generator - Database Tables Reference

Dokumentasi lengkap tentang tabel-tabel database yang digunakan oleh fitur **AI Question Generator** dalam sistem DashboardGuru.

## 📋 Daftar Tabel - Quick Reference

| No  | Tabel                  | Fungsi                             | Relasi Utama                               |
| --- | ---------------------- | ---------------------------------- | ------------------------------------------ |
| 1   | **exercises**          | Container/kumpulan soal            | exercise_items, exercise_types, serials    |
| 2   | **exercise_items**     | Pertanyaan individual              | exercises, exercise_types, exercise_models |
| 3   | **exercise_types**     | Tipe soal (UH/SL)                  | exercises, exercise_items                  |
| 4   | **exercise_models**    | Model soal (MCQ/Essay)             | exercise_items                             |
| 5   | **posts**              | Sumber materi dari guru            | mapels, serials, users                     |
| 6   | **lessons**            | Sumber materi dari admin/kurikulum | mapels, themes                             |
| 7   | **mapels**             | Mata pelajaran                     | posts, lessons, exercises                  |
| 8   | **serials**            | License/serial activation          | users, products, exercises                 |
| 9   | **classrooms**         | Kelas untuk berbagi soal           | serials, students                          |
| 10  | **exercise_points**    | Jawaban siswa & nilai              | exercises, students, exercise_items        |
| 11  | **quiz_activity_logs** | Log aktivitas pengerjaan soal      | students, exercises                        |

---

## 🎯 Tabel Utama (Core Tables)

### 1. exercises

**Purpose:** Menyimpan kumpulan soal/latihan yang dibuat oleh guru atau AI

```sql
CREATE TABLE exercises (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    serial_id BIGINT NOT NULL,
    lesson_id BIGINT,
    exercise_type_id BIGINT NOT NULL,
    mapel_id BIGINT,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_admin TINYINT DEFAULT 0,           -- 1=admin, 0=guru
    created_by BIGINT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (serial_id) REFERENCES serials(id),
    FOREIGN KEY (exercise_type_id) REFERENCES exercise_types(id)
);
```

**Kolom Penting:**

- `id`: Primary key
- `serial_id`: Link ke serial/license (multi-tenant)
- `exercise_type_id`: Jenis latihan (UH/SL)
- `title`: Nama latihan (dari AI atau manual)
- `is_admin`: Flag apakah dibuat admin (0=guru, 1=admin)
- `created_by`: User ID yang membuat

**Saat AI Generator:**

- Soal yang dihasilkan AI disimpan dengan `is_admin = 0` (guru-generated)
- Satu exercise = satu set soal yang bisa berisi 5-20 pertanyaan

---

### 2. exercise_items

**Purpose:** Menyimpan setiap pertanyaan individual dalam latihan

```sql
CREATE TABLE exercise_items (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    exercise_id BIGINT NOT NULL,
    exercise_type_id BIGINT NOT NULL,
    exercise_model_id BIGINT,
    no INT,
    question LONGTEXT NOT NULL,         -- Pertanyaan (dari AI)
    selection JSON,                      -- Opsi jawaban (JSON array)
    answer VARCHAR(255),                 -- Kunci jawaban (dari AI)
    point DECIMAL(8,2),
    illustration LONGTEXT,               -- Gambar/deskripsi soal
    is_user TINYINT DEFAULT 0,           -- 1=dibuat user, 0=admin
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE,
    FOREIGN KEY (exercise_type_id) REFERENCES exercise_types(id),
    FOREIGN KEY (exercise_model_id) REFERENCES exercise_models(id)
);
```

**Kolom Penting:**

- `exercise_id`: Foreign key ke exercises (1 exercise bisa punya banyak items)
- `question`: Isi soal (langsung dari response AI)
- `selection`: Opsi jawaban dalam format JSON (e.g., `["A. Pilihan 1", "B. Pilihan 2", ...]`)
- `answer`: Kunci jawaban (e.g., `"A"` atau `"option_1"`)
- `illustration`: Deskripsi/konteks soal (dari material source)
- `point`: Poin untuk soal tersebut

**Saat AI Generator:**

- Setiap soal yang dihasilkan AI = 1 row di tabel ini
- Query: `INSERT INTO exercise_items (exercise_id, question, selection, answer, point, ...)`
- Soal bisa langsung digunakan atau diedit sebelum save

---

### 3. exercise_types

**Purpose:** Menentukan jenis latihan (UH=Ulangan Harian, SL=Studi Lanjut, etc)

```sql
CREATE TABLE exercise_types (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    kode VARCHAR(10) UNIQUE,             -- UH, SL, QUIZ, TEST
    name VARCHAR(100),                   -- Ulangan Harian, Studi Lanjut
    description TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Kolom Penting:**

- `kode`: Kode unik (UH, SL, QUIZ, TEST)
- `name`: Nama deskriptif

**Contoh Data:**

- `kode=UH, name='Ulangan Harian'`
- `kode=SL, name='Studi Lanjut'`
- `kode=QUIZ, name='Kuis'`

---

### 4. exercise_models

**Purpose:** Template/model soal yang tersedia (Pilihan Ganda, Essay, Benar/Salah, Isian)

```sql
CREATE TABLE exercise_models (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE,            -- Pilihan Ganda, Essay, Benar/Salah, Isian Singkat
    description TEXT,
    format JSON,                          -- Format template soal
    auto_grading TINYINT DEFAULT 0,      -- 1=bisa auto-grade, 0=manual
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Kolom Penting:**

- `name`: Jenis model (Pilihan Ganda, Essay, True/False, Short Answer)
- `auto_grading`: 1 jika bisa di-auto-grade, 0 jika harus manual

**Contoh Data:**

- `name='Pilihan Ganda', auto_grading=1`
- `name='Essay', auto_grading=0`
- `name='Benar/Salah', auto_grading=1`
- `name='Isian Singkat', auto_grading=1`

---

## 📚 Tabel Sumber Materi (Material Source)

### 5. posts

**Purpose:** Materi yang di-upload guru dan bisa menjadi sumber untuk AI generation

```sql
CREATE TABLE posts (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    serial_id BIGINT NOT NULL,
    user_id BIGINT NOT NULL,
    mapel_id BIGINT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description LONGTEXT,
    attachment VARCHAR(255),             -- File path
    link VARCHAR(255),                   -- External URL
    is_task TINYINT DEFAULT 0,           -- 1=tugas, 0=materi
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (serial_id) REFERENCES serials(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (mapel_id) REFERENCES mapels(id)
);
```

**Kolom Penting:**

- `title`: Judul materi
- `description`: Konten/deskripsi (sumber untuk AI)
- `attachment`: File yang di-upload (PDF, DOC, dll)
- `link`: Link eksternal
- `is_task`: Jika 1 = tugas, jika 0 = materi pembelajaran

**Saat AI Generator:**

- Guru bisa pilih post sebagai sumber material
- AI akan membaca `description` + `title` sebagai konteks
- Query: `SELECT * FROM posts WHERE serial_id = ? AND id = ? AND is_task = 0`

---

### 6. lessons

**Purpose:** Materi pembelajaran dari admin/kurikulum yang bisa menjadi sumber AI generation

```sql
CREATE TABLE lessons (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    mapel_id BIGINT NOT NULL,
    theme_id BIGINT,
    serial_id BIGINT,
    name VARCHAR(255) NOT NULL,
    description LONGTEXT,
    grade INT,
    semester INT,
    category INT,                        -- 1=materi pembelajaran
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (mapel_id) REFERENCES mapels(id),
    FOREIGN KEY (serial_id) REFERENCES serials(id)
);
```

**Kolom Penting:**

- `name`: Nama pelajaran
- `description`: Konten pelajaran (sumber untuk AI)
- `mapel_id`: Mata pelajaran
- `category`: 1=materi pembelajaran, bisa nilai lain

**Saat AI Generator:**

- Guru bisa pilih lesson sebagai sumber material
- AI akan membaca `description` + `name` sebagai konteks
- Query: `SELECT l.*, m.name as mapel_name FROM lessons l JOIN mapels m ON l.mapel_id = m.id WHERE l.id = ? AND l.category = 1`

---

### 7. mapels

**Purpose:** Mata pelajaran (Subject/Course)

```sql
CREATE TABLE mapels (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) UNIQUE NOT NULL,  -- Matematika, Bahasa Indonesia, dll
    code VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

**Kolom Penting:**

- `name`: Nama mata pelajaran (Matematika, Bahasa Indonesia, Fisika, dll)
- `code`: Kode singkat

---

## 🔐 Tabel Kontrol & Akses

### 8. serials

**Purpose:** Serial/license yang mengontrol akses dan penggunaan fitur

```sql
CREATE TABLE serials (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL,
    product_id BIGINT NOT NULL,
    serial_code VARCHAR(50) UNIQUE,
    activated_at TIMESTAMP,
    active ENUM('yes', 'no') DEFAULT 'no',
    expired_at TIMESTAMP,
    usage_count INT DEFAULT 0,          -- Tracking penggunaan AI
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id)
);
```

**Kolom Penting:**

- `active`: `yes`/`no` untuk enable/disable fitur
- `user_id`: User (guru) yang punya serial ini
- `usage_count`: Tracking berapa kali menggunakan AI generator

**Saat AI Generator:**

- Sistem cek apakah `serials.active = 'yes'` sebelum allow AI generation
- Increment `usage_count` setiap AI generate soal

---

### 9. classrooms

**Purpose:** Kelas untuk berbagi soal yang dihasilkan AI kepada siswa

```sql
CREATE TABLE classrooms (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    serial_id BIGINT NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(10) UNIQUE,            -- Kode unik untuk join kelas
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (serial_id) REFERENCES serials(id)
);
```

**Kolom Penting:**

- `serial_id`: Serial pemilik kelas
- `name`: Nama kelas
- `code`: Kode join kelas (auto-generated)

**Saat AI Generator:**

- Setelah soal di-generate dan di-save, guru bisa pilih kelas mana yang dapat soal ini
- Link: `exercises` → `classroom_exercises` → `classrooms`

---

## 📊 Tabel Hasil & Tracking

### 10. exercise_points

**Purpose:** Menyimpan jawaban siswa, nilai, dan hasil scoring

```sql
CREATE TABLE exercise_points (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    exercise_id BIGINT NOT NULL,
    exercise_item_id BIGINT NOT NULL,
    student_id BIGINT NOT NULL,
    answer VARCHAR(255),                 -- Jawaban siswa
    is_correct TINYINT DEFAULT 0,        -- 1=benar, 0=salah
    exercise_point DECIMAL(8,2) DEFAULT 0,  -- Nilai/poin
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id),
    FOREIGN KEY (exercise_item_id) REFERENCES exercise_items(id),
    FOREIGN KEY (student_id) REFERENCES students(id)
);
```

**Kolom Penting:**

- `exercise_id`: Referensi ke latihan (soal dari AI)
- `student_id`: Siswa yang mengerjakan
- `answer`: Jawaban siswa
- `is_correct`: Hasil auto-grading (1=benar)
- `exercise_point`: Poin yang diperoleh

**Saat Siswa Kerjakan AI-Generated Soal:**

- Untuk soal MCQ/True-False/Short Answer: Otomatis di-score
- Query: `INSERT INTO exercise_points (exercise_id, exercise_item_id, student_id, answer, is_correct, exercise_point) VALUES (...)`

---

### 11. quiz_activity_logs

**Purpose:** Log aktivitas pengerjaan soal untuk monitoring dan anti-cheating

```sql
CREATE TABLE quiz_activity_logs (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    exercise_id BIGINT NOT NULL,
    student_id BIGINT NOT NULL,
    event_type VARCHAR(50),              -- start, submit, finish, pause
    duration_seconds INT,                -- Durasi pengerjaan
    suspicious_flag TINYINT DEFAULT 0,   -- 1=suspicious activity terdeteksi
    ip_address VARCHAR(45),
    created_at TIMESTAMP,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id),
    FOREIGN KEY (student_id) REFERENCES students(id)
);
```

**Kolom Penting:**

- `event_type`: Jenis event (start, submit, finish, pause, cheating detected)
- `duration_seconds`: Berapa lama siswa mengerjakan
- `suspicious_flag`: Flag jika ada indikasi kecurangan

---

## 🔄 Alur Data AI Question Generator

### Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│ GURU MEMULAI GENERATE SOAL DENGAN AI                        │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ SISTEM VALIDASI:                                             │
│ - CHECK: serials.active = 'yes'                             │
│ - CHECK: guru memiliki akses ke serial ini                  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ GURU PILIH SUMBER MATERI:                                   │
│ ├─ OPSI 1: Post (Materi Guru)                              │
│ │  └─ QUERY: SELECT * FROM posts                           │
│ │     WHERE serial_id = ? AND is_task = 0 LIMIT 1         │
│ │                                                           │
│ └─ OPSI 2: Lesson (Materi Admin)                           │
│    └─ QUERY: SELECT l.*, m.name FROM lessons l             │
│       JOIN mapels m WHERE l.id = ? AND l.category = 1     │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ EKSTRAK KONTEN MATERI:                                      │
│ ├─ material_title = posts.title / lessons.name             │
│ ├─ material_content = posts.description / lessons.description
│ └─ material_subject = mapels.name                          │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ GURU SET PARAMETER AI:                                      │
│ ├─ exercise_type: UH / SL / QUIZ / TEST                   │
│ ├─ model: Pilihan Ganda / Essay / True-False / Isian      │
│ ├─ difficulty: Mudah / Sedang / Sulit                     │
│ └─ jumlah_soal: 5, 10, 15, 20                            │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ SEND KE OPENAI API GPT-4o-mini:                            │
│ ├─ Prompt: Buatkan N soal [tipe] tentang [materi]         │
│ ├─ Context: [material_content + exercise_type]            │
│ ├─ Constraints: JSON format, terpisah per soal            │
│ └─ Model: gpt-4o-mini (efficient & fast)                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ TERIMA & PARSE RESPONSE DARI AI:                           │
│ ├─ Parse JSON response                                     │
│ ├─ Ekstrak: question, options[], answer, points          │
│ └─ Validasi format & length                               │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ GURU PREVIEW & EDIT SOAL (OPTIONAL):                       │
│ ├─ Lihat semua soal yang dihasilkan                        │
│ ├─ Edit question, options, answer, points                 │
│ ├─ Delete soal yang tidak perlu                           │
│ └─ Re-generate soal tertentu jika perlu                   │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ SIMPAN KE DATABASE (ATOMIC TRANSACTION):                   │
│                                                             │
│ STEP 1: INSERT INTO exercises                             │
│   VALUES (                                                 │
│     serial_id = guru.serial_id,                           │
│     exercise_type_id = [UH/SL/QUIZ],                      │
│     mapel_id = posts.mapel_id / lessons.mapel_id,         │
│     title = "Generated: " + material_title,               │
│     is_admin = 0,                                          │
│     created_at = NOW()                                    │
│   )                                                        │
│   RESULT: $exercise_id                                    │
│                                                             │
│ STEP 2: INSERT INTO exercise_items (LOOP per soal)       │
│   FOR EACH soal from AI response:                         │
│     INSERT VALUES (                                        │
│       exercise_id = $exercise_id,                         │
│       exercise_model_id = [Pilihan Ganda / Essay / dll], │
│       question = soal.question,                           │
│       selection = JSON.stringify(soal.options),           │
│       answer = soal.answer,                               │
│       point = soal.point,                                 │
│       is_user = 1                                         │
│     )                                                      │
│                                                             │
│ STEP 3: UPDATE serials                                    │
│   UPDATE serials SET                                      │
│     usage_count = usage_count + 1                         │
│   WHERE id = guru.serial_id                              │
│                                                             │
│ RESULT: $exercise_items_count soal tersimpan             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ GURU BAGIKAN KE KELAS (OPTIONAL):                          │
│ ├─ QUERY: SELECT * FROM classrooms                        │
│ │  WHERE serial_id = guru.serial_id                       │
│ │                                                          │
│ └─ CREATE classroom_exercise links:                       │
│    INSERT INTO classroom_exercises (exercise_id, classroom_id)
│    FOR EACH classroom selected                            │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ SISWA TERIMA NOTIFIKASI & AKSES SOAL:                      │
│ ├─ QUERY: SELECT * FROM exercises                         │
│ │  WHERE serial_id IN (classroom.serial_id)              │
│ │  AND classroom_id IN (siswa.classrooms)               │
│ │                                                          │
│ └─ QUERY: SELECT * FROM exercise_items                    │
│    WHERE exercise_id = ?                                  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ SISWA KERJAKAN SOAL:                                        │
│ ├─ event_log START: INSERT quiz_activity_logs            │
│ ├─ LOOP per soal:                                         │
│ │  ├─ INSERT exercise_points (jawaban siswa)            │
│ │  ├─ AUTO-GRADE (MCQ/True-False/Short):                │
│ │  │  - Compare: siswa.answer === exercise_items.answer │
│ │  │  - Set: is_correct = (match ? 1 : 0)              │
│ │  │  - Set: exercise_point = (is_correct ? poin : 0)  │
│ │  └─ MANUAL-GRADE (Essay):                             │
│ │     - Tunggu guru untuk score                          │
│ │                                                         │
│ └─ event_log SUBMIT: INSERT quiz_activity_logs          │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ LIHAT HASIL:                                                │
│ ├─ SISWA: Total score dari exercise_points               │
│ ├─ GURU: Laporan hasil siswa per soal                    │
│ └─ ANALYTICS: Rata-rata kelas, kesulitan per soal, dll  │
└─────────────────────────────────────────────────────────────┘
```

---

## 💾 SQL Queries - Operasi Utama

### Query 1: Ambil Daftar Materi (Posts) untuk di-AI-Generate

```sql
-- Guru pilih post sebagai sumber soal
SELECT p.id, p.title, p.description, m.name as mapel
FROM posts p
JOIN mapels m ON p.mapel_id = m.id
WHERE p.serial_id = ?
  AND p.is_task = 0
  AND p.deleted_at IS NULL
ORDER BY p.created_at DESC;
```

### Query 2: Ambil Lesson sebagai Sumber Soal

```sql
-- Guru pilih lesson sebagai sumber soal
SELECT l.id, l.name, l.description, m.name as mapel
FROM lessons l
JOIN mapels m ON l.mapel_id = m.id
WHERE l.mapel_id = ?
  AND l.category = 1
  AND l.deleted_at IS NULL
ORDER BY l.created_at DESC;
```

### Query 3: Simpan Exercise (Kumpulan Soal Hasil AI)

```sql
-- Setelah AI generate, simpan exercise container
INSERT INTO exercises
  (serial_id, exercise_type_id, mapel_id, title, is_admin, created_at, updated_at)
VALUES
  (?, ?, ?, CONCAT('AI Generated: ', ?), 0, NOW(), NOW());
-- Returns: $exercise_id

-- Dapatkan ID yang baru diinsert
SELECT LAST_INSERT_ID() as exercise_id;
```

### Query 4: Simpan Exercise Items (Soal Individual dari AI)

```sql
-- Simpan setiap soal yang dihasilkan AI
INSERT INTO exercise_items
  (exercise_id, exercise_model_id, exercise_type_id, question, selection, answer, point, is_user, created_at, updated_at)
VALUES
  (?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW());

-- Contoh untuk 5 soal MCQ:
INSERT INTO exercise_items
  (exercise_id, exercise_model_id, exercise_type_id, question, selection, answer, point, is_user, created_at, updated_at)
VALUES
  (123, 1, 1, 'Apa ibu kota Indonesia?', '["A. Jakarta", "B. Bandung", "C. Yogyakarta"]', 'A', 20, 1, NOW(), NOW()),
  (123, 1, 1, 'Berapa 2+2?', '["A. 3", "B. 4", "C. 5"]', 'B', 20, 1, NOW(), NOW()),
  ...
```

### Query 5: Update Usage Count Serial

```sql
-- Track penggunaan AI generator
UPDATE serials
SET usage_count = usage_count + 1
WHERE id = ?;
```

### Query 6: Link Exercise ke Classroom (Berbagi Soal)

```sql
-- Bagikan soal AI ke kelas tertentu
INSERT INTO classroom_exercises (exercise_id, classroom_id, created_at)
VALUES (?, ?, NOW());
```

### Query 7: Ambil Soal untuk Siswa Kerjakan

```sql
-- Siswa lihat soal dari exercise yang dibagikan
SELECT ei.id, ei.question, ei.selection, ei.exercise_model_id, ei.point
FROM exercise_items ei
JOIN exercises e ON ei.exercise_id = e.id
WHERE e.id = ?
ORDER BY ei.no ASC;
```

### Query 8: Auto-Grade Soal Pilihan Ganda

```sql
-- Setelah siswa submit, auto-grade untuk MCQ/True-False/Short Answer
INSERT INTO exercise_points
  (exercise_id, exercise_item_id, student_id, answer, is_correct, exercise_point, created_at)
VALUES
  (?, ?, ?, ?,
   (SELECT IF(? = answer, 1, 0) FROM exercise_items WHERE id = ?),
   (SELECT IF(? = answer, point, 0) FROM exercise_items WHERE id = ?),
   NOW()
  );
```

### Query 9: Lihat Hasil Siswa

```sql
-- Dashboard siswa lihat nilai soal dari AI
SELECT
  ei.question,
  ep.answer as jawaban_siswa,
  ei.answer as jawaban_benar,
  ep.is_correct,
  ep.exercise_point
FROM exercise_points ep
JOIN exercise_items ei ON ep.exercise_item_id = ei.id
WHERE ep.exercise_id = ? AND ep.student_id = ?
ORDER BY ei.no;
```

### Query 10: Report Guru - Hasil Siswa

```sql
-- Guru lihat performa siswa di soal yang di-AI-generate
SELECT
  s.name,
  COUNT(ei.id) as total_soal,
  SUM(CASE WHEN ep.is_correct = 1 THEN 1 ELSE 0 END) as benar,
  SUM(ep.exercise_point) as total_nilai,
  ROUND(SUM(ep.exercise_point) / e.total_point * 100, 2) as persentase
FROM students s
LEFT JOIN exercise_points ep ON s.id = ep.student_id
LEFT JOIN exercise_items ei ON ep.exercise_item_id = ei.id
LEFT JOIN exercises e ON ep.exercise_id = e.id
WHERE e.id = ?
GROUP BY s.id
ORDER BY total_nilai DESC;
```

---

## 🔍 Entity Relationship Diagram (ER Untuk AI Generator)

```
                        ┌──────────────────┐
                        │    serials       │
                        ├──────────────────┤
                        │ id (PK)          │
                        │ user_id (FK)     │
                        │ active           │
                        │ usage_count      │
                        └──────────────────┘
                                 │
                 ┌───────────────┼────────────────┐
                 │               │                │
                 ▼               ▼                ▼
         ┌──────────────┐  ┌──────────────┐  ┌────────────────┐
         │  exercises   │  │ classrooms   │  │ quiz_activity  │
         ├──────────────┤  ├──────────────┤  │    _logs       │
         │ id (PK)      │  │ id (PK)      │  ├────────────────┤
         │ serial_id(FK)│  │ serial_id(FK)│  │ id (PK)        │
         │ exercise_    │  │ code         │  │ exercise_id(FK)│
         │  type_id(FK) │  │ name         │  │ student_id(FK) │
         │ mapel_id(FK) │  └──────────────┘  │ event_type     │
         │ title        │                     │ duration_      │
         │ is_admin     │                     │  seconds       │
         └──────────────┘                     │ suspicious_    │
                 │                            │  flag          │
                 │                            └────────────────┘
                 │
                 ▼
         ┌──────────────────┐
         │  exercise_items  │
         ├──────────────────┤
         │ id (PK)          │
         │ exercise_id (FK) │────────────┐
         │ exercise_model_id│            │
         │ question         │            │
         │ selection (JSON) │            │
         │ answer           │            │
         │ point            │            │
         │ is_user          │            │
         └──────────────────┘            │
                 │                       │
                 │                       ▼
                 │            ┌──────────────────────┐
                 │            │  exercise_points     │
                 │            ├──────────────────────┤
                 │            │ id (PK)              │
                 │            │ exercise_id (FK)     │
                 ├────────────▶│ exercise_item_id(FK)│
                 │            │ student_id (FK)      │
                 │            │ answer               │
                 │            │ is_correct           │
                 │            │ exercise_point       │
                 │            └──────────────────────┘
                 │
                 ├─────────────────┬─────────────────┐
                 │                 │                 │
                 ▼                 ▼                 ▼
        ┌────────────────┐ ┌──────────────┐ ┌─────────────────┐
        │ exercise_types │ │ exercise_    │ │    mapels       │
        ├────────────────┤ │   models     │ ├─────────────────┤
        │ id (PK)        │ ├──────────────┤ │ id (PK)         │
        │ kode           │ │ id (PK)      │ │ name            │
        │ name           │ │ name         │ │ code            │
        └────────────────┘ │ auto_grading │ └─────────────────┘
                           └──────────────┘

        Sumber Materi (Input untuk AI):
        ┌──────────────┐         ┌──────────────┐
        │   posts      │         │   lessons    │
        ├──────────────┤         ├──────────────┤
        │ id (PK)      │         │ id (PK)      │
        │ serial_id(FK)│         │ mapel_id(FK) │
        │ user_id (FK) │         │ name         │
        │ mapel_id (FK)│         │ description  │
        │ title        │         │ category = 1 │
        │ description  │         └──────────────┘
        │ is_task = 0  │
        └──────────────┘
```

---

## 📌 Key Points untuk AI Question Generator

### ✅ Tabel yang WAJIB Diakses:

1. **serials** - Validasi akses & tracking
2. **exercises** - Container soal hasil AI
3. **exercise_items** - Pertanyaan individual
4. **exercise_types** - Tipe latihan (UH/SL/QUIZ)
5. **exercise_models** - Model soal (MCQ/Essay/True-False)
6. **posts / lessons** - Sumber materi untuk AI
7. **mapels** - Subject konteks
8. **classrooms** - Berbagi soal ke kelas
9. **exercise_points** - Jawaban siswa & scoring
10. **quiz_activity_logs** - Anti-cheating tracking

### 🔐 Constraint yang Penting:

- ✅ **serials.active = 'yes'** → Guru boleh generate soal
- ✅ **exercises.is_admin = 0** → Soal yang di-generate guru (bukan admin)
- ✅ **exercise_items.is_user = 1** → Soal dibuat user (dari AI)
- ✅ **posts.is_task = 0** → Hanya materi, bukan tugas yang bisa di-AI
- ✅ **lessons.category = 1** → Hanya materi pelajaran yang dipilih

### 🚀 Performance Tips:

- Indexed columns: `exercises.serial_id`, `exercise_items.exercise_id`, `exercise_points.exercise_id`
- Use JSON untuk `exercise_items.selection` (opsi jawaban)
- Cache: Mapels, exercise_types, exercise_models (jarang berubah)
- Batch insert exercise_items untuk speed

### 📊 Usage Monitoring:

- Track: `serials.usage_count` → Monitor beban AI usage per guru
- Log: `quiz_activity_logs` → Deteksi unusual patterns
- Alert: Jika `quiz_activity_logs.suspicious_flag = 1` → Notifikasi guru

---

## 🎓 Contoh Skenario Lengkap

### Skenario: Guru Membuat Soal Pilihan Ganda dari Material Pelajaran

**Aksi Guru:**

1. Login ke sistem → serial divalidasi dari `serials` table
2. Navigasi ke "AI Question Generator"
3. Pilih mata pelajaran (Matematika)
4. Pilih sumber: "Lesson - Integral Calculus"
   - System query: `SELECT * FROM lessons WHERE id = ? AND category = 1`
   - Ambil konten: `Integral adalah proses kebalikan dari diferensiasi...`
5. Set parameter:
   - Exercise Type: UH (Ulangan Harian) → dari `exercise_types`
   - Question Model: Pilihan Ganda → dari `exercise_models`
   - Difficulty: Sedang
   - Jumlah soal: 5
6. Click "Generate dengan AI"

**Backend Process:**

```
1. Validasi: SELECT * FROM serials WHERE id = ? AND active = 'yes'
2. Ambil materi:
   SELECT l.name, l.description FROM lessons l WHERE id = ?
3. Build prompt untuk AI:
   "Buatkan 5 soal pilihan ganda tingkat sedang tentang [lesson.name].
    Materi: [lesson.description]. Format JSON dengan fields: question, options[], answer."
4. Call OpenAI API dengan prompt
5. Parse response: 5 soal dalam format JSON
6. Insert ke DB:
   - INSERT exercises: (serial_id, exercise_type_id, mapel_id, title, is_admin=0)
   - INSERT exercise_items x5: (exercise_id, question, selection[], answer, point, is_user=1)
   - UPDATE serials: usage_count++
7. Return: Exercise ID & preview soal ke frontend
```

**Guru Preview & Save:**

- Preview kelima soal
- Edit soal #2 (ganti opsi D)
- Save → Soal langsung masuk ke bank soal

**Guru Bagikan ke Kelas:**

- Pilih kelas: "Kelas X IPA 1"
- System query: `SELECT * FROM classrooms WHERE serial_id = ?`
- Click "Bagikan ke kelas"
- System: `INSERT classroom_exercises (exercise_id, classroom_id)`

**Siswa Kerjakan:**

- Siswa terima notifikasi
- Buka exercise
- System query: `SELECT * FROM exercise_items WHERE exercise_id = ?`
- Siswa jawab 5 soal
- For each soal:
  - `INSERT exercise_points (exercise_id, exercise_item_id, student_id, answer, ...)`
  - `is_correct = (siswa.answer === exercise_items.answer ? 1 : 0)`
  - `exercise_point = (is_correct ? point : 0)`
- Log activity: `INSERT quiz_activity_logs (exercise_id, student_id, event_type='submit', duration_seconds=...)`

**Hasil:**

- Siswa lihat nilai: 80/100 (4 benar dari 5)
- Guru lihat laporan:
  ```
  Soal #1: 90% siswa benar (mudah)
  Soal #2: 50% siswa benar (sedang)
  Soal #3: 30% siswa benar (sulit)
  Rata-rata nilai: 72.5
  ```

---

## 📚 Reference Files

- **File dokumentasi:** `/docs/AI_QUESTION_GENERATOR.md` - User guide
- **Setup file:** `/docs/AI_QUESTION_GENERATOR_SETUP.md` - Instalasi & konfigurasi
- **Service file:** `/app/Services/OpenAIService.php` - Implementasi AI
- **Controller file:** `/app/Http/Controllers/Guru/SoalController.php` - Routes & logic
- **ERD:** `/docs/uml/erd-current-system.puml` - Database schema lengkap
