# Aplikasi PKK (Working Title)

Aplikasi ini dikembangkan untuk mendukung proses kerja PKK dengan fokus pada **struktur kode yang rapi, terpisah tanggung jawab, dan mudah dikembangkan** seiring bertambahnya kebutuhan bisnis.

> ⚠️ **Catatan Tahap Awal**
> Pada tahap ini, aplikasi **belum berisi logika bisnis inti**.
> Seluruh folder, class, dan lapisan disiapkan sebagai **kerangka (skeleton)** agar aturan bisnis dapat ditambahkan di kemudian hari tanpa mengubah struktur besar aplikasi.

---

## 🎯 Tujuan Utama

- Menyediakan fondasi aplikasi yang stabil dan mudah dirawat
- Memisahkan proses bisnis dari controller dan tampilan
- Memudahkan perubahan workflow tanpa refactor besar
- Mendukung pengembangan jangka panjang dan kerja tim

---

## 🧱 Prinsip Struktur Kode

Aplikasi ini menerapkan pemisahan tanggung jawab secara **pragmatis**, bukan arsitektur teoritis yang kompleks.

Prinsip yang digunakan:

- **Controller tipis**
  - Controller hanya menerima request dan meneruskan proses ke layer lain
  - Tidak berisi perhitungan atau aturan bisnis

- **Logika bisnis terpusat**
  - Aturan bisnis dan alur proses ditempatkan di `Services/`
  - Perubahan aturan bisnis diharapkan hanya berdampak pada Service

- **Helper untuk logika murni**
  - `Helpers/` berisi fungsi statis dan perhitungan murni
  - Tidak bergantung pada Request, Auth, atau Database

- **Model fokus pada data**
  - Model hanya berisi relasi, scope, casting, dan aturan data dasar
  - Tidak memuat proses bisnis yang kompleks

- **Authorization eksplisit**
  - Hak akses diatur melalui Policy dan Permission
  - Tidak disembunyikan di dalam controller atau view

- **Siap berubah**
  - Struktur disiapkan agar perubahan kebutuhan tidak merusak controller dan tampilan

---

## 📂 Struktur Folder (Ringkas)

```text
app/
├── Http/
│   └── Controllers/        # Orkestrasi request & response
├── Models/                 # Relasi dan representasi data
├── Services/               # Aturan bisnis dan alur proses
├── Helpers/                # Logika murni dan utilitas
└── Policies/               # Authorization
```

---

## 🧠 Catatan Arsitektur

Struktur ini **bukan implementasi formal Clean Architecture atau Hexagonal Architecture**.

Pendekatan yang digunakan adalah:
- Bertahap
- Mudah dipahami
- Mudah dirawat

Tujuan utamanya adalah **mengurangi beban mental saat membaca dan mengubah kode**, bukan mengejar kompleksitas arsitektur.

---

## 🧹 Code Hygiene

Beberapa aturan dasar yang dipegang dalam pengembangan:

- Penamaan konsisten (class, method, variable)
- Controller dijaga tetap tipis
- Logika bisnis tidak ditulis di controller
- Helper bersifat stateless dan dapat diuji
- Format kode mengikuti PSR-12 secara manual

---

> Struktur ini disiapkan agar developer dapat fokus pada logika bisnis
> tanpa harus terus-menerus merapikan ulang kode dasar aplikasi.

