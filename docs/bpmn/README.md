# BPMN - Business Process Model and Notation

## Daftar BPMN Diagram

Folder ini berisi BPMN diagram untuk semua proses bisnis utama dalam sistem Dashboard Guru.

### 📋 Daftar Proses

1. **[Login dan Pilih Aplikasi](01_LOGIN_DAN_PILIH_APLIKASI.md)**
   - Autentikasi guru
   - Pemilihan aplikasi/kurikulum
   - Akses ke dashboard

2. **[Manajemen Materi](02_MANAJEMEN_MATERI.md)**
   - CRUD materi pembelajaran
   - Upload file berbagai format
   - Preview materi online
   - Forum diskusi pada materi
   - Sistem komentar berjenjang

3. **[Manajemen Soal](03_MANAJEMEN_SOAL.md)**
   - Pembuatan soal (MC, Essay, T/F)
   - Distribusi soal ke kelas
   - Auto-grading untuk Multiple Choice
   - Manual grading untuk Essay
   - Randomize soal dan pilihan

4. **[Manajemen Tugas](04_MANAJEMEN_TUGAS.md)**
   - Pembuatan dan distribusi tugas
   - Pengumpulan jawaban siswa
   - Penilaian tugas
   - Forum diskusi pada tugas
   - Tracking status pengumpulan

5. **[Kelas Online - Jitsi Meet](05_KELAS_ONLINE_JITSI.md)**
   - Integrasi dengan Jitsi Meet
   - Penjadwalan kelas online
   - Fitur video conference lengkap
   - Tracking kehadiran siswa
   - Recording meeting

6. **[Penilaian dan Rekap Nilai](06_PENILAIAN_DAN_REKAP.md)**
   - Input nilai manual
   - Rekap nilai otomatis
   - Analisis ketuntasan KKM
   - Raport digital
   - Export nilai (Excel/PDF)
   - Grafik perkembangan siswa

7. **[Laporan Pembelajaran Harian](07_LAPORAN_PEMBELAJARAN_HARIAN.md)**
   - Pembuatan laporan harian
   - Template standar
   - Auto-generate PDF
   - Export periode
   - Statistik laporan

---

## 📊 Tentang BPMN

**BPMN (Business Process Model and Notation)** adalah standar grafis untuk memodelkan proses bisnis dalam bentuk flowchart.

### Manfaat BPMN dalam Proyek Ini:

✅ **Dokumentasi Lengkap**: Setiap proses bisnis terdokumentasi dengan jelas
✅ **Komunikasi Tim**: Memudahkan komunikasi antara developer, tester, dan stakeholder
✅ **Basis Testing**: Menjadi acuan untuk black box testing
✅ **Maintenance**: Memudahkan pemeliharaan dan pengembangan fitur baru
✅ **Onboarding**: Membantu developer baru memahami alur sistem
✅ **Presentasi Sidang**: Dokumentasi komprehensif untuk sidang skripsi/tugas akhir

### Komponen BPMN yang Digunakan:

- **Start Event** (bulat hijau): Titik mulai proses
- **End Event** (bulat merah/hijau): Titik akhir proses
- **Task/Activity** (kotak): Aktivitas yang dilakukan
- **Gateway Decision** (belah ketupat kuning): Percabangan/keputusan
- **Flow** (anak panah): Alur proses

### Format Diagram:

Semua BPMN diagram dalam folder ini menggunakan **Mermaid syntax** yang bisa di-render otomatis di:
- GitHub
- GitLab
- VS Code (dengan extension)
- Markdown viewers
- Documentation platforms

---

## 🎯 Cara Membaca BPMN

Setiap file BPMN berisi:

1. **Deskripsi Proses**: Penjelasan singkat tentang proses
2. **Diagram BPMN**: Visualisasi flowchart lengkap dengan Mermaid
3. **Actor**: Siapa saja yang terlibat dalam proses
4. **Preconditions**: Kondisi yang harus terpenuhi sebelum proses dimulai
5. **Postconditions**: Hasil yang dicapai setelah proses selesai
6. **Main Flow**: Alur utama proses step-by-step
7. **Alternative Flow**: Alur alternatif atau exception handling
8. **Business Rules**: Aturan bisnis yang berlaku
9. **Technical Notes**: Detail implementasi teknis

---

## 🔗 Relasi Antar Proses

```
Login & Pilih Aplikasi
    ↓
Dashboard Aplikasi
    ├── Manajemen Materi ──→ Forum Diskusi
    ├── Manajemen Soal ──→ Auto-Grading ──→ Penilaian
    ├── Manajemen Tugas ──→ Forum Diskusi ──→ Penilaian
    ├── Kelas Online ──→ Tracking Kehadiran
    ├── Penilaian & Rekap ──→ Raport Digital
    └── Laporan Harian ──→ Export PDF
```

---

## 📚 Referensi

- **BPMN 2.0 Specification**: [OMG BPMN](https://www.omg.org/spec/BPMN/2.0/)
- **Mermaid Documentation**: [Mermaid.js](https://mermaid.js.org/)
- **Laravel Documentation**: [Laravel 11](https://laravel.com/docs/11.x)

---

## 💡 Tips Penggunaan

### Untuk Developer:
- Ikuti alur BPMN saat implementasi fitur
- Pastikan semua alternative flow ter-handle
- Business rules harus konsisten dengan kode

### Untuk Tester:
- Gunakan BPMN sebagai basis test case
- Pastikan semua path di-test (positive & negative)
- Alternative flow wajib di-test

### Untuk Presenter/Sidang:
- Gunakan diagram untuk menjelaskan alur sistem
- Fokus pada main flow untuk overview
- Detail alternative flow untuk pertanyaan teknis
- Business rules untuk pertanyaan logika bisnis

---

## 📝 Update Log

| Tanggal | Perubahan | Author |
|---------|-----------|--------|
| 2026-01-25 | Initial creation - 7 BPMN diagrams | GitHub Copilot |

---

**Note**: BPMN diagram akan diupdate seiring perkembangan sistem. Pastikan selalu mengacu pada versi terbaru.
