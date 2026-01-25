# Query INSERT INTO - DashboardGuru System

Dokumentasi ini berisi query INSERT INTO untuk manipulasi data pada tabel-tabel utama di sistem DashboardGuru.

---

## A. Manipulasi Data Pada Tabel Users

**Deskripsi:** Menyimpan data pengguna (guru/admin)

```sql
INSERT INTO users (name, username, password, password_text, email, email_verified_at, role, address, phone, img, login_at, created_at, updated_at)
VALUES 
('Admin Utama', 'admin', '$2y$10$...hashedpassword...', 'admin123', 'admin@dashboardguru.com', NOW(), 1, 'Jl. Pendidikan No. 123', '081234567890', 'admin.jpg', NOW(), NOW(), NOW()),
('Guru Matematika', 'guru_math', '$2y$10$...hashedpassword...', 'guru123', 'guru.math@school.com', NOW(), 2, 'Jl. Guru No. 45', '082345678901', 'guru1.jpg', NOW(), NOW(), NOW());
```

**Penjelasan Kolom:**
- `id`: Auto increment (tidak perlu diisi)
- `role`: 1=Admin, 2=Guru
- `password`: Password terenkripsi menggunakan bcrypt
- `password_text`: Password dalam bentuk plain text (hanya untuk development)

---

## B. Manipulasi Data Pada Tabel Products

**Deskripsi:** Menyimpan data produk/paket pembelajaran

```sql
INSERT INTO products (lesson_id, name, grade, grade_category, semester, created_at, updated_at)
VALUES 
('1', 'Paket Matematika SD', 'SD', 'Kelas 1-6', 'Ganjil', NOW(), NOW()),
('2', 'Paket IPA SMP', 'SMP', 'Kelas 7-9', 'Genap', NOW(), NOW()),
('3', 'Paket Bahasa Indonesia SMA', 'SMA', 'Kelas 10-12', 'Ganjil', NOW(), NOW());
```

**Penjelasan Kolom:**
- `lesson_id`: ID pelajaran (nullable)
- `grade`: Tingkat pendidikan (SD/SMP/SMA)
- `grade_category`: Kategori kelas
- `semester`: Ganjil/Genap

---

## C. Manipulasi Data Pada Tabel Serials

**Deskripsi:** Menyimpan data serial/lisensi produk

```sql
INSERT INTO serials (user_id, product_id, serial, paket, active, expired_at, created_at, updated_at)
VALUES 
(1, 1, 'SRL-2025-MATH-001', 'A', 'yes', '2025-12-31 23:59:59', NOW(), NOW()),
(2, 2, 'SRL-2025-IPA-002', 'B', 'yes', '2026-06-30 23:59:59', NOW(), NOW()),
(NULL, 3, 'SRL-2025-BHS-003', 'C', 'no', '2025-12-31 23:59:59', NOW(), NOW());
```

**Penjelasan Kolom:**
- `user_id`: ID guru pemilik (nullable jika belum diaktifkan)
- `paket`: Tipe paket (A/B/C)
- `active`: Status aktif (yes/no)
- `expired_at`: Tanggal kadaluarsa

---

## D. Manipulasi Data Pada Tabel Classrooms

**Deskripsi:** Menyimpan data kelas

```sql
INSERT INTO classrooms (serial_id, name, grade, code, created_at, updated_at)
VALUES 
(1, 'Kelas 1A', '1', 'CLS-1A-2025', NOW(), NOW()),
(1, 'Kelas 2B', '2', 'CLS-2B-2025', NOW(), NOW()),
(2, 'Kelas 7A', '7', 'CLS-7A-2025', NOW(), NOW());
```

**Penjelasan Kolom:**
- `serial_id`: ID serial/lisensi
- `grade`: Tingkat kelas (1-12)
- `code`: Kode unik kelas

---

## E. Manipulasi Data Pada Tabel Students

**Deskripsi:** Menyimpan data siswa

