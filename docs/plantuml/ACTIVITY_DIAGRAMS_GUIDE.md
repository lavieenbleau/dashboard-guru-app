# Activity Diagrams - Extended Suite

Dokumentasi untuk 4 activity diagrams yang menunjukkan user processes di AI Question Generator system.

---

## 📋 Daftar Activity Diagrams

### 1. **10b - Generate Soal dengan AI** (Guru Workflow)
**File:** `10b-ai-generator-detailed-flow.puml`  
**Audience:** Guru, QA, Business Analyst

**Alur:**
```
Akses form → Pilih materi (post:ID / lesson:ID)
→ Input preferensi → OpenAI generate
→ Preview hasil → Review & edit
→ Save ke bank soal
```

**Decision Points:**
- ✓ Material valid?
- ✓ Input lengkap?
- ✓ API success?
- ✓ JSON valid?
- ✓ Questions valid?

**User Actions:**
- Pilih sumber materi (Post atau Lesson)
- Configure: jumlah soal, tingkat kesulitan, tipe soal
- Review pertanyaan
- Edit jika perlu
- Save atau regenerate

**Key Feature:**
- Dual-source material: `post:ID` vs `lesson:ID`
- Caching untuk efficiency
- Error handling & retry options

---

### 2. **11 - Admin Upload & Manage Material** (Admin Workflow)
**File:** `11-admin-material-upload-activity.puml`  
**Audience:** Admin, System Manager

**Alur:**
```
Login → Pilih kategori materi → Upload baru / Edit / Delete
→ Material tersimpan & siap untuk Guru
```

**User Actions:**

| Aksi | Deskripsi |
|------|-----------|
| **Upload Baru** | Upload file PDF/docx atau text description untuk kurikulum |
| **Edit** | Update judul, deskripsi, file |
| **Delete** | Hapus materi (dengan warning jika sudah digunakan) |
| **Publish/Archive** | Toggle visibility status |

**Validasi:**
- File size: < 50MB
- Format: PDF, DOCX, text
- Data completeness
- Duplicate check

**Token Generation:**
- Otomatis generate `lesson:{id}` format
- Siap untuk digunakan Guru di AI Generator

**Safety Features:**
- Warning jika materi sudah linked ke Exercise
- Soft delete atau cascade handling
- Version history (optional)

---

### 3. **12 - Student Answer Questions** (Siswa Workflow)
**File:** `12-student-answer-activity.puml`  
**Audience:** Siswa, QA, Teacher Analytics

**Alur:**
```
Login → Lihat Exercise → Preview soal → Mulai kerjakan
→ Jawab setiap pertanyaan → Submit
→ Auto-evaluate & tampilkan hasil
```

**Skenario Pertanyaan:**

| Tipe Soal | Flow |
|-----------|------|
| **MCQ** | Pilih dari 4-5 opsi |
| **Essay** | Ketik jawaban text |
| **True/False** | Pilih Benar atau Salah |

**Navigation:**
- ✓ Next question
- ✓ Previous question (jika diizinkan)
- ✓ Save & continue later

**Submission Flow:**
1. Completeness check: semua pertanyaan terjawab?
2. Show summary sebelum submit
3. Final confirmation
4. Backend evaluation:
   - Compare jawaban vs correct answer
   - Calculate score per item
   - Total score computation
5. Generate report dengan detailed feedback

**Analytics Generated:**
- Total score
- Persentase
- Per-item analysis (kesulitan, discrimination)
- Comparison dengan rata-rata kelas
- Detailed feedback untuk siswa

**Key Features:**
- Timer countdown
- Progress tracker
- Auto-save
- Review sebelum submit
- Instant result display

---

### 4. **13 - Teacher Share & Edit Questions** (Guru Workflow - Post-Generation)
**File:** `13-teacher-share-edit-activity.puml`  
**Audience:** Guru, QA

**Alur:**
```
Buka Bank Soal → Pilih Exercise → Share/Edit/Duplicate/Analytics
```

**User Actions:**

| Aksi | Deskripsi |
|------|-----------|
| **Share ke Kelas** | Assign soal ke 1+ kelas dengan deadline |
| **Edit Pertanyaan** | Ubah pertanyaan, jawaban, bobot |
| **Duplicate** | Salin soal untuk reuse |
| **View Analytics** | Lihat hasil siswa & item analysis |

