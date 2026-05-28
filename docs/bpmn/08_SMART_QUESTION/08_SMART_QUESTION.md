# BPMN: Smart Question Generation Feature

## 📋 Daftar Isi
- [Ringkasan Proses](#ringkasan-proses)
- [Aktor dan Sistem](#aktor-dan-sistem)
- [Alur Proses Detil](#alur-proses-detil)
- [Keputusan Bisnis](#keputusan-bisnis)
- [Integrasi Sistem](#integrasi-sistem)
- [Data Flow](#data-flow)

---

## 🎯 Ringkasan Proses

Fitur **Smart Question** adalah sistem otomatis yang memungkinkan guru untuk membuat soal berkualitas tinggi menggunakan AI. Sistem ini:

1. **Menerima input** dari guru berupa sumber materi, tipe soal, dan tingkat kesulitan
2. **Memproses konten** materi dari berbagai sumber (Post guru atau Lesson admin)
3. **Memanggil API AI** (OpenAI/OpenRouter) untuk generate soal otomatis
4. **Validasi dan parsing** response dari AI
5. **Menyimpan ke database** dan membagikan ke siswa

---

## 👥 Aktor dan Sistem

### Aktor Utama:
- **Guru**: Inisiator yang membuat soal baru
- **Sistem**: Aplikasi Dashboard Guru
- **AI API**: Layanan eksternal (OpenAI atau OpenRouter)
- **Database**: Penyimpanan soal (exercises table) dan sharing info (share_exercises pivot)

---

## 🔄 Alur Proses Detil

### **1. Tahap Inisialisasi**
```
Guru masuk ke Menu "Generate Soal"
     ↓
Pilih Sumber Materi
```

**Keputusan:** Sumber materi dari mana?

### **2. Tahap Baca Materi**

#### **Path A: Dari Post (Materi Guru)**
```
✓ Baca materi dari table `posts`
✓ Ambil konten berdasarkan post_id
✓ Filter konten aktif/published
```

#### **Path B: Dari Lesson (Materi Admin)**
```
✓ Baca materi dari table `lessons`
✓ Ambil lesson_items dengan konten detail
✓ Gabungkan semua items dalam lesson
```

**Hasil:** Konten materi siap diproses

### **3. Tahap Konfigurasi Soal**

**Keputusan 1: Tipe Soal?**
- **Multiple Choice** - Pilihan ganda dengan 4-5 opsi
- **Essay** - Soal uraian terbuka
- **Fill the Blank** - Soal melengkapi kalimat/angka

**Keputusan 2: Tingkat Kesulitan?**
- **Mudah (Easy)** - Level HOTS 1-2
- **Sedang (Medium)** - Level HOTS 2-3
- **Sulit (Hard)** - Level HOTS 3-4

### **4. Tahap AI Generation**

**Request ke AI API:**
```json
{
  "model": "gpt-3.5-turbo atau model OpenRouter lain",
  "messages": [
    {
      "role": "system",
      "content": "Kamu adalah pengajar profesional yang membuat soal berkualitas..."
    },
    {
      "role": "user",
      "content": "Buat soal [tipe] dengan tingkat kesulitan [level] berdasarkan materi: [content]"
    }
  ],
  "temperature": 0.7,
  "max_tokens": 500
}
```

**Validasi Response:**
- ✅ Response valid JSON
- ✅ Mengandung field yang diperlukan
- ✅ Format sesuai tipe soal
- ❌ Jika invalid → Retry (max 3 kali)

### **5. Tahap Parsing & Penyimpanan**

```
Parse Response AI
     ↓
Mapping ke struktur Exercise:
  - question: text soal
  - type: mc/essay/fill_blank
  - options: [opsi-opsi] (jika MC)
  - correct_answer: jawaban benar
  - explanation: penjelasan
  - difficulty_level: easy/medium/hard
  - created_by: guru_id
     ↓
Simpan ke table `exercises`
```

### **6. Tahap Review & Validasi**

```
Preview Soal di UI
     ↓
Guru Review:
  - Apakah soal jelas?
  - Apakah jawaban benar?
  - Apakah sesuai materi?
```

**Keputusan: Terima Soal?**
- ✅ **Ya** → Lanjut ke Publikasi
- ❌ **Tidak** → Generate Ulang (kembali ke Step 4)

### **7. Tahap Publikasi & Sharing**

```
Publikasi Soal ke Kelas/Siswa
     ↓
Update share_exercises Pivot Table:
  - exercise_id: soal yang dibuat
  - user_id/student_id: siswa yang bisa lihat
  - permission: view/answer
     ↓
Soal Berhasil Dibuat & Dipublikasikan ✅
```

---

## 🔑 Keputusan Bisnis

| Keputusan | Opsi | Dampak |
|-----------|------|--------|
| **Sumber Materi** | Post vs Lesson | Menentukan konten materi yang digunakan |
| **Tipe Soal** | MC / Essay / Fill Blank | Menentukan format soal yang dihasilkan |
| **Tingkat Kesulitan** | Easy / Medium / Hard | Menyesuaikan kompleksitas soal dengan level siswa |
| **Validasi AI** | Valid / Retry | Memastikan kualitas soal sebelum disimpan |
| **Review Guru** | Accept / Regenerate | Guru punya kontrol akhir atas soal |

---

## 🔌 Integrasi Sistem

### **1. Integrasi Database**

**Tables yang terlibat:**
```sql
-- Master soal
exercises (id, question, type, options, correct_answer, explanation, difficulty_level, created_by, ...)

-- Master materi
posts (id, content, title, teacher_id, ...)
lessons (id, title, content, ...)
lesson_items (id, lesson_id, content, ...)

-- Sharing/Distribution
share_exercises (id, exercise_id, student_id, permission, shared_at)
```

### **2. Integrasi API AI**

**Supported Providers:**
- **OpenAI** - GPT-3.5-turbo, GPT-4
- **OpenRouter** - Berbagai model (Claude, Mistral, Llama, dll)

**API Endpoint:**
```
POST https://api.openai.com/v1/chat/completions
atau
POST https://openrouter.ai/api/v1/chat/completions
```

**Rate Limiting:**
- Handle retry dengan exponential backoff
- Max retry: 3 kali per request
- Timeout: 30 detik per request

### **3. Integrasi Frontend (Inertia React)**

**Components:**
- `SmartQuestionForm.jsx` - Input form
- `QuestionTypeSelector.jsx` - Pilih tipe soal
- `DifficultySelector.jsx` - Pilih tingkat kesulitan
- `MaterialSourceSelector.jsx` - Pilih sumber materi
- `QuestionPreview.jsx` - Preview hasil generate
- `LoadingSpinner.jsx` - Loading indicator saat AI process

---

## 📊 Data Flow

### **Input Data:**
```
Guru Selection
├─ Sumber Materi: post_id / lesson_id
├─ Tipe Soal: multiple_choice / essay / fill_blank
├─ Tingkat Kesulitan: easy / medium / hard
└─ Jumlah Soal: 1-10 (optional)
```

### **Processing Data:**
```
Materi Content
├─ Text cleaning & normalization
├─ Tokenization
├─ Extraction keywords untuk prompt AI
└─ Format prompt untuk AI
```

### **Output Data:**
```
Generated Question
├─ question: teks soal (max 500 chars)
├─ options: array of choices (untuk MC, 4-5 items)
├─ correct_answer: jawaban benar
├─ explanation: penjelasan jawaban
├─ difficulty_level: calculated from AI prompt
├─ type: question type enum
├─ created_by: guru_id
└─ created_at: timestamp
```

---

## ⚙️ Konfigurasi Sistem

### **Environment Variables:**
```env
# AI API Configuration
OPENAI_API_KEY=sk-xxx...
OPENAI_MODEL=gpt-3.5-turbo
OPENROUTER_API_KEY=sk-or-xxx...
OPENROUTER_MODEL=openai/gpt-3.5-turbo

# Rate Limiting
AI_REQUEST_TIMEOUT=30
AI_MAX_RETRIES=3
AI_RETRY_DELAY=1000

# Storage
EXERCISE_IMAGE_PATH=storage/exercises
```

### **Config Parameters:**
```php
// config/services.php
'ai_question' => [
    'provider' => env('AI_QUESTION_PROVIDER', 'openai'),
    'timeout' => env('AI_REQUEST_TIMEOUT', 30),
    'max_retries' => env('AI_MAX_RETRIES', 3),
    'temperature' => 0.7,
    'max_tokens' => 500,
]
```

---

## 🛡️ Error Handling

### **Kemungkinan Error:**

| Error | Penyebab | Solusi |
|-------|---------|--------|
| **API Timeout** | Koneksi lambat | Retry dengan exponential backoff |
| **Invalid Response** | AI return format salah | Validate & retry |
| **Rate Limit** | Terlalu banyak request | Throttle request, queue job |
| **Database Error** | DB down/lock | Retry dengan delay |
| **Missing Material** | Post/Lesson tidak ditemukan | Show error message ke guru |

---

## 📝 Catatan Teknis

### **Backlog/Enhancements:**
- [ ] Bulk generation (generate multiple questions sekaligus)
- [ ] Question bank template (pre-built questions)
- [ ] AI model comparison (A/B testing berbagai model)
- [ ] Analytics (track success rate, quality score)
- [ ] Custom prompt builder untuk guru
- [ ] Question duplication detection

### **Performance Considerations:**
- Gunakan queue job untuk AI request (tidak blocking)
- Cache material content untuk reuse
- Batch multiple questions dalam single API call
- Implement rate limiting per guru

---

## 📚 Referensi

- **AI Question Generator Documentation**: [AI_QUESTION_GENERATOR.md](../AI_QUESTION_GENERATOR.md)
- **Database Schema**: [DATABASE_SCHEMA.md](../DATABASE_SCHEMA.md)
- **API Documentation**: [API_RATE_LIMIT_TROUBLESHOOTING.md](../AI_RATE_LIMIT_TROUBLESHOOTING.md)

---

**Diagram BPMN:** File `08_SMART_QUESTION.drawio` dapat dibuka di [draw.io](https://app.diagrams.net/)

Last Updated: 2026-05-23