```sql
INSERT INTO students (serial_id, user_id, classroom_id, name, username, password, password_text, nis, email, phone, created_at, updated_at)
VALUES 
(1, 1, 1, 'Ahmad Fadli', 'ahmad.fadli', '$2y$10$...hashedpassword...', 'siswa123', '2025001', 'ahmad@student.com', '081234567890', NOW(), NOW()),
(1, 1, 1, 'Siti Nurhaliza', 'siti.nur', '$2y$10$...hashedpassword...', 'siswa123', '2025002', 'siti@student.com', '082345678901', NOW(), NOW()),
(2, 2, 3, 'Budi Santoso', 'budi.santoso', '$2y$10$...hashedpassword...', 'siswa123', '2025003', 'budi@student.com', '083456789012', NOW(), NOW());
```

**Penjelasan Kolom:**
- `serial_id`: ID serial/lisensi
- `user_id`: ID guru pembuat
- `classroom_id`: ID kelas
- `nis`: Nomor Induk Siswa

---

## F. Manipulasi Data Pada Tabel Lessons

**Deskripsi:** Menyimpan data pelajaran/modul

```sql
INSERT INTO lessons (mapel_id, name, grade, semester, category, created_at, updated_at)
VALUES 
(1, 'Bilangan Bulat', '7', 1, 1, NOW(), NOW()),
(1, 'Aljabar Dasar', '7', 1, 1, NOW(), NOW()),
(3, 'Sistem Pernapasan', '8', 1, 1, NOW(), NOW()),
(2, 'Teks Narasi', '7', 2, 1, NOW(), NOW());
```

**Penjelasan Kolom:**
- `mapel_id`: ID mata pelajaran
- `grade`: Tingkat kelas
- `semester`: 1=Ganjil, 2=Genap
- `category`: Kategori pelajaran (1=default)

---

## G. Manipulasi Data Pada Tabel Lesson Items

**Deskripsi:** Menyimpan item/materi pembelajaran detail

```sql
INSERT INTO lesson_items (lesson_id, theme_id, subtheme_id, number, title, description, link, embed, attachment, is_admin, shared_to_classes, created_at, updated_at)
VALUES 
(1, 1, 1, 1, 'Pengenalan Bilangan Positif', 'Materi tentang bilangan positif dan cara penulisannya', 'https://example.com/materi1', NULL, 'materi1.pdf', 0, '[1,2]', NOW(), NOW()),
(1, 1, 2, 2, 'Video Pembelajaran Bilangan Negatif', 'Video interaktif tentang bilangan negatif', NULL, '<iframe src="https://youtube.com/embed/xxx"></iframe>', NULL, 1, '[1,2,3]', NOW(), NOW()),
(2, NULL, NULL, 1, 'Konsep Dasar Variabel', 'Penjelasan tentang variabel dalam aljabar', NULL, NULL, 'variabel.pptx', 0, NULL, NOW(), NOW());
```

**Penjelasan Kolom:**
- `number`: Nomor urut materi
- `is_admin`: 0=Dari guru, 1=Dari admin
- `shared_to_classes`: JSON array ID kelas yang dibagikan (nullable)

---

## H. Manipulasi Data Pada Tabel Exercise

**Deskripsi:** Menyimpan data latihan/ujian

```sql
INSERT INTO exercises (lesson_id, serial_id, exercise_type_id, title, description, is_admin, shared_to_classes, created_at, updated_at)
VALUES 
(1, 1, 1, 'Latihan Bilangan Bulat', 'Latihan soal tentang operasi bilangan bulat', 1, '[1,2]', NOW(), NOW()),
(2, 1, 2, 'Essay Aljabar', 'Soal essay tentang konsep aljabar', 0, '[1]', NOW(), NOW()),
(3, 2, 1, 'Quiz Sistem Pernapasan', 'Quiz pilihan ganda sistem pernapasan', 1, '[3]', NOW(), NOW());
```

**Penjelasan Kolom:**
- `lesson_id`: ID pelajaran
- `serial_id`: ID serial (nullable)
- `exercise_type_id`: ID tipe soal
- `is_admin`: 1=Dari admin, 0=Dari guru
- `shared_to_classes`: JSON array ID kelas (nullable)

---

