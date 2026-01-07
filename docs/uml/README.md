# UML Diagrams - Dashboard Guru System

Dokumentasi lengkap sistem Dashboard Guru menggunakan PlantUML.

## 📁 Struktur Folder

Semua diagram UML diorganisir dalam folder-folder berdasarkan kategori:

```
docs/uml/
├── diagrams/
│   ├── use-case/          # Use Case Diagrams
│   ├── class-erd/         # Class & ERD Diagrams
│   ├── sequence/          # Sequence Diagrams
│   ├── activity/          # Activity Diagrams
│   ├── business-process/  # Business Process (BPMN)
│   ├── architecture/      # Architecture & Deployment
│   └── state/             # State Diagrams
└── README.md

```

---

## 📋 Daftar Diagram

### 1. Use Case Diagram

**File:** `diagrams/use-case/01-use-case-diagram.puml`

Menampilkan semua use case yang dapat dilakukan oleh Guru dalam sistem:

- Manajemen Aplikasi (Pilih aplikasi, Dashboard)
- Manajemen Kelas (Kelola kelas, Tambah/Import/Hapus siswa)
- Manajemen Materi (Lihat/Bagikan/Buat materi)
- Manajemen Soal/Ujian (Lihat/Bagikan/Buat soal)
- Manajemen Tugas (Buat/Edit/Nilai tugas)
- Laporan & Nilai (Rekap nilai, Download PDF)
- Kelas Online (Buat/Kelola meeting)
- Pengaturan (Update profile, Ubah password)

### 2. Class Diagram

**File:** `diagrams/class-erd/02-class-diagram.puml`

Menampilkan struktur class/model dalam sistem dengan relationships:

- **Core Models:** User, Product, Serial, Classroom, Student
- **Content Models:** Mapel, Lesson, Exercise, ExerciseType, ExerciseItem, Post
- **Grading Models:** Task, ExercisePoint, Competence
- **Pivot Tables:** LessonClassroom, ExerciseClassroom
- **Other:** OnlineMeeting

**Relationships:**

- One-to-Many: User->Serial, Serial->Classroom, Classroom->Student
- Many-to-Many: Lesson<->Classroom, Exercise<->Classroom
- BelongsTo: Student->Classroom, Exercise->Lesson

### 3. ERD (Entity Relationship Diagram)

**Files:** 
- `diagrams/class-erd/03-erd-diagram.puml`
- `diagrams/class-erd/03-erd-diagram.drawio`
- `diagrams/class-erd/03-erd-diagram-clean.drawio`

Menampilkan struktur database dengan detail kolom dan foreign keys:

