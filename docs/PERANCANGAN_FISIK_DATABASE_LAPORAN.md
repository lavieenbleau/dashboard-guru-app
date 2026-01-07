# PERANCANGAN FISIK BASIS DATA - SISTEM LAPORAN

## DashboardGuru ISCP

---

## 1. PENDAHULUAN

### 1.1 Latar Belakang

Dokumen ini menjelaskan perancangan fisik basis data khusus untuk modul pelaporan pada sistem DashboardGuru ISCP (Integrated School Curriculum Platform). Sistem laporan dirancang untuk mendukung:

- Rekap nilai siswa
- Laporan harian siswa
- Laporan kehadiran
- Laporan pengumpulan tugas
- Laporan hasil latihan/ujian
- Laporan aktivitas kelas online

### 1.2 Tujuan Perancangan

- Menyediakan struktur data yang terorganisir untuk berbagai jenis laporan
- Mendukung multiple format laporan (harian, mingguan, bulanan, semester)
- Memudahkan tracking dan monitoring progres siswa
- Mengoptimalkan query untuk generate laporan yang kompleks

---

## 2. TABEL EKSISTING YANG DIGUNAKAN

### 2.1 Tabel Master

#### users

Tabel pengguna (guru dan admin)

```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(100) NOT NULL,
    password VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    role TINYINT NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### students

Tabel data siswa

```sql
CREATE TABLE students (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    serial_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    classroom_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(200) NOT NULL,
    username VARCHAR(100) NOT NULL,
    nis VARCHAR(20),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (serial_id) REFERENCES serials(id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id)
);
```

#### classrooms

Tabel kelas

```sql
CREATE TABLE classrooms (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    serial_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(100) NOT NULL,
    grade VARCHAR(10) NOT NULL,
    code VARCHAR(24) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    FOREIGN KEY (serial_id) REFERENCES serials(id)
);
```

#### mapels

Tabel mata pelajaran

```sql
CREATE TABLE mapels (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 2.2 Tabel Transaksi

#### reports (Eksisting)

Tabel laporan harian siswa yang sudah ada

```sql
CREATE TABLE reports (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    serial_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    report TEXT NOT NULL,
    img VARCHAR(50),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### exercise_points

Tabel nilai latihan/ujian

```sql
CREATE TABLE exercise_points (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    serial_id BIGINT UNSIGNED NOT NULL,
    exercise_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    final_score DECIMAL(5,2) DEFAULT 0,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

#### tasks

Tabel tugas

```sql
CREATE TABLE tasks (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    serial_id BIGINT UNSIGNED NOT NULL,
    student_id BIGINT UNSIGNED NOT NULL,
    post_id BIGINT UNSIGNED NOT NULL,
    score DECIMAL(5,2),
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

---

## 3. TABEL BARU UNTUK SISTEM LAPORAN

### 3.1 grade_summaries

**Tabel rekap nilai per mata pelajaran per siswa**

```sql
CREATE TABLE grade_summaries (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    classroom_id BIGINT UNSIGNED NOT NULL,
    mapel_id BIGINT UNSIGNED NOT NULL,
    semester TINYINT NOT NULL COMMENT '1 atau 2',
    academic_year VARCHAR(20) NOT NULL COMMENT 'Format: 2025/2026',

    -- Komponen Nilai
    attendance_count INT DEFAULT 0 COMMENT 'Jumlah kehadiran',
    total_meetings INT DEFAULT 0 COMMENT 'Total pertemuan',
    attendance_percentage DECIMAL(5,2) DEFAULT 0,

    assignment_avg DECIMAL(5,2) DEFAULT 0 COMMENT 'Rata-rata tugas',
    assignment_count INT DEFAULT 0 COMMENT 'Jumlah tugas',

    quiz_avg DECIMAL(5,2) DEFAULT 0 COMMENT 'Rata-rata kuis',
    quiz_count INT DEFAULT 0 COMMENT 'Jumlah kuis',

    uh_avg DECIMAL(5,2) DEFAULT 0 COMMENT 'Rata-rata Ulangan Harian',
    uh_count INT DEFAULT 0,

    pts_score DECIMAL(5,2) DEFAULT 0 COMMENT 'Nilai PTS',
    pas_score DECIMAL(5,2) DEFAULT 0 COMMENT 'Nilai PAS',

    -- Nilai Akhir
    final_score DECIMAL(5,2) DEFAULT 0 COMMENT 'Nilai akhir (0-100)',
    letter_grade CHAR(2) COMMENT 'A, B, C, D',
    predicate VARCHAR(20) COMMENT 'Sangat Baik, Baik, Cukup, Kurang',

    -- Catatan
    teacher_notes TEXT,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE,
    FOREIGN KEY (mapel_id) REFERENCES mapels(id) ON DELETE CASCADE,

    UNIQUE KEY unique_grade (student_id, mapel_id, semester, academic_year),
    INDEX idx_classroom_semester (classroom_id, semester, academic_year),
    INDEX idx_academic_year (academic_year)
);
```

**Keterangan:**

- Menyimpan rekap nilai per mata pelajaran untuk setiap siswa
- Mendukung perhitungan otomatis nilai akhir
- Bisa di-generate dari data tasks, exercise_points, dan attendance

---

### 3.2 attendance_logs

**Tabel log kehadiran siswa**

```sql
CREATE TABLE attendance_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    classroom_id BIGINT UNSIGNED NOT NULL,
    mapel_id BIGINT UNSIGNED NULL COMMENT 'NULL jika kehadiran umum',
    meeting_id BIGINT UNSIGNED NULL COMMENT 'Ref ke online_meetings jika ada',

    attendance_date DATE NOT NULL,
    status ENUM('present', 'absent', 'sick', 'permission', 'late') NOT NULL,

    check_in_time TIME,
    check_out_time TIME,

    notes TEXT COMMENT 'Catatan kehadiran',
    attachment VARCHAR(255) COMMENT 'File surat izin/keterangan',

    recorded_by BIGINT UNSIGNED NOT NULL COMMENT 'Guru yang mencatat',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE,
    FOREIGN KEY (mapel_id) REFERENCES mapels(id) ON DELETE SET NULL,
    FOREIGN KEY (meeting_id) REFERENCES online_meetings(id) ON DELETE SET NULL,
    FOREIGN KEY (recorded_by) REFERENCES users(id) ON DELETE CASCADE,

    UNIQUE KEY unique_attendance (student_id, classroom_id, attendance_date, mapel_id),
    INDEX idx_date (attendance_date),
    INDEX idx_student_date (student_id, attendance_date),
    INDEX idx_classroom_date (classroom_id, attendance_date)
);
```

**Keterangan:**

- Tracking kehadiran harian siswa
- Mendukung kehadiran per mata pelajaran atau umum
- Bisa terintegrasi dengan online meetings
- Mendukung upload surat izin

---

### 3.3 report_cards

**Tabel rapor siswa (rekap semester)**

```sql
CREATE TABLE report_cards (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    classroom_id BIGINT UNSIGNED NOT NULL,
    semester TINYINT NOT NULL,
    academic_year VARCHAR(20) NOT NULL,

    -- Informasi Umum
    total_attendance INT DEFAULT 0,
    total_absence INT DEFAULT 0,
    total_sick INT DEFAULT 0,
    total_permission INT DEFAULT 0,

    -- GPA
    total_subjects INT DEFAULT 0,
    gpa DECIMAL(4,2) DEFAULT 0 COMMENT 'Rata-rata nilai',

    -- Ranking
    class_rank INT COMMENT 'Ranking di kelas',
    total_students INT COMMENT 'Total siswa di kelas',

    -- Catatan Wali Kelas
    homeroom_teacher_notes TEXT,

    -- Status
    status ENUM('draft', 'published', 'archived') DEFAULT 'draft',
    published_at TIMESTAMP NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE,

    UNIQUE KEY unique_report_card (student_id, semester, academic_year),
    INDEX idx_classroom_semester (classroom_id, semester, academic_year)
);
```

**Keterangan:**

- Rapor siswa per semester
- Auto-calculate dari grade_summaries
- Termasuk ranking dan GPA
- Support workflow draft → published

---

### 3.4 daily_activity_logs

**Tabel log aktivitas harian siswa**

```sql
CREATE TABLE daily_activity_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    student_id BIGINT UNSIGNED NOT NULL,
    classroom_id BIGINT UNSIGNED NOT NULL,
    activity_date DATE NOT NULL,

    -- Jenis Aktivitas
    activity_type ENUM('lesson', 'task', 'exercise', 'meeting', 'discussion', 'report') NOT NULL,

    -- Referensi ke tabel lain
    reference_id BIGINT UNSIGNED COMMENT 'ID lesson/task/exercise/dll',
    reference_type VARCHAR(50) COMMENT 'Type: lessons, tasks, exercises, dll',

    -- Detail Aktivitas
    title VARCHAR(255),
    description TEXT,
    duration_minutes INT DEFAULT 0,

    -- Status Completion
    status ENUM('started', 'in_progress', 'completed', 'submitted') DEFAULT 'started',
    completed_at TIMESTAMP NULL,

    -- Score (jika applicable)
    score DECIMAL(5,2) NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE CASCADE,

    INDEX idx_student_date (student_id, activity_date),
    INDEX idx_classroom_date (classroom_id, activity_date),
    INDEX idx_type (activity_type),
    INDEX idx_reference (reference_type, reference_id)
);
```

**Keterangan:**

- Tracking semua aktivitas siswa per hari
- Polymorphic reference ke berbagai tabel
- Mendukung generate laporan aktivitas harian/mingguan
- Bisa untuk analisis engagement siswa

---

### 3.5 report_templates

**Tabel template laporan**

```sql
CREATE TABLE report_templates (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    type ENUM('daily', 'weekly', 'monthly', 'semester', 'annual', 'custom') NOT NULL,
    description TEXT,

    -- Template Configuration
    template_config JSON COMMENT 'Konfigurasi field yang ditampilkan',

    -- Layout
    header_content TEXT,
    footer_content TEXT,

    -- Status
    is_active BOOLEAN DEFAULT TRUE,
    is_default BOOLEAN DEFAULT FALSE,

    created_by BIGINT UNSIGNED,
    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,

    INDEX idx_type (type),
    INDEX idx_active (is_active)
);
```

**Keterangan:**

- Template untuk berbagai jenis laporan
- JSON config untuk flexible field configuration
- Support custom template per sekolah/guru

---

### 3.6 report_archives

**Tabel arsip laporan yang sudah di-generate**

```sql
CREATE TABLE report_archives (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    report_type ENUM('grade_summary', 'report_card', 'attendance', 'activity', 'custom') NOT NULL,

    -- Target Laporan
    student_id BIGINT UNSIGNED NULL COMMENT 'NULL jika laporan kelas',
    classroom_id BIGINT UNSIGNED NULL,

    -- Periode
    period_start DATE,
    period_end DATE,
    semester TINYINT NULL,
    academic_year VARCHAR(20) NULL,

    -- File
    title VARCHAR(255) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type VARCHAR(10) DEFAULT 'pdf' COMMENT 'pdf, xlsx, csv',
    file_size INT COMMENT 'Dalam bytes',

    -- Metadata
    template_id BIGINT UNSIGNED NULL,
    generated_data JSON COMMENT 'Snapshot data saat generate',

    -- Tracking
    generated_by BIGINT UNSIGNED NOT NULL,
    generated_at TIMESTAMP NOT NULL,
    downloaded_count INT DEFAULT 0,
    last_downloaded_at TIMESTAMP NULL,

    created_at TIMESTAMP,
    updated_at TIMESTAMP,

    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE SET NULL,
    FOREIGN KEY (classroom_id) REFERENCES classrooms(id) ON DELETE SET NULL,
    FOREIGN KEY (template_id) REFERENCES report_templates(id) ON DELETE SET NULL,
    FOREIGN KEY (generated_by) REFERENCES users(id) ON DELETE CASCADE,

    INDEX idx_type (report_type),
    INDEX idx_student (student_id),
    INDEX idx_classroom (classroom_id),
    INDEX idx_period (period_start, period_end),
    INDEX idx_academic (academic_year, semester)
);
```

**Keterangan:**

- Menyimpan file laporan yang sudah di-generate
- Mendukung re-download tanpa perlu re-generate
- Tracking download statistics
- JSON snapshot untuk audit trail

---

## 4. DIAGRAM RELASI TABEL LAPORAN

```
┌─────────────────┐
│    students     │
└────────┬────────┘
         │
         ├──────────────────────┐
         │                      │
         ▼                      ▼
