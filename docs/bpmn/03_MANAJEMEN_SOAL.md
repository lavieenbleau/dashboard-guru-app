# BPMN: Manajemen Soal Latihan

## Deskripsi Proses
Proses pembuatan, pengelolaan, dan distribusi soal latihan dengan berbagai tipe (Multiple Choice, Essay, True/False) serta sistem auto-grading.

## Diagram BPMN

```mermaid
graph TD
    Start([Guru Akses Menu Soal]) --> LoadSoal[Load Halaman Soal]
    LoadSoal --> ShowOptions{Pilih Aksi}
    
    %% CREATE SOAL
    ShowOptions -->|Buat Soal Baru| SelectType[Pilih Tipe Soal]
    SelectType --> TypeChosen{Tipe Soal}
    
    TypeChosen -->|Multiple Choice| FormMC[Form Multiple Choice]
    FormMC --> InputQuestionMC[Input Pertanyaan]
    InputQuestionMC --> InputOptions[Input 4-5 Pilihan]
    InputOptions --> MarkCorrect[Tandai Jawaban Benar]
    MarkCorrect --> SetPoint[Set Poin Soal]
    SetPoint --> SaveMC[Simpan Soal MC]
    SaveMC --> SuccessSave[Success: Soal Disimpan]
    
    TypeChosen -->|Essay| FormEssay[Form Essay]
    FormEssay --> InputQuestionEssay[Input Pertanyaan Essay]
    InputQuestionEssay --> SetPointEssay[Set Poin Soal]
    SetPointEssay --> SaveEssay[Simpan Soal Essay]
    SaveEssay --> SuccessSave
    
    TypeChosen -->|True/False| FormTF[Form True/False]
    FormTF --> InputQuestionTF[Input Pertanyaan]
    InputQuestionTF --> MarkTrueFalse[Tandai Jawaban Benar]
    MarkTrueFalse --> SetPointTF[Set Poin Soal]
    SetPointTF --> SaveTF[Simpan Soal T/F]
    SaveTF --> SuccessSave
    
    SuccessSave --> LoadSoal
    
    %% SHARE SOAL KE KELAS
    ShowOptions -->|Share ke Kelas| SelectSoal[Pilih Soal]
    SelectSoal --> SelectClass[Pilih Kelas Tujuan]
    SelectClass --> ValidateClass{Kelas Valid?}
    ValidateClass -->|Tidak| ErrorClass[Error: Kelas Invalid]
    ErrorClass --> SelectClass
    ValidateClass -->|Ya| AssignToClass[Assign Soal ke Kelas]
    AssignToClass --> SetDeadline[Set Deadline Opsional]
    SetDeadline --> ActivateSoal[Aktifkan Soal untuk Kelas]
    ActivateSoal --> NotifySiswa[Notifikasi ke Siswa]
    NotifySiswa --> SuccessShare[Success: Soal Dibagikan]
    SuccessShare --> LoadSoal
    
    %% VIEW HASIL PENGERJAAN
    ShowOptions -->|Lihat Hasil| SelectExercise[Pilih Soal]
    SelectExercise --> FetchResults[Ambil Data Hasil Siswa]
    FetchResults --> ShowResults[Tampilkan Hasil per Siswa]
    ShowResults --> ResultAction{Aksi}
    
    ResultAction -->|Auto-Grade MC| AutoGrade[Sistem Hitung Otomatis]
    AutoGrade --> ShowScore[Tampilkan Skor]
    ShowScore --> ShowResults
    
    ResultAction -->|Grade Essay| ManualGrade[Guru Input Nilai Manual]
    ManualGrade --> SaveGrade[Simpan Nilai]
    SaveGrade --> UpdateStatus[Update Status: Dinilai]
    UpdateStatus --> ShowResults
    
    ResultAction -->|Export| ExportData[Export ke Excel/PDF]
    ExportData --> DownloadExport[Download File]
    DownloadExport --> ShowResults
    
    ResultAction -->|Kembali| LoadSoal
    
    %% EDIT SOAL
    ShowOptions -->|Edit Soal| LoadEditSoal[Load Form Edit]
    LoadEditSoal --> CheckUsage{Soal Sudah Dikerjakan?}
    CheckUsage -->|Ya| WarningEdit[Warning: Data Hasil Terpengaruh]
    WarningEdit --> ConfirmEdit{Lanjut Edit?}
    ConfirmEdit -->|Tidak| LoadSoal
    ConfirmEdit -->|Ya| EditSoalData[Edit Data Soal]
    CheckUsage -->|Tidak| EditSoalData
    EditSoalData --> UpdateSoal[Update Database]
    UpdateSoal --> SuccessEdit[Success: Soal Diupdate]
    SuccessEdit --> LoadSoal
    
    %% DELETE SOAL
    ShowOptions -->|Hapus Soal| ConfirmDeleteSoal[Konfirmasi Hapus]
    ConfirmDeleteSoal --> DeleteResults[Hapus Hasil Pengerjaan]
    DeleteResults --> DeleteAssignment[Hapus Assignment ke Kelas]
    DeleteAssignment --> DeleteSoalDB[Hapus Soal dari DB]
    DeleteSoalDB --> SuccessDeleteSoal[Success: Soal Dihapus]
    SuccessDeleteSoal --> LoadSoal
    
    LoadSoal --> End([End])
    
    style Start fill:#90EE90
    style End fill:#90EE90
    style TypeChosen fill:#FFE4B5
    style ValidateClass fill:#FFE4B5
    style CheckUsage fill:#FFE4B5
    style ConfirmEdit fill:#FFE4B5
```

