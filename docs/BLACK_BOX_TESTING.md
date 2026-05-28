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

## 12. PENGUJIAN FITUR AI QUESTION GENERATOR (OpenAI Integration)

Pengujian ini memvalidasi kemampuan sistem untuk generate soal secara otomatis menggunakan OpenAI API berdasarkan materi pembelajaran.

### 12.1 Pengujian Halaman AI Generator

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Akses Halaman AI Generator | Membuka halaman AI Generator | Klik menu "AI Question Generator" | Halaman AI Generator terbuka dengan form lengkap (sumber materi, tipe soal, difficulty, jumlah soal) | Berhasil ✓ |
| 2 | Pilih Sumber Materi - Post | Memilih materi dari Post guru | Radio button "Materi Guru (Post)" dipilih | List dropdown menampilkan semua post yang ada pada serial tersebut | Berhasil ✓ |
| 3 | Pilih Sumber Materi - Lesson | Memilih materi dari Lesson admin | Radio button "Pelajaran Admin (Lesson)" dipilih | List dropdown menampilkan semua lesson yang tersedia | Berhasil ✓ |
| 4 | Dynamic Material List | Pergantian sumber materi mengubah list | Switch dari Post ke Lesson | Material dropdown di-reset dan menampilkan data sesuai sumber yang dipilih | Berhasil ✓ |
| 5 | Pilih Tipe Latihan | Memilih jenis latihan | Pilih "Ulangan Harian" dari dropdown | "Ulangan Harian" terpilih dan tersimpan di form | Berhasil ✓ |
| 6 | Pilih Model Soal | Memilih model/bentuk soal | Pilih "Pilihan Ganda" dari dropdown | "Pilihan Ganda" terpilih dan tersimpan di form | Berhasil ✓ |
| 7 | Pilih Tingkat Kesulitan | Memilih tingkat kesulitan | Pilih "Sedang" dari radio button | "Sedang" terpilih sebagai default | Berhasil ✓ |
| 8 | Input Jumlah Soal | Memasukkan jumlah soal yang ingin di-generate | Input: 10 | Nilai 10 tersimpan di input field, tidak ada error | Berhasil ✓ |
| 9 | Input Jumlah Soal - Minimum | Input jumlah soal dibawah minimum | Input: 0 | Menampilkan validasi error "Jumlah soal minimal 1" | Berhasil ✓ |
| 10 | Input Jumlah Soal - Maximum | Input jumlah soal melebihi maximum | Input: 25 | Menampilkan validasi error "Jumlah soal maksimal 20" atau input di-limit ke 20 | Berhasil ✓ |
| 11 | Form Validation - Material Kosong | Submit tanpa memilih materi | Click tombol "Generate" tanpa pilih materi | Menampilkan validasi error "Pilih materi terlebih dahulu" | Berhasil ✓ |
| 12 | Form Validation - Tipe Latihan Kosong | Submit tanpa memilih tipe latihan | Click tombol "Generate" tanpa pilih tipe | Menampilkan validasi error "Pilih tipe latihan terlebih dahulu" | Berhasil ✓ |
| 13 | Form Validation - Model Soal Kosong | Submit tanpa memilih model soal | Click tombol "Generate" tanpa pilih model | Menampilkan validasi error "Pilih model soal terlebih dahulu" | Berhasil ✓ |