┌─────────────────┐    ┌──────────────────┐
│ grade_summaries │    │ attendance_logs  │
└────────┬────────┘    └────────┬─────────┘
         │                      │
         │             ┌────────┴─────────┐
         │             │                  │
         ▼             ▼                  ▼
┌─────────────────┐  ┌─────────────────┐ ┌──────────────────────┐
│  report_cards   │  │daily_activity_  │ │  report_archives     │
│                 │  │      logs       │ │                      │
└─────────────────┘  └─────────────────┘ └──────────────────────┘
                              │
                              ▼
                     ┌─────────────────┐
                     │report_templates │
                     └─────────────────┘
```

---

## 5. QUERY OPTIMASI

### 5.1 Index Strategy

**Composite Indexes:**

```sql
-- Untuk query laporan nilai per kelas per semester
CREATE INDEX idx_grade_class_semester
ON grade_summaries(classroom_id, semester, academic_year);

-- Untuk query kehadiran bulanan
CREATE INDEX idx_attendance_month
ON attendance_logs(student_id, YEAR(attendance_date), MONTH(attendance_date));

-- Untuk query aktivitas range tanggal
CREATE INDEX idx_activity_range
ON daily_activity_logs(student_id, activity_date, activity_type);
```

**Covering Indexes:**

```sql
-- Untuk count kehadiran cepat
CREATE INDEX idx_attendance_status_covering
ON attendance_logs(student_id, classroom_id, status, attendance_date);
```

### 5.2 Partitioning Strategy

Untuk tabel yang besar, gunakan partitioning by academic year:

```sql
-- Partisi attendance_logs by year
ALTER TABLE attendance_logs
PARTITION BY RANGE (YEAR(attendance_date)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);

