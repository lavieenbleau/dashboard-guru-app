# BPMN: Kelas Online dengan Jitsi Meet

## Deskripsi Proses
Proses pembuatan, penjadwalan, dan pelaksanaan kelas online menggunakan integrasi Jitsi Meet untuk video conference.

## Diagram BPMN

```mermaid
graph TD
    Start([Guru Akses Menu Kelas Online]) --> LoadMeetings[Load Halaman Kelas Online]
    LoadMeetings --> ShowActions{Pilih Aksi}
    
    %% CREATE MEETING
    ShowActions -->|Buat Kelas Online| FormMeeting[Tampilkan Form Meeting]
    FormMeeting --> InputMeetingData[Input Data Meeting]
    InputMeetingData --> InputRequired[Input Field Required]
    InputRequired --> GenerateURL[Generate Jitsi Meeting URL]
    GenerateURL --> GenerateToken{Butuh Token/Password?}
    GenerateToken -->|Ya| CreateToken[Generate Random Token]
    CreateToken --> SaveMeeting[Simpan ke Database]
    GenerateToken -->|Tidak| SaveMeeting
    SaveMeeting --> SendNotifSiswa[Kirim Notifikasi ke Siswa]
    SendNotifSiswa --> SuccessCreateMeeting[Success: Meeting Dibuat]
    SuccessCreateMeeting --> LoadMeetings
    
    %% START MEETING
    ShowActions -->|Mulai Meeting| SelectMeeting[Pilih Meeting]
    SelectMeeting --> CheckSchedule{Cek Jadwal}
    CheckSchedule -->|Belum Waktunya| EarlyWarning[Warning: Belum Waktunya]
    EarlyWarning --> ConfirmEarly{Tetap Mulai?}
    ConfirmEarly -->|Tidak| LoadMeetings
    ConfirmEarly -->|Ya| InitiateMeeting[Inisiasi Meeting]
    CheckSchedule -->|Sudah Waktunya| InitiateMeeting
    CheckSchedule -->|Lewat Jadwal| LateWarning[Warning: Lewat Jadwal]
    LateWarning --> InitiateMeeting
    
    InitiateMeeting --> OpenJitsi[Redirect ke Jitsi Meet]
    OpenJitsi --> ConfigureJitsi[Konfigurasi Jitsi Room]
    ConfigureJitsi --> SetModerator[Set Guru sebagai Moderator]
    SetModerator --> EnableFeatures[Enable Fitur Jitsi]
    EnableFeatures --> JitsiReady[Jitsi Room Siap]
    JitsiReady --> UpdateStatus[Update Status: Sedang Berlangsung]
    UpdateStatus --> WaitParticipants[Tunggu Siswa Join]
    
    WaitParticipants --> SiswaJoin[Siswa Join Meeting]
    SiswaJoin --> ValidateSiswa{Siswa Valid?}
    ValidateSiswa -->|Tidak| RejectJoin[Tolak Akses]
    RejectJoin --> WaitParticipants
    ValidateSiswa -->|Ya| AllowJoin[Izinkan Masuk]
    AllowJoin --> RecordAttendance[Catat Kehadiran]
    RecordAttendance --> InMeeting[Dalam Meeting]
    
    InMeeting --> MeetingActions{Aksi dalam Meeting}
    
    MeetingActions -->|Screen Share| EnableScreenShare[Enable Screen Sharing]
    EnableScreenShare --> InMeeting
    
    MeetingActions -->|Chat| SendMessage[Kirim Pesan Chat]
    SendMessage --> InMeeting
    
    MeetingActions -->|Hand Raise| RaiseHand[Angkat Tangan Virtual]
    RaiseHand --> GuruRespond[Guru Beri Izin Bicara]
    GuruRespond --> InMeeting
    
    MeetingActions -->|Recording| StartRecording[Mulai Recording]
    StartRecording --> RecordVideo[Record Audio+Video]
    RecordVideo --> StopRecording[Stop Recording]
    StopRecording --> SaveRecording[Simpan Recording]
    SaveRecording --> InMeeting
    
    MeetingActions -->|Mute All| MuteParticipants[Mute Semua Siswa]
    MuteParticipants --> InMeeting
    
    MeetingActions -->|End Meeting| ConfirmEnd{Yakin End?}
    ConfirmEnd -->|Tidak| InMeeting
    ConfirmEnd -->|Ya| EndMeeting[End Meeting untuk Semua]
    EndMeeting --> CloseMeetingRoom[Close Jitsi Room]
    CloseMeetingRoom --> GenerateReport[Generate Laporan Meeting]
    
    GenerateReport --> RecordDuration[Catat Durasi Actual]
    RecordDuration --> FinalizeAttendance[Finalisasi Daftar Hadir]
    FinalizeAttendance --> UpdateStatusDone[Update Status: Selesai]
    UpdateStatusDone --> SaveReport[Simpan Laporan Meeting]
    SaveReport --> SuccessEnd[Success: Meeting Selesai]
    SuccessEnd --> LoadMeetings
    
    %% VIEW HISTORY
    ShowActions -->|Lihat Riwayat| LoadHistory[Load Riwayat Meeting]
    LoadHistory --> ShowHistoryList[Tampilkan List Meeting Lalu]
    ShowHistoryList --> HistoryAction{Pilih Aksi}
    
    HistoryAction -->|Lihat Detail| ShowMeetingDetail[Tampilkan Detail Meeting]
    ShowMeetingDetail --> ShowAttendanceList[Tampilkan Daftar Hadir]
    ShowAttendanceList --> CalculateStats[Hitung Statistik]
    CalculateStats --> ShowStats[Tampilkan: Hadir, Tidak Hadir, %]
    ShowStats --> HistoryAction
    
    HistoryAction -->|Export Absensi| ExportAttendance[Export ke Excel/PDF]
    ExportAttendance --> DownloadReport[Download Laporan]
    DownloadReport --> HistoryAction
    
    HistoryAction -->|Lihat Recording| CheckRecordingExists{Ada Recording?}
    CheckRecordingExists -->|Tidak| NoRecording[Tidak Ada Recording]
    NoRecording --> HistoryAction
    CheckRecordingExists -->|Ya| PlayRecording[Play/Download Recording]
    PlayRecording --> HistoryAction
    
    HistoryAction -->|Kembali| LoadMeetings
    
    %% EDIT MEETING
    ShowActions -->|Edit Meeting| LoadEditMeeting[Load Form Edit]
    LoadEditMeeting --> CheckStarted{Meeting Sudah Dimulai?}
    CheckStarted -->|Ya| CannotEdit[Error: Tidak Bisa Edit]
    CannotEdit --> LoadMeetings
    CheckStarted -->|Tidak| EditMeetingData[Edit Data Meeting]
    EditMeetingData --> UpdateMeetingDB[Update Database]
    UpdateMeetingDB --> NotifyUpdate[Notif Update ke Siswa]
    NotifyUpdate --> SuccessEdit[Success]
    SuccessEdit --> LoadMeetings
    
    %% DELETE MEETING
    ShowActions -->|Hapus Meeting| ConfirmDeleteMeeting[Konfirmasi Hapus]
    ConfirmDeleteMeeting --> DeleteAttendanceData[Hapus Data Kehadiran]
    DeleteAttendanceData --> DeleteMeetingDB[Hapus dari Database]
    DeleteMeetingDB --> NotifyCancel[Notif Pembatalan ke Siswa]
    NotifyCancel --> SuccessDelete[Success]
    SuccessDelete --> LoadMeetings
    
    LoadMeetings --> End([End])
    
    style Start fill:#90EE90
    style End fill:#90EE90
    style GenerateToken fill:#FFE4B5
    style CheckSchedule fill:#FFE4B5
    style ConfirmEarly fill:#FFE4B5
    style ValidateSiswa fill:#FFE4B5
    style ConfirmEnd fill:#FFE4B5
    style CheckStarted fill:#FFE4B5
    style CheckRecordingExists fill:#FFE4B5
```

