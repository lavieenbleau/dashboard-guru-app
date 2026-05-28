# ERD Index - DashboardGuru System

Dokumen ini berisi index dari semua Entity Relationship Diagrams yang tersedia untuk sistem DashboardGuru.

## File-file yang Tersedia

### 1. **ERD_DOCUMENTATION.md** (MAIN DOCUMENTATION)

📄 **Path:** `/docs/ERD_DOCUMENTATION.md`

Dokumentasi lengkap dan komprehensif tentang ERD sistem:

- Overview arsitektur database
- Penjelasan detail setiap entitas (40+ tabel)
- Deskripsi setiap kolom & relationships
- Data flow untuk setiap proses
- Catatan desain & best practices
- Rules & constraints

**Kapan membaca:**

- Memahami struktur database secara mendalam
- Melakukan development/modification
- Dokumentasi untuk new team members
- Reference untuk troubleshooting

---

### 2. **erd-current-system.puml** (DETAILED ERD)

📊 **Path:** `/docs/uml/erd-current-system.puml`

PlantUML format ERD yang menampilkan:

- Semua 40+ tabel
- Semua relationships (60+ foreign keys)
- Setiap kolom dengan tipe data
- Primary keys (PK) dan Foreign keys (FK)
- Unique keys (UK)
- Detailed attributes

**Cara membuka:**

- VS Code: Install PlantUML extension (jgraph.plantuml)
- Online: http://www.plantuml.com/plantuml/
- Command line: `plantuml erd-current-system.puml -Tpng`

**Kegunaan:**

- Complete technical reference
- Database schema validation
- Query optimization analysis
- Team discussion & design review

---

### 3. **erd-simplified.puml** (SIMPLIFIED ERD)

📊 **Path:** `/docs/uml/erd-simplified.puml`

PlantUML format yang menampilkan hanya **core entities**:

- 12 main tables
- Key relationships saja
- Fokus ke learning flow

**Tabel yang ditampilkan:**

- Users
- Serials
- Classrooms
- Students
- Lessons
- Exercises
- Exercise Items
- Exercise Points
- Posts
- Tasks
- Online Meetings
- Reports

**Kegunaan:**

- High-level system overview
- Presentasi kepada stakeholders
- Onboarding new developers
- Quick reference guide

---

### 4. **Mermaid ERD** (INTERACTIVE)

🎨 **Format:** Mermaid Diagram (dalam chat)

Interactive ERD yang ditampilkan dalam response sebelumnya dengan:

- All entities color-coded
- Click-friendly relationships
- Readable format

**Kegunaan:**

- Quick visualization
- Easy to share
- Collaborative discussions

---

## Entity Categories

### User Management (3 tables)

- `users` - Guru & educators
- `admins` - Administrators
- `students` - Learners

### Licensing & Organization (4 tables)

- `products` - Product/package definitions
- `serials` - License keys
- `classrooms` - Class grouping
- `mapels` - Subjects

### Learning Content (7 tables)

- `lessons` - Main lessons/units
- `competences` - Learning objectives
- `themes` - Main topics
- `subthemes` - Subtopics
- `lesson_items` - Content items (video, link, file, text)

### Assessment & Exercises (8 tables)

- `exercises` - Quiz/practice sets
- `exercise_types` - Question types (MCQ, Essay, etc)
- `exercise_models` - Question templates
- `exercise_items` - Individual questions
- `exercise_points` - Student answers & scores
- `quiz_activity_logs` - Anti-cheating logs
- `share_exercises` - Exercise distribution (pivot)

### Tasks & Assignments (4 tables)

- `posts` - Teacher posts (materials & assignments)
- `tasks` - Student submissions
- `post_comments` - Discussions
- `post_child_comments` - Nested replies

### Online Learning (2 tables)

- `online_meetings` - Jitsi meetings
- `online_meeting_participants` - Attendance

### Reporting (1 table)

- `reports` - Student feedback & issues

### Customer Support (4 tables)

- `question_categories` - FAQ categories
- `cs_rooms` - Support chat rooms
- `cs_messages` - Chat messages
- `cs_files` - Attached files

### Audit & Logging (5 tables)

- `admin_activity_logs` - Admin actions
- `email_logs` - Email tracking
- `serial_logs` - License history
- Other system tables (cache, sessions, migrations)

---

## Key Concepts

### 1. Multi-Tenant Architecture

Setiap serial (lisensi) adalah tenant terpisah:

```
User (Guru)
    ↓
Serials (Multiple licenses)
    ├─ Serial 1 (Kelas 4 K13)
    │   ├─ Classrooms
    │   ├─ Students
    │   ├─ Lessons
    │   └─ Exercises
    ├─ Serial 2 (Kelas 4 Merdeka)
    │   └─ [Separate data]
    └─ Serial 3 (SMP Kelas 7)
        └─ [Separate data]
```