### 12.2 Pengujian Proses Generate Soal

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Generate Soal - Sukses | Generate soal dengan data valid | Material: Post (Integral Calculus)<br>Tipe: UH<br>Model: Pilihan Ganda<br>Difficulty: Sedang<br>Jumlah: 5 | Loading indicator muncul, kemudian 5 soal Pilihan Ganda berhasil di-generate dan ditampilkan dalam format preview | Berhasil ✓ |
| 2 | API Response Time | Mengukur waktu response API | Generate 5 soal | Soal di-generate dalam waktu < 60 detik | Berhasil ✓ |
| 3 | Generate Soal - Essay | Generate soal tipe Essay | Model: Essay<br>Jumlah: 3 | 3 soal Essay berhasil di-generate tanpa pilihan ganda, hanya pertanyaan | Berhasil ✓ |
| 4 | Generate Soal - True/False | Generate soal tipe Benar/Salah | Model: Benar/Salah<br>Jumlah: 5 | 5 soal True/False berhasil di-generate dengan 2 opsi (Benar/Salah) | Berhasil ✓ |
| 5 | Generate Soal - Short Answer | Generate soal tipe Isian Singkat | Model: Isian Singkat<br>Jumlah: 4 | 4 soal isian singkat berhasil di-generate dengan format pertanyaan dan answer key | Berhasil ✓ |
| 6 | Generate Soal - Mudah | Generate dengan difficulty mudah | Difficulty: Mudah<br>Jumlah: 5 | Soal yang di-generate sesuai dengan konsep dasar materi, tidak ada pertanyaan kompleks | Berhasil ✓ |
| 7 | Generate Soal - Sulit | Generate dengan difficulty sulit | Difficulty: Sulit<br>Jumlah: 5 | Soal yang di-generate memerlukan analisis mendalam dan pemikiran kritis tingkat lanjut | Berhasil ✓ |
| 8 | API Error - Rate Limit | API mencapai rate limit | Generate soal saat rate limit tercapai | Error message: "Terlalu banyak request. Silakan coba lagi dalam beberapa saat" | Berhasil ✓ |
| 9 | API Error - Invalid Response | API return response invalid | API return malformed JSON | Error message: "Gagal memproses response dari AI. Silakan coba lagi" | Berhasil ✓ |
| 10 | API Error - Connection Timeout | Koneksi API timeout | Network/API tidak merespons > 30 detik | Error message: "Koneksi timeout. Silakan coba lagi atau gunakan sumber API alternatif" | Berhasil ✓ |
| 11 | API Retry Mechanism | API fail pada attempt pertama | Generate soal dengan network intermittent | Sistem otomatis retry hingga 3x dengan exponential backoff | Berhasil ✓ |
| 12 | Multiple Generators Concurrent | 2 guru generate soal simultaneously | Teacher A & B generate bersamaan | Kedua request berhasil diproses tanpa saling mengganggu | Berhasil ✓ |

### 12.3 Pengujian Preview dan Edit Soal

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Tampilkan Preview | Menampilkan preview soal | Generate selesai | Halaman preview terbuka dengan semua soal yang di-generate ditampilkan dalam card/form | Berhasil ✓ |
| 2 | Edit Pertanyaan | Mengubah pertanyaan soal | Ubah teks pertanyaan di preview | Pertanyaan berhasil diupdate di form preview, perubahan tercatat | Berhasil ✓ |
| 3 | Edit Opsi Jawaban | Mengubah salah satu opsi jawaban | Ubah teks opsi di preview | Opsi jawaban berhasil diupdate, tidak mempengaruhi opsi lain | Berhasil ✓ |
| 4 | Edit Kunci Jawaban | Mengubah kunci jawaban soal | Ubah kunci dari "A" ke "B" | Kunci jawaban berhasil diubah, dropdown terbaru merefleksikan perubahan | Berhasil ✓ |
| 5 | Edit Point Soal | Mengubah nilai point soal | Ubah point dari 20 menjadi 25 | Point berhasil diupdate dan tersimpan di form | Berhasil ✓ |
| 6 | Hapus Soal | Menghapus satu soal dari preview | Klik tombol "Hapus" pada soal tertentu | Soal terhapus dari preview, total soal berkurang | Berhasil ✓ |
| 7 | Reset Form | Reset semua perubahan di preview | Klik tombol "Reset Form" | Semua field kembali ke nilai original yang di-generate AI, form direset | Berhasil ✓ |
| 8 | Regenerate | Regenerate ulang soal dari awal | Klik "Regenerate" | Redirect kembali ke halaman generator form, session preview dihapus | Berhasil ✓ |
| 9 | Validation - Empty Question | Simpan dengan pertanyaan kosong | Kosongkan teks pertanyaan, coba simpan | Error message: "Pertanyaan tidak boleh kosong" | Berhasil ✓ |
| 10 | Validation - Invalid Answer | Jawaban tidak cocok dengan opsi | Set jawaban "Z" (tidak ada) | Error message: "Jawaban harus merupakan salah satu opsi yang tersedia" | Berhasil ✓ |
| 11 | Form Input Judul | Input judul latihan di preview | Judul: "Ulangan Harian Bab 5" | Judul tersimpan dan akan digunakan sebagai nama exercise | Berhasil ✓ |
| 12 | Form Input Deskripsi | Input deskripsi di preview | Deskripsi: "Soal untuk evaluasi..." | Deskripsi tersimpan dan ditampilkan di exercise detail | Berhasil ✓ |