## I. Manipulasi Data Pada Tabel Exercise Items

**Deskripsi:** Menyimpan item soal latihan

```sql
INSERT INTO exercise_items (admin_id, user_id, competence_id, exercise_id, exercise_type_id, exercise_model_id, exercise_choice, exercise_number, question, selection, answer, is_user, created_at, updated_at)
VALUES 
(1, NULL, 1, 1, 1, 1, 4, 1, 'Hasil dari -5 + 8 adalah...', '["A. -13","B. -3","C. 3","D. 13"]', 'C', 0, NOW(), NOW()),
(NULL, 2, 2, 2, 2, 2, 0, 1, 'Jelaskan pengertian variabel dalam aljabar!', NULL, 'Variabel adalah simbol yang mewakili suatu nilai yang belum diketahui', 1, NOW(), NOW()),
(1, NULL, 3, 3, 1, 1, 5, 1, 'Organ utama pernapasan manusia adalah...', '["A. Jantung","B. Paru-paru","C. Lambung","D. Ginjal","E. Hati"]', 'B', 0, NOW(), NOW());
```

**Penjelasan Kolom:**
- `admin_id`: ID admin pembuat (nullable)
- `user_id`: ID guru pembuat (nullable)
- `competence_id`: ID kompetensi (nullable)
- `exercise_choice`: Jumlah pilihan jawaban (0 untuk essay)
- `exercise_number`: Nomor urut soal
- `selection`: JSON array pilihan jawaban (nullable)
- `is_user`: 0=Admin, 1=Guru

---

## J. Manipulasi Data Pada Tabel Exercise Points

**Deskripsi:** Menyimpan postingan/pengumuman

```sql
INSERT INTO posts (serial_id, user_id, mapel_id, title, description, slug, link, attachment, embed, category, shared_to_classes, deadline, is_task, created_at, updated_at)
VALUES 
(1, 1, 1, 'Pengumuman Jadwal Ujian', 'Ujian matematika akan dilaksanakan minggu depan', 'pengumuman-jadwal-ujian', NULL, NULL, NULL, 'Pengumuman', '[1,2]', NULL, 0, NOW(), NOW()),
(1, 2, 3, 'Tugas Sistem Pernapasan', 'Kerjakan tugas tentang sistem pernapasan', 'tugas-sistem-pernapasan', NULL, 'tugas.pdf', NULL, 'Tugas', '[3]', '2025-01-20 23:59:59', 1, NOW(), NOW()),
(2, 2, 2, 'Materi Teks Narasi', 'Silakan pelajari materi berikut', 'materi-teks-narasi', 'https://example.com', NULL, NULL, 'Materi', '[1]', NULL, 0, NOW(), NOW());
```

**Penjelasan Kolom:**
- `slug`: URL-friendly version dari title
- `category`: Kategori post (Pengumuman/Tugas/Materi/dll)
- `shared_to_classes`: JSON array ID kelas
- `is_task`: 1=Tugas, 0=Bukan tugas

---

## L. Manipulasi Data Pada Tabel Tasks

**Deskripsi:** Menyimpan pengumpulan tugas siswa

```sql
INSERT INTO tasks (serial_id, post_id, student_id, description, attachment, point, created_at, updated_at)
VALUES 
(1, 2, 1, 'Saya sudah mengerjakan tugas sistem pernapasan', 'tugas_ahmad.pdf', '85', NOW(), NOW()),
(1, 2, 2, 'Berikut jawaban tugas saya', 'tugas_siti.pdf', '90', NOW(), NOW()),
(2, 2, 3, 'Tugas terlampir', 'tugas_budi.pdf', '75', NOW(), NOW());
```

**Penjelasan Kolom:**
- `serial_id`: ID serial
- `post_id`: ID post/pengumuman
- `student_id`: ID siswa
- `point`: Nilai tugas (0-100)

---

## M. Manipulasi Data Pada Tabel Task Submissions

**Deskripsi:** Menyimpan data kelas online/meeting

