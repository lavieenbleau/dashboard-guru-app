# Entity Relationship Diagram - DashboardGuru System

Dokumen ini menjelaskan Entity Relationship Diagram (ERD) dari sistem DashboardGuru secara komprehensif.

## Daftar Isi

1. [Overview](#overview)
2. [Entitas Utama](#entitas-utama)
3. [Relationships](#relationships)
4. [Data Flow](#data-flow)
5. [Catatan Desain](#catatan-desain)

---

## Overview

Sistem DashboardGuru dibangun dengan arsitektur database yang mendukung:

- **Multi-tenant system** dengan serial/lisensi (Serials + Products)
- **Manajemen pengguna** (Users, Admins, Students)
- **Manajemen pembelajaran** (Lessons, Exercises, Posts)
- **Kelas online** (Online Meetings dengan Jitsi integration)
- **Penilaian otomatis** (Auto-grading dengan Exercise Points)
- **Customer Support** (CS Rooms dengan QnA & Chatbot)
- **Audit & Logging** (Activity Logs, Email Logs, Serial Logs)

Total: **40+ tabel** dengan **60+ foreign key relationships**

---

## Entitas Utama

### 1. User Management Layer

#### `users` (Guru/Pembuat Konten)

| Kolom      | Tipe        | Deskripsi                   |
| ---------- | ----------- | --------------------------- |
| id         | PK          | Primary key                 |
| name       | varchar     | Nama guru                   |
| username   | varchar(UK) | Unique username untuk login |
| password   | varchar     | Password terenkripsi        |
| role       | tinyint     | Role (1=Guru, 2=Admin)      |
| email      | varchar     | Email address               |
| phone      | varchar     | Phone number                |
| img        | varchar     | Photo filename              |
| login_at   | timestamp   | Last login                  |
| created_at | timestamp   | Created time                |
| updated_at | timestamp   | Updated time                |

**Relationships:**

- Owns multiple `Serials` (lisensi produk)
- Creates multiple `Students`
- Creates multiple `Posts` (tugas/materi)
- Schedules multiple `Online Meetings`
- Participates in `Online Meeting Participants`

#### `admins` (Administrator/Content Creator)

| Kolom    | Tipe        | Deskripsi       |
| -------- | ----------- | --------------- |
| id       | PK          | Primary key     |
| name     | varchar     | Admin name      |
| username | varchar(UK) | Unique username |
| password | varchar     | Hashed password |
| role     | tinyint     | Admin role      |
| position | varchar     | Position        |
| phone    | varchar     | Phone           |
| img      | varchar     | Profile image   |
| login_at | timestamp   | Last login      |

**Relationships:**

- Creates `Lesson Items` (materi pembelajaran)
- Creates `Exercise Items` (soal latihan)
- Generates `Admin Activity Logs`
- Manages `CS Rooms` (customer support)

#### `students` (Siswa/Learner)

| Kolom        | Tipe        | Deskripsi              |
| ------------ | ----------- | ---------------------- |
| id           | PK          | Primary key            |
| serial_id    | FK          | Lisensi aktif          |
| user_id      | FK          | Guru pembuat           |
| classroom_id | FK          | Kelas tempat bergabung |
| name         | varchar     | Nama siswa             |
| username     | varchar(UK) | Login username         |
| password     | varchar     | Hashed password        |
| nis          | varchar     | Student ID number      |
| absen_number | int         | Attendance counter     |
| email        | varchar     | Email                  |
| phone        | varchar     | Phone                  |
| photo        | varchar     | Student photo          |
| created_at   | timestamp   | Created time           |

**Relationships:**

- Submits `Exercise Points` (jawaban latihan)
- Submits `Tasks` (tugas)
- Creates `Reports` (laporan/feedback)
- Comments on `Posts` (post_comments)
- Joins `Online Meeting Participants`
- Uses `CS Rooms` (customer support)

---

### 2. Licensing & Product Layer

#### `products` (Paket Produk)

| Kolom          | Tipe    | Deskripsi                          |
| -------------- | ------- | ---------------------------------- |
| id             | PK      | Primary key                        |
| name           | varchar | Product name (e.g., "Kelas 4 K13") |
| grade          | varchar | Grade level                        |
| grade_category | varchar | Category (SD, SMP, SMA)            |
| semester       | varchar | Semester                           |

**Relationships:**

- Contains multiple `Serials` (lisensi aktif)

#### `serials` (Lisensi/License Key)

| Kolom      | Tipe        | Deskripsi                 |
| ---------- | ----------- | ------------------------- |
| id         | PK          | Primary key               |
| user_id    | FK          | Guru pemilik              |
| product_id | FK          | Product yang dilisensikan |
| serial     | varchar(UK) | Serial code unik          |
| paket      | varchar     | Package type (A, B, C)    |
| active     | varchar     | Status aktif (yes/no)     |
| expired_at | timestamp   | Tanggal expire            |
| notif      | enum        | Notification status       |

**Relationships:**

- Belongs to `Users` (guru pemilik)
- References `Products` (jenis produk)
- Activates multiple `Classrooms`
- Licenses multiple `Students`
- Creates `Email Logs` (notifikasi)
- Tracks `Serial Logs` (history)

---

### 3. Classroom & Organization Layer

#### `classrooms` (Kelas)

| Kolom     | Tipe        | Deskripsi                     |
| --------- | ----------- | ----------------------------- |
| id        | PK          | Primary key                   |
| serial_id | FK          | Serial yang mengaktifkan      |
| name      | varchar     | Class name (e.g., "Kelas 4A") |
| grade     | varchar     | Grade level                   |
| code      | varchar(UK) | Unique class code             |

**Relationships:**

- Groups multiple `Students`
- Hosts multiple `Online Meetings`

#### `mapels` (Mata Pelajaran/Subject)

| Kolom | Tipe    | Deskripsi    |
| ----- | ------- | ------------ |
| id    | PK      | Primary key  |
| name  | varchar | Subject name |

**Relationships:**

- Contains multiple `Lessons`
- Defines multiple `Competences`
- Related to multiple `Posts`

---

### 4. Learning Content Layer

#### `lessons` (Pelajaran/Materi)

| Kolom    | Tipe    | Deskripsi         |
| -------- | ------- | ----------------- |
| id       | PK      | Primary key       |
| mapel_id | FK      | Subject           |
| name     | varchar | Lesson title      |
| grade    | varchar | Grade level       |
| semester | tinyint | Semester (1 or 2) |
| category | tinyint | Category type     |

**Relationships:**

- Belongs to `Mapels` (subject)
- Defines `Competences` (learning objectives)
- Contains `Themes` (main topics)
- Contains `Lesson Items` (actual content)
- Includes `Exercises` (practice questions)

#### `competences` (Kompetensi/Learning Objectives)

| Kolom       | Tipe    | Deskripsi       |
| ----------- | ------- | --------------- |
| id          | PK      | Primary key     |
| lesson_id   | FK      | Pelajaran       |
| mapel_id    | FK      | Subject         |
| point       | varchar | Competency code |
| description | text    | Description     |

**Relationships:**

- Required by `Lessons`
- Required by `Exercise Items`

#### `themes` (Tema/Topik Utama)

| Kolom | Tipe    | Deskripsi   |
| ----- | ------- | ----------- |
| id    | PK      | Primary key |
| name  | varchar | Theme name  |

**Relationships:**

- Contains multiple `Subthemes`
- Organizes multiple `Lesson Items`

#### `subthemes` (Subtema)

| Kolom     | Tipe    | Deskripsi       |
| --------- | ------- | --------------- |
| id        | PK      | Primary key     |
| lesson_id | FK      | Pelajaran       |
| theme_id  | FK      | Tema utama      |
| subtheme  | int     | Subtheme number |
| name      | varchar | Subtheme name   |

**Relationships:**

- Belongs to `Themes`
- Subdivides `Lesson Items`

#### `lesson_items` (Item Pelajaran/Konten)

| Kolom       | Tipe | Deskripsi                           |
| ----------- | ---- | ----------------------------------- |
| id          | PK   | Primary key                         |
| lesson_id   | FK   | Pelajaran                           |
| theme_id    | FK   | Tema                                |
| subtheme_id | FK   | Subtema                             |
| admin_id    | FK   | Admin creator                       |
| number      | int  | Sequence number                     |
| title       | text | Item title                          |
| embed       | text | Embedded content (video, link, PDF) |

**Content Types:**

- Video (YouTube embed)
- External links
- File uploads (PDF, DOC, PPT)
- Text descriptions

**Relationships:**

- Created by `Admins`
- Belongs to `Lessons`
- Organized by `Themes` and `Subthemes`

---

### 5. Exercise & Assessment Layer

#### `exercises` (Latihan/Quiz)

| Kolom            | Tipe    | Deskripsi             |
| ---------------- | ------- | --------------------- |
| id               | PK      | Primary key           |
| lesson_id        | FK      | Associated lesson     |
| serial_id        | FK      | Serial untuk tracking |
| exercise_type_id | FK      | Type of exercise      |
| title            | varchar | Exercise title        |
| is_admin         | boolean | Created by admin?     |

**Relationships:**

- Belongs to `Lessons`
- Contains `Exercise Items` (questions)
- Contains `Exercise Types`
- Generates `Exercise Points` (student answers)
- Logs `Quiz Activity Logs`

#### `exercise_types` (Tipe Soal)

| Kolom | Tipe    | Deskripsi   |
| ----- | ------- | ----------- |
| id    | PK      | Primary key |
| kode  | varchar | Type code   |
| name  | varchar | Type name   |

**Tipe Soal yang Didukung:**

1. Pilihan Ganda (Multiple Choice)
2. Essay (Free text)
3. Benar/Salah (True/False)
4. Isian Singkat (Short answer)
5. Matching
6. Multiple response

#### `exercise_models` (Model/Template Soal)

| Kolom | Tipe    | Deskripsi   |
| ----- | ------- | ----------- |
| id    | PK      | Primary key |
| name  | varchar | Model name  |

#### `exercise_items` (Item Soal/Pertanyaan)

| Kolom             | Tipe    | Deskripsi             |
| ----------------- | ------- | --------------------- |
| id                | PK      | Primary key           |
| exercise_id       | FK      | Parent exercise       |
| exercise_type_id  | FK      | Question type         |
| exercise_model_id | FK      | Question template     |
| exercise_number   | int     | Question number       |
| question          | text    | Question text         |
| selection         | text    | Answer choices (JSON) |
| answer            | text    | Correct answer(s)     |
| is_user           | boolean | User-created?         |

**Relationships:**

- Belongs to `Exercises`
- Uses `Exercise Types`
- Uses `Exercise Models`
- Requires `Competences`

#### `exercise_points` (Jawaban/Scoring)

| Kolom            | Tipe    | Deskripsi        |
| ---------------- | ------- | ---------------- |
| id               | PK      | Primary key      |
| serial_id        | FK      | Serial           |
| exercise_id      | FK      | Exercise         |
| student_id       | FK      | Student          |
| answer           | text    | Student's answer |
| exercise_point   | varchar | Score (0-100)    |
| competence_point | text    | Competency score |

**Scoring Logic:**

- Multiple choice: Auto-scored
- True/False: Auto-scored
- Short answer: Auto-scored (keyword matching)
- Essay: Manual-scored (guru)
- Point calculation: Sum of question points

**Relationships:**

- Student `answers` Exercise
- Tracked by `Serial`

#### `quiz_activity_logs` (Monitoring Aktivitas)

| Kolom            | Tipe    | Deskripsi                  |
| ---------------- | ------- | -------------------------- |
| id               | PK      | Primary key                |
| student_id       | FK      | Student                    |
| exercise_id      | FK      | Exercise                   |
| event_type       | varchar | Event (start, submit, etc) |
| duration_seconds | int     | Time spent                 |
| suspicious_flag  | boolean | Cheating suspected?        |
| device_info      | varchar | Device used                |
| ip_address       | varchar | IP address                 |

**Anti-Cheating Features:**

- Track suspicious behavior
- Monitor tab switching
- Log time spent per question
- Record device/IP changes

---

### 6. Task & Assignment Layer

#### `posts` (Postingan/Tugas)

| Kolom       | Tipe        | Deskripsi        |
| ----------- | ----------- | ---------------- |
| id          | PK          | Primary key      |
| serial_id   | FK          | Serial           |
| user_id     | FK          | Guru pembuat     |
| mapel_id    | FK          | Subject          |
| title       | varchar     | Post title       |
| description | text        | Description      |
| slug        | varchar(UK) | URL slug         |
| link        | text        | External link    |
| attachment  | text        | File attachment  |
| embed       | text        | Embedded content |
| due_date    | timestamp   | Deadline tugas   |
| category    | varchar     | Category type    |
| is_task     | boolean     | Is assignment?   |

**Post Types:**

- Materi (is_task=false): Informasi/materi pembelajaran
- Tugas (is_task=true): Assignment dengan deadline
- Pengumuman: News/announcements

**Relationships:**

- Created by `Users` (guru)
- Belongs to `Mapels`
- Contains `Tasks` (submissions)
- Contains `Post Comments` (discussions)

#### `tasks` (Submission Tugas)

| Kolom       | Tipe    | Deskripsi             |
| ----------- | ------- | --------------------- |
| id          | PK      | Primary key           |
| serial_id   | FK      | Serial                |
| post_id     | FK      | Associated post       |
| student_id  | FK      | Student               |
| description | text    | Student's answer/work |
| attachment  | text    | Uploaded file         |
| point       | varchar | Score (0-100)         |

**Relationships:**

- Student `submits` Task
- Task belongs to `Posts` (assignment)

#### `post_comments` (Komentar)

| Kolom      | Tipe        | Deskripsi    |
| ---------- | ----------- | ------------ |
| id         | PK          | Primary key  |
| post_id    | FK          | Post         |
| user_id    | FK          | Guru         |
| student_id | FK          | Student      |
| message    | text        | Comment text |
| code       | varchar(UK) | Unique code  |
| is_user    | boolean     | By guru?     |

**Relationships:**

- Belongs to `Posts`
- Contains `Post Child Comments` (replies)
- Created by Users or Students

#### `post_child_comments` (Balasan Komentar)

| Kolom           | Tipe    | Deskripsi      |
| --------------- | ------- | -------------- |
| id              | PK      | Primary key    |
| post_comment_id | FK      | Parent comment |
| user_id         | FK      | Guru           |
| student_id      | FK      | Student        |
| message         | text    | Reply text     |
| is_user         | boolean | By guru?       |

---

### 7. Online Learning Layer

#### `online_meetings` (Kelas Online/Jitsi Meeting)

| Kolom        | Tipe        | Deskripsi                    |
| ------------ | ----------- | ---------------------------- |
| id           | PK          | Primary key                  |
| serial_id    | FK          | Serial                       |
| classroom_id | FK          | Classroom                    |
| user_id      | FK          | Guru                         |
| title        | varchar     | Meeting title                |
| description  | text        | Meeting description          |
| meeting_code | varchar(UK) | Jitsi room code              |
| meeting_link | text        | Jitsi meeting URL            |
| platform     | varchar     | Platform (Jitsi)             |
| start_time   | datetime    | Start time                   |
| end_time     | datetime    | End time                     |
| status       | enum        | Status (upcoming/live/ended) |

**Relationships:**

- Scheduled by `Users` (guru)
- For `Classrooms`
- Contains `Online Meeting Participants`

#### `online_meeting_participants` (Peserta Kelas Online)

| Kolom             | Tipe     | Deskripsi                |
| ----------------- | -------- | ------------------------ |
| id                | PK       | Primary key              |
| online_meeting_id | FK       | Meeting                  |
| user_id           | FK       | Participant (guru/siswa) |
| role              | enum     | Role (teacher/student)   |
| joined_at         | datetime | Join time                |
| left_at           | datetime | Leave time               |

**Tracking:**

- Attendance recording
- Duration of participation
- Join/leave timestamps

---

### 8. Reporting Layer

#### `reports` (Laporan Siswa)

| Kolom      | Tipe    | Deskripsi        |
| ---------- | ------- | ---------------- |
| id         | PK      | Primary key      |
| serial_id  | FK      | Serial           |
| student_id | FK      | Student          |
| report     | text    | Report content   |
| img        | varchar | Screenshot/image |

**Report Types:**

- Kendala belajar (learning issues)
- Feedback pembelajaran (learning feedback)
- Pertanyaan umum (general questions)
- Progress report

**Relationships:**

- Student `reports`
- Related to `Serials`

---

### 9. Customer Support Layer

#### `question_categories` (Kategori Pertanyaan/FAQ)

| Kolom           | Tipe | Deskripsi               |
| --------------- | ---- | ----------------------- |
| id              | PK   | Primary key             |
| name            | text | Category name           |
| level           | enum | Level (Umum/Siswa/Guru) |
| solution_text   | text | Solution                |
| guide_file      | text | Guide file              |
| guide_video     | text | Video guide             |
| category_status | enum | Active/Inactive         |

#### `cs_rooms` (Chat Room Support)

| Kolom                  | Tipe        | Deskripsi         |
| ---------------------- | ----------- | ----------------- |
| id                     | PK          | Primary key       |
| room_code              | varchar(UK) | Unique room code  |
| question_categories_id | FK          | Category          |
| student_id             | FK          | Student           |
| user_id                | FK          | Guru              |
| admin_id               | FK          | Admin             |
| chat_status            | enum        | QnA/ChatBot/Admin |

**Chat Flow:**

1. Student submits question
2. System tries QnA (knowledge base)
3. If no match, ChatBot responds
4. If ChatBot fails, escalate to Admin
5. Admin provides solution

#### `cs_messages` (Chat Messages)

| Kolom           | Tipe     | Deskripsi                     |
| --------------- | -------- | ----------------------------- |
| id              | PK       | Primary key                   |
| cs_rooms_id     | FK       | Room                          |
| message_sender  | enum     | Sender (Pelapor/Admin/Sistem) |
| message_content | text     | Message                       |
| sent_time       | datetime | Timestamp                     |

#### `cs_files` (File dalam CS Room)

| Kolom     | Tipe    | Deskripsi     |
| --------- | ------- | ------------- |
| id        | PK      | Primary key   |
| room_id   | FK      | Room          |
| file_path | varchar | File location |

---

### 10. Audit & Logging Layer

#### `admin_activity_logs` (Log Aktivitas Admin)

| Kolom       | Tipe    | Deskripsi        |
| ----------- | ------- | ---------------- |
| id          | PK      | Primary key      |
| admin_id    | FK      | Admin            |
| action      | varchar | Action performed |
| model       | varchar | Model affected   |
| data_id     | bigint  | Record ID        |
| description | text    | Details          |
| ip_address  | varchar | IP address       |

**Tracked Actions:**

- Create/Update/Delete operations
- Login/Logout
- File uploads/downloads

#### `email_logs` (Log Email)

| Kolom      | Tipe    | Deskripsi                            |
| ---------- | ------- | ------------------------------------ |
| id         | PK      | Primary key                          |
| serial_id  | FK      | Serial                               |
| email_to   | varchar | Recipient                            |
| subject    | varchar | Email subject                        |
| email_type | enum    | Type (Serial/Peringatan/Kedaluwarsa) |
| status     | enum    | Status (Berhasil/Gagal)              |
| source     | enum    | Source (Otomatis/Login_Admin/Manual) |

**Email Types:**

- Serial activation notification
- License expiration warning (30 days before)
- License expired notification

#### `serial_logs` (Log Serial)

| Kolom     | Tipe    | Deskripsi                |
| --------- | ------- | ------------------------ |
| id        | PK      | Primary key              |
| serial_id | FK      | Serial                   |
| active    | varchar | Active status            |
| status    | enum    | Status (Baru/Perpanjang) |

---

## Relationships

### Primary Relationship Patterns

#### 1. One-to-Many (1:M)

```
Users ──── 1:M ──── Serials
         (owns)

Serials ──── 1:M ──── Students
          (licenses)

Lessons ──── 1:M ──── Exercises
          (contains)

Exercises ──── 1:M ──── Exercise Items
            (contains)

Posts ──── 1:M ──── Tasks
       (assigns)
```

#### 2. Many-to-One (M:1)

```
Students ──── M:1 ──── Classrooms
           (belongs to)

Exercise Items ──── M:1 ──── Exercise Types
               (uses)

Lesson Items ──── M:1 ──── Lessons
            (belongs to)
```

#### 3. Many-to-Many (M:M) - Via Pivot Table

```
Serials ──M:M── Exercises
        (share_exercises)
```

#### 4. Cascading Relationships

- Delete Serial → Delete Classrooms, Students, Online Meetings, Posts, Exercises, Tasks, Reports
- Delete Classroom → Delete Students
- Delete Lesson → Delete Exercises, Themes, Subthemes, Lesson Items
- Delete Exercise → Delete Exercise Items, Exercise Points
- Delete Post → Delete Tasks, Post Comments

---

## Data Flow

### 1. Learning Path Flow

```
Product
   ↓
Serial (License activation)
   ↓
Classroom (Student grouping)
   ↓
Student Registration
   ↓
Mapel → Lessons → Themes/Subthemes → Lesson Items
                                ↓
                          Student studies
   ↓
Exercises → Exercise Items → Exercise Points
        ↓
   Student takes quiz → Auto-grading
        ↓
   Results shown to student & teacher
```

### 2. Assignment & Task Flow

```
Teacher creates Post (is_task=true)
   ↓
Set deadline + attachment
   ↓
Publish to Classroom
   ↓
Notification to Students
   ↓
Students submit Tasks
   ↓
Teacher review & grade
   ↓
Student see results
```

### 3. Online Class Flow

```
Teacher schedules Online Meeting
   ↓
Set time + room name
   ↓
Publish to Classroom
   ↓
Notification to Students
   ↓
Start meeting (Jitsi)
   ↓
Students join
   ↓
Record Online Meeting Participants
   ↓
End meeting
   ↓
Attendance tracked
```

### 4. Customer Support Flow

```
Student/Teacher submits question
   ↓
Create CS Room
   ↓
Try QnA (Question Categories)
   ↓
If no answer → ChatBot response
   ↓
If ChatBot fails → Escalate to Admin
   ↓
Admin provides solution (CS Messages)
   ↓
Log resolved case (CS Logs)
```

---

## Catatan Desain

### 1. Soft Deletes

Tabel-tabel berikut menggunakan soft deletes (`deleted_at` column):

- exercises
- lesson_items (implisit melalui cascade)
- posts
- tasks
- students

Keuntungan:

- Data tidak benar-benar terhapus
- Dapat di-restore jika diperlukan
- Audit trail tetap ada

### 2. Unique Keys

| Tabel           | Kolom        |
| --------------- | ------------ |
| users           | username     |
| admins          | username     |
| students        | username     |
| serials         | serial       |
| classrooms      | code         |
| posts           | slug         |
| online_meetings | meeting_code |
| cs_rooms        | room_code    |
| post_comments   | code         |

### 3. Indexes untuk Performance

Primary indexes di:

- Foreign keys (untuk JOIN operations)
- Filtering columns (status, is_task, is_admin)
- Sorting columns (created_at, start_time)
- Search columns (username, slug, title)

### 4. Enum Fields

Digunakan untuk fields dengan nilai terbatas:

- `users.role`: 1=Guru, 2=Admin, 3=SuperAdmin
- `students.role`: Similar
- `exercise_points.status`: Not started/In progress/Submitted
- `online_meetings.status`: upcoming/live/ended/cancelled
- `cs_rooms.chat_status`: QnA/ChatBot/Admin
- Email status: Berhasil/Gagal
- Serial status: Baru/Perpanjang

### 5. Foreign Key Constraints

**ON DELETE CASCADE:** Untuk data dependent

- Classroom → Students
- Lesson → Exercises
- Exercise → Exercise Items
- Serial → All child records

**ON DELETE SET NULL:** Untuk data optional

- User (creator) pada beberapa tabel
- Optional foreign keys

### 6. Data Integrity Rules

1. **Serial Activation:**
   - Serial harus aktif untuk classroom/student bisa digunakan
   - Expired serial akan meng-set notif="Kedaluwarsa"

2. **Student Registration:**
   - Harus terdaftar di classroom
   - Classroom harus linked ke active serial
   - Unique username per classroom

3. **Exercise Grading:**
   - Auto-grading untuk multiple choice, true/false, short answer
   - Manual grading untuk essay
   - Point validation (0-100)

4. **Meeting Participation:**
   - Unique constraint (online_meeting_id, user_id)
   - Prevent duplicate participation records
   - Track join/leave times

---

## Kesimpulan

ERD DashboardGuru didesain untuk mendukung:

✅ **Multi-tenant architecture** dengan serial/lisensi
✅ **Flexible learning content** dengan multiple hierarchy levels
✅ **Comprehensive assessment** dengan auto-grading
✅ **Real-time interaction** dengan online meetings
✅ **Complete audit trail** untuk compliance & analytics
✅ **Data integrity** dengan proper constraints & validations

Sistem ini siap untuk scale ke 1000+ pengguna dengan performa optimal.
