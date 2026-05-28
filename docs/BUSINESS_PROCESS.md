# Business Process DashboardGuru System

Dokumen ini menjelaskan alur business process dari sistem DashboardGuru untuk berbagai fitur utama.

## Daftar Business Process

### 1. [Registrasi dan Aktivasi Serial](04-business-process-registration.puml)

**Aktor:** Admin/Sales, Guru
**Deskripsi:** Proses registrasi pengguna baru dan aktivasi serial code untuk mengakses sistem.

**Alur:**

1. Admin/Sales membuat produk dan generate serial code
2. Serial dikirim ke guru
3. Guru registrasi akun dan input serial code
4. Sistem validasi dan aktivasi serial
5. Guru dapat mengakses dashboard

---

### 2. [Manajemen Kelas dan Siswa](05-business-process-classroom.puml)

**Aktor:** Guru, Siswa
**Deskripsi:** Proses pembuatan kelas, generate kode kelas, dan pendaftaran siswa.

**Alur:**

1. Guru membuat kelas baru
2. Sistem generate kode kelas unik
3. Guru membagikan kode ke siswa
4. Siswa registrasi dengan kode kelas
5. Sistem link siswa ke kelas dan serial
6. Siswa dapat akses dashboard

**Fitur Utama:**

- Auto-generate kode kelas
- Multi-class support untuk 1 guru
- Tracking siswa per kelas
- Monitoring aktivitas siswa

---

### 3. [Pembuatan dan Akses Pelajaran](06-business-process-lesson.puml)

**Aktor:** Guru, Siswa
**Deskripsi:** Proses pembuatan materi pelajaran dan akses oleh siswa.

**Alur:**

1. Guru pilih mata pelajaran
2. Buat pelajaran dengan tema & subtema (opsional)
3. Tambah item pelajaran (link, video, file, atau teks)
4. Pilih kelas tujuan (bisa multiple)
5. Publish pelajaran
6. Siswa terima notifikasi
7. Siswa akses dan baca materi

**Tipe Konten:**

- Link eksternal
- Video embed (YouTube, dll)
- File upload (PDF, DOC, PPT)
- Teks deskripsi

---

### 4. [Pemberian dan Pengerjaan Tugas](07-business-process-task.puml)

**Aktor:** Guru, Siswa
**Deskripsi:** Proses pemberian tugas, pengerjaan oleh siswa, dan penilaian.

**Alur:**

1. Guru buat postingan sebagai tugas
2. Set deadline dan kelas tujuan
3. Publish tugas
4. Siswa terima notifikasi
5. Siswa kerjakan dan submit jawaban
6. Guru review dan beri nilai
7. Siswa terima notifikasi nilai

**Fitur:**

- Upload attachment untuk instruksi
- Upload file jawaban oleh siswa
- Deadline tracking
- Grading system

---

### 5. [Pembuatan dan Pengerjaan Latihan](08-business-process-exercise.puml)

**Aktor:** Guru, Siswa
**Deskripsi:** Proses pembuatan latihan/exercise dengan berbagai tipe soal dan auto-grading.

**Alur:**

1. Guru pilih pelajaran
2. Buat latihan dengan tipe soal
3. Tambah item soal dengan poin
4. Publish ke kelas
5. Siswa kerjakan latihan
6. Sistem auto-grading (untuk pilihan ganda, benar/salah, isian)
7. Guru review manual untuk essay
8. Siswa lihat hasil dan nilai

**Tipe Soal:**

- Pilihan Ganda (auto-grading)
- Essay (manual grading)
- Benar/Salah (auto-grading)
- Isian Singkat (auto-grading)

**Fitur:**

- Auto-grading system
- Point system per soal
- Mixed question types dalam 1 latihan
- Instant feedback untuk objective questions

---

### 6. [Kelas Online (Jitsi Meeting)](09-business-process-online-class.puml)