-- Partisi daily_activity_logs by year
ALTER TABLE daily_activity_logs
PARTITION BY RANGE (YEAR(activity_date)) (
    PARTITION p2024 VALUES LESS THAN (2025),
    PARTITION p2025 VALUES LESS THAN (2026),
    PARTITION p2026 VALUES LESS THAN (2027),
    PARTITION p_future VALUES LESS THAN MAXVALUE
);
```

---

## 6. STORED PROCEDURES & FUNCTIONS

### 6.1 Calculate Grade Summary

```sql
DELIMITER //

CREATE PROCEDURE sp_calculate_grade_summary(
    IN p_student_id BIGINT,
    IN p_mapel_id BIGINT,
    IN p_semester TINYINT,
    IN p_academic_year VARCHAR(20)
)
BEGIN
    -- Hitung attendance
    DECLARE v_attendance_count INT;
    DECLARE v_total_meetings INT;
    DECLARE v_attendance_pct DECIMAL(5,2);

    -- Hitung nilai tugas
    DECLARE v_assignment_avg DECIMAL(5,2);
    DECLARE v_assignment_count INT;

    -- Hitung nilai latihan
    DECLARE v_quiz_avg DECIMAL(5,2);
    DECLARE v_uh_avg DECIMAL(5,2);
    DECLARE v_pts DECIMAL(5,2);
    DECLARE v_pas DECIMAL(5,2);

    -- Nilai akhir
    DECLARE v_final_score DECIMAL(5,2);
    DECLARE v_letter_grade CHAR(2);

    -- Get student's classroom
    DECLARE v_classroom_id BIGINT;
    SELECT classroom_id INTO v_classroom_id
    FROM students WHERE id = p_student_id;

    -- Hitung kehadiran
    SELECT
        COUNT(CASE WHEN status = 'present' THEN 1 END),
        COUNT(*),
        (COUNT(CASE WHEN status = 'present' THEN 1 END) / COUNT(*)) * 100
    INTO v_attendance_count, v_total_meetings, v_attendance_pct
    FROM attendance_logs
    WHERE student_id = p_student_id
    AND mapel_id = p_mapel_id
    AND attendance_date BETWEEN
        (CASE WHEN p_semester = 1 THEN CONCAT(p_academic_year, '-07-01') ELSE CONCAT(p_academic_year, '-01-01') END)
        AND
        (CASE WHEN p_semester = 1 THEN CONCAT(p_academic_year, '-12-31') ELSE CONCAT(p_academic_year, '-06-30') END);

    -- Hitung rata-rata tugas
    SELECT AVG(score), COUNT(*)
    INTO v_assignment_avg, v_assignment_count
    FROM tasks
    WHERE student_id = p_student_id;

    -- Hitung nilai latihan berdasarkan tipe
    SELECT
        AVG(CASE WHEN et.name = 'Kuis' THEN ep.final_score END),
        AVG(CASE WHEN et.name = 'UH' THEN ep.final_score END),
        AVG(CASE WHEN et.name = 'PTS' THEN ep.final_score END),
        AVG(CASE WHEN et.name = 'PAS' THEN ep.final_score END)
    INTO v_quiz_avg, v_uh_avg, v_pts, v_pas
    FROM exercise_points ep
    JOIN exercises e ON ep.exercise_id = e.id
    JOIN exercise_types et ON e.exercise_type_id = et.id
    WHERE ep.student_id = p_student_id
    AND e.mapel_id = p_mapel_id;

    -- Hitung nilai akhir (formula: 20% tugas + 20% kuis + 30% UH + 15% PTS + 15% PAS)
    SET v_final_score = (
        (COALESCE(v_assignment_avg, 0) * 0.20) +
        (COALESCE(v_quiz_avg, 0) * 0.20) +
        (COALESCE(v_uh_avg, 0) * 0.30) +
        (COALESCE(v_pts, 0) * 0.15) +
        (COALESCE(v_pas, 0) * 0.15)
    );

    -- Tentukan letter grade
    SET v_letter_grade = CASE
        WHEN v_final_score >= 85 THEN 'A'
        WHEN v_final_score >= 70 THEN 'B'
        WHEN v_final_score >= 60 THEN 'C'
        ELSE 'D'
    END;

    -- Insert or Update grade_summaries
    INSERT INTO grade_summaries (
        student_id, classroom_id, mapel_id, semester, academic_year,
        attendance_count, total_meetings, attendance_percentage,
        assignment_avg, assignment_count,
        quiz_avg, uh_avg, pts_score, pas_score,
        final_score, letter_grade,
        created_at, updated_at
    ) VALUES (
        p_student_id, v_classroom_id, p_mapel_id, p_semester, p_academic_year,
        v_attendance_count, v_total_meetings, v_attendance_pct,
        v_assignment_avg, v_assignment_count,
        v_quiz_avg, v_uh_avg, v_pts, v_pas,
        v_final_score, v_letter_grade,
        NOW(), NOW()
    )
    ON DUPLICATE KEY UPDATE
        attendance_count = v_attendance_count,
        total_meetings = v_total_meetings,
        attendance_percentage = v_attendance_pct,
        assignment_avg = v_assignment_avg,
        assignment_count = v_assignment_count,
        quiz_avg = v_quiz_avg,
        uh_avg = v_uh_avg,
        pts_score = v_pts,
        pas_score = v_pas,
        final_score = v_final_score,
        letter_grade = v_letter_grade,
        updated_at = NOW();
