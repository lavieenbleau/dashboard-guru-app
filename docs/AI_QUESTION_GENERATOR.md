# Smart Question Generator - AI Feature Documentation

## Overview

Fitur **Smart Question Generator** memungkinkan guru untuk membuat soal secara otomatis menggunakan AI (OpenAI GPT-4). Guru cukup memberikan deskripsi materi, dan AI akan menghasilkan soal-soal berkualitas yang dapat langsung digunakan atau diedit sesuai kebutuhan.

---

## Keunggulan Fitur

✅ **Hemat Waktu** - Generate multiple soal dalam hitungan detik  
✅ **Kualitas Terjamin** - AI menghasilkan soal dengan struktur yang baik  
✅ **Fleksibel** - Mendukung soal Pilihan Ganda dan Essay  
✅ **Customizable** - Dapat mengedit hasil sebelum menyimpan  
✅ **Terintegrasi** - Langsung masuk ke bank soal dan bisa dibagikan ke kelas

---

## Instalasi & Konfigurasi

### 1. Requirements
- Laravel 12.x
- PHP 8.2+
- OpenAI API Key (dapatkan dari [platform.openai.com](https://platform.openai.com/api-keys))

### 2. Install Dependencies

```bash
composer require openai-php/client
```

Package ini sudah terinstall otomatis saat setup.

### 3. Konfigurasi API Key

Tambahkan OpenAI API key ke file `.env`:

```env
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_ORGANIZATION=org-xxxxxxxxxxxxxxxxxxxxx
```

**Cara mendapatkan API Key:**
1. Daftar/login ke [OpenAI Platform](https://platform.openai.com/)
2. Masuk ke menu API Keys
3. Buat API key baru
4. Copy dan paste ke `.env` file

⚠️ **PENTING**: Jangan commit API key ke Git! Pastikan `.env` ada di `.gitignore`.

### 4. Verifikasi Instalasi

Jalankan command berikut untuk memastikan tidak ada error:

```bash
php artisan config:clear
php artisan cache:clear
```

---

## Cara Menggunakan

### Langkah 1: Akses AI Generator

1. Login sebagai Guru
2. Masuk ke **Dashboard Guru** → **Bank Soal**
3. Pilih kategori **Soal Tambahan**
4. Klik tombol **"Generate Soal dengan AI"** (tombol hijau dengan icon brain)

### Langkah 2: Isi Form Generator

Form terdiri dari beberapa field:

#### 1. Ilustrasi / Deskripsi Materi (Required)
Berikan deskripsi materi yang jelas dan detail. Contoh:

```
Buatkan soal tentang Perkalian untuk siswa kelas 3 SD. 
Fokus pada perkalian bilangan 1-10 dengan pendekatan penjumlahan berulang. 
Siswa sudah memahami penjumlahan dasar 1-100. 
Gunakan contoh yang mudah dipahami anak usia 8-9 tahun.
```

**Tips Deskripsi yang Baik:**
- Sebutkan topik/materi yang spesifik
- Jelaskan level/tingkatan siswa
- Sebutkan prasyarat pengetahuan siswa
- Berikan konteks atau fokus pembelajaran

#### 2. Jenis Soal (Required)
- **Pilihan Ganda**: Soal dengan 4 opsi jawaban (A, B, C, D)
- **Essay**: Soal terbuka yang memerlukan jawaban panjang

#### 3. Tingkat Kesulitan (Required)
- **Mudah**: Cocok untuk pemula atau pengenalan konsep
- **Sedang**: Tingkat menengah dengan sedikit analisis
- **Sulit**: Tingkat lanjut dengan analisis mendalam

#### 4. Jumlah Soal (Required)
- Minimal: 1 soal
- Maksimal: 10 soal per generate
- Untuk soal lebih banyak, lakukan generate beberapa kali

#### 5. Mata Pelajaran (Required)
Pilih mata pelajaran yang sesuai dari dropdown

#### 6. Tipe Soal (Required)
Pilih tipe soal:
- Ulangan Harian (UH)
- Soal Latihan (SL)

#### 7. Bagikan ke Kelas (Optional)
- Anda dapat langsung membagikan soal ke kelas tertentu
- Atau skip dan bagikan nanti setelah review

### Langkah 3: Generate Soal

1. Klik tombol **"Generate Soal dengan AI"**
2. Tunggu beberapa detik (biasanya 5-15 detik)
3. Loading indicator akan muncul
4. Setelah selesai, Anda akan diarahkan ke halaman Preview

### Langkah 4: Review & Edit

Di halaman Preview, Anda dapat:

#### View Generated Questions
Setiap soal akan ditampilkan dengan:
- Judul soal
- Pertanyaan lengkap
- Opsi jawaban (jika pilihan ganda)
- Kunci jawaban
- Penjelasan dari AI

#### Edit Questions
- Edit judul soal
- Edit pertanyaan
- Edit opsi jawaban
- Edit kunci jawaban
- Ubah mata pelajaran atau tipe soal

#### Delete Questions
- Klik tombol **"Hapus"** pada soal yang tidak diinginkan
- Soal yang dihapus akan di-mark dan tidak akan disimpan
- Minimal harus ada 1 soal untuk disimpan

### Langkah 5: Simpan ke Bank Soal

1. Setelah puas dengan hasil edit, klik **"Simpan ke Bank Soal"**
2. Soal akan masuk ke Bank Soal kategori **Soal Tambahan**
3. Soal otomatis dibagikan ke kelas yang dipilih (jika ada)

---

## Struktur Database

### Tabel: `exercises`
```sql
- id
- lesson_id (FK ke lessons)
- serial_id (FK ke serials)
- exercise_type_id (FK ke exercise_types)
- title (judul soal)
- description
- is_admin (0 = custom guru, 1 = admin)
- shared_to_classes (JSON array class IDs)
- timestamps
```

### Tabel: `exercise_items`
```sql
- id
- exercise_id (FK ke exercises)
- exercise_type_id
- exercise_model_id (1=Pilgan, 2=Essay, 3=Isian)
- exercise_choice
- exercise_number
- question (teks soal)
- selection (JSON untuk opsi pilihan ganda)
- answer (kunci jawaban)
- is_user (1 = user-created)
- timestamps
```

---

## API Integration

### Service: `OpenAIService`

Location: `app/Services/OpenAIService.php`

#### Method: `generateQuestions()`

```php
public function generateQuestions(
    string $illustration,    // Deskripsi materi
    string $questionType,    // 'pilihan_ganda' atau 'essai'
    string $difficulty,      // 'mudah', 'sedang', 'sulit'
    int $count              // Jumlah soal (1-10)
): array
```

**Response Format:**
```php
[
    [
        'title' => 'Judul Soal',
        'question' => 'Teks pertanyaan lengkap',
        'options' => [        // Hanya untuk pilihan ganda
            'Opsi A',
            'Opsi B',
            'Opsi C',
            'Opsi D'
        ],
        'correct_answer' => 'A',  // atau teks untuk essay
        'explanation' => 'Penjelasan jawaban'
    ],
    // ... more questions
]
```

#### Model Used
- **GPT-4o-mini** - Model OpenAI yang efisien dan cost-effective
- Temperature: 0.7 (balance between creativity and consistency)
- Response format: JSON object

---

## Routes

```php
// AI Question Generator Routes
Route::get('/ai-generator', [SoalController::class, 'aiGenerator'])
    ->name('guru.soal.ai-generator');

Route::post('/ai-generate', [SoalController::class, 'generateWithAI'])
    ->name('guru.soal.ai-generate');

Route::get('/ai-preview', [SoalController::class, 'aiPreview'])
    ->name('guru.soal.ai-preview');

Route::post('/ai-save', [SoalController::class, 'saveAIQuestions'])
    ->name('guru.soal.ai-save');
```

---

## Controller Methods

### 1. `aiGenerator($serial)`
Menampilkan form AI generator

### 2. `generateWithAI(Request $request, $serial)`
Melakukan request ke OpenAI API dan generate soal

**Validation:**
- `illustration`: required, string, min:20
- `question_type`: required, in:pilihan_ganda,essai
- `difficulty`: required, in:mudah,sedang,sulit
- `count`: required, integer, min:1, max:10

**Session Storage:**
Menyimpan hasil generate ke session untuk preview

### 3. `aiPreview($serial)`
Menampilkan preview soal yang sudah di-generate

### 4. `saveAIQuestions(Request $request, $serial)`
Menyimpan soal yang sudah direview ke database

---

## Error Handling

### Common Errors & Solutions

#### 1. "OpenAI API key is not configured"
**Penyebab:** API key belum diset di `.env`  
**Solusi:** 
```bash
# Edit .env file
OPENAI_API_KEY=your-api-key-here

# Clear config cache
php artisan config:clear
```

#### 2. "Failed to parse OpenAI response"
**Penyebab:** Response dari OpenAI tidak sesuai format JSON  
**Solusi:** 
- Coba generate ulang
- Perbaiki deskripsi materi (buat lebih spesifik)
- Check OpenAI API status

#### 3. "OpenAI API Error: Insufficient quota"
**Penyebab:** Limit API OpenAI sudah habis  
**Solusi:**
- Top up credit di OpenAI Platform
- Upgrade plan jika perlu

#### 4. timeout atau slow response
**Penyebab:** Network atau OpenAI server lambat  
**Solusi:**
- Kurangi jumlah soal yang di-generate
- Coba lagi beberapa saat

---

## Best Practices

### 1. Menulis Ilustrasi yang Efektif
✅ **BAIK:**
```
Buatkan soal tentang ekosistem untuk siswa kelas 5 SD. 
Fokus pada rantai makanan dan jaring-jaring makanan.
Siswa sudah memahami konsep produsen, konsumen, dan dekomposer.
Gunakan contoh ekosistem yang umum seperti sawah atau kolam.
```

❌ **KURANG BAIK:**
```
Buatkan soal IPA
```

### 2. Generate dalam Batch Kecil
- Generate 3-5 soal sekaligus lebih baik daripada 10 soal
- Hasil lebih konsisten dan berkualitas
- Lebih mudah untuk review

### 3. Selalu Review Sebelum Menyimpan
- Periksa kebenaran jawaban
- Pastikan opsi jawaban tidak ambigu
- Sesuaikan dengan kurrikulum yang berlaku

### 4. Combine AI dengan Manual
- Gunakan AI untuk draft awal
- Edit dan sempurnakan sesuai kebutuhan
- Tambah konteks lokal atau contoh spesifik

---

## Cost Estimation

### OpenAI Pricing (as of 2024)
- **GPT-4o-mini**: $0.15 / 1M input tokens, $0.60 / 1M output tokens

### Estimasi per Generate:
- 5 soal pilihan ganda: ~$0.01 - $0.02
- 5 soal essay: ~$0.015 - $0.025

### Monthly Estimation:
- 100 generates/bulan: ~$1.50 - $2.50
- 500 generates/bulan: ~$7.50 - $12.50

💡 **Tips Hemat:**
- Gunakan batch generate (1 request untuk beberapa soal)
- Review dan refine deskripsi untuk hasil lebih akurat
- Re-use dan edit soal yang sudah ada

---

## Troubleshooting

### Logs Location
```
storage/logs/laravel.log
```

### Debug Mode
Enable debug untuk melihat error detail:
```env
APP_DEBUG=true
```

### Check API Connection
Test API key dengan:
```bash
php artisan tinker

>>> $client = OpenAI::client(config('services.openai.api_key'));
>>> $response = $client->chat()->create([
...     'model' => 'gpt-4o-mini',
...     'messages' => [['role' => 'user', 'content' => 'Hello']]
... ]);
>>> $response->choices[0]->message->content;
```

---

## Security Considerations

### 1. API Key Protection
- ✅ Simpan di `.env` (tidak di-commit ke Git)
- ✅ Gunakan environment variables
- ❌ Jangan hardcode di code
- ❌ Jangan share di dokumentasi public

### 2. Input Validation
- Validasi semua input dari user
- Limit panjang deskripsi untuk avoid abuse
- Rate limiting untuk prevent spam

### 3. Output Sanitization
- Escape HTML di output
- Validate JSON structure dari OpenAI
- Handle error gracefully

---

## Future Enhancements

### Planned Features:
1. **Question Bank Templates**
   - Save successful prompts
   - Re-use templates untuk topik serupa

2. **Batch Generation**
   - Generate untuk multiple topics sekaligus
   - Schedule generation

3. **Advanced Customization**
   - Custom prompt templates
   - Fine-tune AI parameters

4. **Analytics**
   - Track usage statistics
   - Cost monitoring
   - Quality metrics

5. **Integration**
   - Export to Word/PDF
   - Import from existing question banks
   - Integration with quiz platforms

---

## Support & Contact

Untuk pertanyaan atau issue terkait fitur ini:
- Check documentation ini terlebih dahulu
- Review error logs di `storage/logs/laravel.log`
- Contact development team dengan informasi:
  - Error message lengkap
  - Steps to reproduce
  - Screenshot (jika ada)

---

## Changelog

### Version 1.0.0 (March 2026)
- ✨ Initial release
- ✅ Multiple choice question generation
- ✅ Essay question generation
- ✅ Difficulty level selection
- ✅ Preview and edit functionality
- ✅ Direct integration with question bank
- ✅ Classroom sharing

---

## References

- [OpenAI API Documentation](https://platform.openai.com/docs)
- [OpenAI PHP Client](https://github.com/openai-php/client)
- [Laravel Documentation](https://laravel.com/docs)
