# Secure Multiple Image Upload (PHP)

Skrip PHP siap pakai (*production-ready*) untuk mengunggah beberapa gambar sekaligus (*multiple upload*) secara aman. Projek ini dirancang dengan fokus pada keamanan data untuk mencegah serangan berbasis file seperti *arbitrary file upload* atau *script injection*.

## ✨ Fitur Utama

*   **Multiple Upload:** Mengunggah banyak gambar sekaligus dalam satu kali klik.
*   **Keamanan Ketat (Production Ready):**
    *   **Validasi MIME Type asli:** Menggunakan ekstensi `finfo` untuk memeriksa tipe file sebenarnya, bukan sekadar membaca ekstensi nama file.
    *   **Nama File Acak (Obfuscation):** Mengubah nama file asli menjadi string acak berbasis `random_bytes()` untuk mencegah *script injection*.
    *   **Auto .htaccess Protection:** Otomatis membuat file `.htaccess` di dalam folder tujuan untuk mematikan eksekusi skrip PHP ilegal.
*   **Validasi Ukuran:** Membatasi ukuran file maksimal (bawaan: 10MB) guna menghemat ruang penyimpanan.
*   **User Experience (UX):** Dilengkapi dengan mekanisme *Session Flash* (Post/Redirect/Get) untuk mencegah pengiriman ulang form saat halaman di-*refresh*.

## 🛠️ Teknologi yang Digunakan

*   **Backend:** PHP (Native)
*   **Frontend:** HTML5 & CSS3 (Desain kartu minimalis dan responsif)
*   **Security Modules:** `finfo` (File Information), `random_bytes()`

## 📦 Cara Instalasi & Penggunaan

### 1. Persyaratan Sistem
*   PHP versi 7.0 atau yang lebih baru.
*   Ekstensi `fileinfo` aktif pada konfigurasi PHP kamu (`php.ini`).

### 2. Langkah Penggunaan
Langsung pake aja, cuman 1 file doang, berikan hak akses ke halaman ini karna ini belum ada batasan banyaknya upload file, jadi bisa ngespam