```sql
INSERT INTO online_meetings (serial_id, classroom_id, user_id, mapel_id, title, description, meeting_code, meeting_link, platform, start_time, end_time, status, created_at, updated_at)
VALUES 
(1, 1, 1, 1, 'Kelas Online Matematika', 'Pembahasan bilangan bulat', 'MTG-2025-001', NULL, 'jitsi', '2025-01-15 08:00:00', '2025-01-15 09:30:00', 'scheduled', NOW(), NOW()),
(1, 2, 2, 3, 'Kelas Online IPA', 'Sistem pernapasan manusia', 'MTG-2025-002', 'https://meet.google.com/xxx-yyyy-zzz', 'gmeet', '2025-01-16 10:00:00', '2025-01-16 11:00:00', 'scheduled', NOW(), NOW()),
(2, 3, 2, 2, 'Kelas Online Bahasa Indonesia', 'Teks narasi', 'MTG-2025-003', NULL, 'jitsi', '2025-01-14 13:00:00', '2025-01-14 14:00:00', 'ended', NOW(), NOW());
```

**Penjelasan Kolom:**
- `platform`: jitsi/zoom/gmeet/teams
- `status`: scheduled/ongoing/ended/cancelled
- `meeting_code`: Kode unik untuk join meeting

---

## 21. Tabel: exercise_points

**Deskripsi:** Menyimpan nilai latihan siswa

```sql
INSERT INTO exercise_points (serial_id, exercise_id, student_id, answer, competence_point, exercise_point, created_at, updated_at)
VALUES 
(1, 1, 1, '{"1":"C","2":"A","3":"B"}', '{"3.1":"80","4.1":"85"}', '85', NOW(), NOW()),
(1, 2, 2, '{"1":"Variabel adalah simbol..."}', '{"3.1":"90"}', '90', NOW(), NOW()),
(2, 3, 3, '{"1":"B","2":"C"}', '{"3.8":"75"}', '75', NOW(), NOW());
```

**Penjelasan Kolom:**
- `answer`: JSON jawaban siswa
- `competence_point`: JSON nilai per kompetensi (nullable)
- `exercise_point`: Nilai total (0-100)

---

## K. Manipulasi Data Pada Tabel Posts

**Deskripsi:** Menyimpan komentar pada post

```sql
INSERT INTO post_comments (post_id, user_id, student_id, message, code, is_user, created_at, updated_at)
VALUES 
(1, 1, NULL, 'Terima kasih atas pengumumannya', 'CMT-001', 1, NOW(), NOW()),
(2, NULL, 1, 'Apakah tugas ini dikumpulkan minggu ini?', 'CMT-002', 0, NOW(), NOW()),
(2, 2, NULL, 'Ya, dikumpulkan paling lambat Jumat', 'CMT-003', 1, NOW(), NOW());
```

**Penjelasan Kolom:**
- `user_id`: ID guru (nullable jika dari siswa)
- `student_id`: ID siswa (nullable jika dari guru)
- `code`: Kode unik komentar
- `is_user`: 1=Dari guru, 0=Dari siswa

---

## 23. Tabel: post_child_comments

**Deskripsi:** Menyimpan balasan komentar (struktur sama dengan post_comments)

```sql
INSERT INTO post_child_comments (post_id, user_id, student_id, message, code, is_user, created_at, updated_at)
VALUES 
(2, 1, NULL, 'Baik Bu, terima kasih', 'RCMT-001', 0, NOW(), NOW());
```

---

## 24. Tabel: lesson_classroom

**Deskripsi:** Pivot table untuk relasi lesson dan classroom

```sql
INSERT INTO lesson_classroom (lesson_id, classroom_id, created_at, updated_at)
VALUES 
(1, 1, NOW(), NOW()),
(1, 2, NOW(), NOW()),
(2, 1, NOW(), NOW()),
(3, 3, NOW(), NOW());
```

---

## 25. Tabel: exercise_classroom

**Deskripsi:** Pivot table untuk relasi exercise dan classroom

```sql
INSERT INTO exercise_classroom (exercise_id, classroom_id, created_at, updated_at)
VALUES 
(1, 1, NOW(), NOW()),
(1, 2, NOW(), NOW()),
(2, 1, NOW(), NOW()),
(3, 3, NOW(), NOW());
```

