# BPMN: Login dan Pilih Aplikasi

## Deskripsi Proses
Proses autentikasi guru dan pemilihan aplikasi/kurikulum yang akan digunakan.

## Diagram BPMN

```mermaid
graph TD
    Start([Guru Mengakses Sistem]) --> LoginPage[Tampilkan Halaman Login]
    LoginPage --> InputCredentials[Input Email & Password]
    InputCredentials --> ValidateAuth{Validasi Kredensial}
    
    ValidateAuth -->|Invalid| ErrorMessage[Tampilkan Pesan Error]
    ErrorMessage --> LoginPage
    
    ValidateAuth -->|Valid| CheckRole{Cek Role User}
    CheckRole -->|Bukan Guru| AccessDenied[Akses Ditolak]
    AccessDenied --> LoginPage
    
    CheckRole -->|Guru| SetSession[Set Session & Token]
    SetSession --> RedirectAplikasi[Redirect ke /aplikasi]
    
    RedirectAplikasi --> FetchSerials[Ambil Data Serial User]
    FetchSerials --> CheckSerials{Ada Serial?}
    
    CheckSerials -->|Tidak Ada| NoApps[Tampilkan: Tidak Ada Aplikasi]
    NoApps --> ContactAdmin[Hubungi Admin]
    ContactAdmin --> End1([End])
    
    CheckSerials -->|Ada| ShowApps[Tampilkan Daftar Aplikasi]
    ShowApps --> GuruPilih[Guru Pilih Aplikasi]
    GuruPilih --> ValidateSerial{Serial Valid?}
    
    ValidateSerial -->|Tidak| Error404[Error 404]
    Error404 --> ShowApps
    
    ValidateSerial -->|Valid| LoadDashboard[Load Dashboard Aplikasi]
    LoadDashboard --> FetchStats[Ambil Statistik]
    FetchStats --> ShowDashboard[Tampilkan Dashboard]
    ShowDashboard --> End2([End: Guru di Dashboard])
    
    style Start fill:#90EE90
    style End1 fill:#FFB6C1
    style End2 fill:#90EE90
    style ValidateAuth fill:#FFE4B5
    style CheckRole fill:#FFE4B5
    style CheckSerials fill:#FFE4B5
    style ValidateSerial fill:#FFE4B5
```

## Actor
- **Guru** (Primary Actor)
- **Sistem Authentication** (Laravel Breeze)

## Preconditions
- Guru sudah terdaftar di sistem
- Guru memiliki minimal 1 serial produk aktif

## Postconditions
- Guru berhasil login
- Session aktif
- Guru berada di dashboard aplikasi yang dipilih

## Main Flow
1. Guru mengakses halaman login
2. Sistem menampilkan form login
3. Guru memasukkan email dan password
4. Sistem memvalidasi kredensial
5. Sistem mengecek role user (harus guru)
6. Sistem membuat session dan redirect ke `/aplikasi`
7. Sistem mengambil daftar serial milik guru
8. Sistem menampilkan daftar aplikasi
9. Guru memilih salah satu aplikasi
10. Sistem memvalidasi serial yang dipilih
11. Sistem load dashboard dengan statistik
12. Sistem menampilkan dashboard aplikasi

## Alternative Flow
### A1: Kredensial Invalid
- 4a. Jika kredensial salah, tampilkan error dan kembali ke login

### A2: Bukan Role Guru
- 5a. Jika user bukan guru, akses ditolak

### A3: Tidak Ada Serial
- 7a. Jika guru tidak memiliki serial, tampilkan pesan dan sarankan hubungi admin

### A4: Serial Invalid
- 10a. Jika serial tidak valid atau bukan milik guru, tampilkan error 404

## Business Rules
- BR-001: Hanya user dengan role "guru" yang bisa akses
- BR-002: Guru hanya bisa akses serial yang terdaftar atas namanya
- BR-003: Session berlaku selama 2 jam (configurable)
- BR-004: Password harus di-hash menggunakan Bcrypt

## Technical Notes
- **Controller**: `AplikasiController@index`, `AplikasiController@dashboard`
- **Middleware**: `auth`, `verified`
- **Routes**: `/aplikasi`, `/aplikasi/{serial}`
- **Models**: Serial, Product, User
- **View**: `guru.aplikasi.index`, `guru.aplikasi.dashboard`
