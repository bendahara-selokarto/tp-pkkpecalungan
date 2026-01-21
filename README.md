# Aplikasi PKK (Working Title)

Aplikasi ini dikembangkan untuk mendukung proses kerja PKK 
dengan fokus pada alur kerja yang jelas, terkontrol, dan mudah disesuaikan 
terhadap perubahan kebutuhan di masa depan.

> ⚠️ Catatan:
> Pada tahap awal ini, aplikasi belum mengimplementasikan aturan bisnis final.
> Seluruh struktur disiapkan agar mudah dikembangkan setelah kebutuhan pengguna dikonfirmasi.

---

## 🎯 Tujuan Utama
- Menyediakan fondasi aplikasi yang stabil dan mudah dirawat
- Memisahkan logika bisnis dari layer presentasi
- Memudahkan perubahan workflow tanpa refactor besar
- Mendukung kerja tim dan pengembangan jangka panjang

---

## 🧱 Prinsip Arsitektur

Aplikasi ini mengikuti prinsip berikut:

- **Controller tipis**
  - Controller hanya menerima request dan meneruskan ke Action
- **Business logic terpusat**
  - Logika bisnis berada di `Actions/` dan `Services/`
- **Authorization eksplisit**
  - Hak akses diatur melalui Policy dan Permission
- **Status berbasis Enum**
  - Status data didefinisikan secara eksplisit untuk menghindari error
- **Siap berubah**
  - Arsitektur dirancang agar perubahan aturan bisnis dapat dilakukan secara terkontrol

---

## 📂 Struktur Folder (Ringkas)

