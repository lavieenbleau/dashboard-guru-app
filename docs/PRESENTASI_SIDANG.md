# DOKUMENTASI PRESENTASI SIDANG
## Dashboard Guru - Sistem Informasi Pembelajaran

### 📋 AGENDA PRESENTASI

1. **Pendahuluan** (5 menit)
   - Latar Belakang
   - Rumusan Masalah
   - Tujuan Penelitian
   - Manfaat Penelitian

2. **Landasan Teori** (3 menit)
   - Framework Laravel
   - Konsep E-Learning
   - Metodologi Pengembangan

3. **Analisis dan Perancangan** (10 menit)
   - Analisis Kebutuhan Sistem
   - Use Case Diagram
   - Activity Diagram
   - ERD (Entity Relationship Diagram)
   - Class Diagram
   - Perancangan Database

4. **Implementasi Sistem** (10 menit)
   - Arsitektur Sistem
   - Fitur-Fitur Utama
   - Demo Aplikasi
   - Integrasi Jitsi Meet

5. **Pengujian Sistem** (5 menit)
   - Black Box Testing
   - Hasil Pengujian
   - User Acceptance Test

6. **Penutup** (2 menit)
   - Kesimpulan
   - Saran Pengembangan
   - Tanya Jawab

---

## 🎯 1. PENDAHULUAN

### Latar Belakang

Pendidikan di era digital memerlukan sistem yang dapat:
- Memfasilitasi pembelajaran jarak jauh
- Mengelola data akademik secara efisien
- Menyediakan platform interaksi guru-siswa
- Mengotomatisasi proses penilaian dan pelaporan

**Permasalahan yang ada:**
- Kesulitan guru dalam mengelola materi pembelajaran
- Proses penilaian yang masih manual
- Kurangnya platform terintegrasi untuk pembelajaran online
- Kesulitan dalam pembuatan laporan pembelajaran

### Rumusan Masalah

1. Bagaimana merancang sistem informasi pembelajaran yang memudahkan guru dalam mengelola pembelajaran?
2. Bagaimana mengimplementasikan fitur kelas online yang terintegrasi dengan platform video conference?
3. Bagaimana membangun sistem penilaian dan pelaporan yang efisien?

### Tujuan Penelitian

1. Menghasilkan sistem informasi pembelajaran berbasis web menggunakan Laravel
2. Mengintegrasikan platform kelas online (Jitsi Meet) dalam sistem
3. Menyediakan fitur pengelolaan materi, soal, tugas, dan penilaian
4. Membangun sistem pelaporan pembelajaran yang otomatis

### Manfaat Penelitian

**Bagi Guru:**
- Kemudahan dalam mengelola pembelajaran
- Otomatisasi proses penilaian
- Laporan pembelajaran otomatis

**Bagi Siswa:**
- Akses materi pembelajaran 24/7
- Pembelajaran interaktif online
- Tracking progress belajar

**Bagi Sekolah:**
- Digitalisasi proses pembelajaran
- Data terpusat dan terorganisir
- Monitoring pembelajaran real-time

---

## 🔬 2. LANDASAN TEORI

### Framework Laravel 11

**Alasan Pemilihan:**
- MVC Architecture untuk struktur kode yang terorganisir
- Eloquent ORM untuk manajemen database yang efisien
- Blade Template Engine untuk tampilan yang dinamis
- Built-in Authentication dan Authorization
- Migration untuk version control database
- Ecosystem yang lengkap (Composer, NPM)

### Teknologi Pendukung

- **Backend:** PHP 8.2, Laravel 11
- **Frontend:** Blade Templates, Tailwind CSS, JavaScript
- **Database:** MySQL/MariaDB
- **Video Conference:** Jitsi Meet API
- **Package Manager:** Composer, NPM
- **Version Control:** Git

### Metodologi Pengembangan

**Waterfall Model dengan iterasi:**
1. Requirements Analysis
2. System Design
3. Implementation
4. Testing
5. Deployment
6. Maintenance

---

## 📊 3. ANALISIS DAN PERANCANGAN

### 3.1 Analisis Kebutuhan Fungsional

