# LAPORAN PENGUJIAN BLACK BOX
## Sistem Learning Management System (LMS) – Dashboard Guru

---

### INFORMASI DOKUMEN

**Nama Sistem:** Dashboard Guru - Learning Management System  
**Versi:** 1.0  
**Tanggal Pengujian:** 12 Januari 2026  
**Metode Pengujian:** Black Box Testing  
**Tester:** QA Engineer  
**Tujuan Pengujian:** Memvalidasi fungsionalitas sistem dari perspektif pengguna (guru) untuk memastikan kesesuaian antara input dan output tanpa melihat kode sumber.

---

## 1. PENGUJIAN FITUR LOGIN GURU

Pengujian ini bertujuan untuk memvalidasi proses autentikasi pengguna dengan peran guru pada sistem.

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Login Guru | Login dengan kredensial yang valid | Email: guru@example.com<br>Password: password123 | Berhasil login dan diarahkan ke halaman pilih aplikasi | Berhasil ✓ |
| 2 | Login Guru | Login dengan email yang tidak terdaftar | Email: invalid@example.com<br>Password: password123 | Menampilkan pesan error "Email tidak ditemukan" dan tetap di halaman login | Berhasil ✓ |
| 3 | Login Guru | Login dengan password yang salah | Email: guru@example.com<br>Password: wrongpassword | Menampilkan pesan error "Password salah" dan tetap di halaman login | Berhasil ✓ |
| 4 | Login Guru | Login dengan field email kosong | Email: (kosong)<br>Password: password123 | Menampilkan validasi error "Email wajib diisi" | Berhasil ✓ |
| 5 | Login Guru | Login dengan field password kosong | Email: guru@example.com<br>Password: (kosong) | Menampilkan validasi error "Password wajib diisi" | Berhasil ✓ |
| 6 | Login Guru | Login dengan kedua field kosong | Email: (kosong)<br>Password: (kosong) | Menampilkan validasi error untuk kedua field | Berhasil ✓ |
| 7 | Logout Guru | Logout dari sistem setelah login | Klik tombol logout | Berhasil logout dan diarahkan kembali ke halaman login, session dihapus | Berhasil ✓ |

---

## 2. PENGUJIAN FITUR PENGELOLAAN KELAS

Pengujian ini memvalidasi kemampuan guru dalam membuat, mengelola, dan menghapus kelas.

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Tambah Kelas | Membuat kelas baru dengan data lengkap | Nama Kelas: "Kelas 6A"<br>Deskripsi: "Kelas 6 Tahun Ajaran 2025/2026" | Kelas berhasil dibuat, muncul notifikasi sukses, kode kelas otomatis ter-generate, kelas muncul di daftar | Berhasil ✓ |
| 2 | Tambah Kelas | Membuat kelas tanpa mengisi nama | Nama Kelas: (kosong)<br>Deskripsi: "Kelas Test" | Menampilkan validasi error "Nama kelas wajib diisi" | Berhasil ✓ |
| 3 | Tambah Kelas | Membuat kelas dengan nama sangat panjang | Nama Kelas: String 255+ karakter | Sistem membatasi input atau menampilkan validasi error | Berhasil ✓ |
| 4 | Lihat Kelas | Mengakses daftar kelas yang tersedia | Membuka halaman daftar kelas | Menampilkan semua kelas yang dibuat oleh guru beserta informasi kode kelas dan jumlah siswa | Berhasil ✓ |
| 5 | Dashboard Kelas | Mengakses dashboard kelas tertentu | Klik pada kelas "Kelas 6A" | Menampilkan dashboard kelas dengan informasi siswa, materi, tugas, dan statistik kelas | Berhasil ✓ |
| 6 | Hapus Kelas | Menghapus kelas yang sudah tidak digunakan | Pilih kelas, klik tombol hapus | Muncul konfirmasi penghapusan, setelah konfirmasi kelas terhapus dari daftar | Berhasil ✓ |
| 7 | Hapus Kelas | Membatalkan penghapusan kelas | Pilih kelas, klik hapus, klik batal pada konfirmasi | Kelas tidak terhapus, tetap muncul di daftar | Berhasil ✓ |
| 8 | Kode Kelas | Verifikasi kode kelas unik | Membuat 2 kelas berbeda | Setiap kelas mendapat kode unik yang berbeda | Berhasil ✓ |

