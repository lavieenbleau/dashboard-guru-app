# BAB 5

# IMPLEMENTASI DAN PEMBAHASAN SISTEM

## 5.1 Implementasi

### 5.1.1 Implementasi Basis Data

Implementasi basis data DashboardGuru menggunakan sistem manajemen basis data MySQL dengan Laravel 12 sebagai framework PHP. Database dirancang dengan pendekatan relasional yang mengikuti prinsip normalisasi untuk memastikan integritas data dan efisiensi query.

#### 5.1.1.1 Spesifikasi Lingkungan Database

Berikut adalah spesifikasi lingkungan database yang digunakan dalam implementasi sistem DashboardGuru:

| Komponen                   | Spesifikasi           |
| -------------------------- | --------------------- |
| Database Management System | MySQL / MariaDB       |
| PHP Version                | 8.2 atau lebih tinggi |
| Laravel Framework          | 12.35.0               |
| Database Character Set     | utf8mb4               |
| Database Collation         | utf8mb4_unicode_ci    |
| Storage Engine             | InnoDB                |

#### 5.1.1.2 Struktur Database

Database DashboardGuru terdiri dari 38 tabel yang dikelompokkan menjadi beberapa kategori fungsional:

**1. Tabel Inti Sistem (Core Tables)**

Tabel-tabel inti yang mengelola pengguna dan autentikasi:

- **users**: Menyimpan informasi pengguna (guru/administrator)
- **password_reset_tokens**: Menyimpan token untuk reset password
- **sessions**: Menyimpan data sesi pengguna

**2. Tabel Manajemen Produk dan Serial**

Tabel untuk mengelola lisensi dan aktivasi produk:

- **products**: Menyimpan informasi produk/paket pembelajaran
- **serials**: Menyimpan serial number/lisensi untuk setiap produk

**3. Tabel Manajemen Kelas dan Siswa**

Tabel untuk mengelola kelas dan data siswa:

- **classrooms**: Menyimpan informasi kelas
- **students**: Menyimpan data siswa

**4. Tabel Manajemen Kurikulum**

Tabel untuk mengelola struktur kurikulum pembelajaran:

- **mapels**: Menyimpan mata pelajaran
- **themes**: Menyimpan tema pembelajaran
- **subthemes**: Menyimpan subtema pembelajaran
- **competences**: Menyimpan kompetensi dasar

**5. Tabel Manajemen Pembelajaran**

Tabel untuk mengelola materi dan pelajaran:

- **lessons**: Menyimpan pelajaran/materi
- **lesson_items**: Menyimpan item-item dalam pelajaran (video, link, file, teks)
- **lesson_classroom**: Tabel pivot untuk relasi many-to-many antara pelajaran dan kelas
- **materis**: Menyimpan materi tambahan

**6. Tabel Manajemen Latihan dan Penilaian**

Tabel untuk mengelola soal, latihan, dan penilaian:

- **exercise_types**: Menyimpan tipe latihan (Ulangan Harian, PTS, PAS, Tambahan)
- **exercise_models**: Menyimpan model/bentuk soal (Pilihan Ganda, Essay, dll)
- **exercises**: Menyimpan data latihan/soal
- **exercise_items**: Menyimpan item soal dalam latihan
- **exercise_points**: Menyimpan nilai/poin siswa untuk latihan
- **exercise_classroom**: Tabel pivot untuk relasi many-to-many antara latihan dan kelas

**7. Tabel Manajemen Tugas dan Postingan**

Tabel untuk mengelola tugas dan postingan:

- **posts**: Menyimpan postingan/pengumuman
- **tasks**: Menyimpan tugas untuk siswa
- **task_submissions**: Menyimpan pengumpulan tugas oleh siswa
- **post_comments**: Menyimpan komentar pada postingan
- **post_child_comments**: Menyimpan balasan komentar

**8. Tabel Manajemen Laporan**

Tabel untuk sistem pelaporan:

- **reports**: Menyimpan laporan umum
- **report_types**: Menyimpan jenis laporan
- **student_reports**: Menyimpan laporan per siswa
- **grade_reports**: Menyimpan laporan nilai
- **attendance_reports**: Menyimpan laporan kehadiran

**9. Tabel Kelas Online**

Tabel untuk mengelola kelas online/meeting:

- **online_meetings**: Menyimpan jadwal dan informasi meeting online
- **meeting_participants**: Menyimpan data peserta meeting

**10. Tabel Sistem**

Tabel untuk sistem cache dan antrian:

- **cache**: Menyimpan data cache aplikasi
- **cache_locks**: Menyimpan lock untuk cache
- **jobs**: Menyimpan antrian pekerjaan
- **job_batches**: Menyimpan batch job
- **failed_jobs**: Menyimpan log job yang gagal

**11. Tabel Bantuan**

- **helps**: Menyimpan data bantuan/help untuk pengguna

#### 5.1.1.3 Detail Implementasi Tabel Utama

**Tabel users**

