# Smart Question Generator - Quick Setup Guide

## 🚀 Quick Start

### Step 1: Install Dependencies
Already installed! Package: `openai-php/client`

### Step 2: Configure API Key

1. Get your OpenAI API Key from [platform.openai.com](https://platform.openai.com/api-keys)

2. Add to your `.env` file:
```env
OPENAI_API_KEY=sk-proj-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_ORGANIZATION=org-xxxxxxxxxxxxxxxxxxxxxxxx
```

3. Clear cache:
```bash
php artisan config:clear
php artisan cache:clear
```

### Step 3: Access the Feature

1. Login sebagai **Guru**
2. Buka **Dashboard Guru** → **Bank Soal**
3. Pilih **Soal Tambahan**
4. Klik tombol **"Generate Soal dengan AI"** (hijau)

---

## 📝 Quick Usage

### Form Fields:
- **Ilustrasi Materi** (required): Deskripsi materi yang detail
- **Jenis Soal** (required): Pilihan Ganda atau Essay
- **Tingkat Kesulitan** (required): Mudah, Sedang, atau Sulit
- **Jumlah Soal** (required): 1-10 soal
- **Mata Pelajaran** (required): Pilih dari dropdown
- **Tipe Soal** (required): UH atau SL
- **Bagikan ke Kelas** (optional): Pilih kelas untuk share langsung

### Example Illustration:
```
Buatkan soal tentang Perkalian untuk siswa kelas 3 SD.
Fokus pada perkalian bilangan 1-10 dengan pendekatan penjumlahan berulang.
Siswa sudah memahami penjumlahan dasar 1-100.
Gunakan contoh yang mudah dipahami anak usia 8-9 tahun.
```

---

## 🔍 Features

✅ Auto-generate soal menggunakan AI (OpenAI GPT-4o-mini)  
✅ Support Pilihan Ganda (4 opsi) dan Essay  
✅ Adjustable difficulty level  
✅ Preview & edit sebelum save  
✅ Direct integration dengan Bank Soal  
✅ Share langsung ke kelas  

---

## 📂 Files Created/Modified

### New Files:
```
app/Services/OpenAIService.php
resources/views/guru/soal/ai-generator.blade.php
resources/views/guru/soal/ai-preview.blade.php
docs/AI_QUESTION_GENERATOR.md
```

### Modified Files:
```
config/services.php (added OpenAI config)
routes/web.php (added AI routes)
app/Http/Controllers/Guru/SoalController.php (added AI methods)
resources/views/guru/soal/list-direct.blade.php (added AI button)
.env.example (added OpenAI keys)
```

---

## 🎯 Key Methods

### Controller: `SoalController`
- `aiGenerator()` - Show form
- `generateWithAI()` - Call OpenAI API
- `aiPreview()` - Show preview & edit
- `saveAIQuestions()` - Save to database

### Service: `OpenAIService`
- `generateQuestions()` - Main API call method
- `buildPrompt()` - Build prompt for OpenAI
- `formatQuestions()` - Format response

---

## 🛠 Troubleshooting

### Error: "OpenAI API key is not configured"
**Solution:**
```bash
# Add to .env
OPENAI_API_KEY=your-key-here

# Clear cache
php artisan config:clear
```

### Error: "Insufficient quota"
**Solution:** Top up credit di OpenAI Platform

### Slow Response
**Solution:** Reduce jumlah soal atau try again

---

## 💰 Cost Estimation

- **Per generate (5 soal)**: ~$0.01 - $0.02
- **100 generates/month**: ~$1.50 - $2.50
- **500 generates/month**: ~$7.50 - $12.50

Model: GPT-4o-mini (cost-effective)

---

## 📚 Documentation

Full documentation: [docs/AI_QUESTION_GENERATOR.md](AI_QUESTION_GENERATOR.md)

---

## ✅ Complete!

Feature is ready to use! Make sure to add your OpenAI API key to `.env` file before testing.