### Share to Class
```
Pilih Exercise → Pilih kelas → Set:
- Deadline
- Durasi pengerjaan
- Passing score
- Attempt limit

→ Notifikasi siswa → Assignment active
```

### Edit Questions
```
Pilih Exercise → Edit ExerciseItem:
- Pertanyaan text
- Gambar/media
- Jawaban & opsi
- Bobot nilai

→ Validasi bobot = 100%
→ If shared: notify siswa
```

### Duplicate
```
Pilih Exercise → Clone:
- Copy semua items
- Copy jawaban & opsi
- New ID + suffix
→ Ready to edit/share
```

### View Analytics
```
Show:
- Total siswa / selesai / pending
- Average score & passing rate
- Per-item analysis:
  * Difficulty level
  * Discrimination index
  * Efficacy metrics
- Export option: PDF/Excel
```

**Safety Features:**
- Confirm before edit (jika shared)
- Notify students on changes
- Maintain edit history
- Lock after submission (optional)

---

## 🎯 Complete User Journey

```
ADMIN:
├─ Upload Material (11)
│  └─ Material → token: lesson:ID

GURU:
├─ Generate Soal (10b)
│  ├─ Select Material (post:ID / lesson:ID)
│  └─ Save → Exercise bank
│
├─ Share & Edit (13)
│  ├─ Share ke Classroom
│  ├─ Edit questions
│  └─ View student results

SISWA:
└─ Answer Questions (12)
   ├─ Kerjakan soal
   ├─ Submit jawaban
   └─ View hasil & feedback
```

---

## 📊 Activity Diagram Comparison

| Aspek | 10b (Generate) | 11 (Admin) | 12 (Student) | 13 (Share/Edit) |
|-------|---|---|---|---|
| **Aktor** | Guru | Admin | Siswa | Guru |
| **Main Activity** | Generate questions | Manage material | Answer questions | Share & edit |
| **Duration** | ~1-2 menit | ~5-10 menit | ~30-60 menit | ~5-10 menit |
| **Critical Decisions** | Material valid? Token OK? | File valid? Delete confirm? | Format handling? Submit ready? | Edit impact? |
| **System Processing** | OpenAI API call | File storage | Evaluation & scoring | Notification |
| **Outcome** | Questions saved | Material indexed | Answers evaluated | Assignment active |

---

## 🔄 Interrelated Flows

```
Admin Upload (11)
    ↓
    Creates lesson:ID material
    ↓
Guru Generate (10b)
    ↓
    Selects lesson:ID material
    Creates Exercise + ExerciseItems
    ↓
Guru Share (13)
    ↓
    Assigns Exercise to Classroom
    ↓
Siswa Answer (12)
    ↓
    Submits jawaban
    Auto-evaluated
    ↓
Back to Guru (13)
    ↓
    View analytics & results
```

---

## 🔐 Security & Validation Points

**Diagram 11 (Admin):**
- File size validation
- Format validation
- Duplicate check
- Delete cascade handling

**Diagram 10b (Guru Generate):**
- Material ownership/access check
- Token format validation
- OpenAI rate limiting
- Response validation

**Diagram 12 (Siswa Answer):**
- Authentication check
- Exercise access permission
- Attempt limit check
- Answer validation

**Diagram 13 (Guru Share/Edit):**
- Exercise ownership check
- Permission check per classroom
- Data validation before update
- Cascade impact analysis

---

## 📝 Related Documentation

- Diagram 10b: [10b-ai-generator-detailed-flow.puml]
- Diagram 11: [11-admin-material-upload-activity.puml]
- Diagram 12: [12-student-answer-activity.puml]
- Diagram 13: [13-teacher-share-edit-activity.puml]

Complete Guide: [AI_GENERATOR_DIAGRAMS_COMPLETE_GUIDE.md]

---

## 🎓 Usage Recommendations

| Role | Primary Diagrams |
|------|-----------------|
| **Guru** | 10b, 13 |
| **Admin** | 11 |
| **Siswa** | 12 |
| **QA/Tester** | 10b, 11, 12, 13 (all) |
| **Product Manager** | 10b, 11, 12, 13 (all) |
| **Developer** | 10b, 11, 12, 13 + architecture (10c, 10d, 10f) |

---

**Status:** ✅ Complete Activity Diagram Suite  
**Last Updated:** May 2026  
**Version:** 1.0