### 2. Learning Path

```
User creates Lesson
    ↓
Organizes with Themes/Subthemes
    ↓
Adds Learning Items (videos, links, files)
    ↓
Creates Exercises with Questions
    ↓
Students take Exercise
    ↓
Auto-grading for objective questions
    ↓
Results displayed immediately
```

### 3. Task Management

```
User creates Post (is_task=true)
    ↓
Students submit Tasks
    ↓
User grades (manual)
    ↓
Results in Tasks table
    ↓
Students view feedback via Post Comments
```

### 4. Online Classes

```
Jitsi Integration
    ↓
Create Online Meeting with room code
    ↓
Students join via URL
    ↓
Record participants
    ↓
Track attendance
```

---

## Relationship Types

### One-to-Many (1:M)

```
Users ──1:M── Serials
Serials ──1:M── Students
Lessons ──1:M── Exercises
Exercises ──1:M── Exercise Items
```

### Many-to-One (M:1)

```
Students ──M:1── Classrooms
Exercise Items ──M:1── Exercises
```

### Many-to-Many (M:M) via Pivot

```
Serials ──M:M── Exercises
        (via share_exercises table)
```

---

## Data Integrity

### Cascade Delete

Menghapus parent akan menghapus children:

- Delete Serial → Delete Classrooms, Students, Exercises, etc.
- Delete Lesson → Delete Exercises, Themes
- Delete Exercise → Delete Exercise Items & Scores

### Soft Delete

Beberapa tabel menggunakan soft delete (deleted_at):

- exercises
- posts
- tasks
- students

Keuntungan:

- Data dapat di-restore
- Audit trail terjaga
- No permanent data loss

### Unique Constraints

Mencegah duplikasi data kritis:

- users.username (unique)
- students.username (unique per classroom)
- serials.serial (unique)
- posts.slug (unique)
- online_meetings.meeting_code (unique)

---

## Query Examples

### Find all students in a classroom

```sql
SELECT s.* FROM students s
JOIN classrooms c ON s.classroom_id = c.id
WHERE c.id = ?;
```

### Get all exercises for a lesson

```sql
SELECT e.*, et.name as exercise_type
FROM exercises e
JOIN exercise_types et ON e.exercise_type_id = et.id
WHERE e.lesson_id = ?;
```

### Check student's exercise scores

```sql
SELECT ep.*, e.title, e.exercise_type_id
FROM exercise_points ep
JOIN exercises e ON ep.exercise_id = e.id
WHERE ep.student_id = ?
ORDER BY ep.created_at DESC;
```

### Get active serials (not expired)

```sql
SELECT * FROM serials
WHERE active = 'yes'
AND (expired_at IS NULL OR expired_at > NOW());
```

### Find tasks pending grading

```sql
SELECT t.* FROM tasks t
WHERE t.point IS NULL
AND t.created_at < DATE_SUB(NOW(), INTERVAL 1 DAY);
```

---

## Performance Considerations

### Important Indexes

- Foreign key columns (for JOINs)
- Filtering: `status`, `is_task`, `active`
- Sorting: `created_at`, `start_time`
- Search: `username`, `slug`, `title`

### Query Optimization

- Use indexes for WHERE clauses
- Avoid SELECT \* (select specific columns)
- Use LIMIT for pagination
- Consider query caching for readonly data

### Large Tables

- `exercise_points` - Can grow to millions of records
- `quiz_activity_logs` - Consider partitioning by date
- `post_comments` - Denormalize comment count if needed

---

## Future Enhancements

### Potential New Tables

1. `notifications` - Push/email notifications
2. `user_preferences` - User settings
3. `payment_history` - License billing
4. `certificates` - Student achievements
5. `gamification` - Points, badges, leaderboards
6. `api_tokens` - API authentication
7. `audit_logs` - Complete audit trail

### Design Improvements

1. Separate read/write databases (CQRS)
2. Add caching layer (Redis)
3. Message queue for async operations
4. Event sourcing for critical operations

---

## Links

- 📄 [Full ERD Documentation](./ERD_DOCUMENTATION.md)
- 📊 [Detailed PlantUML Diagram](./uml/erd-current-system.puml)
- 📊 [Simplified PlantUML Diagram](./uml/erd-simplified.puml)
- 📋 [Database Schema](./DATABASE_SCHEMA.md)
- 📚 [Business Process Documentation](./BUSINESS_PROCESS.md)

---

**Last Updated:** May 14, 2026
**Version:** 1.0
**Status:** Current System (Post password_text removal)