## Actor
- **Guru** (Primary Actor - Moderator)
- **Siswa** (Secondary Actor - Participant)
- **Jitsi Meet Server** (External System)

## Preconditions
- Guru sudah login dan berada di aplikasi/serial
- Guru memiliki akses ke kelas
- Internet connection stabil
- Jitsi Meet API accessible

## Postconditions
- Meeting terjadwal atau selesai dilaksanakan
- Daftar kehadiran terekam
- Laporan meeting tersimpan
- Recording tersedia (jika direkam)

## Main Flow: Buat Kelas Online
1. Guru klik "Buat Kelas Online"
2. Sistem tampilkan form input
3. Guru input:
   - Judul meeting
   - Pilih kelas
   - Pilih mata pelajaran
   - Tanggal dan waktu mulai
   - Durasi (dalam menit)
   - Deskripsi/agenda (opsional)
4. Sistem generate unique Jitsi room URL
   - Format: `https://meet.jit.si/DashboardGuru-{randomString}`
5. Sistem simpan ke tabel `online_meetings`
6. Sistem kirim notifikasi ke siswa di kelas tersebut
7. Meeting muncul di dashboard siswa dan guru

## Main Flow: Mulai dan Jalankan Meeting
1. Guru klik "Mulai Meeting" sesuai jadwal
2. Sistem validasi waktu (bisa mulai lebih awal)
3. Sistem redirect ke Jitsi Meet dengan URL unik
4. Jitsi load dengan konfigurasi:
   - Guru otomatis sebagai moderator
   - Room name sesuai judul meeting
   - Display name: Nama guru
