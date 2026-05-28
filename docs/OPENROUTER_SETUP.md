# OpenRouter Setup Guide

## 🚀 Quick Setup

### Step 1: Get OpenRouter API Key (FREE)

1. Buka [https://openrouter.ai](https://openrouter.ai)
2. Klik **"Sign In"** (kanan atas)
3. Login dengan:
   - Google account, atau
   - GitHub account, atau
   - Email
4. Setelah login, klik profile → **"Keys"**
   - Atau langsung: [https://openrouter.ai/keys](https://openrouter.ai/keys)
5. Klik **"Create Key"**
6. Beri nama: `DashboardGuru`
7. Copy API key yang muncul (format: `sk-or-v1-...`)

### Step 2: Add to .env

Paste API key ke file `.env`:

```env
OPENAI_API_KEY=sk-or-v1-xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
OPENAI_ORGANIZATION=
OPENAI_BASE_URL=https://openrouter.ai/api/v1
```

### Step 3: Clear Cache

```bash
php artisan config:clear
```

### Step 4: Test

```bash
php test-openai.php
```

Seharusnya langsung ✅ SUCCESS!

---

## 💰 OpenRouter Pricing

### Free Credits
- **$1 free credits** untuk akun baru
- Cukup untuk ~50-100 generates
- No credit card required!

### Model Prices (Per 1M tokens)
| Model | Input | Output | Best For |
|-------|-------|--------|----------|
| **GPT-4o-mini** | $0.15 | $0.60 | Balanced (Recommended) |
| **GPT-3.5-turbo** | $0.50 | $1.50 | Fast & cheap |
| **Claude 3 Haiku** | $0.25 | $1.25 | Quality |
| **Gemini Pro** | FREE | FREE | Free option! |

### Cost per Generate (5 soal)
- GPT-4o-mini: ~$0.01-0.02
- GPT-3.5-turbo: ~$0.03-0.04
- **Gemini Pro: FREE!**

---

## 🎯 Benefits vs OpenAI Direct

| Feature | OpenAI Free | OpenAI Paid | OpenRouter |
|---------|-------------|-------------|------------|
| Free credits | ❌ None | ❌ | ✅ $1 |
| Rate limit (RPM) | 3 | 500+ | ✅ High |
| Payment required | ✅ Yes | ✅ Yes | ❌ No |
| Multiple models | ❌ | Limited | ✅ Many |
| Price | - | Standard | ✅ Cheaper |

---

## 🔧 Model Options

Sistem sudah support automatic model detection:

### Using OpenRouter:
Model format: `openai/gpt-4o-mini`

Available models:
- `openai/gpt-4o-mini` - Best balance (recommended)
- `openai/gpt-3.5-turbo` - Faster, cheaper
- `anthropic/claude-3-haiku` - High quality
- `google/gemini-pro` - FREE!

### Using OpenAI Direct:
Model format: `gpt-4o-mini`

Sistem akan otomatis detect provider dari `OPENAI_BASE_URL`.

---

## 🧪 Testing

### Test 1: Basic Connection
```bash
php test-openai.php
```

### Test 2: Generate Questions
1. Login as Guru
2. Bank Soal → Soal Tambahan
3. Klik "Generate Soal dengan AI"
4. Fill form dan generate!

---

## ⚙️ Advanced Configuration

### Using Different Model

Edit `app/Services/OpenAIService.php`:

```php
$model = config('services.openai.base_url') 
    ? 'google/gemini-pro'    // Use FREE Gemini!
    : 'gpt-4o-mini';
```

### Add Credits (Optional)

If you run out of free credits:
1. Go to [https://openrouter.ai/credits](https://openrouter.ai/credits)
2. Add $5-10 (much cheaper than OpenAI!)
3. Enjoy hundreds of generates

---

## 🆘 Troubleshooting

### Error: "Invalid API key"
- Check API key di .env
- Make sure starts with `sk-or-v1-`
- Regenerate key di OpenRouter dashboard

### Error: "Insufficient credits"
- Free $1 sudah habis
- Add credits di dashboard
- Or use `google/gemini-pro` (FREE model)

### Still Rate Limit?
- OpenRouter's free tier is much more generous
- Try different model
- Add small credit ($1-5)

---

## 📊 Usage Monitoring

Check your usage at:
- [https://openrouter.ai/activity](https://openrouter.ai/activity)

See:
- Total requests
- Credits used
- Cost per request
- Model usage breakdown

---

## ✅ Summary

**Setup Steps:**
1. ✅ Get free API key dari OpenRouter
2. ✅ Add to `.env`
3. ✅ Test dengan `php test-openai.php`
4. ✅ Generate soal!

**Benefits:**
- ✅ Free $1 credits (no payment needed)
- ✅ Higher rate limits
- ✅ Cheaper than OpenAI
- ✅ More model options
- ✅ Same quality output

**Cost:**
- Free tier: 50-100 generates
- $5 add-on: 250-500 generates
- Much cheaper than OpenAI direct!

---

Happy generating! 🎓✨