END //

DELIMITER ;
```

### 6.2 Generate Report Card

```sql
DELIMITER //

CREATE PROCEDURE sp_generate_report_card(
    IN p_student_id BIGINT,
    IN p_semester TINYINT,
    IN p_academic_year VARCHAR(20)
)
BEGIN
    DECLARE v_classroom_id BIGINT;
    DECLARE v_total_subjects INT;
    DECLARE v_gpa DECIMAL(4,2);
    DECLARE v_class_rank INT;
    DECLARE v_total_students INT;

    -- Get attendance summary
    DECLARE v_present INT;
    DECLARE v_absent INT;
    DECLARE v_sick INT;
    DECLARE v_permission INT;

    SELECT classroom_id INTO v_classroom_id
    FROM students WHERE id = p_student_id;

    -- Calculate GPA
    SELECT COUNT(*), AVG(final_score)
    INTO v_total_subjects, v_gpa
    FROM grade_summaries
    WHERE student_id = p_student_id
    AND semester = p_semester
    AND academic_year = p_academic_year;

    -- Calculate rank
    SELECT COUNT(*) + 1 INTO v_class_rank
    FROM (
        SELECT s.id, AVG(gs.final_score) as avg_score
        FROM students s
        JOIN grade_summaries gs ON s.id = gs.student_id
        WHERE s.classroom_id = v_classroom_id
        AND gs.semester = p_semester
        AND gs.academic_year = p_academic_year
        GROUP BY s.id
        HAVING avg_score > v_gpa
    ) as rankings;

    SELECT COUNT(*) INTO v_total_students
    FROM students WHERE classroom_id = v_classroom_id;

    -- Get attendance
    SELECT
        COUNT(CASE WHEN status = 'present' THEN 1 END),
        COUNT(CASE WHEN status = 'absent' THEN 1 END),
        COUNT(CASE WHEN status = 'sick' THEN 1 END),
        COUNT(CASE WHEN status = 'permission' THEN 1 END)
    INTO v_present, v_absent, v_sick, v_permission
    FROM attendance_logs
    WHERE student_id = p_student_id
    AND attendance_date BETWEEN
        (CASE WHEN p_semester = 1 THEN CONCAT(SUBSTR(p_academic_year, 1, 4), '-07-01')
              ELSE CONCAT(SUBSTR(p_academic_year, 6, 4), '-01-01') END)
        AND
        (CASE WHEN p_semester = 1 THEN CONCAT(SUBSTR(p_academic_year, 1, 4), '-12-31')
              ELSE CONCAT(SUBSTR(p_academic_year, 6, 4), '-06-30') END);

    -- Insert or update report card
    INSERT INTO report_cards (
        student_id, classroom_id, semester, academic_year,
        total_attendance, total_absence, total_sick, total_permission,
        total_subjects, gpa, class_rank, total_students,
        status, created_at, updated_at
    ) VALUES (
        p_student_id, v_classroom_id, p_semester, p_academic_year,
        v_present, v_absent, v_sick, v_permission,
        v_total_subjects, v_gpa, v_class_rank, v_total_students,
        'draft', NOW(), NOW()
    )
    ON DUPLICATE KEY UPDATE
        total_attendance = v_present,
        total_absence = v_absent,
        total_sick = v_sick,
        total_permission = v_permission,
        total_subjects = v_total_subjects,
        gpa = v_gpa,
        class_rank = v_class_rank,
        total_students = v_total_students,
        updated_at = NOW();