---

## 3. PENGUJIAN FITUR PENGELOLAAN SISWA (MANUAL)

Pengujian ini memvalidasi penambahan dan penghapusan siswa secara manual ke dalam kelas.

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Tambah Siswa Manual | Menambahkan siswa baru dengan data lengkap | Nama: "Ahmad Fauzi"<br>Email: ahmad@student.com<br>NISN: 1234567890<br>Kelas: Kelas 6A | Siswa berhasil ditambahkan, muncul notifikasi sukses, siswa muncul di daftar siswa kelas | Berhasil ✓ |
| 2 | Tambah Siswa Manual | Menambahkan siswa tanpa nama | Nama: (kosong)<br>Email: student@test.com<br>NISN: 1234567890 | Menampilkan validasi error "Nama siswa wajib diisi" | Berhasil ✓ |
| 3 | Tambah Siswa Manual | Menambahkan siswa tanpa email | Nama: "Budi Santoso"<br>Email: (kosong)<br>NISN: 1234567890 | Menampilkan validasi error "Email wajib diisi" | Berhasil ✓ |
| 4 | Tambah Siswa Manual | Menambahkan siswa dengan email yang sudah terdaftar | Nama: "Citra Dewi"<br>Email: ahmad@student.com<br>NISN: 9876543210 | Menampilkan error "Email sudah digunakan" | Berhasil ✓ |
| 5 | Tambah Siswa Manual | Menambahkan siswa dengan format email tidak valid | Nama: "Dedi Pratama"<br>Email: invalidemail<br>NISN: 1122334455 | Menampilkan validasi error "Format email tidak valid" | Berhasil ✓ |
| 6 | Lihat Daftar Siswa | Melihat semua siswa dalam kelas | Mengakses halaman daftar siswa kelas | Menampilkan tabel berisi semua siswa dengan kolom nama, email, NISN, dan status | Berhasil ✓ |
| 7 | Hapus Siswa | Menghapus siswa dari kelas | Pilih siswa, klik tombol hapus | Muncul konfirmasi, setelah konfirmasi siswa terhapus dari daftar kelas | Berhasil ✓ |
| 8 | Hapus Siswa | Membatalkan penghapusan siswa | Pilih siswa, klik hapus, klik batal | Siswa tidak terhapus, tetap muncul di daftar | Berhasil ✓ |

---

## 4. PENGUJIAN FITUR PENGELOLAAN SISWA (IMPORT CSV)

Pengujian ini memvalidasi fungsionalitas import siswa secara massal menggunakan file CSV.

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Download Template CSV | Mengunduh template CSV untuk import siswa | Klik tombol "Download Template CSV" | File template CSV berhasil diunduh dengan format kolom yang benar (nama, email, nisn) | Berhasil ✓ |
| 2 | Import CSV Valid | Import file CSV dengan data siswa yang valid | File CSV berisi:<br>- Ahmad,ahmad@test.com,1234567890<br>- Budi,budi@test.com,0987654321 | Semua siswa berhasil diimport, muncul notifikasi sukses dengan jumlah siswa yang berhasil ditambahkan | Berhasil ✓ |
| 3 | Import CSV | Import file CSV dengan format salah | File TXT atau Excel (.xlsx) | Menampilkan error "Format file tidak sesuai, gunakan file CSV" | Berhasil ✓ |
| 4 | Import CSV | Import CSV dengan data duplikat email | File CSV berisi email yang sudah ada di sistem | Menampilkan pesan error untuk baris yang duplikat, siswa lain tetap diimport | Berhasil ✓ |
| 5 | Import CSV | Import CSV dengan kolom tidak lengkap | File CSV dengan kolom nama saja tanpa email dan NISN | Menampilkan error "Format CSV tidak sesuai template" | Berhasil ✓ |
| 6 | Import CSV | Import CSV dengan baris kosong | File CSV dengan beberapa baris kosong di antaranya | Sistem melewati baris kosong dan hanya mengimport baris yang berisi data | Berhasil ✓ |
| 7 | Import CSV | Import CSV dengan data email tidak valid | File CSV berisi email: "invalidemail" | Menampilkan error validasi untuk baris dengan email tidak valid | Berhasil ✓ |
| 8 | Import CSV Kosong | Import file CSV tanpa data (hanya header) | File CSV hanya berisi header tanpa data | Menampilkan error "File CSV tidak berisi data siswa" | Berhasil ✓ |

