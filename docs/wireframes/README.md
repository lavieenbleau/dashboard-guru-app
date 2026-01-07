# Wireframe Lo-Fi - Dashboard Guru

Wireframe lo-fi untuk sistem Dashboard Guru yang telah dibuat dengan gaya minimalis dan sketch-like.

## 📁 Daftar File Wireframe

### 1. Dashboard
- **File**: `dashboard-guru-lofi-minimal.html`
- **Deskripsi**: Halaman utama dengan overview statistik, aktivitas terbaru, dan meeting mendatang
- **Komponen**:
  - 4 kartu statistik (Materi, Soal, Tugas, Kelas)
  - 3 kartu info (Total Siswa, Meeting, Tugas)
  - Aktivitas terbaru list
  - Quick access meeting

### 2. Materi
- **File**: `materi-list.html`
- **Deskripsi**: Halaman daftar mapel dengan grid layout
- **Komponen**:
  - Tabs (Admin, Custom, Semua)
  - Grid mapel dengan badge jumlah item
  - Filter dan tombol tambah

- **File**: `materi-detail.html`
- **Deskripsi**: Detail materi per mapel dalam format tabel
- **Komponen**:
  - Breadcrumb navigation
  - Tabel list materi
  - Action buttons (View, Edit, Delete)

### 3. Soal
- **File**: `soal-list.html`
- **Deskripsi**: Kategori soal dengan grid layout
- **Komponen**:
  - 4 kategori soal dengan icon dan counter
  - Label floating di atas border
  - Hover effect

### 4. Tugas
- **File**: `tugas-list.html`
- **Deskripsi**: Daftar tugas dalam format card grid
- **Komponen**:
  - Card dengan header, body, footer
  - Status badge
  - Meta info (tanggal, kelas)
  - Stats (submisi, nilai)
  - Action buttons

### 5. Kelas
- **File**: `kelas-list.html`
- **Deskripsi**: Daftar kelas dengan statistik
- **Komponen**:
  - Card grid kelas
  - Nama kelas dan kode
  - Stats siswa dan tugas
  - Footer dengan action buttons

### 6. Laporan Harian
- **File**: `laporan-harian.html`
- **Deskripsi**: Kalender laporan harian
- **Komponen**:
  - Calendar grid 7 hari
  - Navigasi bulan
  - Badge indikator laporan per hari
  - Filter dropdown

### 7. Rekap Nilai
- **File**: `rekap-nilai.html`
- **Deskripsi**: Tabel rekap nilai siswa
- **Komponen**:
  - Summary stats (Rata-rata, Tertinggi, Terendah, Total)
  - Tabel nilai dengan kolom scoring
  - Filter kelas
  - Export PDF button

### 8. Pengaturan
- **File**: `pengaturan.html`
- **Deskripsi**: Halaman settings profile
- **Komponen**:
  - Tabs (Profile, Password, Notifikasi)
  - Upload avatar section
  - Form fields
  - Save/Cancel buttons

## 🎨 Desain Karakteristik

- **Font**: Courier New (monospace) untuk kesan sketch
- **Border**: Tegas dengan variasi ketebalan (1px, 2px)
- **Border Style**: Solid untuk struktur utama, dashed untuk pemisah
- **Warna**: Grayscale (#333, #666, #999, #ccc, #f5f5f5)
- **Background**: #fafafa untuk halaman, #fff untuk card
- **Layout**: Consistent sidebar 200px + main content
- **Navigation**: 8 menu utama di sidebar

## 📐 Layout Grid

- **Dashboard**: 4 kolom stats + 3 kolom info + 2 kolom content
- **Materi List**: 3 kolom grid
- **Soal**: 2 kolom grid kategori
- **Tugas**: 2 kolom card grid
- **Kelas**: 3 kolom card grid
- **Laporan**: 7 kolom calendar
- **Rekap**: 4 kolom stats + table full width

## 🚀 Cara Menggunakan

1. Buka file HTML langsung di browser
2. Tidak memerlukan server atau dependencies
3. Pure HTML + CSS inline
4. Responsif untuk desktop (min-width: 1400px optimal)

## 📝 Catatan

- Semua wireframe menggunakan struktur konsisten (header, sidebar, content)
- Placeholder elements (border boxes) menggantikan konten real
- Fokus pada layout structure, bukan visual detail
- Mudah dipahami untuk handoff ke developer