END //

DELIMITER ;
```

---

## 7. VIEWS UNTUK REPORTING

### 7.1 View Rekap Nilai Kelas

```sql
CREATE VIEW v_class_grade_summary AS
SELECT
    c.id as classroom_id,
    c.name as classroom_name,
    m.id as mapel_id,
    m.name as mapel_name,
    gs.semester,
    gs.academic_year,
    COUNT(DISTINCT gs.student_id) as total_students,
    AVG(gs.final_score) as class_average,
    MAX(gs.final_score) as highest_score,
    MIN(gs.final_score) as lowest_score,
    COUNT(CASE WHEN gs.letter_grade = 'A' THEN 1 END) as grade_a_count,
    COUNT(CASE WHEN gs.letter_grade = 'B' THEN 1 END) as grade_b_count,
    COUNT(CASE WHEN gs.letter_grade = 'C' THEN 1 END) as grade_c_count,
    COUNT(CASE WHEN gs.letter_grade = 'D' THEN 1 END) as grade_d_count
FROM grade_summaries gs
JOIN students s ON gs.student_id = s.id
JOIN classrooms c ON gs.classroom_id = c.id
JOIN mapels m ON gs.mapel_id = m.id
GROUP BY c.id, m.id, gs.semester, gs.academic_year;
```

### 7.2 View Kehadiran Bulanan

```sql
CREATE VIEW v_monthly_attendance AS
SELECT
    s.id as student_id,
    s.name as student_name,
    c.id as classroom_id,
    c.name as classroom_name,
    YEAR(al.attendance_date) as year,
    MONTH(al.attendance_date) as month,
    COUNT(*) as total_days,
    COUNT(CASE WHEN al.status = 'present' THEN 1 END) as present_count,
    COUNT(CASE WHEN al.status = 'absent' THEN 1 END) as absent_count,
    COUNT(CASE WHEN al.status = 'sick' THEN 1 END) as sick_count,
    COUNT(CASE WHEN al.status = 'permission' THEN 1 END) as permission_count,
    COUNT(CASE WHEN al.status = 'late' THEN 1 END) as late_count,
    ROUND((COUNT(CASE WHEN al.status = 'present' THEN 1 END) / COUNT(*)) * 100, 2) as attendance_percentage