---

## 5. PENGUJIAN FITUR PENGELOLAAN MATERI

Pengujian ini memvalidasi kemampuan guru dalam membuat, berbagi, dan mengelola materi pembelajaran.

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Lihat Materi Admin | Mengakses materi dari admin/sistem | Membuka menu "Materi Admin" | Menampilkan daftar mata pelajaran dengan materi yang tersedia dari admin | Berhasil ✓ |
| 2 | Berbagi Materi Admin | Membagikan materi admin ke kelas | Pilih materi admin, pilih kelas tujuan: "Kelas 6A", klik bagikan | Materi berhasil dibagikan ke kelas, siswa dapat mengakses materi | Berhasil ✓ |
| 3 | Lihat Materi Custom | Mengakses materi buatan sendiri | Membuka menu "Materi Custom" | Menampilkan daftar mata pelajaran untuk membuat materi sendiri | Berhasil ✓ |
| 4 | Buat Materi Custom | Membuat materi baru dengan tipe link | Mata Pelajaran: Matematika<br>Judul: "Pengertian Pecahan"<br>Deskripsi: "Materi tentang pecahan"<br>Tipe: Link<br>Link: https://example.com<br>Kelas: Kelas 6A | Materi berhasil dibuat, muncul notifikasi sukses, materi muncul di daftar | Berhasil ✓ |
| 5 | Buat Materi Custom | Membuat materi dengan tipe video | Mata Pelajaran: IPA<br>Judul: "Fotosintesis"<br>Tipe: Video<br>URL Video: https://youtube.com/watch?v=xxx<br>Kelas: Kelas 6A | Materi video berhasil dibuat dengan embed video yang dapat diputar | Berhasil ✓ |
| 6 | Buat Materi Custom | Membuat materi dengan upload file | Mata Pelajaran: Bahasa Indonesia<br>Judul: "Teks Narasi"<br>Tipe: File<br>File: document.pdf (2MB)<br>Kelas: Kelas 6A | File berhasil diupload, materi tersimpan, siswa dapat mengunduh file | Berhasil ✓ |
| 7 | Buat Materi Custom | Membuat materi dengan tipe teks | Mata Pelajaran: PKN<br>Judul: "Pancasila"<br>Tipe: Teks<br>Konten: Teks penjelasan Pancasila<br>Kelas: Kelas 6A | Materi teks berhasil dibuat dengan formatting yang sesuai | Berhasil ✓ |
| 8 | Buat Materi Custom | Membuat materi tanpa judul | Mata Pelajaran: Matematika<br>Judul: (kosong)<br>Deskripsi: "Test" | Menampilkan validasi error "Judul wajib diisi" | Berhasil ✓ |
| 9 | Buat Materi Custom | Upload file dengan ukuran melebihi batas | File PDF ukuran 15MB | Menampilkan error "Ukuran file maksimal 10MB" | Berhasil ✓ |
| 10 | Edit Materi | Mengubah materi yang sudah dibuat | Edit judul dari "Pecahan" menjadi "Operasi Pecahan" | Materi berhasil diupdate, perubahan tersimpan | Berhasil ✓ |
| 11 | Hapus Materi | Menghapus materi yang tidak digunakan | Pilih materi, klik hapus | Muncul konfirmasi, setelah konfirmasi materi terhapus | Berhasil ✓ |
| 12 | Berbagi ke Multiple Kelas | Membagikan materi ke lebih dari satu kelas | Pilih materi, pilih kelas: Kelas 6A & 6B | Materi berhasil dibagikan ke semua kelas yang dipilih | Berhasil ✓ |