---

## 26. Tabel: report_types

**Deskripsi:** Menyimpan tipe laporan

```sql
INSERT INTO report_types (name, code, description, template, created_at, updated_at)
VALUES 
('Rapor Semester', 'RAPOR_SEMESTER', 'Laporan hasil belajar siswa per semester', '{"sections":["nilai","kehadiran","catatan"]}', NOW(), NOW()),
('Laporan Harian', 'LAPORAN_HARIAN', 'Laporan kegiatan belajar harian', '{"sections":["kegiatan","catatan"]}', NOW(), NOW()),
('Laporan Bulanan', 'LAPORAN_BULANAN', 'Laporan progres belajar bulanan', '{"sections":["nilai","kehadiran","prestasi"]}', NOW(), NOW());
```

**Penjelasan Kolom:**
- `code`: Kode unik tipe laporan
- `template`: JSON template laporan (nullable)

---

## 27. Tabel: grade_reports

**Deskripsi:** Menyimpan laporan nilai siswa

```sql
INSERT INTO grade_reports (student_id, mapel_id, classroom_id, semester, academic_year, attendance_score, assignment_score, quiz_score, uh_score, pts_score, pas_score, final_score, grade, notes, created_at, updated_at)
VALUES 
(1, 1, 1, 1, '2024/2025', 95.00, 85.50, 80.00, 88.00, 90.00, 87.50, 86.83, 'A', 'Siswa sangat aktif dan memiliki pemahaman yang baik', NOW(), NOW()),
(2, 1, 1, 1, '2024/2025', 100.00, 90.00, 85.00, 92.00, 95.00, 93.00, 91.67, 'A', 'Prestasi sangat memuaskan', NOW(), NOW()),
(3, 3, 3, 1, '2024/2025', 90.00, 75.00, 70.00, 78.00, 80.00, 76.00, 76.50, 'B', 'Perlu lebih banyak latihan', NOW(), NOW());
```

**Penjelasan Kolom:**
- `semester`: 1=Ganjil, 2=Genap
- `attendance_score`: Nilai kehadiran
- `assignment_score`: Nilai tugas
- `quiz_score`: Nilai kuis
- `uh_score`: Nilai ulangan harian
- `pts_score`: Nilai PTS (Penilaian Tengah Semester)
- `pas_score`: Nilai PAS (Penilaian Akhir Semester)
- `final_score`: Nilai akhir
- `grade`: Nilai huruf (A/B/C/D/E)

---

## 28. Tabel: student_reports

**Deskripsi:** Menyimpan laporan siswa terstruktur

```sql
INSERT INTO student_reports (student_id, classroom_id, report_type_id, period_start, period_end, title, content, attachments, status, submitted_at, reviewed_by, reviewed_at, feedback, created_at, updated_at)
VALUES 
(1, 1, 1, '2025-01-01', '2025-06-30', 'Rapor Semester Ganjil 2024/2025', 'Laporan nilai dan kehadiran semester ganjil', '["rapor.pdf","sertifikat.jpg"]', 'approved', '2025-06-25 10:00:00', 1, '2025-06-26 14:00:00', 'Rapor sudah sesuai', NOW(), NOW()),
(2, 1, 2, '2025-01-01', '2025-01-31', 'Laporan Bulanan Januari 2025', 'Progres belajar bulan Januari sangat baik', '["laporan_jan.pdf"]', 'submitted', '2025-02-01 09:00:00', NULL, NULL, NULL, NOW(), NOW());
```

**Penjelasan Kolom:**
- `status`: draft/submitted/reviewed/approved
- `attachments`: JSON array file attachments (nullable)
- `reviewed_by`: ID user yang mereview (nullable)

---

## 29. Tabel: attendance_reports

**Deskripsi:** Menyimpan data kehadiran siswa