FROM attendance_logs al
JOIN students s ON al.student_id = s.id
JOIN classrooms c ON al.classroom_id = c.id
GROUP BY s.id, c.id, YEAR(al.attendance_date), MONTH(al.attendance_date);
```

### 7.3 View Aktivitas Siswa Harian

```sql
CREATE VIEW v_daily_student_activities AS
SELECT
    dal.activity_date,
    s.id as student_id,
    s.name as student_name,
    c.id as classroom_id,
    c.name as classroom_name,
    dal.activity_type,
    COUNT(*) as activity_count,
    SUM(dal.duration_minutes) as total_duration,
    COUNT(CASE WHEN dal.status = 'completed' THEN 1 END) as completed_count,
    AVG(dal.score) as average_score
FROM daily_activity_logs dal
JOIN students s ON dal.student_id = s.id
JOIN classrooms c ON dal.classroom_id = c.id
GROUP BY dal.activity_date, s.id, c.id, dal.activity_type;
```

---

## 8. TRIGGER OTOMASI

### 8.1 Trigger untuk Auto-log Activity

```sql
DELIMITER //

-- Trigger ketika task di-submit
CREATE TRIGGER trg_task_submitted_log_activity
AFTER UPDATE ON tasks
FOR EACH ROW
BEGIN
    IF NEW.score IS NOT NULL AND OLD.score IS NULL THEN
        INSERT INTO daily_activity_logs (
            student_id, classroom_id, activity_date,
            activity_type, reference_id, reference_type,
            title, status, score, completed_at, created_at, updated_at
        )
        SELECT
            NEW.student_id,
            s.classroom_id,
            CURDATE(),
            'task',
            NEW.id,
            'tasks',
            'Task Submitted',
            'completed',
            NEW.score,
            NOW(),
            NOW(),
            NOW()
        FROM students s
        WHERE s.id = NEW.student_id;
    END IF;