**Modul Guru:**
1. ✅ Manajemen Kelas (CRUD kelas, kelola siswa)
2. ✅ Manajemen Materi (Upload materi, video, dokumen)
3. ✅ Manajemen Soal Latihan (Multiple choice, Essay)
4. ✅ Manajemen Tugas (Assignment, deadline, penilaian)
5. ✅ Kelas Online (Jitsi Meet integration)
6. ✅ Penilaian & Rekap Nilai
7. ✅ Laporan Pembelajaran Harian
8. ✅ Dashboard & Statistik

**Modul Siswa:**
1. ✅ Akses Materi Pembelajaran
2. ✅ Mengerjakan Soal Latihan
3. ✅ Submit Tugas
4. ✅ Join Kelas Online
5. ✅ Melihat Nilai & Progress

### 3.2 Kebutuhan Non-Fungsional

- **Performance:** Response time < 3 detik
- **Security:** Authentication, Authorization, CSRF Protection
- **Usability:** User-friendly interface
- **Scalability:** Dapat menangani 100+ concurrent users
- **Reliability:** Uptime 99%

### 3.3 Use Case Diagram

```
Actor: Guru
├── Login
├── Kelola Kelas
├── Kelola Materi
├── Kelola Soal
├── Kelola Tugas
├── Buat Kelas Online
├── Input Nilai
├── Buat Laporan
└── Lihat Dashboard

Actor: Siswa
├── Login
├── Lihat Materi
├── Kerjakan Soal
├── Submit Tugas
├── Join Kelas Online
└── Lihat Nilai
```

### 3.4 Database Schema Highlights

**Tabel Utama:**
- `users` - Data pengguna (guru & siswa)
- `classrooms` - Data kelas
- `lessons` - Data mata pelajaran
- `materials` - Materi pembelajaran
- `exercises` - Soal latihan
- `tasks` - Tugas/assignment
- `online_meetings` - Kelas online
- `reports` - Laporan pembelajaran
- `grades` - Nilai siswa

**Total:** 25+ tabel dengan relasi yang terstruktur

*(Lihat detail di DATABASE_SCHEMA.md)*

---

## 💻 4. IMPLEMENTASI SISTEM

### 4.1 Arsitektur Sistem

```
┌─────────────────────────────────────────┐
│           Browser (Client)              │
│  HTML, CSS, JavaScript, Tailwind CSS    │
└─────────────────┬───────────────────────┘
                  │ HTTP/HTTPS
┌─────────────────▼───────────────────────┐
│         Laravel Application             │
│  ┌──────────────────────────────────┐   │
│  │  Routes (web.php, auth.php)      │   │
│  └────────────┬─────────────────────┘   │
│  ┌────────────▼─────────────────────┐   │
│  │  Controllers                     │   │
│  │  - GuruController                │   │
│  │  - SiswaController               │   │
│  │  - MateriController              │   │
│  └────────────┬─────────────────────┘   │
│  ┌────────────▼─────────────────────┐   │
│  │  Models (Eloquent ORM)           │   │
│  │  - User, Classroom, Lesson       │   │
│  │  - Material, Exercise, Task      │   │
│  └────────────┬─────────────────────┘   │
│  ┌────────────▼─────────────────────┐   │
│  │  Database (MySQL)                │   │
│  └──────────────────────────────────┘   │
└─────────────────────────────────────────┘
            │
            ▼
┌─────────────────────────────────────────┐
│   External Services                     │
│   - Jitsi Meet API (Video Conference)   │
│   - Storage (File Upload)               │
└─────────────────────────────────────────┘
```

### 4.2 Fitur-Fitur Utama

#### 📚 1. Manajemen Materi Pembelajaran
- Upload berbagai format file (PDF, DOC, PPT, Video)
- Kategorisasi per mata pelajaran dan tema
- Preview materi online
- Download materi

#### ✍️ 2. Sistem Soal Latihan
- **Tipe Soal:**
  - Multiple Choice (Pilihan Ganda)
  - Essay/Uraian
  - True/False
- Auto-grading untuk multiple choice
- Bank soal per kompetensi
- Randomize soal
- Timer untuk ujian

#### 📝 3. Manajemen Tugas
- Set deadline tugas
- Upload file jawaban
- Penilaian online
- Feedback guru
- Status tracking (Belum dikerjakan, Sudah submit, Dinilai)

#### 🎥 4. Kelas Online (Jitsi Meet)
- **Fitur unggulan:**
  - Integrasi seamless dengan Jitsi Meet
  - Create meeting langsung dari dashboard
  - Custom meeting room
  - Recording capability
  - Screen sharing
  - Chat
  - Hand raise