- Primary keys (bold)
- Foreign keys (italic)
- Unique constraints (green)
- Data types
- Relationships dengan cardinality (||--o{)

### 4. Sequence Diagram - Share Material

**File:** `diagrams/sequence/04-sequence-share-material.puml`

Menampilkan alur ketika Guru membagikan materi ke kelas dengan fitur share as task:

1. Guru akses halaman Materi Admin
2. Sistem tampilkan daftar materi & kelas
3. Guru pilih kelas (dengan opsi "Pilih Semua Kelas")
4. Guru toggle "Bagikan sebagai Tugas" (optional)
5. Sistem sync pivot table lesson_classroom
6. Jika as_task=true, create Post records untuk setiap kelas
7. Redirect dengan success message

### 5. Sequence Diagram - Import CSV

**File:** `diagrams/sequence/05-sequence-import-csv.puml`

Menampilkan alur import data siswa dari CSV:

1. Download template CSV
2. Upload file CSV
3. Validasi file & format
4. Parse CSV data
5. Loop setiap baris:
   - Validasi data required
   - Check duplikasi username/NIS
   - Create student account
6. Tampilkan hasil import & error list

### 6. Sequence Diagram - Generate PDF

**File:** `diagrams/sequence/06-sequence-generate-pdf.puml`

Menampilkan alur generate PDF rekap nilai:

**Rekap Kelas:**

1. Query students, mapels, tasks, exercise_points
2. Calculate individual scores per student per task/exercise
3. Load view PDF landscape
4. Convert HTML to PDF
5. Download file

**Rekap Siswa:**

1. Query student detail, tasks, exercise_points
2. Load view PDF portrait
3. Render tables with grades
4. Convert to PDF
5. Download file

### 7. Architecture Diagram

**File:** `diagrams/architecture/07-architecture-diagram.puml`

Menampilkan arsitektur sistem dengan layering:

**Presentation Layer:**

- Blade Templates
- Views (Guru, Rekap, Kelas, Materi, Soal)

**Application Layer:**

- Controllers (Aplikasi, Kelas, Materi, Soal, Tugas, RekapNilai)
- Middleware (Auth, CSRF, Trim)
- Validation Rules

**Domain Layer:**

- Models/Eloquent ORM
- Business Logic (Sharing, Grading, Import/Export)

**Infrastructure Layer:**

- Database (MySQL)
- File Storage
- External Services (DomPDF, Laravel)

### 8. Activity Diagram - Manage Class

**File:** `diagrams/activity/08-activity-manage-class.puml`

Menampilkan alur aktivitas kelola kelas & siswa:

- Login & pilih aplikasi
- Buat/pilih kelas
- Decision: Tambah siswa manual vs Import CSV
- Fork activities: View, Add, Import, Delete siswa
- Validasi & error handling untuk import CSV

### 9. Activity Diagram - Rekap Nilai

**File:** `diagrams/activity/09-activity-rekap-nilai.puml`

Menampilkan alur aktivitas rekap nilai:

- Pilih kelas
- Kalkulasi nilai (parallel: tugas & soal)
- Decision: Export PDF kelas vs siswa
- Generate & download PDF
- Kembali ke daftar

### 10. State Diagram - Material

**File:** `diagrams/state/10-state-material.puml`

Menampilkan state transitions untuk materi pembelajaran:

- Initial state: Tidak ada materi
- Created: Materi dibuat (admin/custom)
- Shared: Materi dibagikan ke kelas
- Unshared: Materi dibatalkan share-nya
- Deleted: Materi dihapus
- Transitions dengan events & guards

### 11. Deployment Diagram

**File:** `diagrams/architecture/11-deployment-diagram.puml`

Menampilkan deployment arsitektur sistem:

- Client Browser (HTML, CSS, JS)
- Web Server (Nginx/Apache, Laravel, PHP)
- Application Server (Controllers, Models, Views)
- Database Server (MySQL)
- File Storage Server
- Communication protocols (HTTP, HTTPS, MySQL)

## Activity Diagrams - Simplified Version

### Semua Activity Diagrams (Format Sederhana)

**File:** `diagrams/activity/18-activity-manage-classroom.puml`

- CRUD Kelas (Buat, Hapus, Lihat Dashboard)
- Validasi data kelas
- Tampilan daftar kelas

**File:** `diagrams/activity/19-activity-manage-student.puml`

- Tambah siswa manual dengan validasi
- Import CSV batch (parse & simpan)
- Edit siswa dengan validasi duplikasi
- Hapus siswa dengan konfirmasi

**File:** `diagrams/activity/20-activity-manage-materi.puml`

- Browse & share materi admin ke kelas
- Create custom materi (file/link/text)
- Edit materi dengan file replacement
- Delete materi dengan file cleanup

**File:** `diagrams/activity/21-activity-manage-tugas.puml`

- Create tugas dengan file upload & deadline
- Edit tugas dengan validasi
- Delete tugas dengan file cleanup
- Share tugas ke multiple kelas
- Edit tugas dengan file replacement
- Delete tugas dengan cascade
- View detail & statistik pengumpulan
- Share tugas ke multiple kelas

**File:** `diagrams/activity/22-activity-manage-soal.puml`

- Create soal dengan input judul & items
- Input pertanyaan dengan opsi jawaban
- Edit soal dengan update items
- Delete soal dengan cleanup
- Share soal ke kelas dengan deadline

**File:** `diagrams/activity/23-activity-grading.puml`

- Akses laporan harian (calendar view)
- Pilih tanggal & lihat pengumpulan
- Review file siswa & input nilai
- Input feedback untuk siswa
- Simpan nilai dengan validasi

**File:** `diagrams/activity/24-activity-online-meeting.puml`

- Quick start meeting (instant)
- Schedule meeting dengan waktu & kelas
- Join scheduled meeting
- Delete meeting
- End meeting process

**File:** `diagrams/activity/25-activity-share-content.puml`

- Pilih konten (Materi/Tugas/Soal)
- Pilih kelas tujuan (multiple)
- Set deadline (optional)
- Simpan lesson_items & exercise_points
- Tampilkan hasil sharing

**File:** `diagrams/activity/26-activity-rekap-nilai.puml`

- Pilih kelas untuk rekap
- Load & hitung rata-rata nilai
- View detail nilai siswa
- Download PDF rekap kelas
- Download PDF detail siswa

**File:** `diagrams/activity/27-activity-dashboard.puml`

- View dashboard overview
- Quick stats display

**File:** `diagrams/activity/28-activity-profile.puml`

- View profile settings
- Update profile data
- Change password

## Sequence Diagrams - Lengkap

### Additional Sequence Diagrams

**File:** `diagrams/sequence/12-sequence-share-soal.puml`

- Pilih soal untuk dibagikan
- Pilih kelas tujuan (multiple)
- Set deadline
- Simpan ke pivot table exercise_classroom
- Notifikasi siswa (optional)

**File:** `diagrams/sequence/13-sequence-nilai-tugas.puml`

- View daftar pengumpulan tugas
- Review file tugas siswa
- Input nilai & feedback
- Update tasks table
- Tampilkan hasil penilaian

**File:** `diagrams/sequence/14-sequence-kelas-online.puml`

- Create Jitsi meeting instance
- Set meeting configuration
- Generate meeting URL
- Share ke kelas
- Join meeting

**File:** `diagrams/sequence/15-sequence-laporan-harian.puml` ⭐ **UPDATED**

- Akses laporan harian dengan calendar view
- Query tasks dan exercise_points untuk tanggal tertentu
- Merge collections dengan source_type field
- Kategorisasi berdasarkan lesson.category dan semester
- Display activities dengan badges (Tugas, UH, PTS, PAS, Soal Tambahan)
- Grade submission dengan conditional logic (Task vs ExercisePoint)

**File:** `diagrams/sequence/16-sequence-manage-profile.puml`

- View profile page
- Update user information
- Change password dengan validasi
- Upload profile picture

**File:** `diagrams/sequence/17-sequence-dashboard.puml`

- Load dashboard statistics
- Count total kelas, siswa, tugas
- Display recent activities
- Show quick actions

**File:** `diagrams/sequence/18-sequence-rekap-nilai.puml` ⭐ **NEW**

- Select classroom untuk rekap
- Load semua students & mapels
- Loop per mapel: Fetch all Posts (tasks) & Exercises (UH/PTS/PAS)
- Loop per student: Get individual Task points & ExercisePoint values
- Calculate average per student per mapel
- Display per-mapel tables dengan individual scores
- Optional: Generate & download PDF with same structure

**File:** `diagrams/sequence/19-sequence-authentication.puml`

- Login process dengan validasi email & password
- Session management
- Logout & session invalidation

**File:** `diagrams/sequence/20-sequence-manage-classroom.puml`

- List kelas per serial
- Create classroom
- Delete classroom dengan cascade
- Show classroom dashboard

**File:** `diagrams/sequence/21-sequence-manage-student.puml`

- Create student manual
- Delete student dengan cascade
- Update student (optional)

**File:** `diagrams/sequence/22-sequence-manage-tugas.puml`

- List tugas by mapel
- Create tugas dengan file upload
- Update tugas
- Delete tugas dengan file cleanup

**File:** `diagrams/sequence/23-sequence-manage-soal.puml`

- List soal by tema
- Create exercise dengan multiple items
- Update exercise items
- Delete exercise dengan cleanup images

**File:** `diagrams/sequence/24-sequence-grading.puml`

- View submissions (tasks atau exercise_points)
- Review student work
- Input nilai & feedback
- Save to database

**File:** `diagrams/sequence/25-sequence-online-meeting.puml`

- Create meeting instance
- Configure Jitsi settings
- Generate meeting URL
- Share to students

**File:** `diagrams/sequence/26-sequence-share-content.puml`

- Select content (Materi/Tugas/Soal)
- Choose target classes
- Set sharing options
- Sync pivot tables

**File:** `diagrams/sequence/27-sequence-export-pdf.puml`

- Query data for PDF
- Load PDF view template
- Render with DomPDF
- Download file

**File:** `diagrams/sequence/SequenceDiagram-Guru.puml`

- Comprehensive sequence diagram menampilkan semua fitur guru

## Business Process Diagrams (BPMN)

**Folder:** `diagrams/business-process/`

Business Process Model and Notation untuk dokumentasi proses bisnis:

**File:** `BPMN-Guru.drawio`

- Main workflow guru dalam sistem
- Includes all major processes

**Files:** `BP-*.puml` (19 files)

- BP-01 sampai BP-19 menampilkan berbagai business process
- Proses authentication, manajemen kelas, material, grading, dll.

---

## Cara Menggunakan

### Generate PNG dari PlantUML

```bash
# Generate satu file
java -jar plantuml.jar diagrams/sequence/04-sequence-share-material.puml

# Generate semua file di folder
java -jar plantuml.jar diagrams/sequence/*.puml

# Generate dengan format tertentu
java -jar plantuml.jar -tsvg diagrams/class-erd/02-class-diagram.puml
```

### View di VS Code

Install extension: **PlantUML** (jebbs.plantuml)

- Alt+D: Preview diagram
- Ctrl+Shift+P -> PlantUML: Export Current Diagram

### Update Diagram

1. Edit file .puml
2. Preview untuk cek hasil
3. Commit perubahan
4. Update README.md jika ada perubahan signifikan

---

## Legend/Konvensi

**Sequence Diagrams:**

- Actor: Guru (stick figure)
- Boundary: Controller (control)
- Entity: Model (entity)
- Database: Database (database)
- Return: Dashed arrow
- Alt/Opt: Alternative/Optional flow

**Activity Diagrams:**

- Start: Circle hitam
- End: Circle hitam dengan border
- Action: Rounded rectangle
- Decision: Diamond
- Fork/Join: Black bar
- Swimlane: Partition

**State Diagrams:**

- Initial state: Circle hitam
- State: Rounded rectangle
- Transition: Arrow dengan event
- Final state: Double circle

---

## Catatan Penting

⭐ **Updated Diagrams:** Diagram yang baru di-update untuk fitur terbaru
🆕 **New Diagrams:** Diagram baru yang ditambahkan

**Fitur Terbaru yang Terdokumentasi:**

1. **Laporan Harian:** Dual query (tasks + exercises), merge & categorize
2. **Rekap Nilai:** Individual scores per task/soal, per-mapel tables, calculated averages
3. **Share Material:** Select all classes, share as task, deadline setting
4. **Share Custom Material:** Extended sharing features for materi tambahan

**Diagram Priorities:**

- High: Sequence diagrams (dokumentasi alur lengkap)
- Medium: Activity diagrams (user workflows)
- Low: State diagrams (lifecycle entities)

---

**Last Updated:** January 2025  
**Total Diagrams:** 60+ files organized in 7 categories

- Share single exercise ke multiple kelas
- Bulk share exercises
- Auto-create exercise_points untuk setiap student

**File:** `13-sequence-grading.puml`

- Manual grading untuk tugas
- Auto grading untuk soal (multiple choice)
- View grading details

**File:** `14-sequence-online-meeting.puml`

- Quick start meeting dengan Jitsi
- Create scheduled meeting
- Join meeting dengan JWT token
- End meeting

**File:** `15-sequence-laporan-harian.puml`

- View laporan harian index (calendar view)
- View detail by date
- Grade task from laporan
- View submission detail

**File:** `16-sequence-manage-materi-custom.puml`

- List materi custom by mapel
- Create materi (file/link/text)
- Update materi dengan file replacement
- Delete materi dengan file cleanup

**File:** `17-sequence-select-aplikasi.puml`

- List available applications dengan statistics
- Open application dashboard
- View recent posts & submissions

## Cara Menggunakan

### Online Viewer

1. Buka [PlantUML Online Server](http://www.plantuml.com/plantuml/uml/)
2. Copy paste kode dari file `.puml`
3. Klik "Submit" untuk melihat diagram

### VS Code Extension

1. Install extension: **PlantUML** by jebbs
2. Buka file `.puml`
3. Tekan `Alt+D` untuk preview
4. Export: `Ctrl+Shift+P` -> "PlantUML: Export Current Diagram"

### Command Line

```bash
# Install PlantUML
npm install -g node-plantuml

# Generate PNG
puml generate 01-use-case-diagram.puml -o output.png

# Generate SVG
puml generate 01-use-case-diagram.puml -t svg -o output.svg
```

### Export ke Gambar (Batch)

```bash
# Windows PowerShell
Get-ChildItem *.puml | ForEach-Object { plantuml $_.Name }

# Linux/Mac
for file in *.puml; do plantuml "$file"; done
```

## Format Export

- **PNG:** Untuk dokumentasi, presentasi
- **SVG:** Untuk website, scalable graphics
- **PDF:** Untuk dokumentasi formal
- **ASCII:** Untuk text-based output

## Tips

1. Gunakan online viewer untuk quick preview
2. Export ke PNG/SVG untuk dokumentasi
3. Commit file `.puml` ke repository untuk version control
4. Update diagram ketika ada perubahan sistem
5. Gunakan warna & styling untuk readability

## Dependencies

Tidak ada dependencies tambahan. File `.puml` adalah plain text dan dapat dibuka dengan text editor apapun.

## Referensi

- [PlantUML Official](https://plantuml.com/)
- [PlantUML Guide](https://plantuml.com/guide)
- [Syntax Reference](https://plantuml.com/en/)
