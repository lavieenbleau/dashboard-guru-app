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

### Local Development

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

## Deploy to Railway

1. Fork this repository
2. Go to [Railway.app](https://railway.app)
3. Click "New Project"
4. Select "Deploy from GitHub repo"
5. Select this repository
6. Add MySQL database service
7. Set environment variables:
   - `APP_KEY` (generate with `php artisan key:generate --show`)
   - `DB_CONNECTION=mysql`
   - `DB_HOST=${{MySQL.RAILWAY_PRIVATE_DOMAIN}}`
   - `DB_PORT=${{MySQL.RAILWAY_TCP_PORT}}`
   - `DB_DATABASE=${{MySQL.MYSQL_DATABASE}}`
   - `DB_USERNAME=${{MySQL.MYSQL_USER}}`
   - `DB_PASSWORD=${{MySQL.MYSQL_PASSWORD}}`
8. Deploy!

## Deploy to Heroku

```bash
# Login to Heroku
heroku login

# Create app
heroku create dashboard-guru-app

# Add MySQL addon
heroku addons:create jawsdb:kitefin

# Set APP_KEY
heroku config:set APP_KEY=$(php artisan key:generate --show)

# Deploy
git push heroku main

# Run migrations
heroku run php artisan migrate --force
```

## Environment Variables

Required variables:
- `APP_KEY` - Laravel encryption key
- `DB_CONNECTION` - Database driver (mysql)
- `DB_HOST` - Database host
- `DB_PORT` - Database port
- `DB_DATABASE` - Database name
- `DB_USERNAME` - Database username
- `DB_PASSWORD` - Database password

## License

Open source. Free to use.

## Screenshots

(Add screenshots here)

## Support

For issues or questions, please open an issue on GitHub.