Tabel ini menyimpan informasi lengkap pengguna sistem (guru/administrator).

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    password_text VARCHAR(100) NULL,
    email VARCHAR(100) NULL,
    email_verified_at TIMESTAMP NULL,
    role TINYINT NOT NULL,
    address TEXT NULL,
    phone VARCHAR(20) NULL,
    img VARCHAR(100) NULL,
    login_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `id`: Primary key dengan auto increment
- `name`: Nama lengkap pengguna
- `username`: Username untuk login
- `password`: Password terenkripsi (hashed)
- `password_text`: Backup password dalam plain text (opsional)
- `email`: Alamat email pengguna
- `role`: Peran pengguna (admin/guru)
- `img`: Nama file foto profil
- `login_at`: Timestamp login terakhir

**Tabel products**

Menyimpan informasi produk/paket pembelajaran yang tersedia.

```sql
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lesson_id VARCHAR(100) NULL,
    name VARCHAR(50) NOT NULL,
    grade VARCHAR(50) NULL,
    grade_category VARCHAR(100) NOT NULL,
    semester VARCHAR(50) NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `lesson_id`: Identifier pelajaran terkait
- `name`: Nama produk
- `grade`: Tingkat kelas
- `grade_category`: Kategori kelas
- `semester`: Semester (ganjil/genap)

**Tabel serials**

Menyimpan serial number/lisensi untuk aktivasi produk.

```sql
CREATE TABLE serials (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    serial VARCHAR(50) NOT NULL,
    paket VARCHAR(1) NOT NULL,
    active VARCHAR(3) NOT NULL,
    expired_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `user_id`: Foreign key ke tabel users (guru yang mengaktifkan)
- `product_id`: Foreign key ke tabel products
- `serial`: Kode serial unik
- `paket`: Tipe paket (A, B, C, dll)
- `active`: Status aktivasi (ya/tidak)
- `expired_at`: Tanggal kadaluarsa lisensi

**Tabel classrooms**

Menyimpan informasi kelas yang dikelola guru.

```sql
CREATE TABLE classrooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    serial_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(10) NOT NULL UNIQUE,
    name VARCHAR(50) NOT NULL,
    grade VARCHAR(50) NULL,
    grade_category VARCHAR(100) NOT NULL,
    description TEXT NULL,
    wali_kelas VARCHAR(100) NULL,
    academic_year VARCHAR(20) NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (serial_id) REFERENCES serials(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `serial_id`: Foreign key ke tabel serials
- `code`: Kode kelas unik untuk pendaftaran siswa
- `name`: Nama kelas
- `grade`: Tingkat kelas
- `wali_kelas`: Nama wali kelas
- `academic_year`: Tahun ajaran

**Tabel students**

Menyimpan data siswa yang terdaftar di sistem.

```sql
CREATE TABLE students (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    serial_id BIGINT UNSIGNED NOT NULL,
    classroom_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    nisn VARCHAR(20) NULL,
    nis VARCHAR(20) NULL,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    password_text VARCHAR(100) NULL,
    email VARCHAR(100) NULL,
    phone VARCHAR(20) NULL,
    gender ENUM('L', 'P') NULL,
    birth_date DATE NULL,
    address TEXT NULL,
    img VARCHAR(100) NULL,
    login_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (serial_id) REFERENCES serials(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `serial_id`: Foreign key ke tabel serials
- `classroom_id`: Foreign key ke tabel classrooms
- `nisn`: Nomor Induk Siswa Nasional
- `nis`: Nomor Induk Siswa
- `gender`: Jenis kelamin (L/P)
- `birth_date`: Tanggal lahir

**Tabel lessons**

Menyimpan materi pelajaran yang dibuat guru.

```sql
CREATE TABLE lessons (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    serial_id BIGINT UNSIGNED NOT NULL,
    mapel_id BIGINT UNSIGNED NOT NULL,
    theme_id BIGINT UNSIGNED NULL,
    subtheme_id BIGINT UNSIGNED NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NULL,
    file VARCHAR(100) NULL,
    deadline TIMESTAMP NULL,
    shared_to_classes BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (serial_id) REFERENCES serials(id) ON DELETE CASCADE,
    FOREIGN KEY (mapel_id) REFERENCES mapels(id) ON DELETE CASCADE,
    FOREIGN KEY (theme_id) REFERENCES themes(id) ON DELETE SET NULL,
    FOREIGN KEY (subtheme_id) REFERENCES subthemes(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `serial_id`: Foreign key ke tabel serials
- `mapel_id`: Foreign key ke tabel mapels (mata pelajaran)
- `theme_id`: Foreign key ke tabel themes (opsional)
- `subtheme_id`: Foreign key ke tabel subthemes (opsional)
- `deadline`: Batas waktu akses pelajaran
- `shared_to_classes`: Indikator dibagikan ke kelas

**Tabel lesson_items**

Menyimpan item-item konten dalam pelajaran.

```sql
CREATE TABLE lesson_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    lesson_id BIGINT UNSIGNED NOT NULL,
    type ENUM('link', 'video', 'file', 'text') NOT NULL,
    title VARCHAR(100) NOT NULL,
    content TEXT NOT NULL,
    order_num TINYINT DEFAULT 0,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `type`: Tipe konten (link eksternal, video embed, file upload, atau teks)
- `content`: Konten sesuai tipe (URL, embed code, filename, atau teks)
- `order_num`: Urutan tampilan item

**Tabel exercises**

Menyimpan data latihan/soal yang dibuat guru.

```sql
CREATE TABLE exercises (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    serial_id BIGINT UNSIGNED NOT NULL,
    lesson_id BIGINT UNSIGNED NOT NULL,
    exercise_type_id BIGINT UNSIGNED NOT NULL,
    title VARCHAR(100) NOT NULL,
    description TEXT NULL,
    duration INTEGER NULL,
    show_result BOOLEAN DEFAULT TRUE,
    randomize BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (serial_id) REFERENCES serials(id) ON DELETE CASCADE,
    FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE,
    FOREIGN KEY (exercise_type_id) REFERENCES exercise_types(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `exercise_type_id`: Foreign key ke tabel exercise_types (UH, PTS, PAS, Tambahan)
- `duration`: Durasi pengerjaan dalam menit
- `show_result`: Tampilkan hasil langsung atau tidak
- `randomize`: Acak urutan soal atau tidak

**Tabel exercise_items**

Menyimpan item soal dalam latihan.

```sql
CREATE TABLE exercise_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    exercise_id BIGINT UNSIGNED NOT NULL,
    exercise_model_id BIGINT UNSIGNED NOT NULL,
    question TEXT NOT NULL,
    option_a TEXT NULL,
    option_b TEXT NULL,
    option_c TEXT NULL,
    option_d TEXT NULL,
    option_e TEXT NULL,
    correct_answer VARCHAR(1) NULL,
    point DECIMAL(5,2) DEFAULT 0,
    order_num INTEGER DEFAULT 0,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE,
    FOREIGN KEY (exercise_model_id) REFERENCES exercise_models(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `exercise_model_id`: Foreign key ke tabel exercise_models (tipe soal: PG, Essay, dll)
- `option_a` sampai `option_e`: Pilihan jawaban untuk soal pilihan ganda
- `correct_answer`: Jawaban benar (A/B/C/D/E untuk PG)
- `point`: Bobot nilai soal

**Tabel exercise_points**

Menyimpan nilai/poin siswa untuk setiap latihan.

```sql
CREATE TABLE exercise_points (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    serial_id BIGINT UNSIGNED NOT NULL,
    exercise_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    score DECIMAL(5,2) DEFAULT 0,
    max_score DECIMAL(5,2) DEFAULT 0,
    status ENUM('pending', 'graded') DEFAULT 'pending',
    started_at TIMESTAMP NULL,
    submitted_at TIMESTAMP NULL,
    graded_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (serial_id) REFERENCES serials(id) ON DELETE CASCADE,
    FOREIGN KEY (exercise_id) REFERENCES exercises(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `score`: Nilai yang diperoleh siswa
- `max_score`: Nilai maksimal yang bisa dicapai
- `status`: Status penilaian (pending/graded)
- `started_at`: Waktu mulai mengerjakan
- `submitted_at`: Waktu pengumpulan
- `graded_at`: Waktu dinilai oleh guru

**Tabel posts**

Menyimpan postingan/pengumuman dari guru.

```sql
CREATE TABLE posts (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    serial_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    classroom_id BIGINT UNSIGNED NULL,
    type ENUM('announcement', 'task', 'quiz') NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    attachment VARCHAR(100) NULL,
    is_pinned BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (serial_id) REFERENCES serials(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `type`: Jenis postingan (pengumuman, tugas, atau kuis)
- `attachment`: File lampiran
- `is_pinned`: Status pin (ditampilkan di atas)

**Tabel tasks**

Menyimpan tugas yang diberikan kepada siswa.

```sql
CREATE TABLE tasks (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    post_id BIGINT UNSIGNED NOT NULL,
    deadline TIMESTAMP NULL,
    max_score DECIMAL(5,2) DEFAULT 100,
    allow_late_submission BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (post_id) REFERENCES posts(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `deadline`: Batas waktu pengumpulan
- `max_score`: Nilai maksimal
- `allow_late_submission`: Izinkan pengumpulan terlambat

**Tabel task_submissions**

Menyimpan pengumpulan tugas oleh siswa.

```sql
CREATE TABLE task_submissions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    task_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    content TEXT NULL,
    attachment VARCHAR(100) NULL,
    score DECIMAL(5,2) NULL,
    feedback TEXT NULL,
    status ENUM('pending', 'graded', 'late') DEFAULT 'pending',
    submitted_at TIMESTAMP NULL,
    graded_at TIMESTAMP NULL,
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (task_id) REFERENCES tasks(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `content`: Jawaban tugas dalam bentuk teks
- `attachment`: File jawaban yang diupload
- `score`: Nilai yang diberikan guru
- `feedback`: Komentar/feedback dari guru
- `status`: Status pengumpulan (pending/graded/late)

**Tabel online_meetings**

Menyimpan jadwal dan informasi meeting online.

```sql
CREATE TABLE online_meetings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    serial_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    classroom_id BIGINT UNSIGNED NULL,
    title VARCHAR(200) NOT NULL,
    description TEXT NULL,
    platform ENUM('zoom', 'google_meet', 'teams', 'jitsi', 'other') NOT NULL,
    meeting_url TEXT NOT NULL,
    meeting_id VARCHAR(100) NULL,
    passcode VARCHAR(50) NULL,
    scheduled_at TIMESTAMP NOT NULL,
    duration INTEGER DEFAULT 60,
    status ENUM('scheduled', 'ongoing', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP NOT NULL,
    updated_at TIMESTAMP NOT NULL,
    FOREIGN KEY (serial_id) REFERENCES serials(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Keterangan Field:**

- `platform`: Platform meeting (Zoom, Google Meet, Teams, Jitsi, dll)
- `meeting_url`: Link meeting
- `meeting_id`: ID meeting
- `passcode`: Password/kode akses meeting
- `scheduled_at`: Waktu jadwal meeting
- `duration`: Durasi meeting dalam menit
- `status`: Status meeting (scheduled/ongoing/completed/cancelled)

#### 5.1.1.4 Relasi Antar Tabel

Sistem DashboardGuru mengimplementasikan berbagai jenis relasi database:

**1. One-to-Many Relationships**

- `users` → `serials`: Satu guru dapat memiliki banyak serial
- `products` → `serials`: Satu produk dapat memiliki banyak serial
- `serials` → `classrooms`: Satu serial dapat memiliki banyak kelas
- `serials` → `students`: Satu serial dapat memiliki banyak siswa
- `classrooms` → `students`: Satu kelas dapat memiliki banyak siswa
- `lessons` → `lesson_items`: Satu pelajaran dapat memiliki banyak item
- `exercises` → `exercise_items`: Satu latihan dapat memiliki banyak soal
- `posts` → `tasks`: Satu postingan dapat menjadi satu tugas
- `tasks` → `task_submissions`: Satu tugas dapat memiliki banyak pengumpulan

**2. Many-to-Many Relationships**

- `lessons` ↔ `classrooms`: Satu pelajaran dapat dibagikan ke banyak kelas, dan satu kelas dapat menerima banyak pelajaran (melalui tabel `lesson_classroom`)
- `exercises` ↔ `classrooms`: Satu latihan dapat dibagikan ke banyak kelas, dan satu kelas dapat menerima banyak latihan (melalui tabel `exercise_classroom`)

**3. Polymorphic Relationships**

- Sistem menggunakan relasi polimorfik untuk fleksibilitas dalam menghubungkan berbagai tipe konten

#### 5.1.1.5 Indexing dan Optimasi

Untuk meningkatkan performa query, sistem mengimplementasikan beberapa strategi indexing:

**Primary Keys**

Semua tabel menggunakan `BIGINT UNSIGNED AUTO_INCREMENT` sebagai primary key untuk skalabilitas.

**Foreign Keys**

Semua foreign key dilengkapi dengan constraint `ON DELETE CASCADE` atau `ON DELETE SET NULL` untuk menjaga integritas referensial.

**Unique Indexes**

- `classrooms.code`: Memastikan kode kelas unik untuk pendaftaran
- `users.username`: Memastikan username unik
- `students.username`: Memastikan username siswa unik

**Composite Indexes**

Untuk query yang sering menggunakan kombinasi kolom, seperti:

- `(serial_id, classroom_id)` pada tabel students
- `(exercise_id, student_id)` pada tabel exercise_points

#### 5.1.1.6 Migration Files

Laravel menggunakan migration files untuk version control database schema. Berikut adalah daftar migration yang telah diimplementasikan secara kronologis:

1. `0001_01_01_000000_create_users_table.php`
2. `0001_01_01_000001_create_cache_table.php`
3. `0001_01_01_000002_create_jobs_table.php`
4. `2025_11_19_064554_create_products_table.php`
5. `2025_11_19_064600_create_serials_table.php`
6. `2025_11_19_064606_create_classrooms_table.php`
7. `2025_11_19_064611_create_students_table.php`
8. `2025_11_19_064649_create_mapels_table.php`
9. `2025_11_19_064656_create_lessons_table.php`
10. `2025_11_19_064659_create_themes_table.php`
11. `2025_11_19_064703_create_subthemes_table.php`
12. `2025_11_19_064709_create_lesson_items_table.php`
13. `2025_11_19_064712_create_competences_table.php`
14. `2025_11_19_064716_create_exercise_types_table.php`
15. `2025_11_19_064722_create_exercise_models_table.php`
16. `2025_11_19_064736_create_exercises_table.php`
17. `2025_11_19_064740_create_exercise_items_table.php`
18. `2025_11_19_064747_create_posts_table.php`
19. `2025_11_19_064749_create_tasks_table.php`
20. `2025_11_19_064800_create_reports_table.php`
21. `2025_11_19_064801_create_helps_table.php`
22. `2025_11_19_064850_create_online_meetings_table.php`
23. `2025_11_25_090456_add_quiz_fields_to_posts_table.php`
24. `2025_12_01_042030_add_description_and_file_to_lessons_table.php`
25. `2025_12_12_103149_add_deadline_to_lessons_table.php`
26. `2025_12_18_082854_add_missing_fields_to_online_meetings_table.php`
27. `2025_12_18_083458_update_status_enum_in_online_meetings_table.php`
28. `2025_12_19_022836_create_exercise_points_table.php`
29. `2025_12_19_022854_create_post_comments_table.php`
30. `2025_12_19_022854_create_post_child_comments_table.php`
31. `2025_12_24_200000_add_shared_to_classes_to_lessons.php`
32. `2025_12_24_200000_create_lesson_classroom_table.php`
33. `2025_12_24_202000_create_exercise_classroom_table.php`
34. `2026_01_04_155919_create_report_types_table.php`
35. `2026_01_04_160011_create_student_reports_table.php`
36. `2026_01_04_160011_create_grade_reports_table.php`
37. `2026_01_04_160012_create_attendance_reports_table.php`
38. `2026_01_04_160013_create_task_submissions_table.php`
39. `2026_01_04_160013_create_meeting_participants_table.php`

Migration files ini memungkinkan tracking perubahan database dan memudahkan deployment ke berbagai environment.

#### 5.1.1.7 Seeding Data

Untuk keperluan development dan testing, sistem menggunakan seeder untuk mengisi data awal:

- **DatabaseSeeder**: Seeder utama yang mengorkestrasi seeding process
- **UserFactory**: Factory untuk generate data user dummy
- Custom seeders untuk data master seperti mapels, exercise_types, dan exercise_models

### 5.1.2 Implementasi Sistem

#### 5.1.2.1 Bahasa Pemrograman dan Framework

Sistem DashboardGuru dibangun menggunakan teknologi modern yang reliable dan scalable:

**Backend:**

- **PHP 8.2+**: Bahasa pemrograman server-side dengan fitur modern seperti typed properties, named arguments, dan match expressions
- **Laravel 12.35.0**: Framework PHP full-stack dengan arsitektur MVC (Model-View-Controller)
- **Composer**: Dependency manager untuk PHP

**Frontend:**

- **HTML5**: Markup language untuk struktur halaman
- **CSS3**: Styling dengan dukungan flexbox dan grid
- **JavaScript (ES6+)**: Programming language untuk interaktivitas
- **Bootstrap 5**: CSS framework untuk responsive design
- **Tailwind CSS**: Utility-first CSS framework
- **Vite**: Modern build tool untuk asset bundling

**Database:**

- **MySQL/MariaDB**: Relational Database Management System

**Package Management:**

- **NPM (Node Package Manager)**: Untuk mengelola dependencies frontend
- **Composer**: Untuk mengelola dependencies backend

#### 5.1.2.2 Arsitektur Sistem

Sistem DashboardGuru mengimplementasikan arsitektur **MVC (Model-View-Controller)** yang merupakan pattern arsitektur standar dalam Laravel:

**1. Model Layer**

Model merepresentasikan struktur data dan business logic. Setiap tabel database memiliki model Eloquent ORM yang sesuai:

- `User.php`: Model untuk tabel users
- `Product.php`: Model untuk tabel products
- `Serial.php`: Model untuk tabel serials
- `Classroom.php`: Model untuk tabel classrooms
- `Student.php`: Model untuk tabel students
- `Mapel.php`: Model untuk tabel mapels
- `Lesson.php`: Model untuk tabel lessons
- `LessonItem.php`: Model untuk tabel lesson_items
- `Exercise.php`: Model untuk tabel exercises
- `ExerciseItem.php`: Model untuk tabel exercise_items
- `ExerciseType.php`: Model untuk tabel exercise_types
- `ExerciseModel.php`: Model untuk tabel exercise_models
- `ExercisePoint.php`: Model untuk tabel exercise_points
- `Post.php`: Model untuk tabel posts
- `Task.php`: Model untuk tabel tasks
- `OnlineMeeting.php`: Model untuk tabel online_meetings
- `Report.php`: Model untuk tabel reports
- `Theme.php`: Model untuk tabel themes
- `Subtheme.php`: Model untuk tabel subthemes
- `Competence.php`: Model untuk tabel competences
- `Help.php`: Model untuk tabel helps
- `Materi.php`: Model untuk tabel materis

**2. View Layer**

View menangani presentasi data ke user interface. Laravel menggunakan Blade templating engine:

- Layout templates di `resources/views/layouts/`
- Component templates di `resources/views/components/`
- Page templates di `resources/views/` (diorganisir per fitur)

**3. Controller Layer**

Controller menangani logic aplikasi dan koordinasi antara Model dan View:

- Controllers di `app/Http/Controllers/`
- Request validation di `app/Http/Requests/`
- Middleware di `app/Http/Middleware/`

**4. Routing**

Routing mendefinisikan endpoint dan mapping ke controller:

- `routes/web.php`: Routes untuk web interface
- `routes/auth.php`: Routes untuk autentikasi
- `routes/console.php`: Routes untuk artisan commands

#### 5.1.2.3 Implementasi Fitur Utama

**A. Sistem Autentikasi dan Otorisasi**

Implementasi autentikasi menggunakan Laravel Breeze dengan fitur:

- Login untuk guru dan siswa
- Logout
- Remember me functionality
- Session management
- Password hashing dengan bcrypt

**B. Manajemen Serial dan Aktivasi**

Alur implementasi:

1. Admin/Sales membuat produk
2. Generate serial code unik
3. Distribusi serial ke guru
4. Guru input serial untuk aktivasi
5. Validasi dan aktivasi serial
6. Akses fitur sesuai paket

**C. Manajemen Kelas**

Implementasi fitur:

- CRUD kelas
- Auto-generate kode kelas unik
- Distribusi kode kelas ke siswa
- Pendaftaran siswa dengan kode kelas
- Multi-class support

**D. Manajemen Pelajaran**

Implementasi fitur:

- CRUD pelajaran
- Support multiple content types (link, video, file, text)
- Relasi dengan mapel, tema, dan subtema
- Sharing ke multiple classes
- Deadline management

**E. Sistem Latihan dan Penilaian**

Implementasi fitur:

- CRUD latihan/soal
- 4 tipe latihan (Ulangan Harian, PTS, PAS, Tambahan)
- Multiple question types (Pilihan Ganda, Essay, dll)
- Auto-grading untuk pilihan ganda
- Manual grading untuk essay
- Score tracking
- Time-based exercises

**F. Manajemen Tugas**

Implementasi fitur:

- CRUD tugas
- File attachment support
- Deadline management
- Student submission tracking
- Grading dengan feedback
- Late submission handling

**G. Kelas Online/Meeting**

Implementasi fitur:

- Schedule online meetings
- Support multiple platforms (Zoom, Google Meet, Teams, Jitsi)
- Meeting URL management
- Participant tracking
- Meeting status tracking

**H. Sistem Laporan**

Implementasi fitur:

- Laporan harian otomatis
- Rekap nilai per kelas
- Rekap nilai per siswa
- Export PDF
- Grade reports
- Attendance reports

### 5.1.3 Implementasi Fitur Utama

#### 5.1.3.1 Dashboard dan Navigasi

Dashboard merupakan halaman utama setelah login yang menampilkan ringkasan informasi penting:

**Dashboard Guru:**

- Statistik jumlah kelas
- Statistik jumlah siswa
- Statistik materi/pelajaran
- Statistik tugas
- Aktivitas terbaru
- Notifikasi pengumpulan tugas
- Jadwal meeting hari ini

**Dashboard Siswa:**

- Informasi kelas
- Tugas terbaru
- Pelajaran terbaru
- Nilai terbaru
- Jadwal meeting
- Notifikasi

**Implementasi Navigasi:**

Routing utama aplikasi didefinisikan di `routes/web.php`:

```php
// Dashboard utama
Route::middleware('auth')->get('/', function () {
    return redirect('/aplikasi');
});

// Pilih aplikasi/serial
Route::get('/pilih-aplikasi', [AplikasiController::class, 'index']);

// Dashboard per aplikasi
Route::get('/aplikasi/{serial}', [AplikasiController::class, 'dashboard']);

// Fitur-fitur utama
Route::get('/aplikasi/{serial}/materi', [MateriController::class, 'index']);
Route::get('/aplikasi/{serial}/soal', [SoalController::class, 'index']);
Route::get('/aplikasi/{serial}/tugas', [TugasController::class, 'index']);
Route::get('/aplikasi/{serial}/laporan-harian', [LaporanHarianController::class, 'index']);
Route::get('/aplikasi/{serial}/rekap-nilai', [RekapNilaiController::class, 'index']);
Route::get('/aplikasi/{serial}/pengaturan', [PengaturanController::class, 'index']);
```

Sistem menggunakan middleware untuk proteksi route:

- `auth`: Memastikan user sudah login
- `verified`: Memastikan email sudah diverifikasi (opsional)

#### 5.1.3.2 Manajemen Kelas

**Fitur yang diimplementasikan:**

1. **Membuat Kelas Baru**

   - Input nama kelas
   - Pilih tingkat kelas
   - Input nama wali kelas
   - Input tahun ajaran
   - Auto-generate kode kelas unik (6-10 karakter)

2. **Melihat Daftar Kelas**

   - Tampilan card/grid kelas
   - Informasi jumlah siswa per kelas
   - Quick action (edit, delete, view detail)

3. **Detail Kelas**

   - Informasi lengkap kelas
   - Daftar siswa terdaftar
   - Kode kelas untuk pendaftaran
   - Statistik kelas

4. **Mengedit Kelas**

   - Update informasi kelas
   - Validasi input

5. **Menghapus Kelas**
   - Soft delete dengan konfirmasi
   - Cascade delete students (opsional)

**Implementasi Model Classroom:**

```php
class Classroom extends Model
{
    protected $fillable = [
        'serial_id',
        'code',
        'name',
        'grade',
        'grade_category',
        'description',
        'wali_kelas',
        'academic_year',
    ];

    // Relasi ke Serial
    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }

    // Relasi ke Students
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    // Relasi many-to-many dengan Lessons
    public function lessons()
    {
        return $this->belongsToMany(Lesson::class, 'lesson_classroom');
    }

    // Relasi many-to-many dengan Exercises
    public function exercises()
    {
        return $this->belongsToMany(Exercise::class, 'exercise_classroom');
    }
}
```

#### 5.1.3.3 Manajemen Siswa

**Fitur yang diimplementasikan:**

1. **Pendaftaran Siswa**

   - Form registrasi dengan kode kelas
   - Validasi kode kelas
   - Auto-assign ke serial yang sesuai
   - Generate username dan password

2. **Melihat Daftar Siswa**

   - Filter per kelas
   - Search by name/NIS/NISN
   - Sorting

3. **Edit Data Siswa**

   - Update informasi pribadi
   - Upload foto profil
   - Reset password

4. **Hapus Siswa**
   - Soft delete dengan konfirmasi

**Implementasi Model Student:**

```php
class Student extends Model
{
    protected $fillable = [
        'serial_id',
        'classroom_id',
        'name',
        'nisn',
        'nis',
        'username',
        'password',
        'password_text',
        'email',
        'phone',
        'gender',
        'birth_date',
        'address',
        'img',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Relasi ke Classroom
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    // Relasi ke Serial
    public function serial()
    {
        return $this->belongsTo(Serial::class);
    }

    // Relasi ke Exercise Points
    public function exercisePoints()
    {
        return $this->hasMany(ExercisePoint::class);
    }

    // Relasi ke Task Submissions
    public function taskSubmissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }
}
```

#### 5.1.3.4 Manajemen Materi Pelajaran

**Fitur yang diimplementasikan:**

1. **Membuat Pelajaran Baru**

   - Pilih mata pelajaran
   - Pilih tema (opsional)
   - Pilih subtema (opsional)
   - Input judul dan deskripsi
   - Upload file (opsional)
   - Set deadline (opsional)
   - Pilih kelas tujuan (multiple)

2. **Menambah Item Pelajaran**

   - Tipe: Link eksternal
   - Tipe: Video embed (YouTube, dll)
   - Tipe: File upload
   - Tipe: Teks/konten
   - Atur urutan item

3. **Sharing ke Kelas**

   - Pilih multiple kelas
   - Preview sebelum publish
   - Notifikasi ke siswa

4. **Edit dan Hapus Pelajaran**
   - Update konten
   - Tambah/hapus item
   - Delete dengan konfirmasi

**Implementasi Model Lesson:**

```php
class Lesson extends Model
{
    protected $fillable = [
        'serial_id',
        'mapel_id',
        'theme_id',
        'subtheme_id',
        'title',
        'description',
        'file',
        'deadline',
        'shared_to_classes',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'shared_to_classes' => 'boolean',
    ];

    // Relasi ke Items
    public function items()
    {
        return $this->hasMany(LessonItem::class)->orderBy('order_num');
    }

    // Relasi ke Mapel
    public function mapel()
    {
        return $this->belongsTo(Mapel::class);
    }

    // Relasi ke Theme
    public function theme()
    {
        return $this->belongsTo(Theme::class);
    }

    // Relasi ke Subtheme
    public function subtheme()
    {
        return $this->belongsTo(Subtheme::class);
    }

    // Relasi many-to-many ke Classrooms
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'lesson_classroom');
    }
}
```

#### 5.1.3.5 Sistem Latihan dan Soal

**Fitur yang diimplementasikan:**

1. **Membuat Latihan**

   - Pilih pelajaran
   - Pilih tipe (UH, PTS, PAS, Tambahan)
   - Input judul dan deskripsi
   - Set durasi (menit)
   - Set opsi randomize
   - Set opsi show result
   - Pilih kelas tujuan

2. **Menambah Soal**

   - Pilih model soal (PG, Essay, dll)
   - Input pertanyaan
   - Input pilihan jawaban (untuk PG)
   - Set jawaban benar
   - Set bobot nilai
   - Atur urutan

3. **Auto-Grading**

   - Otomatis untuk pilihan ganda
   - Hitung total score
   - Update status ke graded

4. **Manual Grading**
   - Review jawaban essay
   - Input score manual
   - Berikan feedback

**Implementasi Model Exercise:**

```php
class Exercise extends Model
{
    protected $fillable = [
        'serial_id',
        'lesson_id',
        'exercise_type_id',
        'title',
        'description',
        'duration',
        'show_result',
        'randomize',
    ];

    protected $casts = [
        'show_result' => 'boolean',
        'randomize' => 'boolean',
    ];

    // Relasi ke Items
    public function items()
    {
        return $this->hasMany(ExerciseItem::class)
                    ->orderBy('order_num');
    }

    // Relasi ke Exercise Type
    public function exerciseType()
    {
        return $this->belongsTo(ExerciseType::class);
    }

    // Relasi ke Points (nilai siswa)
    public function points()
    {
        return $this->hasMany(ExercisePoint::class);
    }

    // Relasi many-to-many ke Classrooms
    public function classrooms()
    {
        return $this->belongsToMany(Classroom::class, 'exercise_classroom');
    }

    // Helper method untuk get total poin
    public function getTotalPointsAttribute()
    {
        return $this->items->sum('point');
    }
}
```

#### 5.1.3.6 Manajemen Tugas

**Fitur yang diimplementasikan:**

1. **Membuat Tugas**

   - Buat sebagai post type 'task'
   - Input judul dan instruksi
   - Upload attachment instruksi
   - Set deadline
   - Set max score
   - Set allow late submission
   - Pilih kelas tujuan

2. **Pengumpulan Tugas (Siswa)**

   - View instruksi tugas
   - Input jawaban teks
   - Upload file jawaban
   - Submit sebelum deadline
   - View status pengumpulan

3. **Penilaian Tugas (Guru)**

   - View daftar pengumpulan
   - Review jawaban siswa
   - Download file jawaban
   - Input nilai
   - Berikan feedback
   - Update status ke graded

4. **Notifikasi**
   - Notif saat tugas baru
   - Notif saat mendekati deadline
   - Notif saat dinilai

**Implementasi Model Task:**

```php
class Task extends Model
{
    protected $fillable = [
        'post_id',
        'deadline',
        'max_score',
        'allow_late_submission',
    ];

    protected $casts = [
        'deadline' => 'datetime',
        'allow_late_submission' => 'boolean',
    ];

    // Relasi ke Post
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    // Relasi ke Submissions
    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    // Helper: Check if overdue
    public function isOverdue()
    {
        return $this->deadline && now()->gt($this->deadline);
    }

    // Helper: Get submission rate
    public function getSubmissionRate()
    {
        $total = $this->post->classroom->students()->count();
        $submitted = $this->submissions()->count();
        return $total > 0 ? ($submitted / $total) * 100 : 0;
    }
}
```

#### 5.1.3.7 Kelas Online dan Meeting

**Fitur yang diimplementasikan:**

1. **Schedule Meeting**

   - Input judul dan deskripsi
   - Pilih platform (Zoom, Google Meet, Teams, Jitsi)
   - Input meeting URL
   - Input meeting ID dan passcode
   - Set jadwal (tanggal dan waktu)
   - Set durasi
   - Pilih kelas tujuan

2. **Manage Meeting**

   - View upcoming meetings
   - Edit meeting details
   - Cancel meeting
   - Update status (ongoing, completed)

3. **Join Meeting**
   - View meeting list
   - Quick join dengan redirect ke meeting URL
   - Track participants

**Implementasi Model OnlineMeeting:**

```php
class OnlineMeeting extends Model
{
    protected $fillable = [
        'serial_id',
        'user_id',
        'classroom_id',
        'title',
        'description',
        'platform',
        'meeting_url',
        'meeting_id',
        'passcode',
        'scheduled_at',
        'duration',
        'status',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];

    // Relasi ke User (guru)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Classroom
    public function classroom()
    {
        return $this->belongsTo(Classroom::class);
    }

    // Relasi ke Participants
    public function participants()
    {
        return $this->hasMany(MeetingParticipant::class);
    }

    // Helper: Check if meeting is today
    public function isToday()
    {
        return $this->scheduled_at->isToday();
    }

    // Helper: Check if meeting is upcoming
    public function isUpcoming()
    {
        return $this->scheduled_at->isFuture();
    }
}
```

#### 5.1.3.8 Sistem Laporan

**Fitur yang diimplementasikan:**

1. **Laporan Harian**

   - Auto-generate laporan per hari
   - Tracking pengumpulan tugas
   - Tracking nilai latihan
   - View per tanggal
   - Filter per kelas

2. **Rekap Nilai**

   - View per kelas
   - View per siswa
   - Aggregate nilai tugas
   - Aggregate nilai latihan
   - Export PDF

3. **Laporan Kehadiran**

   - Tracking kehadiran meeting
   - Statistics per siswa
   - Export data

4. **Grade Reports**
   - Comprehensive grade report
   - Per mata pelajaran
   - Per periode
   - Export PDF

**Implementasi:**

```php
// Controller untuk Laporan Harian
class LaporanHarianController extends Controller
{
    public function index($serial)
    {
        // Get dates dengan aktivitas
        $dates = TaskSubmission::whereHas('task.post', function($q) use ($serial) {
            $q->where('serial_id', $serial);
        })
        ->selectRaw('DATE(submitted_at) as date')
        ->groupBy('date')
        ->orderByDesc('date')
        ->get();

        return view('laporan.harian.index', compact('dates', 'serial'));
    }

    public function show($serial, $date)
    {
        // Get submissions untuk tanggal tertentu
        $submissions = TaskSubmission::whereDate('submitted_at', $date)
            ->whereHas('task.post', function($q) use ($serial) {
                $q->where('serial_id', $serial);
            })
            ->with(['task.post', 'student'])
            ->get();

        return view('laporan.harian.show', compact('submissions', 'date', 'serial'));
    }

    public function grade($serial, $taskId, Request $request)
    {
        $submission = TaskSubmission::findOrFail($request->submission_id);

        $submission->update([
            'score' => $request->score,
            'feedback' => $request->feedback,
            'status' => 'graded',
            'graded_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Nilai berhasil diberikan');
    }
}
```

Dokumen BAB 5 ini akan dilanjutkan ke bagian **5.2 Pembahasan** dan **5.3 Pengujian**. Apakah Anda ingin saya lanjutkan ke bagian tersebut?