5. Guru menunggu siswa join
6. Siswa klik "Join Meeting" dari dashboard
7. Sistem validasi siswa terdaftar di kelas
8. Siswa join Jitsi room
9. Sistem catat kehadiran siswa (timestamp join)
10. Meeting berlangsung dengan fitur:
    - Video & Audio
    - Screen sharing
    - Chat
    - Hand raise
    - Recording (opsional)
    - Mute participants (guru)
11. Guru klik "End Meeting" saat selesai
12. Sistem tutup room untuk semua participant
13. Sistem generate laporan:
    - Durasi actual
    - Daftar hadir (siapa join, kapan join, berapa lama)
    - Total peserta
14. Sistem update status meeting: "Selesai"

## Alternative Flow
### A1: Mulai Meeting Lebih Awal
- Guru bisa start meeting sebelum jadwal
- Sistem beri warning, tapi tetap izinkan

### A2: Siswa Join Terlambat
- Siswa tetap bisa join meskipun meeting sudah dimulai
- Sistem catat waktu join actual (terlambat)

### A3: Siswa Tidak Join
- Status kehadiran: "Tidak Hadir"
- Otomatis tercatat di laporan

### A4: Recording Meeting
- Guru klik "Start Recording" di Jitsi
- Video terekam di server Jitsi
- Link recording disimpan di database
- Siswa bisa akses recording di-kemudian hari

### A5: Meeting Gagal/Koneksi Terputus
- Jika guru disconnect, siswa tetap di room
- Guru bisa rejoin sebagai moderator
- Jitsi auto-reconnect jika internet pulih

### A6: Edit Meeting
- Hanya bisa edit meeting yang belum dimulai
- Bisa ubah jadwal, durasi, deskripsi
- Tidak bisa ubah kelas (harus buat baru)

### A7: Hapus/Cancel Meeting
- Bisa cancel meeting sebelum dimulai
- Sistem hapus data dan kirim notifikasi pembatalan
- Tidak bisa hapus meeting yang sudah selesai (hanya arsip)

## Business Rules
- BR-001: URL Jitsi unique per meeting (no reuse)
- BR-002: Guru otomatis moderator dengan full control
- BR-003: Siswa hanya participant (no moderator rights)
- BR-004: Meeting bisa dimulai max 30 menit sebelum jadwal
- BR-005: Kehadiran dicatat saat siswa join Jitsi room
- BR-006: Durasi actual bisa beda dengan durasi planned
- BR-007: Recording opsional, perlu consent
- BR-008: Chat history tidak disimpan (Jitsi behavior)
- BR-009: Meeting auto-close jika moderator end
- BR-010: Siswa tidak bisa start meeting (hanya join)

## Technical Notes
- **Controller**: `KelasOnlineController`
- **Models**: OnlineMeeting, Classroom, Mapel
- **Jitsi Integration**: 
  - URL: `https://meet.jit.si/{roomName}`
  - Room name: unique string generate dengan `Str::random(20)`
  - Iframe embed atau redirect langsung
  - Moderator: Pass JWT token (opsional untuk security)
- **Attendance Tracking**: 
  - Table: `meeting_attendances`
  - Fields: meeting_id, student_id, joined_at, left_at
- **Recording**: 
  - Jitsi local recording atau Jibri integration
  - Storage: Jitsi server atau S3
  - Link simpan di `online_meetings.recording_url`
- **Notification**: 
  - Email (Laravel Mail)
  - In-app notification
  - Optional: WhatsApp via API
- **Export**: Laravel Excel untuk daftar hadir