END //

-- Trigger ketika exercise selesai dikerjakan
CREATE TRIGGER trg_exercise_completed_log_activity
AFTER INSERT ON exercise_points
FOR EACH ROW
BEGIN
    INSERT INTO daily_activity_logs (
        student_id, classroom_id, activity_date,
        activity_type, reference_id, reference_type,
        title, status, score, completed_at, created_at, updated_at
    )
    SELECT
        NEW.student_id,
        s.classroom_id,
        CURDATE(),
        'exercise',
        NEW.exercise_id,
        'exercises',
        e.name,
        'completed',
        NEW.final_score,
        NOW(),
        NOW(),
        NOW()
    FROM students s
    JOIN exercises e ON e.id = NEW.exercise_id
    WHERE s.id = NEW.student_id;
END //

DELIMITER ;
```

### 8.2 Trigger Update Grade Summary

```sql
DELIMITER //

CREATE TRIGGER trg_update_grade_on_task_score
AFTER UPDATE ON tasks
FOR EACH ROW
BEGIN
    IF NEW.score IS NOT NULL THEN
        -- Get mapel from post
        DECLARE v_mapel_id BIGINT;
        DECLARE v_semester TINYINT;
        DECLARE v_academic_year VARCHAR(20);

        SELECT p.mapel_id INTO v_mapel_id
        FROM posts p
        WHERE p.id = NEW.post_id;

        -- Determine current semester and academic year
        SET v_semester = IF(MONTH(CURDATE()) >= 7, 1, 2);
        SET v_academic_year = IF(MONTH(CURDATE()) >= 7,
            CONCAT(YEAR(CURDATE()), '/', YEAR(CURDATE()) + 1),
            CONCAT(YEAR(CURDATE()) - 1, '/', YEAR(CURDATE()))
        );

        -- Recalculate grade summary
        CALL sp_calculate_grade_summary(
            NEW.student_id,
            v_mapel_id,
            v_semester,
            v_academic_year
        );
    END IF;
END //

DELIMITER ;
```

---

## 9. IMPLEMENTASI

### 9.1 Migration Files

Buat migration untuk setiap tabel baru:

```bash
php artisan make:migration create_grade_summaries_table
php artisan make:migration create_attendance_logs_table
php artisan make:migration create_report_cards_table
php artisan make:migration create_daily_activity_logs_table
php artisan make:migration create_report_templates_table
php artisan make:migration create_report_archives_table
```

### 9.2 Model Eloquent

Buat model untuk setiap tabel:

```bash
php artisan make:model GradeSummary
php artisan make:model AttendanceLog
php artisan make:model ReportCard
php artisan make:model DailyActivityLog
php artisan make:model ReportTemplate
php artisan make:model ReportArchive
```

### 9.3 Seeder untuk Report Templates

```php
// database/seeders/ReportTemplateSeeder.php
public function run()
{
    ReportTemplate::create([
        'name' => 'Rapor Semester',
        'type' => 'semester',
        'description' => 'Template rapor semester standar',
        'template_config' => json_encode([
            'sections' => [
                'student_info', 'grade_summary', 'attendance',
                'teacher_notes', 'parent_signature'
            ]
        ]),
        'is_active' => true,
        'is_default' => true
    ]);

    ReportTemplate::create([
        'name' => 'Rekap Nilai Harian',
        'type' => 'daily',
        'description' => 'Template rekap nilai harian',
        'template_config' => json_encode([
            'sections' => ['attendance', 'daily_grades', 'activities']
        ]),
        'is_active' => true
    ]);
}
```

---

## 10. USE CASE LAPORAN

### 10.1 Generate Rekap Nilai Kelas

```sql
-- Query untuk menampilkan rekap nilai satu kelas
SELECT
    s.name as nama_siswa,
    s.nis,
    gs.assignment_avg as rata_tugas,
    gs.quiz_avg as rata_kuis,
    gs.uh_avg as rata_uh,
    gs.pts_score as nilai_pts,
    gs.pas_score as nilai_pas,
    gs.final_score as nilai_akhir,
    gs.letter_grade as grade,
    gs.attendance_percentage as kehadiran_persen