---

## 6. PENGUJIAN FITUR PENGELOLAAN TUGAS

Pengujian ini memvalidasi pembuatan dan pengelolaan tugas untuk siswa.

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Buat Tugas Baru | Membuat tugas dengan data lengkap | Mata Pelajaran: Matematika<br>Judul: "Latihan Soal Pecahan"<br>Deskripsi: "Kerjakan soal 1-10"<br>Deadline: 2026-01-20<br>Kelas: Kelas 6A | Tugas berhasil dibuat, notifikasi sukses, tugas muncul di daftar | Berhasil ✓ |
| 2 | Buat Tugas | Membuat tugas dengan attachment | Judul: "Essay Lingkungan"<br>File: soal_essay.pdf<br>Deadline: 2026-01-25<br>Kelas: Kelas 6A | Tugas berhasil dibuat dengan file attachment yang dapat diunduh siswa | Berhasil ✓ |
| 3 | Buat Tugas | Membuat tugas tanpa judul | Mata Pelajaran: IPA<br>Judul: (kosong)<br>Deadline: 2026-01-20 | Menampilkan validasi error "Judul tugas wajib diisi" | Berhasil ✓ |
| 4 | Buat Tugas | Membuat tugas tanpa deadline | Judul: "Tugas IPA"<br>Deskripsi: "Bab 5"<br>Deadline: (kosong) | Menampilkan validasi error "Deadline wajib diisi" | Berhasil ✓ |
| 5 | Buat Tugas | Membuat tugas dengan deadline yang sudah lewat | Judul: "Tugas Test"<br>Deadline: 2025-12-01 (tanggal lalu) | Menampilkan error "Deadline tidak boleh tanggal yang sudah lewat" | Berhasil ✓ |
| 6 | Lihat Tugas | Melihat daftar tugas yang dibuat | Membuka halaman daftar tugas | Menampilkan semua tugas dengan informasi judul, deadline, kelas, dan jumlah submission | Berhasil ✓ |
| 7 | Edit Tugas | Mengubah informasi tugas | Edit deadline dari 2026-01-20 ke 2026-01-25 | Tugas berhasil diupdate, perubahan tersimpan | Berhasil ✓ |
| 8 | Hapus Tugas | Menghapus tugas yang tidak digunakan | Pilih tugas, klik hapus | Muncul konfirmasi, setelah konfirmasi tugas terhapus | Berhasil ✓ |
| 9 | Publish Tugas | Publish tugas ke multiple kelas | Pilih kelas: Kelas 6A, 6B, 6C<br>Klik publish | Tugas terpublish ke semua kelas yang dipilih, siswa dapat melihat tugas | Berhasil ✓ |
| 10 | Lihat Detail Tugas | Melihat detail dan submission tugas | Klik pada tugas tertentu | Menampilkan detail tugas dan daftar siswa yang sudah submit beserta status | Berhasil ✓ |

---

## 7. PENGUJIAN FITUR PENILAIAN TUGAS DAN SOAL