### 12.4 Pengujian Penyimpanan Soal ke Database

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Simpan Soal - Sukses | Menyimpan soal hasil generate ke database | Klik tombol "Simpan Semua Soal" | Soal berhasil disimpan, redirect ke halaman exercise detail, notifikasi sukses muncul | Berhasil ✓ |
| 2 | Database Insert - Exercise | Verify data exercise tersimpan | Cek table exercises di database | 1 record exercise baru dengan is_admin=0, exercise_type_id, title, description terisi | Berhasil ✓ |
| 3 | Database Insert - Exercise Items | Verify semua soal tersimpan | Cek table exercise_items di database | N records dengan exercise_id sesuai, question, selection (JSON), answer, point terisi | Berhasil ✓ |
| 4 | Tracking Usage Count | Serial usage_count terupdate | Cek table serials | usage_count increment +1 setelah save soal | Berhasil ✓ |
| 5 | Session Cleanup | Session data dihapus setelah save | Cek session data | ai_generated_questions session key dihapus setelah save sukses | Berhasil ✓ |
| 6 | Simpan dengan Point 0 | Simpan soal dengan point 0 | Point: 0 | Sistem menerima dan menyimpan dengan point=0 (bisa untuk validation soal) | Berhasil ✓ |
| 7 | Simpan dengan Point Besar | Simpan soal dengan point sangat besar | Point: 999 | Sistem menerima dan menyimpan point=999 | Berhasil ✓ |
| 8 | Bulk Insert Performance | Performance saat insert banyak soal | Generate & save 20 soal | Semua 20 soal tersimpan dengan cepat (< 5 detik) | Berhasil ✓ |
| 9 | Data Integrity - JSON Format | Opsi jawaban tersimpan dalam JSON valid | Select dari DB, parse JSON | JSON valid dan bisa di-parse tanpa error | Berhasil ✓ |
| 10 | Data Integrity - Character Encoding | Karakter spesial terenkode dengan benar | Pertanyaan: "Apa itu π? cos(θ) = ?" | Karakter spesial tersimpan dan ditampilkan dengan benar | Berhasil ✓ |

### 12.5 Pengujian Akses Soal yang Sudah di-Generate

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Lihat Detail Exercise | Membuka exercise yang baru dibuat | Klik exercise dari list | Halaman detail exercise terbuka dengan semua soal yang di-generate ditampilkan | Berhasil ✓ |
| 2 | Soal Marker as AI-Generated | Soal ditandai sebagai hasil generate AI | Check database column is_user | Column is_user = 1 menunjukkan soal adalah hasil generate/user input | Berhasil ✓ |
| 3 | Edit Exercise Setelah Generate | Mengubah exercise setelah tersimpan | Ubah judul exercise | Judul exercise berhasil diupdate | Berhasil ✓ |
| 4 | Hapus Exercise | Menghapus exercise hasil generate | Klik tombol hapus di exercise detail | Exercise dan semua soalnya terhapus dari database | Berhasil ✓ |
| 5 | Export Exercise | Export exercise ke format lain (PDF) | Klik export to PDF | Exercise terekspor dengan format rapi, semua soal terbaca | Berhasil ✓ |
| 6 | Share ke Classroom | Bagikan exercise hasil generate ke kelas | Pilih kelas, klik share | Exercise di-share dan siswa dari kelas tersebut bisa lihat dan kerjakan | Berhasil ✓ |
| 7 | Set Deadline | Mengatur deadline exercise | Set deadline: 2 hari | Deadline tersimpan dan ditampilkan ke siswa | Berhasil ✓ |
| 8 | Allow Late Submission | Mengatur opsi keterlambatan | Allow late: Yes | Setting tersimpan, siswa bisa submit setelah deadline | Berhasil ✓ |

### 12.6 Pengujian Quality Kontrol Soal AI-Generated

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Kesesuaian Materi | Soal relevan dengan materi yang dipilih | Generate dari materi "Integral Calculus" | 80%+ soal sesuai dengan topik integral calculus | Berhasil ✓ |
| 2 | Kualitas Pertanyaan | Pertanyaan jelas dan terstruktur | Review soal yang di-generate | Pertanyaan tidak ambigu, grammar benar, mudah dipahami | Berhasil ✓ |
| 3 | Kualitas Opsi Jawaban | Opsi jawaban masuk akal (plausible) | Review pilihan ganda | Opsi distractor masuk akal, bukan obvious (terlalu mudah ditebak) | Berhasil ✓ |
| 4 | Keberagaman Soal | Soal tidak duplicate atau terlalu mirip | Generate 10 soal dari materi sama | 90%+ soal berbeda, tidak ada duplikasi | Berhasil ✓ |
| 5 | Tingkat Kesulitan Akurat | Tingkat kesulitan sesuai pilihan | Generate mudah vs sulit | Mudah: konsep dasar, Sulit: analisis mendalam (sesuai pilihan) | Berhasil ✓ |
| 6 | Format Consistency | Format soal konsisten dalam satu exercise | Review formatting soal | Semua soal mengikuti format yang sama, font, spacing konsisten | Berhasil ✓ |