## Actor
- **Guru** (Primary Actor)
- **Siswa** (Secondary Actor - mengerjakan soal)
- **Sistem Auto-Grading** (Supporting System)

## Preconditions
- Guru sudah login dan berada di aplikasi/serial
- Guru memiliki akses ke kelas
- Kompetensi dan materi sudah terdefinisi

## Postconditions
- Soal berhasil dibuat dan tersimpan
- Soal terdistribusi ke kelas yang dituju
- Hasil pengerjaan siswa terekam
- Auto-grading berfungsi untuk MC dan T/F

## Main Flow: Buat Soal Multiple Choice
1. Guru klik "Buat Soal Baru"
2. Guru pilih tipe "Multiple Choice"
3. Sistem tampilkan form MC
4. Guru input pertanyaan
5. Guru input 4-5 pilihan jawaban (A, B, C, D, E)
6. Guru tandai 1 jawaban sebagai benar
7. Guru set poin soal (misal: 10 poin)
8. Guru pilih kompetensi terkait
9. Sistem validasi input
10. Sistem simpan ke tabel `exercises` dan `exercise_items`
11. Sistem redirect dengan pesan sukses

## Main Flow: Share Soal ke Kelas
1. Guru pilih soal dari daftar
2. Guru klik "Share ke Kelas"
3. Sistem tampilkan daftar kelas guru
4. Guru pilih 1 atau lebih kelas
5. Guru set deadline (opsional)
6. Guru set timer/durasi pengerjaan (opsional)
7. Sistem assign soal ke kelas via pivot table
8. Sistem buat notifikasi untuk siswa di kelas tersebut
9. Soal muncul di dashboard siswa

## Main Flow: Auto-Grading Multiple Choice
1. Siswa submit jawaban MC
2. Sistem ambil jawaban siswa
3. Sistem bandingkan dengan jawaban benar di database
4. Sistem hitung: (Jumlah Benar / Total Soal) × 100
5. Sistem simpan nilai otomatis
6. Sistem update status: "Selesai - Dinilai"
7. Siswa bisa lihat nilai langsung

## Main Flow: Manual Grading Essay
1. Guru lihat hasil pengerjaan essay
2. Sistem tampilkan jawaban essay siswa
3. Guru baca dan nilai jawaban
4. Guru input nilai (0-100 atau sesuai poin max)
5. Guru bisa tambahkan feedback
6. Sistem simpan nilai
7. Status berubah menjadi "Dinilai"
8. Siswa mendapat notifikasi nilai

## Alternative Flow
### A1: Soal Sudah Dikerjakan Siswa
- Jika guru edit soal yang sudah dikerjakan, sistem beri warning
- Data hasil pengerjaan siswa bisa terpengaruh

### A2: Randomize Soal
- Sistem bisa acak urutan soal untuk tiap siswa
- Sistem acak urutan pilihan jawaban MC

### A3: Bank Soal Admin
- Guru bisa akses soal dari admin/pusat
- Guru tinggal assign ke kelas tanpa buat dari nol

## Business Rules
- BR-001: MC harus punya minimal 2 pilihan, maksimal 5 pilihan
- BR-002: Hanya 1 jawaban benar untuk MC dan T/F
- BR-003: Essay tidak ada auto-grading, harus manual
- BR-004: Poin soal minimal 1, maksimal 100
- BR-005: Deadline opsional, tapi recommended
- BR-006: Timer bisa diset untuk ujian (misal: 90 menit)
- BR-007: Siswa hanya bisa kerjakan 1x (no retry default)

## Technical Notes
- **Controller**: `SoalController`
- **Models**: Exercise, ExerciseItem, ExerciseType, Competence, ExercisePoint
- **Auto-Grading Logic**: Controller method `autoGrade()`
- **Pivot Table**: `classroom_exercise` untuk assignment
- **Randomizer**: `->inRandomOrder()` untuk acak soal
- **Timer**: JavaScript countdown di frontend
- **Export**: Laravel Excel package