Pengujian ini memvalidasi kemampuan guru memberikan nilai pada submission tugas dan soal siswa.

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Beri Nilai Tugas | Memberikan nilai pada submission tugas siswa | Pilih submission siswa<br>Nilai: 85<br>Komentar: "Bagus, tingkatkan lagi" | Nilai berhasil disimpan, siswa dapat melihat nilai dan komentar | Berhasil ✓ |
| 2 | Beri Nilai Tugas | Memberikan nilai tanpa komentar | Pilih submission siswa<br>Nilai: 90<br>Komentar: (kosong) | Nilai berhasil disimpan tanpa komentar | Berhasil ✓ |
| 3 | Beri Nilai Tugas | Memberikan nilai di luar range (>100) | Nilai: 105 | Menampilkan validasi error "Nilai maksimal 100" | Berhasil ✓ |
| 4 | Beri Nilai Tugas | Memberikan nilai negatif | Nilai: -10 | Menampilkan validasi error "Nilai tidak boleh negatif" | Berhasil ✓ |
| 5 | Beri Nilai Tugas | Memberikan nilai dengan karakter non-numerik | Nilai: "delapan puluh" | Menampilkan validasi error "Nilai harus berupa angka" | Berhasil ✓ |
| 6 | Edit Nilai Tugas | Mengubah nilai yang sudah diberikan | Edit nilai dari 85 menjadi 90 | Nilai berhasil diupdate, siswa melihat nilai terbaru | Berhasil ✓ |
| 7 | Lihat Submission | Melihat file jawaban siswa | Klik link file jawaban | File jawaban siswa dapat dibuka/diunduh untuk review | Berhasil ✓ |
| 8 | Beri Nilai Soal Essay | Memberikan nilai pada jawaban essay | Pilih jawaban essay siswa<br>Nilai: 20/25<br>Komentar: "Jelaskan lebih detail" | Nilai essay tersimpan, total nilai soal terupdate | Berhasil ✓ |
| 9 | Auto Grading Soal | Melihat hasil auto-grading pilihan ganda | Siswa mengerjakan 10 soal pilihan ganda | Sistem otomatis memberikan nilai berdasarkan jawaban benar, hasil langsung tampil | Berhasil ✓ |
| 10 | Review Soal Isian | Review dan konfirmasi jawaban isian singkat | Jawaban siswa: "fotosintesis"<br>Kunci jawaban: "Fotosintesis" | Sistem auto-grade dengan case-insensitive matching | Berhasil ✓ |
| 11 | Bulk Grading | Memberikan nilai untuk multiple submission sekaligus | Pilih 5 submission, set nilai sama untuk semua | Sistem memproses dan menyimpan nilai untuk semua submission yang dipilih | Berhasil ✓ |

---

## 8. PENGUJIAN FITUR LAPORAN HARIAN

Pengujian ini memvalidasi fitur monitoring dan pelaporan aktivitas siswa harian.

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Lihat Laporan Harian | Melihat laporan aktivitas hari ini | Membuka menu Laporan Harian | Menampilkan daftar tanggal dengan submission tugas/soal dari siswa | Berhasil ✓ |
| 2 | Filter Laporan by Date | Melihat laporan pada tanggal tertentu | Pilih tanggal: 2026-01-10 | Menampilkan semua submission dan aktivitas siswa pada tanggal tersebut | Berhasil ✓ |
| 3 | Detail Laporan Harian | Melihat detail submission pada hari tertentu | Klik pada tanggal tertentu | Menampilkan detail submission dengan nama siswa, tugas/soal, waktu submit, dan status penilaian | Berhasil ✓ |
| 4 | Lihat Laporan Kosong | Melihat laporan pada hari tanpa aktivitas | Pilih tanggal yang tidak ada submission | Menampilkan pesan "Tidak ada aktivitas pada tanggal ini" | Berhasil ✓ |
| 5 | Beri Nilai dari Laporan | Memberikan nilai langsung dari laporan harian | Klik submission di laporan, input nilai: 88 | Nilai berhasil disimpan, status berubah menjadi "Dinilai" | Berhasil ✓ |
| 6 | Filter by Kelas | Melihat laporan harian per kelas | Filter kelas: Kelas 6A | Menampilkan laporan harian hanya untuk siswa Kelas 6A | Berhasil ✓ |
| 7 | Export Laporan | Mengekspor laporan harian ke format Excel/PDF | Klik tombol export | File laporan berhasil diunduh dengan format yang benar | Berhasil ✓ |
| 8 | Statistik Harian | Melihat statistik submission harian | Membuka dashboard statistik | Menampilkan grafik/chart jumlah submission per hari/minggu | Berhasil ✓ |

---

## 9. PENGUJIAN FITUR REKAP NILAI DAN UNDUH PDF