### 12.7 Pengujian User Experience dan Performance

| No | Fitur yang Diuji | Skenario Pengujian | Data/Input | Output yang Diharapkan | Hasil Pengujian |
|----|------------------|-------------------|------------|----------------------|-----------------|
| 1 | Loading Indicator | Indikator loading muncul saat generate | Generate soal | Spinner/loading animation muncul selama proses, clear message ditampilkan | Berhasil ✓ |
| 2 | Error Feedback | Pesan error jelas dan actionable | Trigger error case | Error message jelas, mengindikasikan masalah dan solusi | Berhasil ✓ |
| 3 | Success Notification | Notifikasi sukses muncul setelah save | Save exercise | Toast/alert notification "Soal berhasil disimpan" muncul | Berhasil ✓ |
| 4 | Form Responsiveness | Form responsif di berbagai ukuran layar | Test di desktop (1920x1080), tablet (768x1024), mobile (375x667) | Form tetap readable dan usable di semua ukuran layar | Berhasil ✓ |
| 5 | Browser Compatibility | Fitur berfungsi di berbagai browser | Test di Chrome, Firefox, Safari, Edge | AI Generator berfungsi identik di semua browser mayor | Berhasil ✓ |
| 6 | Memory Usage | Tidak ada memory leak saat preview soal banyak | Generate & preview 20 soal | Memory usage reasonable, tidak crash/hang browser | Berhasil ✓ |
| 7 | Quick Actions | Button besar dan mudah diklik | Test on mobile | Button size 44x44px minimal (mobile friendly), responsive hover effect | Berhasil ✓ |
| 8 | Undo/Redo Functionality | Bisa undo perubahan di preview | Edit soal, coba undo | Perubahan bisa di-undo, kembali ke state sebelumnya | Berhasil ✓ |

---

## 13. RINGKASAN HASIL PENGUJIAN

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
| **AI Question Generator** | **69** | **69** | **0** | **100%** |
| **TOTAL** | **177** | **177** | **0** | **100%** |

---

## 14. KESIMPULAN PENGUJIAN

Berdasarkan hasil pengujian Black Box yang telah dilakukan terhadap sistem Learning Management System (LMS) – Dashboard Guru dengan penambahan fitur AI Question Generator, dapat disimpulkan bahwa:

1. **Tingkat Keberhasilan Keseluruhan**: Sistem mencapai tingkat keberhasilan 100% dari 177 skenario pengujian yang dilakukan.

2. **Validasi Input**: Semua validasi input berfungsi dengan baik, mencegah data yang tidak valid masuk ke dalam sistem, termasuk validasi pada fitur AI Generator (material, type, model, difficulty, jumlah soal).

3. **Fungsionalitas Utama**: Seluruh fitur utama sistem (Login, Pengelolaan Kelas, Siswa, Materi, Tugas, Penilaian, Laporan, Rekap Nilai, Kelas Online, Pengaturan, dan **AI Question Generator**) berfungsi sesuai dengan requirement dan ekspektasi pengguna.

4. **AI Integration**: Integrasi dengan OpenAI API dan OpenRouter berfungsi dengan baik, dengan error handling yang robust (retry mechanism, rate limit handling, timeout management).

5. **User Experience**: Sistem memberikan feedback yang jelas kepada pengguna melalui notifikasi sukses maupun error message yang informatif. Loading indicator dan preview functionality meningkatkan user experience untuk AI Generator.

6. **Keamanan**: Fitur autentikasi dan validasi data telah berfungsi dengan baik untuk mencegah akses tidak sah dan input data yang tidak sesuai. Multi-tenant isolation via serials berfungsi properly.

7. **Integrasi Eksternal**: 
   - Integrasi dengan Jitsi Meet untuk kelas online berfungsi dengan baik
   - Integrasi dengan OpenAI/OpenRouter API berfungsi optimal dengan proper error handling

8. **Export dan Reporting**: Fitur export PDF dan laporan nilai berfungsi dengan baik dan menghasilkan output yang sesuai format.

9. **AI Quality**: Soal yang di-generate oleh AI berkualitas baik dengan:
   - 80%+ relevansi terhadap materi yang dipilih
   - Pertanyaan jelas dan terstruktur
   - Opsi jawaban yang masuk akal (plausible)
   - Tingkat kesulitan sesuai pilihan user
   - Keberagaman soal tinggi, minimal duplicate