FROM grade_summaries gs
JOIN students s ON gs.student_id = s.id
WHERE gs.classroom_id = ?
AND gs.mapel_id = ?
AND gs.semester = ?
AND gs.academic_year = ?
ORDER BY gs.final_score DESC;
```

### 10.2 Generate Laporan Kehadiran Bulanan

```sql
-- Laporan kehadiran per siswa per bulan
SELECT
    s.name as nama_siswa,
    DATE_FORMAT(al.attendance_date, '%Y-%m') as bulan,
    COUNT(*) as total_hari,
    SUM(CASE WHEN al.status = 'present' THEN 1 ELSE 0 END) as hadir,
    SUM(CASE WHEN al.status = 'absent' THEN 1 ELSE 0 END) as alpha,
    SUM(CASE WHEN al.status = 'sick' THEN 1 ELSE 0 END) as sakit,
    SUM(CASE WHEN al.status = 'permission' THEN 1 ELSE 0 END) as izin,
    SUM(CASE WHEN al.status = 'late' THEN 1 ELSE 0 END) as terlambat
FROM attendance_logs al
JOIN students s ON al.student_id = s.id
WHERE al.classroom_id = ?
AND YEAR(al.attendance_date) = ?
AND MONTH(al.attendance_date) = ?
GROUP BY s.id, DATE_FORMAT(al.attendance_date, '%Y-%m')
ORDER BY s.name;
```

### 10.3 Download Rapor Semester (PDF)

Workflow:

1. Check if report card already exists in `report_cards`
2. If not, call `sp_generate_report_card()`
3. Generate PDF using template from `report_templates`
4. Save file and create record in `report_archives`
5. Return download link

---

## 11. KEAMANAN DAN BACKUP

### 11.1 Backup Strategy

- **Daily Backup:** Semua tabel transaksi (attendance_logs, daily_activity_logs)
- **Weekly Backup:** Full database backup
- **Monthly Archive:** Archive ke `report_archives` untuk data semester lama

### 11.2 Data Retention Policy

- `attendance_logs`: Keep 2 years, then archive
- `daily_activity_logs`: Keep 1 year, then archive
- `grade_summaries`: Keep forever
- `report_cards`: Keep forever
- `report_archives`: Keep 5 years

---

## 12. MONITORING & PERFORMANCE

### 12.1 Slow Query Monitoring

```sql
-- Enable slow query log
SET GLOBAL slow_query_log = 'ON';
SET GLOBAL long_query_time = 2; -- queries > 2 seconds

-- Check slow queries
SELECT * FROM mysql.slow_log
WHERE sql_text LIKE '%grade_summaries%'
ORDER BY query_time DESC
LIMIT 10;
```

### 12.2 Table Statistics

```sql
-- Check table sizes
SELECT
    table_name,
    ROUND(((data_length + index_length) / 1024 / 1024), 2) as size_mb,
    table_rows
FROM information_schema.TABLES
WHERE table_schema = 'dashboardguru'
AND table_name IN (
    'grade_summaries', 'attendance_logs', 'report_cards',
    'daily_activity_logs', 'report_archives'
)
ORDER BY (data_length + index_length) DESC;
```

---

## KESIMPULAN

Perancangan fisik basis data sistem laporan ini mencakup:

✅ **6 Tabel Baru:**

- grade_summaries (rekap nilai)
- attendance_logs (kehadiran)
- report_cards (rapor)
- daily_activity_logs (aktivitas harian)
- report_templates (template laporan)
- report_archives (arsip file laporan)

✅ **Fitur Utama:**

- Auto-calculate nilai dengan stored procedure
- Trigger otomatis untuk logging aktivitas
- Views untuk query kompleks
- Partitioning untuk performa
- Archive system untuk historical data

✅ **Support Multiple Report Types:**

- Rekap nilai per mapel
- Rapor semester
- Kehadiran harian/bulanan
- Aktivitas siswa
- Custom reports

✅ **Optimasi:**

- Composite indexes untuk query cepat
- Partitioning by year
- Materialized views (optional)
- Caching strategy

---

**Dokumen dibuat:** 4 Januari 2026  
**Versi:** 1.0  
**Status:** Ready for Implementation