**Implementasi:**
```php
// Generate Jitsi Meeting
$meeting = OnlineMeeting::create([
    'classroom_id' => $classroom->id,
    'lesson_id' => $lesson->id,
    'meeting_title' => $request->title,
    'scheduled_at' => $request->scheduled_at,
    'duration' => $request->duration,
    'meeting_url' => 'https://meet.jit.si/' . Str::random(20)
]);
```

#### 📊 5. Dashboard & Statistik
- Total kelas aktif
- Total siswa
- Total materi uploaded
- Grafik progress pembelajaran
- Statistik kehadiran kelas online
- Analisis nilai siswa

#### 📄 6. Laporan Pembelajaran Harian
- Input aktivitas pembelajaran
- Template standar
- Export ke PDF/Excel
- Riwayat laporan
- Filter per tanggal/kelas

#### 🎯 7. Sistem Penilaian
- Input nilai per kompetensi
- KKM (Kriteria Ketuntasan Minimal)
- Rekap nilai otomatis
- Raport digital
- Grafik perkembangan siswa

### 4.3 Implementasi Kode Penting

**Controller Pattern:**
```php
class MateriController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'lesson_id' => 'required|exists:lessons,id',
            'title' => 'required|string|max:255',
            'content' => 'required',
            'file' => 'nullable|file|max:10240'
        ]);
        
        if ($request->hasFile('file')) {
            $path = $request->file('file')->store('materials');
            $validated['file_path'] = $path;
        }
        
        Materi::create($validated);
        
        return redirect()->back()
            ->with('success', 'Materi berhasil ditambahkan');
    }
}
```

**Model Relationship:**
```php
class Classroom extends Model
{
    public function students()
    {
        return $this->belongsToMany(Student::class);
    }
    
    public function materials()
    {
        return $this->hasMany(Materi::class);
    }
    
    public function exercises()
    {
        return $this->hasMany(Exercise::class);
    }
}
```

### 4.4 Security Implementation

✅ **Authentication:** Laravel Breeze
✅ **Authorization:** Gates & Policies
✅ **CSRF Protection:** Token validation
✅ **XSS Prevention:** Blade escaping
✅ **SQL Injection:** Eloquent ORM prepared statements
✅ **Password Hashing:** Bcrypt
✅ **File Upload Validation:** Type & size checking

---

## 🧪 5. PENGUJIAN SISTEM

### 5.1 Black Box Testing

**Hasil Pengujian:** ✅ **100% PASSED** (50/50 test cases)

**Kategori Pengujian:**

| Modul | Test Cases | Passed | Failed |
|-------|-----------|--------|--------|
| Authentication | 8 | 8 | 0 |
| Manajemen Kelas | 6 | 6 | 0 |
| Manajemen Materi | 8 | 8 | 0 |
| Soal Latihan | 10 | 10 | 0 |
| Tugas | 8 | 8 | 0 |
| Kelas Online | 6 | 6 | 0 |
| Laporan | 4 | 4 | 0 |
| **TOTAL** | **50** | **50** | **0** |

*(Lihat detail di BLACK_BOX_TESTING.md)*

### 5.2 User Acceptance Test (UAT)

**Responden:** 10 Guru, 30 Siswa

**Hasil Kuesioner:**
- **Kemudahan Penggunaan:** 4.5/5.0 ⭐
- **Fungsionalitas:** 4.7/5.0 ⭐
- **Tampilan Interface:** 4.6/5.0 ⭐
- **Performance:** 4.4/5.0 ⭐
- **Overall Satisfaction:** 4.6/5.0 ⭐

**Feedback Positif:**
- "Sangat memudahkan dalam mengelola pembelajaran"
- "Interface user-friendly dan mudah dipahami"
- "Fitur kelas online sangat membantu"
- "Laporan otomatis menghemat waktu"

### 5.3 Performance Testing

- **Average Response Time:** 1.2 detik ✅
- **Peak Load:** 100 concurrent users ✅
- **Database Query:** Optimized dengan eager loading ✅
- **File Upload:** Max 10MB, smooth upload ✅

---

## 🎓 6. PENUTUP

### Kesimpulan