```sql
INSERT INTO attendance_reports (student_id, classroom_id, mapel_id, date, status, notes, created_by, created_at, updated_at)
VALUES 
(1, 1, 1, '2025-01-10', 'present', NULL, 1, NOW(), NOW()),
(2, 1, 1, '2025-01-10', 'present', NULL, 1, NOW(), NOW()),
(3, 3, 3, '2025-01-10', 'sick', 'Sakit flu', 2, NOW(), NOW()),
(1, 1, 1, '2025-01-11', 'late', 'Terlambat 15 menit', 1, NOW(), NOW()),
(2, 1, 1, '2025-01-11', 'absent', 'Izin keperluan keluarga', 1, NOW(), NOW());
```

**Penjelasan Kolom:**
- `status`: present/absent/sick/permission/late
- `mapel_id`: ID mata pelajaran (nullable untuk absensi harian)
- `created_by`: ID guru yang mencatat kehadiran

---

## 30. Tabel: meeting_participants

**Deskripsi:** Menyimpan data peserta meeting online

```sql
INSERT INTO meeting_participants (meeting_id, user_id, student_id, joined_at, left_at, duration, status, created_at, updated_at)
VALUES 
(1, 1, NULL, '2025-01-15 08:00:00', '2025-01-15 09:30:00', 90, 'joined', NOW(), NOW()),
(1, NULL, 1, '2025-01-15 08:05:00', '2025-01-15 09:30:00', 85, 'joined', NOW(), NOW()),
(1, NULL, 2, NULL, NULL, 0, 'absent', NOW(), NOW()),
(2, 2, NULL, '2025-01-16 10:00:00', NULL, 0, 'joined', NOW(), NOW()),
(2, NULL, 3, '2025-01-16 10:10:00', '2025-01-16 11:00:00', 50, 'joined', NOW(), NOW());
```

**Penjelasan Kolom:**
- `user_id`: ID guru (untuk peserta guru)
- `student_id`: ID siswa (untuk peserta siswa)
- `duration`: Durasi kehadiran dalam menit
- `status`: invited/joined/absent

---

## 31. Tabel: task_submissions

**Deskripsi:** Menyimpan pengumpulan tugas siswa (sistem baru)

```sql
INSERT INTO task_submissions (task_id, student_id, user_id, content, file_path, submitted_at, score, feedback, graded_by, graded_at, status, created_at, updated_at)
VALUES 
(1, 1, 1, 'Jawaban lengkap tugas sistem pernapasan terlampir', 'submissions/task1_student1.pdf', '2025-01-18 14:30:00', 85.00, 'Penjelasan sudah baik, diagram perlu diperjelas', 1, '2025-01-19 10:00:00', 'graded', NOW(), NOW()),
(1, 2, 1, 'Berikut jawaban saya untuk tugas ini', 'submissions/task1_student2.pdf', '2025-01-19 16:00:00', 90.00, 'Sangat baik, lengkap dan detail', 1, '2025-01-20 09:00:00', 'graded', NOW(), NOW()),
(2, 3, 2, 'Jawaban tugas aljabar', 'submissions/task2_student3.pdf', '2025-01-21 10:00:00', NULL, NULL, NULL, NULL, 'submitted', NOW(), NOW());
```

**Penjelasan Kolom:**
- `task_id`: ID tugas
- `user_id`: ID guru pembuat tugas
- `content`: Konten jawaban text (nullable)
- `file_path`: Path file jawaban (nullable)
- `graded_by`: ID guru yang menilai (nullable)
- `status`: submitted/graded/late

---

## N. Manipulasi Data Pada Tabel Online Meetings

**Deskripsi:** Menyimpan token reset password

```sql
INSERT INTO password_reset_tokens (email, token, created_at)
VALUES 
('admin@dashboardguru.com', '$2y$10$...hashedtoken...', NOW()),
('guru.math@school.com', '$2y$10$...hashedtoken...', NOW());
```

**Penjelasan Kolom:**
- `email`: Primary key, email pengguna
- `token`: Token reset password terenkripsi
- `created_at`: Waktu pembuatan token

---

## 33. Tabel: sessions

**Deskripsi:** Menyimpan data sesi pengguna

