# Dashboard Guru App

Dashboard aplikasi untuk guru berbasis Laravel 12 dengan template Sneat Bootstrap.

## Features

- **Multi-Aplikasi**: Guru bisa mengelola beberapa aplikasi (serial)
- **Manajemen Kelas**: Kelola kelas dan siswa
- **Materi Pembelajaran**: Upload dan kelola materi ajar
- **Tugas**: Buat dan kelola tugas untuk siswa
- **Soal**: 4 kategori soal (Ulangan Harian, PTS, PAS, Tambahan)
- **Online Class**: Jadwal meeting online (Zoom, Google Meet, Teams)
- **Laporan Harian**: Otomatis mencatat aktivitas pengumpulan tugas
- **Penilaian**: Guru bisa memberi nilai langsung dari sistem
- **Profile Settings**: Kelola profil dan password

## Tech Stack

- Laravel 12.35.0
- PHP 8.4.2
- MySQL
- Bootstrap 5 (Sneat Template)
- Tailwind CSS

## Installation

```bash
# Clone repository
git clone https://github.com/lavieenbleau/dashboard-guru-app.git
cd dashboard-guru-app

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate

# Setup database
# Edit .env file with your database credentials
php artisan migrate

# Build assets
npm run build

# Run server
php artisan serve
```

## License

Open source. Free to use.
