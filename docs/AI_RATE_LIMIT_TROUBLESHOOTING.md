# OpenAI Rate Limit - Troubleshooting Guide

## ❓ Apa itu Rate Limit?

**Rate Limit** adalah batasan jumlah request yang dapat dikirim ke OpenAI API dalam periode waktu tertentu. Ini adalah mekanisme perlindungan dari OpenAI untuk mencegah abuse dan memastikan service yang adil untuk semua pengguna.

---

## 🚨 Error Message yang Mungkin Muncul

```
Gagal generate soal: OpenAI API Error: Request rate limit has been exceeded.
```

atau

```
OpenAI API Error: Rate limit reached for requests
```

atau

```
OpenAI API Error: 429 Too Many Requests
```

---

## 📊 Rate Limit Berdasarkan Tier OpenAI

### Free Tier
- **Requests**: 3 requests/minute (RPM)
- **Tokens**: 40,000 tokens/minute (TPM)
- **Daily**: 200 requests/day (RPD)

### Tier 1 (Pay-as-you-go, $5+ spent)
- **Requests**: 500 RPM
- **Tokens**: 2,000,000 TPM
- **Daily**: Unlimited

### Tier 2 ($50+ spent)
- **Requests**: 5,000 RPM
- **Tokens**: 10,000,000 TPM
- **Daily**: Unlimited

### Tier 3+ ($1,000+ spent)
- Higher limits available