Pengujian ini memvalidasi fitur rekap nilai siswa dan kemampuan mengunduh laporan dalam format PDF.

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Lihat Rekap Nilai | Melihat rekap nilai semua kelas | Membuka menu Rekap Nilai | Menampilkan daftar kelas dengan informasi rata-rata nilai dan jumlah siswa | Berhasil ✓ |
| 2 | Rekap Nilai per Kelas | Melihat rekap nilai kelas tertentu | Klik pada Kelas 6A | Menampilkan tabel siswa dengan nilai tugas, soal, dan rata-rata | Berhasil ✓ |
| 3 | Rekap Nilai per Siswa | Melihat detail nilai siswa tertentu | Klik pada nama siswa "Ahmad Fauzi" | Menampilkan semua nilai siswa (tugas, soal, exercise) dengan detail per item | Berhasil ✓ |
| 4 | Download PDF Kelas | Mengunduh rekap nilai kelas dalam PDF | Klik tombol "Download PDF" pada Kelas 6A | File PDF berhasil diunduh dengan format tabel nilai yang rapi dan lengkap | Berhasil ✓ |
| 5 | Download PDF Siswa | Mengunduh rekap nilai individual siswa | Klik "Download PDF" pada siswa tertentu | File PDF berhasil diunduh dengan rapor nilai lengkap siswa tersebut | Berhasil ✓ |
| 6 | Validasi Format PDF | Memeriksa format dan konten PDF | Buka file PDF yang diunduh | PDF berisi header (nama sekolah, kelas), tabel nilai, total, rata-rata, dan footer dengan watermark/logo | Berhasil ✓ |
| 7 | Filter Rekap by Mapel | Filter rekap nilai berdasarkan mata pelajaran | Pilih filter: Matematika | Menampilkan rekap nilai hanya untuk mata pelajaran Matematika | Berhasil ✓ |
| 8 | Filter Rekap by Periode | Filter rekap nilai berdasarkan periode waktu | Pilih periode: Januari 2026 | Menampilkan rekap nilai untuk submission pada bulan Januari 2026 | Berhasil ✓ |
| 9 | Sorting Rekap Nilai | Mengurutkan siswa berdasarkan rata-rata nilai | Klik header kolom "Rata-rata" | Tabel terurut dari nilai tertinggi ke terendah atau sebaliknya | Berhasil ✓ |
| 10 | Statistik Nilai | Melihat statistik nilai kelas | Membuka tab statistik | Menampilkan chart distribusi nilai, nilai tertinggi, terendah, dan rata-rata kelas | Berhasil ✓ |

---

## 10. PENGUJIAN FITUR KELAS ONLINE

Pengujian ini memvalidasi fitur penjadwalan dan pelaksanaan kelas online menggunakan Jitsi Meet.

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Quick Start Meeting | Memulai meeting langsung tanpa jadwal | Klik tombol "Quick Start"<br>Room Name: "Kelas6A-QuickMeet" | Meeting room langsung dibuat dan guru masuk ke Jitsi interface | Berhasil ✓ |
| 2 | Jadwalkan Meeting | Membuat jadwal meeting baru | Judul: "Pembelajaran Matematika"<br>Tanggal: 2026-01-15<br>Waktu: 09:00<br>Durasi: 90 menit<br>Kelas: Kelas 6A | Meeting berhasil dijadwalkan, notifikasi terkirim ke siswa | Berhasil ✓ |
| 3 | Jadwalkan Meeting | Membuat meeting tanpa judul | Judul: (kosong)<br>Tanggal: 2026-01-15<br>Waktu: 10:00 | Menampilkan validasi error "Judul meeting wajib diisi" | Berhasil ✓ |
| 4 | Jadwalkan Meeting | Membuat meeting dengan waktu yang sudah lewat | Tanggal: 2025-12-01<br>Waktu: 10:00 | Menampilkan error "Tidak dapat membuat meeting untuk waktu yang sudah lewat" | Berhasil ✓ |
| 5 | Lihat Jadwal Meeting | Melihat daftar meeting yang dijadwalkan | Membuka halaman Kelas Online | Menampilkan daftar meeting dengan status (upcoming/ongoing/finished) | Berhasil ✓ |
| 6 | Join Meeting | Guru bergabung ke meeting yang dijadwalkan | Klik tombol "Join Meeting" | Guru masuk ke Jitsi room, interface video conference terbuka | Berhasil ✓ |
| 7 | Edit Meeting | Mengubah jadwal meeting | Edit waktu dari 09:00 ke 10:00 | Meeting berhasil diupdate, notifikasi perubahan terkirim ke siswa | Berhasil ✓ |
| 8 | Hapus Meeting | Membatalkan meeting yang dijadwalkan | Pilih meeting, klik hapus | Muncul konfirmasi, meeting terhapus dan notifikasi pembatalan terkirim | Berhasil ✓ |
| 9 | Room Name Generation | Verifikasi room name unik | Membuat 2 meeting berbeda | Setiap meeting mendapat room name yang unik dan tidak duplikat | Berhasil ✓ |
| 10 | Meeting Notification | Verifikasi notifikasi meeting ke siswa | Buat meeting baru | Siswa di kelas yang dipilih menerima notifikasi jadwal meeting | Berhasil ✓ |
| 11 | Meeting History | Melihat riwayat meeting yang sudah selesai | Membuka tab "Riwayat Meeting" | Menampilkan daftar meeting yang sudah berlangsung dengan durasi dan peserta | Berhasil ✓ |
| 12 | Share Screen Test | Menguji fitur share screen dalam meeting | Join meeting, klik share screen | Screen sharing aktif, peserta lain dapat melihat layar guru | Berhasil ✓ |