**Aktor:** Guru, Siswa
**Deskripsi:** Proses penjadwalan dan pelaksanaan kelas online menggunakan Jitsi.

**Alur:**

1. Guru jadwalkan kelas online
2. Set waktu, durasi, dan room name
3. Publish ke kelas
4. Siswa terima notifikasi
5. Pada waktu mulai, guru start meeting
6. Siswa join room
7. Pembelajaran online berlangsung
8. Guru end meeting
9. Sistem record attendance

**Fitur:**

- Jitsi Meet integration
- Auto-generate room name
- Schedule management
- Attendance tracking
- Screen sharing
- Chat discussion

---

### 7. [Pelaporan dan Monitoring](10-business-process-report.puml)

**Aktor:** Siswa, Guru
**Deskripsi:** Proses pelaporan oleh siswa dan monitoring oleh guru.

**Alur:**

1. Siswa buat laporan (kendala, feedback, pertanyaan)
2. Upload gambar (opsional)
3. Submit laporan
4. Guru terima notifikasi
5. Guru baca dan review laporan
6. Guru berikan feedback jika perlu
7. Generate laporan bulanan

**Konten Laporan:**

- Kendala belajar
- Feedback pembelajaran
- Pertanyaan umum
- Progress report

**Analytics:**

- Jumlah siswa aktif
- Rata-rata nilai
- Completion rate tugas
- Kehadiran kelas online
- Export ke PDF/Excel

---

### 8. [Overview System](11-business-process-overview.puml)

**Deskripsi:** Diagram overview yang menunjukkan semua business process secara keseluruhan.

**Swimlanes:**

- Admin/Sales (Generate serial)
- Guru (Manajemen konten & penilaian)
- Siswa (Pembelajaran & pengerjaan)
- Sistem (Automation & notifikasi)

---

## Komponen Sistem Pendukung

### Notifikasi System

- Email notification
- In-app notification
- Real-time alerts

### AI Question Generator

- OpenAI GPT-4o-mini integration
- Auto-generate pilihan ganda & essay
- Adjustable difficulty levels
- Preview & edit before save
- Direct integration dengan bank soal

### Auto-Grading Engine

- Multiple choice validation
- True/False checking
- Short answer matching
- Point calculation

### Analytics Dashboard

- Student performance
- Class statistics
- Engagement metrics
- Export capabilities

### File Management

- Upload/Download files
- Storage management
- File type validation
- Size limits

---

## Teknologi Pendukung

1. **Laravel Backend** - Business logic & API
2. **Database** - MySQL untuk data persistence
3. **Jitsi Meet** - Video conferencing
4. **Storage** - File upload & management
5. **Queue System** - Background jobs untuk notifikasi
6. **Cache** - Performance optimization

---

## Security & Validation

- Serial code validation
- Class code authentication
- Student-class relationship verification
- File upload security
- XSS & SQL injection prevention
- CSRF protection
- Role-based access control (RBAC)

---

## Future Enhancements

1. **Gamification**

   - Point & badge system
   - Leaderboard
   - Achievement tracking

2. **Advanced Analytics**

   - Predictive analysis
   - Learning pattern recognition
   - Personalized recommendations

3. **Mobile App**

   - Native mobile experience
   - Offline mode
   - Push notifications

4. **Integration**
   - Google Classroom sync
   - LMS integration
   - Third-party tools

---

## Kesimpulan

Business process DashboardGuru dirancang untuk:

- **Efisiensi** - Automated grading & notifications
- **Fleksibilitas** - Multiple content types & delivery methods
- **Skalabilitas** - Support multiple classes & students
- **Monitoring** - Comprehensive analytics & reporting
- **Interaktivitas** - Online classes & real-time feedback

Sistem ini mendukung pembelajaran hybrid dengan kombinasi:

- Self-paced learning (materi & latihan)
- Scheduled assignments (tugas dengan deadline)
- Live interaction (kelas online)
- Continuous feedback (laporan & monitoring)