Source: [OpenAI Rate Limits Documentation](https://platform.openai.com/docs/guides/rate-limits)

---

## ✅ Solusi yang Sudah Diimplementasi

### 1. **Automatic Retry dengan Exponential Backoff**

Sistem sudah otomatis mencoba ulang hingga **3 kali** jika terjadi rate limit error:

```php
// OpenAIService.php
protected $maxRetries = 3;
protected $retryDelay = 2; // seconds

// Retry schedule:
// - Attempt 1: Immediate
// - Attempt 2: Wait 2 seconds
// - Attempt 3: Wait 4 seconds
// - Attempt 4: Wait 8 seconds
```

**Benefit:** User tidak perlu manually retry, sistem akan otomatis menunggu dan mencoba lagi.

### 2. **Rate Limit Detection**

Sistem dapat mendeteksi error rate limit dan hanya retry untuk error tersebut:

```php
private function isRateLimitError(Exception $e): bool
{
    $message = strtolower($e->getMessage());
    return str_contains($message, 'rate limit') || 
           str_contains($message, 'too many requests') ||
           str_contains($message, '429');
}
```

### 3. **User-Friendly Error Messages**

Error message di UI memberikan penjelasan dan solusi praktis:
- Penjelasan apa itu rate limit
- Saran untuk menunggu 1-2 menit
- Tips mengurangi jumlah soal
- Opsi upgrade akun

---

## 🛠 Solusi Praktis untuk User

### ✨ Solusi Jangka Pendek (Immediate)

#### 1. **Tunggu 1-2 Menit**
Paling mudah dan gratis. Setelah 1 menit, rate limit biasanya sudah reset.

```
Generate ❌ Rate Limit
   ↓
Tunggu 60 detik
   ↓
Generate lagi ✅ Success
```

#### 2. **Kurangi Jumlah Soal**
Daripada generate 10 soal sekaligus:
- Generate 3-5 soal saja
- Kemudian generate lagi untuk soal tambahan
- Total waktu bisa lebih cepat dengan batch kecil

**Contoh:**
```
❌ BURUK: Generate 10 soal → Rate Limit
✅ BAIK:  Generate 3 soal → Success
         Tunggu 1 menit
         Generate 3 soal lagi → Success
         Total: 6 soal dalam 2-3 menit
```

#### 3. **Gunakan Jam Sepi**
API biasanya lebih lancar di jam-jam sepi:
- Pagi hari (06:00 - 09:00 WIB)
- Siang hari (12:00 - 14:00 WIB)
- Malam hari (21:00 - 23:00 WIB)

Hindari jam sibuk (09:00 - 11:00 WIB dan 14:00 - 17:00 WIB).

---

### 🚀 Solusi Jangka Menengah

#### 1. **Upgrade OpenAI Tier**

**Cara Upgrade:**
1. Login ke [OpenAI Platform](https://platform.openai.com/)
2. Masuk ke **Settings** → **Billing**
3. Add payment method
4. Add credit minimal $5

**Benefit:**
- Dari 3 RPM → 500 RPM (166x lebih banyak!)
- Dari 200 RPD → Unlimited
- Generate lancar tanpa khawatir limit

**Cost:**
- Initial: $5 (cukup untuk ~250-500 generates)
- Per generate (5 soal): $0.01 - $0.02
- Monthly (100 generates): ~$1.50 - $2.50

#### 2. **Best Practice Usage**

**DO ✅:**
- Generate 3-5 soal per batch
- Tunggu 1-2 menit antar generate
- Generate di jam sepi
- Review dan edit hasil AI (jangan generate berulang-ulang)

**DON'T ❌:**
- Generate 10 soal langsung
- Generate berkali-kali tanpa jeda
- Generate ulang hanya karena kurang puas (edit manual lebih baik)
- Spam button generate

---

### 💡 Solusi Jangka Panjang

#### 1. **Organization Plan**

Jika digunakan oleh banyak guru:
- Buat OpenAI Organization
- Centralized billing
- Shared quota untuk semua guru
- Better rate limits

#### 2. **Caching Strategy** (Future Enhancement)

Implementasi cache untuk:
- Soal yang sering di-generate
- Template soal umum
- Reuse soal dari guru lain (dengan permission)

#### 3. **Alternative AI Providers** (Future)

- Google Gemini API (lebih murah, generous free tier)
- Claude API (Anthropic)
- Open source models (self-hosted)

---

## 📈 Monitoring Rate Limit

### Check Usage di OpenAI Dashboard

1. Login ke [OpenAI Platform](https://platform.openai.com/)
2. Masuk ke **Usage**
3. Lihat grafik requests dan tokens

### Response Headers

OpenAI mengirim informasi rate limit di response headers:

```
x-ratelimit-limit-requests: 3
x-ratelimit-remaining-requests: 2
x-ratelimit-reset-requests: 20s
```

---

## 🔧 Troubleshooting Checklist

Jika mengalami rate limit error, cek:

- [ ] Sudah tunggu minimal 1 menit dari generate terakhir?
- [ ] Jumlah soal sudah dikurangi menjadi 3-5 saja?
- [ ] API key sudah benar dan aktif?
- [ ] Credit OpenAI masih ada (jika pay-as-you-go)?
- [ ] Tier akun OpenAI sudah sesuai kebutuhan?
- [ ] Tidak ada proses generate lain yang berjalan bersamaan?

---

## 📞 Contact & Support

### Error Masih Terjadi?

**Check Logs:**
```bash
tail -f storage/logs/laravel.log
```

**Test API Key:**
```bash
php artisan tinker

>>> $client = OpenAI::client(config('services.openai.api_key'));
>>> $response = $client->chat()->create([
...     'model' => 'gpt-4o-mini',
...     'messages' => [['role' => 'user', 'content' => 'Hello']]
... ]);
```

**OpenAI Status Page:**
- Check [status.openai.com](https://status.openai.com/)
- Lihat apakah ada downtime atau incident

---

## 📚 References

- [OpenAI Rate Limits Guide](https://platform.openai.com/docs/guides/rate-limits)
- [OpenAI Pricing](https://openai.com/pricing)
- [OpenAI Usage Dashboard](https://platform.openai.com/usage)
- [OpenAI Status](https://status.openai.com/)

---

## ✅ Summary

**Quick Fixes:**
1. ⏰ Tunggu 1-2 menit
2. 📉 Kurangi jumlah soal (3-5 soal)
3. 🔄 Sistem sudah auto-retry 3x

**Long-term Solutions:**
1. 💳 Upgrade OpenAI tier ($5 untuk 500 RPM)
2. 📋 Gunakan best practices
3. ⏲️ Generate di jam sepi

**Sistem sudah handle:**
- ✅ Auto-retry dengan exponential backoff
- ✅ User-friendly error messages
- ✅ Rate limit detection
- ✅ Default 3 soal (lebih aman)

---

*Last updated: March 6, 2026*