---

## 11. PENGUJIAN FITUR PENGATURAN AKUN GURU

Pengujian ini memvalidasi kemampuan guru untuk mengelola profil dan pengaturan akun pribadi.

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Lihat Profil | Melihat informasi profil akun | Membuka menu Pengaturan | Menampilkan informasi profil: nama, email, foto, dan informasi lainnya | Berhasil ✓ |
| 2 | Edit Profil | Mengubah nama profil | Nama baru: "Budi Santoso, S.Pd" | Profil berhasil diupdate, nama baru ditampilkan di seluruh sistem | Berhasil ✓ |
| 3 | Edit Profil | Mengubah email | Email baru: newsmail@example.com | Email berhasil diupdate, konfirmasi email dikirim ke email baru | Berhasil ✓ |
| 4 | Edit Profil | Mengubah email dengan format tidak valid | Email: invalidemail | Menampilkan validasi error "Format email tidak valid" | Berhasil ✓ |
| 5 | Edit Profil | Mengubah email yang sudah digunakan user lain | Email: existing@example.com | Menampilkan error "Email sudah digunakan" | Berhasil ✓ |
| 6 | Upload Foto Profil | Mengunggah foto profil baru | File: photo.jpg (1MB, JPG) | Foto berhasil diupload dan ditampilkan sebagai foto profil | Berhasil ✓ |
| 7 | Upload Foto Profil | Upload foto dengan ukuran melebihi batas | File: photo.jpg (6MB) | Menampilkan error "Ukuran foto maksimal 5MB" | Berhasil ✓ |
| 8 | Upload Foto Profil | Upload file non-gambar | File: document.pdf | Menampilkan error "File harus berupa gambar (JPG, PNG, GIF)" | Berhasil ✓ |
| 9 | Ganti Password | Mengubah password dengan data valid | Password Lama: oldpass123<br>Password Baru: newpass456<br>Konfirmasi: newpass456 | Password berhasil diubah, notifikasi sukses muncul | Berhasil ✓ |
| 10 | Ganti Password | Mengubah password dengan password lama salah | Password Lama: wrongpass<br>Password Baru: newpass456<br>Konfirmasi: newpass456 | Menampilkan error "Password lama tidak sesuai" | Berhasil ✓ |
| 11 | Ganti Password | Mengubah password dengan konfirmasi tidak cocok | Password Lama: oldpass123<br>Password Baru: newpass456<br>Konfirmasi: newpass789 | Menampilkan error "Konfirmasi password tidak cocok" | Berhasil ✓ |
| 12 | Ganti Password | Mengubah password dengan panjang kurang dari minimum | Password Baru: "123" | Menampilkan error "Password minimal 8 karakter" | Berhasil ✓ |
| 13 | Edit Field Custom | Mengubah field informasi tambahan | NIP: 198501012010011001<br>No. Telepon: 081234567890 | Field berhasil diupdate dan tersimpan | Berhasil ✓ |
| 14 | Validasi No Telepon | Input nomor telepon dengan format tidak valid | No. Telepon: "abcd1234" | Menampilkan error "Format nomor telepon tidak valid" | Berhasil ✓ |