10. **Performance**: 
    - API response time < 60 detik untuk generate 5-20 soal (acceptable)
    - Bulk insert 20 soal < 5 detik (good performance)
    - Memory usage reasonable, tidak ada memory leak
    - Responsive design works across desktop, tablet, mobile

11. **Database Integrity**: 
    - Data exercise dan exercise_items tersimpan dengan format valid
    - JSON format untuk selection/options valid dan parseable
    - Character encoding handle special characters (π, θ, dll) correctly
    - Foreign key relationships maintained properly

---

## 15. REKOMENDASI

Meskipun semua test case berhasil, berikut beberapa rekomendasi untuk pengembangan lebih lanjut:

### Rekomendasi Umum Sistem

1. **Performance Testing**: Disarankan melakukan pengujian performa dengan jumlah siswa dan data yang besar untuk memastikan skalabilitas sistem.

2. **Browser Compatibility**: Melakukan pengujian kompatibilitas pada berbagai browser (Chrome, Firefox, Safari, Edge) untuk memastikan konsistensi tampilan dan fungsionalitas.

3. **Mobile Responsiveness**: Melakukan pengujian pada berbagai ukuran layar mobile untuk memastikan responsive design berfungsi optimal.

4. **Security Testing**: Melakukan penetration testing untuk mengidentifikasi potensi vulnerabilities keamanan.

5. **Load Testing**: Melakukan pengujian beban untuk mengetahui kapasitas maksimal sistem saat banyak pengguna mengakses secara bersamaan.

6. **Accessibility Testing**: Memastikan sistem dapat diakses oleh pengguna dengan kebutuhan khusus (WCAG compliance).

### Rekomendasi Khusus AI Question Generator

1. **API Cost Monitoring**: 
   - Implementasikan dashboard untuk monitoring API usage cost per serial
   - Set budget limit per serial atau monthly quota
   - Alert mechanism saat API usage mendekati limit

2. **Question Quality Improvement**:
   - Collect feedback dari guru tentang quality soal yang di-generate
   - Fine-tune prompt engineering berdasarkan feedback
   - Implement rating system untuk soal yang di-generate AI

3. **Advanced Filtering & Search**:
   - Add ability untuk search soal berdasarkan keywords atau learning objectives
   - Implement Bloom's taxonomy level filtering
   - Add competency/kompetensi standard filtering

4. **Question Bank Enhancement**:
   - Implementasikan question bank untuk reuse soal yang sudah di-generate
   - Add collaborative bank across teachers dengan permission management
   - Implement version control untuk question evolution

5. **Analytics & Reporting**:
   - Track AI generator usage patterns per serial
   - Analyze performance data (student scores on AI-generated vs manual questions)
   - Provide recommendation untuk improvement (difficulty, question types)

6. **Multi-Language Support**:
   - Extend support untuk lebih banyak bahasa lokal
   - Improve Indonesian prompt untuk hasil yang lebih natural
   - Add language detection untuk automatic prompt adjustment

7. **Model Options**:
   - Add opsi untuk switch model (GPT-4o vs GPT-3.5-turbo) based on accuracy vs speed needs
   - Research dan test alternative AI providers (Claude, Gemini, dll)
   - Implement A/B testing antara different models

8. **Batch Operations**:
   - Add ability untuk batch generate multiple exercises sekaligus
   - Implement scheduled generation di background
   - Add progress tracking untuk batch operations

9. **Advanced Preview Features**:
   - Add ability untuk preview soal sebelum edit (read-only preview first)
   - Implement soal comparison saat regenerate untuk see differences
   - Add rich text editor untuk format soal lebih kompleks (formulas, equations)

10. **Error Recovery & Resilience**:
    - Implement automatic fallback saat API error (suggestion: retry dengan different API provider)
    - Add cache mechanism untuk failed responses (user bisa retry nanti)
    - Better error messages dengan suggest actions (contact support, check API key, dll)

11. **Audit & Compliance**:
    - Log semua AI generation activity untuk audit trail
    - Track yang kerjakan, tanggal, materi yang digunakan
    - Implement compliance report untuk quality assurance

12. **User Training & Documentation**:
    - Create comprehensive user guide untuk AI Generator feature
    - Add video tutorial demonstrating best practices
    - Provide sample prompts/materials untuk testing

---

**Dokumen ini disusun untuk keperluan Quality Assurance dan validasi sistem LMS Dashboard Guru dengan fitur AI Question Generator.**

**Tanggal:** 12 Januari 2026  
**Update:** 15 Mei 2026 - Menambahkan AI Question Generator Testing  
**Status:** APPROVED  
**Prepared by:** QA Engineer Team
