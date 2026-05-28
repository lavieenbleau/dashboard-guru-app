# AI Question Generator System - PlantUML Diagrams

Dokumentasi lengkap diagram sistem untuk fitur **AI Question Generator dengan Dual-Source Material** di DashboardGuru.

---

## 📋 Daftar Diagram

### 1. **10-ai-question-generator-system.puml** 
**Tipe:** Component Diagram (Sistem Keseluruhan)

**Deskripsi:**
Menampilkan arsitektur keseluruhan sistem AI generator dengan fokus pada:
- Aktor (Guru, Admin, OpenAI API)
- Database (Posts, Lessons, Exercise)
- Controller & Service Layer
- Alur data dual-source (Post dan Lesson)
- Caching dan queue system

**Komponen Utama:**
```
Guru → SoalController → readMaterials()
                      → generateQuestions()
                      → saveSoal()

Database:
├── Posts (teacher uploads, serial_id)
├── Lessons (admin materials, category: materi)
└── Exercise (results)

Service:
├── Dual-Source Material Processor
├── OpenAI Chat Client
└── Response Parser
```

**Use Case:**
Untuk pemahaman holistik tentang bagaimana sistem bekerja end-to-end.

---

### 2. **10b-ai-generator-detailed-flow.puml**
**Tipe:** Activity Diagram (Alur Lengkap)

**Deskripsi:**
Diagram alur step-by-step yang detail, mirip dengan diagram existing (04-materi, 05-soal). Menunjukkan:
- Interaksi pengguna (Guru memilih materi)
- Backend processing (readMaterials, token parsing)
- API integration (OpenAI call)
- Error handling
- Review & save process

**Alur Utama:**
```
1. Guru akses form Generate Soal
2. Pilih sumber materi:
   - Post Guru (format: post:ID)
   - Admin Material (format: lesson:ID)
3. Backend query material dari sumber yang dipilih
4. Build prompt dan send ke OpenAI
5. Parse response & show preview
6. Guru review & save ke bank soal
```

**Use Case:**
Untuk dokumentasi proses bisnis dan training pengguna.

---

### 3. **10c-ai-dual-source-architecture.puml**
**Tipe:** Component/Architecture Diagram (Teknis Mendalam)

**Deskripsi:**
Menampilkan arsitektur teknis dengan detail implementasi:
- Token resolver (post: vs lesson:)
- Material fetcher untuk kedua sumber
- Database queries
- Cache strategy
- OpenAI configuration
- Persistence layer

**Highlight Fitur Dual-Source:**
```
Token Parser:
├── post:ID  → Post Fetcher → posts table (check serial_id)
└── lesson:ID → Lesson Fetcher → lessons table (check category)

Cache Manager:
├── Key: ai_material_{token}
└── TTL: 3600 detik (1 jam)

Queries:
├── Post: SELECT * FROM posts WHERE id=? AND serial_id=?
└── Lesson: SELECT * FROM lessons WHERE id=? AND category='materi'
```

**Use Case:**
Untuk developer/technical review dan architectural discussion.

---

### 4. **10d-ai-generator-sequence.puml**
**Tipe:** Sequence Diagram (Interaksi Komponen)

**Deskripsi:**
Menampilkan timeline interaksi detail antara komponen-komponen:
- HTTP request/response flow
- Database queries
- OpenAI API integration
- Caching logic
- Error scenarios
- Final persistence

**Alur Interaksi:**
```
Guru → Frontend → SoalController
       ↓
    TokenParser (post:312 / lesson:156)
       ↓
    Database (ambil material)
       ↓
    Cache Manager (cek/simpan)
       ↓
    OpenAIService (build prompt)
       ↓
    OpenAI API (generate)
       ↓
    ResponseValidator
       ↓
    Frontend (preview)
       ↓
    Exercise Storage (save)
```

**Use Case:**
Untuk debugging, tracing alur eksekusi, dan troubleshooting.

---

## 🔑 Fitur Dual-Source Unggulan

### Token Format
Sistem menggunakan token dengan prefix untuk membedakan sumber material:

| Token | Sumber | Query | Keamanan |
|-------|--------|-------|----------|
| `post:312` | Post guru | `WHERE id=312 AND serial_id={guru}` | Guru hanya akses post sendiri |
| `lesson:156` | Lesson admin | `WHERE id=156 AND category='materi'` | Semua guru akses material admin |

### Keuntungan Dual-Source:
✅ Guru bisa generate soal dari upload personal mereka  
✅ Guru bisa generate soal dari materi kurikulum admin  
✅ Tidak ada bentrok ID antara dua sumber  
✅ Fleksibilitas maksimal untuk pembelajaran  

---

## 📌 Keterkaitan Diagram

```
Diagram 1 (Component) - OVERVIEW
    ↓
Diagram 2 (Activity) - USER FLOW
    ↓
Diagram 3 (Architecture) - TECHNICAL DETAIL
    ↓
Diagram 4 (Sequence) - INTERACTION DETAIL
```

**Rekomendasi Penggunaan:**
- **Stakeholder/Manager:** Diagram 1 + 2
- **Developer Baru:** Diagram 2 → 3 → 4
- **DevOps/Monitoring:** Diagram 3 + 4
- **QA/Tester:** Diagram 2 + 4
- **Code Review:** Diagram 3 + 4

---

## 🔧 Technical Specifications

### OpenAI Integration
```
Model: GPT-4 / GPT-3.5
Max Tokens: 2048
Temperature: 0.7
Rate Limiting: Configured via .env
```

### Caching Strategy
```
Driver: Laravel Cache (Redis/File)
Key Prefix: ai_material_
TTL: 3600 seconds (1 hour)
Purpose: Reduce DB queries & API costs
```

### Database Tables
```
posts:
- id, serial_id, content, created_at

lessons:
- id, category, content, created_at

exercises:
- id, serial_id, exercise_type_id, is_admin

exercise_items:
- id, exercise_id, question, answers
```

---

## 📚 Related Files

| File | Deskripsi |
|------|-----------|
| `app/Http/Controllers/Guru/SoalController.php` | Main controller |
| `app/Services/OpenAIService.php` | AI integration service |
| `app/Models/Post.php` | Teacher material model |
| `app/Models/Lesson.php` | Admin material model |
| `app/Models/Exercise.php` | Exercise bank model |
| `docs/AI_QUESTION_GENERATOR.md` | User guide |

---

## 🚀 Implementasi Notes

1. **Material Validation:**
   - Check material ID exists
   - Verify access permissions
   - Sanitize content before sending to AI

2. **Error Handling:**
   - API rate limit exceeded
   - Invalid material token
   - Malformed AI response
   - Cache failures

3. **Performance Optimization:**
   - Cache material content (TTL: 1 hour)
   - Batch insert exercise items
   - Queue long-running API calls
   - Configure rate limiting

4. **Security:**
   - Verify serial_id ownership for posts
   - Validate token format
   - Sanitize AI output
   - Log all API calls

---

**Last Updated:** April 2026  
**Author:** DashboardGuru Development Team  
**Status:** ✅ Production Ready