---

## 12. RINGKASAN HASIL PENGUJIAN

### Statistik Pengujian

| Kategori Fitur | Total Test Case | Berhasil | Gagal | Persentase Keberhasilan |
|----------------|-----------------|----------|-------|-------------------------|
| Login Guru | 7 | 7 | 0 | 100% |
| Pengelolaan Kelas | 8 | 8 | 0 | 100% |
| Pengelolaan Siswa (Manual) | 8 | 8 | 0 | 100% |
| Pengelolaan Siswa (Import CSV) | 8 | 8 | 0 | 100% |
| Pengelolaan Materi | 12 | 12 | 0 | 100% |
| Pengelolaan Tugas | 10 | 10 | 0 | 100% |
| Penilaian Tugas dan Soal | 11 | 11 | 0 | 100% |
| Laporan Harian | 8 | 8 | 0 | 100% |
| Rekap Nilai dan Unduh PDF | 10 | 10 | 0 | 100% |
| Kelas Online | 12 | 12 | 0 | 100% |
| Pengaturan Akun Guru | 14 | 14 | 0 | 100% |
| **TOTAL** | **108** | **108** | **0** | **100%** |

---

## 13. KESIMPULAN PENGUJIAN

Berdasarkan hasil pengujian Black Box yang telah dilakukan terhadap sistem Learning Management System (LMS) – Dashboard Guru, dapat disimpulkan bahwa:

1. **Tingkat Keberhasilan Keseluruhan**: Sistem mencapai tingkat keberhasilan 100% dari 108 skenario pengujian yang dilakukan.

2. **Validasi Input**: Semua validasi input berfungsi dengan baik, mencegah data yang tidak valid masuk ke dalam sistem.

3. **Fungsionalitas Utama**: Seluruh fitur utama sistem (Login, Pengelolaan Kelas, Siswa, Materi, Tugas, Penilaian, Laporan, Rekap Nilai, Kelas Online, dan Pengaturan) berfungsi sesuai dengan requirement dan ekspektasi pengguna.

4. **User Experience**: Sistem memberikan feedback yang jelas kepada pengguna melalui notifikasi sukses maupun error message yang informatif.

5. **Keamanan**: Fitur autentikasi dan validasi data telah berfungsi dengan baik untuk mencegah akses tidak sah dan input data yang tidak sesuai.

6. **Integrasi Eksternal**: Integrasi dengan Jitsi Meet untuk kelas online berfungsi dengan baik tanpa kendala.

7. **Export dan Reporting**: Fitur export PDF dan laporan nilai berfungsi dengan baik dan menghasilkan output yang sesuai format.

---

## 14. REKOMENDASI

Meskipun semua test case berhasil, berikut beberapa rekomendasi untuk pengembangan lebih lanjut:

1. **Performance Testing**: Disarankan melakukan pengujian performa dengan jumlah siswa dan data yang besar untuk memastikan skalabilitas sistem.

2. **Browser Compatibility**: Melakukan pengujian kompatibilitas pada berbagai browser (Chrome, Firefox, Safari, Edge) untuk memastikan konsistensi tampilan dan fungsionalitas.

3. **Mobile Responsiveness**: Melakukan pengujian pada berbagai ukuran layar mobile untuk memastikan responsive design berfungsi optimal.

4. **Security Testing**: Melakukan penetration testing untuk mengidentifikasi potensi vulnerabilities keamanan.

5. **Load Testing**: Melakukan pengujian beban untuk mengetahui kapasitas maksimal sistem saat banyak pengguna mengakses secara bersamaan.

6. **Accessibility Testing**: Memastikan sistem dapat diakses oleh pengguna dengan kebutuhan khusus (WCAG compliance).

---

**Dokumen ini disusun untuk keperluan Quality Assurance dan validasi sistem LMS Dashboard Guru.**

**Tanggal:** 12 Januari 2026  
**Status:** APPROVED  
**Prepared by:** QA Engineer Team
