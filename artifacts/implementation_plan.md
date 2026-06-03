# Standarisasi Input dan Penyimpanan Jawaban Soal LMS

Berdasarkan audit langsung pada kode sumber sisi Admin (`TA_LEARNING_MANAGEMENT_SYSTEM_LARAVEL`), rencana implementasi ini telah disesuaikan agar **100% identik dan konsisten** dengan format yang digunakan oleh Admin saat ini. 

Hal ini memastikan:
1. Tidak ada perubahan struktur database sama sekali.
2. Form Guru, Penilaian, dan AI Generator akan memproduksi format persis seperti jika Admin yang membuatnya.
3. Kuis buatan Guru dan Admin dapat digunakan bergantian secara mulus.

---

## 1. Audit Format Penyimpanan Sisi Admin (The Source of Truth)

Setelah membedah `ExerciseItemController.php` dan `soal_create.blade.php` sisi Admin, ditemukan bahwa standar sejati (yang benar-benar disimpan di database oleh Admin) adalah:

1. **Kolom Penyimpanan Opsi:** Di Admin disebut `selection` (sedangkan di `dashboardguru` dinamakan `options` namun struktur JSON-nya sama). Admin tidak menggunakan key/abjad (seperti "A", "B"), melainkan hanya array berurutan: `["<p>Teks A</p>", "<p>Teks B</p>", ...]`
2. **Kunci Jawaban (`answer`):** Admin **SELALU** membungkus nilai ke dalam Array JSON melalui `json_encode([$answer])` sekalipun jawabannya hanya satu.

Oleh karena itu, aturan baku yang akan kita terapkan di Dashboard Guru adalah sebagai berikut:

---

## 2. Rencana Format Standar Baku (Standar Sisi Admin)

### A. Pilihan Ganda (PG - Model 1)
- **`options` (JSON):** `["<p>Opsi A</p>", "<p>Opsi B</p>", "<p>Opsi C</p>", "<p>Opsi D</p>"]`
- **`answer` (JSON Array):** `["A"]`

### B. Pilihan Ganda Banyak (PG Banyak - Model 2)
- **`options` (JSON):** `["<p>Opsi A</p>", "<p>Opsi B</p>", "<p>Opsi C</p>", "<p>Opsi D</p>"]`
- **`answer` (JSON Array):** `["A", "C", "D"]`

### C. Pernyataan (Benar Salah - Model 3)
- **`options`:** `[]` (Array kosong, karena admin menyimpan `json_encode([])`)
- **`answer` (JSON Array):** `["Benar"]` atau `["Salah"]`

### D. Isian (Model 4)
- **`options`:** `[]`
- **`answer` (JSON Array):** `["Fotosintesis"]`

### E. Uraian (Model 5)
- **`options`:** `[]`
- **`answer` (JSON Array):** `["Teks panduan penilaian atau jawaban referensi"]`

### F. Iya / Tidak (Model 6)
- **`options`:** `[]`
- **`answer` (JSON Array):** `["Iya"]` atau `["Tidak"]`

### G. Argumen (Model 7)
- **`options`:** `[]`
- **`answer` (JSON Array):** `["Teks argumen referensi"]`

---

## 3. Rencana Perubahan Form (Guru)

**File Terkait:** `resources/views/guru/soal/create-custom.blade.php`, `edit-custom.blade.php`

- Kita akan mengadopsi mekanisme JavaScript sisi Admin yang rapi.
- Saat guru mengklik Simpan, JS akan mengambil HTML dari editor opsi (A, B, C, dst) dan menggabungkannya ke dalam *flat array*.
- Dropdown Benar/Salah akan mematuhi nilai *"Benar"* dan *"Salah"*, bukan "true/false".
- Dropdown Iya/Tidak akan mematuhi nilai *"Iya"* dan *"Tidak"*.
- Ini memastikan bahwa Guru tidak perlu mengklik tombol yang membingungkan, UX tetap bersih, namun hasil JSON-nya sempurna.

## 4. Rencana Perubahan Controller (Validasi & Penyimpanan)

**File Terkait:** `app/Http/Controllers/Guru/SoalController.php` (Fungsi `storeCustom` dan `updateCustom`)

- Kita akan mereplikasi logika Admin:
  ```php
  // Format Options
  $selection = json_encode(array_values($request->options ?? []));
  
  // Format Answer (Selalu Array)
  $answer = is_array($request->answer) ? $request->answer : [$request->answer];
  $answerJson = json_encode($answer);
  ```
- Validasi wajib isi tetap dipertahankan.

## 5. Rencana Perubahan AI Generator

**File Terkait:** `SoalController.php` (Fungsi `generateWithAI`)

- *System Prompt* Gemini akan diinstruksikan untuk menghasilkan output persis dengan spesifikasi di atas (misal: Answer berformat Array String `["A"]` atau `["Benar"]`).
- Ini memangkas *mapping error* dan konversi yang tidak perlu di Controller.

## 6. Rencana Perubahan Renderer Siswa & Auto-Scoring

- **Auto-Scoring Logic:** Karena jawaban yang tersimpan di *database* sudah dipastikan selalu berformat JSON Array `["A"]` atau `["Fotosintesis"]`, maka auto-scoring cukup mendecode string tersebut, membersihkannya dari spasi (trim), mengecilkan huruf (strtolower), dan membandingkannya dengan jawaban mentah dari form ujian siswa.

## Kesimpulan & Persetujuan

Dengan temuan ini, saya telah menyesuaikan rencana awal agar **patuh penuh pada arsitektur Admin yang sesungguhnya (tanpa modifikasi DB/kolom)**. 

Apakah implementasi *strict compliance* terhadap standar Admin ini disetujui untuk mulai saya tuliskan kodenya?