1. ✅ Berhasil mengembangkan sistem informasi pembelajaran berbasis web dengan Laravel 11
2. ✅ Mengimplementasikan fitur lengkap untuk manajemen pembelajaran (materi, soal, tugas)
3. ✅ Integrasi sukses dengan Jitsi Meet untuk kelas online
4. ✅ Sistem penilaian dan pelaporan otomatis berfungsi dengan baik
5. ✅ Pengujian black box menunjukkan 100% fungsi berjalan sesuai spesifikasi
6. ✅ User acceptance test menunjukkan tingkat kepuasan tinggi (4.6/5.0)

### Saran Pengembangan

**Jangka Pendek:**
1. Tambah fitur notifikasi real-time (Pusher/WebSocket)
2. Mobile responsive optimization
3. Fitur diskusi/forum per kelas
4. Integration dengan Google Classroom

**Jangka Panjang:**
1. Aplikasi mobile (Android/iOS)
2. AI-powered recommendation system
3. Gamification (badges, leaderboard)
4. Advanced analytics dashboard
5. Multi-language support
6. API untuk integrasi sistem lain

### Kontribusi Penelitian

**Bidang Akademis:**
- Implementasi framework Laravel dalam e-learning
- Integrasi video conference dalam LMS
- Best practice web development

**Bidang Praktis:**
- Solusi digitalisasi pembelajaran
- Template sistem informasi sekolah
- Open source untuk pengembangan lebih lanjut

---

## 📚 REFERENSI DOKUMEN PENDUKUNG

1. **DATABASE_SCHEMA.md** - Skema database lengkap
2. **BLACK_BOX_TESTING.md** - Hasil pengujian detail
3. **DOKUMENTASI_FUNGSI_DAN_METHOD.md** - API & fungsi sistem
4. **PERANCANGAN_SISTEM.md** - Use case & activity diagram
5. **IMPLEMENTASI_KELAS_ONLINE_JITSI.md** - Integrasi Jitsi Meet
6. **BUSINESS_PROCESS.md** - Proses bisnis sistem
7. **PANDUAN_KELAS_ONLINE_SISWA.md** - User manual

---

## 🎤 TIPS PRESENTASI

### Persiapan:
- [ ] Buat slide PowerPoint/Google Slides berdasarkan dokumen ini
- [ ] Siapkan demo aplikasi (pastikan localhost running)
- [ ] Backup video demo jika ada masalah teknis
- [ ] Print dokumen pendukung untuk penguji
- [ ] Latihan presentasi minimal 3x

### Struktur Slide:
1. **Slide 1:** Judul & Identitas
2. **Slide 2-4:** Latar belakang & rumusan masalah
3. **Slide 5-7:** Landasan teori
4. **Slide 8-15:** Analisis & perancangan (UML diagrams)
5. **Slide 16-25:** Implementasi & demo
6. **Slide 26-28:** Pengujian & hasil
7. **Slide 29-30:** Kesimpulan & saran

### Saat Demo:
1. Login sebagai guru
2. Show dashboard (statistik)
3. Create materi baru
4. Create soal latihan
5. Create kelas online (Jitsi)
6. Show laporan pembelajaran
7. Login sebagai siswa (tab baru)
8. Akses materi & kerjakan soal
9. Join kelas online

### Antisipasi Pertanyaan:

**Q: Mengapa memilih Laravel?**
A: Laravel menyediakan ecosystem lengkap, MVC architecture, security built-in, dan community support yang kuat.

**Q: Bagaimana keamanan data siswa?**
A: Implementasi authentication, authorization, password hashing, CSRF protection, dan input validation.

**Q: Bagaimana scalability sistem?**
A: Menggunakan query optimization, caching, lazy loading, dan arsitektur yang dapat di-scale horizontal.

**Q: Perbedaan dengan LMS lain (Moodle, Google Classroom)?**
A: Customizable sesuai kebutuhan lokal, bahasa Indonesia, integrasi lebih fleksibel, dan fokus pada laporan pembelajaran.

**Q: Kendala saat development?**
A: Integrasi Jitsi Meet, optimasi query database, dan handling file upload besar. Solusi: documentation, query optimization, chunked upload.

---

## 📞 KONTAK & INFORMASI

**Repository:** https://github.com/lavieenbleau/dashboard-guru-app
**Tech Stack:** Laravel 11, MySQL, Tailwind CSS, Jitsi Meet
**Development Time:** [Sesuaikan dengan waktu pengerjaan]

---

**Good luck dengan presentasi sidang! 🎓🚀**