```sql
INSERT INTO sessions (id, user_id, ip_address, user_agent, payload, last_activity)
VALUES 
('session123abc', 1, '192.168.1.100', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiWXJLdGJUd3pJVGxTMVBvZ2RtQ2...', 1705400000),
('session456def', 2, '192.168.1.101', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiQmNLdGJUd3pJVGxTMVBvZ2RtQ2...', 1705400100);
```

**Penjelasan Kolom:**
- `id`: Primary key, ID sesi unik
- `payload`: Data sesi terenkripsi dalam format base64
- `last_activity`: Unix timestamp aktivitas terakhir

---

## 34. Tabel: cache

**Deskripsi:** Menyimpan data cache aplikasi

```sql
INSERT INTO cache (`key`, `value`, expiration)
VALUES 
('laravel_cache:users_count', 's:2:"25";', 1705500000),
('laravel_cache:students_active', 's:3:"150";', 1705510000);
```

---

## 35. Tabel: cache_locks

**Deskripsi:** Menyimpan lock cache untuk concurrent access

```sql
INSERT INTO cache_locks (`key`, owner, expiration)
VALUES 
('laravel_cache:migration_lock', 'server1', 1705400000);
```

---

## 36. Tabel: jobs

**Deskripsi:** Menyimpan antrian pekerjaan background

```sql
INSERT INTO jobs (queue, payload, attempts, reserved_at, available_at, created_at)
VALUES 
('default', '{"displayName":"App\\\\Jobs\\\\SendEmailNotification","job":"Illuminate\\\\Queue\\\\CallQueuedHandler@call"}', 0, NULL, 1705400000, 1705400000);
```

---

## 37. Tabel: job_batches

**Deskripsi:** Menyimpan batch pekerjaan

```sql
INSERT INTO job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at)
VALUES 
('batch123', 'Send Daily Reports', 100, 0, 0, '[]', NULL, NULL, 1705400000, 1705410000);
```

---

## 38. Tabel: failed_jobs

**Deskripsi:** Menyimpan pekerjaan yang gagal

```sql
INSERT INTO failed_jobs (uuid, connection, queue, payload, exception, failed_at)
VALUES 
('uuid-123-abc', 'database', 'default', '{"job":"App\\\\Jobs\\\\SendEmail"}', 'Exception: SMTP connection failed', NOW());
```

---

## Catatan Penting:

1. **Password Hashing**: Semua password harus di-hash menggunakan bcrypt. Gunakan `bcrypt('password')` di Laravel atau `password_hash()` di PHP.

2. **Timestamps**: Kolom `created_at` dan `updated_at` otomatis dikelola oleh Laravel. Gunakan `NOW()` atau biarkan Laravel yang mengisi.

3. **Foreign Keys**: Pastikan referensi ID yang valid sudah ada sebelum melakukan INSERT.

4. **JSON Fields**: Kolom dengan tipe JSON harus berisi valid JSON string, contoh: `'["value1","value2"]'` atau `'{"key":"value"}'`

5. **Enum Values**: Pastikan nilai yang dimasukkan sesuai dengan definisi enum di migration.

6. **Nullable Fields**: Kolom yang nullable bisa diisi dengan `NULL` jika tidak ada data.

7. **Auto Increment**: Kolom `id` tidak perlu diisi karena auto increment.

8. **Unique Constraints**: Perhatikan kolom dengan constraint unique seperti `email`, `code`, `serial`, dll.

## Contoh Penggunaan Batch Insert:

```sql
-- Insert multiple records sekaligus
INSERT INTO students (serial_id, user_id, classroom_id, name, username, password, password_text, nis, created_at, updated_at)
VALUES 
(1, 1, 1, 'Student 1', 'student1', '$2y$10$...', 'pass123', '2025001', NOW(), NOW()),
(1, 1, 1, 'Student 2', 'student2', '$2y$10$...', 'pass123', '2025002', NOW(), NOW()),
(1, 1, 1, 'Student 3', 'student3', '$2y$10$...', 'pass123', '2025003', NOW(), NOW());
```

---

**Dokumen dibuat:** 10 Januari 2026
**Versi:** 1.0
**Sistem:** DashboardGuru Application
