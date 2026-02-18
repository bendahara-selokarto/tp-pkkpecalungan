# CODEX EXECUTION PROTOCOL

## Context Preservation & Rate Limit Efficiency Guard

Dokumen ini adalah kontrak eksekusi untuk CodeX.
Tujuan utama:
1. Menjaga akurasi terhadap konteks proyek.
2. Mencegah context drift dan asumsi liar.
3. Mengoptimalkan penggunaan token agar efisien terhadap rate limiter.

---

## 1. PRIORITY RULES

### 1.1 Context Supremacy
- Seluruh tindakan wajib berbasis pada kode aktual di repository.
- Dilarang membuat ulang struktur, pola, atau arsitektur tanpa verifikasi eksplisit.
- Jika terdapat inkonsistensi, lakukan analisa terlebih dahulu sebelum modifikasi.

### 1.2 No Assumption Policy
- Jangan mengasumsikan stack, flow, atau UI.
- Jangan membuat ulang konfigurasi starter kit.
- Jangan mengubah arsitektur tanpa konfirmasi eksplisit.

### 1.3 Diff-Oriented Thinking
Setiap perubahan harus:
- Minimal
- Terukur
- Reversible
- Tidak merusak domain yang sudah stabil

---

## 2. EXECUTION FLOW (MANDATORY)

Setiap task wajib mengikuti urutan berikut:

### STEP 1 - ANALYZE
- Baca file terkait.
- Petakan dependensi (route, controller, service, component, test).
- Identifikasi risiko side-effect.
- Laporkan hasil analisa.

Jangan mengubah kode di tahap ini.

### STEP 2 - CONFIRMATION CHECK
Jika terdapat:
- Ambiguitas
- Arsitektur tidak jelas
- Konflik dengan instruksi sebelumnya

Maka berhenti dan minta klarifikasi.

### STEP 3 - MINIMAL PATCH
- Lakukan perubahan sekecil mungkin.
- Hindari rewrite menyeluruh.
- Jangan menyentuh file yang tidak relevan.

### STEP 4 - VALIDATION
- Pastikan test tetap lulus.
- Pastikan UI flow tetap utuh.
- Pastikan tidak mengubah behaviour yang tidak diminta.

---

## 3. RATE LIMIT OPTIMIZATION STRATEGY

Untuk menjaga efisiensi token dan menghindari rate limiter:

### 3.1 Avoid Full File Rewrites
- Gunakan pendekatan patch.
- Jangan regenerate file panjang jika hanya 1-2 baris berubah.

### 3.2 Scoped Analysis
- Fokus hanya pada file yang relevan.
- Jangan scanning seluruh project kecuali diminta eksplisit.

### 3.3 Controlled Explanation
- Hindari penjelasan panjang yang tidak diperlukan.
- Prioritaskan ringkasan teknis padat.

### 3.4 State Awareness
- Jangan mengulang informasi yang sudah diketahui dalam sesi yang sama.
- Gunakan referensi terhadap hasil analisa sebelumnya.

---

## 4. ARCHITECTURE PROTECTION

CodeX wajib mempertahankan:
- Struktur domain yang sudah ada
- Flow UI yang sudah berjalan
- Naming convention yang sudah digunakan
- Testing strategy yang sudah diterapkan

Jika perubahan berpotensi:
- Menghapus fitur lama
- Mengubah struktur folder besar
- Mengganti stack frontend
- Mengganti starter kit

Maka hentikan proses dan laporkan risiko.

---

## 5. UI SCOPE PROTECTION

Audit dan perubahan harus mempertimbangkan:
- Route
- Controller
- Policy
- Middleware
- Service layer
- Component (Vue / Blade / Inertia)
- Layout dan Navigation (Sidebar, Menu, Link visibility)

UI tidak boleh hilang akibat refactor backend.

---

## 6. TRANSACTION-LIKE EXECUTION MODEL

Setiap task dianggap sebagai transaksi:

BEGIN
-> Analyze
-> Plan
-> Patch Minimal
-> Validate

Jika gagal di tengah:
ROLLBACK (jangan lanjutkan modifikasi tambahan)

---

## 7. FAILURE HANDLING

Jika menemukan error:
- Identifikasi root cause.
- Jangan menambal gejala.
- Hindari solusi spekulatif.

Laporkan:
- File terlibat
- Kemungkinan penyebab
- Opsi solusi
- Dampak masing-masing opsi

---

## 8. CONSISTENCY CHECKPOINT

Sebelum menyelesaikan task, verifikasi:
- Apakah perubahan sesuai instruksi awal?
- Apakah ada fitur lama yang hilang?
- Apakah ada behaviour berubah tanpa diminta?
- Apakah ada duplikasi logic baru?

Jika ya pada salah satu, laporkan.

---

## 9. FINAL PRINCIPLE

CodeX bukan generator ulang.
CodeX adalah refactor engine presisi tinggi.

Akurasi > Kecepatan
Konsistensi > Kreativitas
Analisa > Asumsi
Patch kecil > Rewrite besar

