CONTEXT:
Kamu adalah AI Developer Assistant tingkat Senior. Kita akan membangun aplikasi web "Rapor ASTS (Asesmen Sumatif Tengah Semester)" menggunakan stack Laravel 11, Inertia.js, Vue 3 (Composition API), dan Tailwind CSS. 
Aplikasi ini ditujukan untuk mempermudah guru melakukan input nilai massal (batch input) dan langsung mencetaknya dalam bentuk PDF tanpa sistem yang terlalu kompleks.

Berikut adalah detail perencanaannya. Kerjakan sesuai instruksi FASE yang kuminta nantinya.

=== PHASE 1: INISIALISASI & AUTENTIKASI ===
1. Setup awal: Asumsikan Laravel 11 sudah terinstall. Lakukan instalasi Laravel Breeze menggunakan stack Vue with Inertia.
2. Modifikasi Migration Users: Buka file migration `users` bawaan. Hapus kolom `email` dan `email_verified_at`. Tambahkan kolom `username` (string, unique) dan `role` (enum: ['admin', 'guru'], default: 'guru').
3. Modifikasi Autentikasi: Ubah logika login bawaan Breeze (baik di Controller backend maupun form UI Vue di Frontend) agar menggunakan `username` alih-alih `email`.
4. Buat file `UserSeeder` untuk membuat 1 akun Admin (username: admin) dan 1 akun Guru (username: guru).

=== PHASE 2: STRUKTUR DATABASE & ELOQUENT ===
1. Migration: Buat file migration untuk tabel `tahun_ajarans`, `kelas`, `siswas`, `mapels`, dan `nilais`. Pastikan menyertakan foreign key constraints (cascade/set null sesuai kewajaran relasi). Di tabel `nilais`, buat unique constraint kombinasi (siswa_id, mapel_id, tahun_ajaran_id).
2. Models: Buat Eloquent model untuk semua tabel tersebut. Definisikan `$fillable` secara eksplisit, dan buat fungsi relasi seperti `kelas()` di model Siswa (BelongsTo), atau `siswas()` di model Kelas (HasMany).
3. Seeder Master: Buat seeder sederhana yang menginput: 1 Tahun Ajaran (2025/2026, Genap, is_active: true), 1 Kelas (contoh: 7-A), 3 Mapel dummy, dan 5 Siswa dummy di kelas tersebut.

=== PHASE 3: BACKEND LOGIC & ROUTES ===
1. Buat `NilaiController`.
2. Method `index()`: Ambil Tahun Ajaran yang `is_active = true`. Ambil daftar semua `kelas` dan `mapels`. Lempar data ini ke UI menggunakan `Inertia::render('Penilaian/Index', [...])`.
3. Method `getSiswa(Request $request)`: Endpoint API yang menerima parameter `kelas_id` dan `mapel_id`. Return berupa JSON daftar siswa di kelas tersebut, di-LEFT JOIN/dilengkapi dengan `nilai_angka` mereka pada mapel dan tahun ajaran aktif tersebut (jika sudah ada nilainya, tampilkan angkanya, jika belum = null).
4. Method `storeBatch(Request $request)`: Menerima payload array objek dari frontend (berisi siswa_id dan nilai_angka). Gunakan fungsi DB Transaction dan `upsert` (atau `updateOrCreate` di dalam loop jika lebih aman) untuk memproses penyimpanan massal ke tabel `nilais` agar ringan di server.
5. Daftarkan routes ini di `routes/web.php` di bawah middleware `auth`.

=== PHASE 4: FRONTEND VUE & INERTIA ===
1. Komponen: Buat file `resources/js/Pages/Penilaian/Index.vue` menggunakan `<script setup>`.
2. UI Filter: Buat dua dropdown select (Kelas dan Mata Pelajaran).
3. Reaktivitas: Gunakan `watch` pada Vue. Jika kedua dropdown sudah terisi, lakukan HTTP Request menggunakan Axios ke endpoint `getSiswa` (dari Phase 3). Simpan response ke dalam variabel reactive (misal: `const form = useForm({ nilais: [] })`).
4. UI Tabel: Tampilkan daftar nama siswa di tabel. Sediakan input field `<input type="number" min="0" max="100">` yang di-binding dengan `v-model` ke `form.nilais[index].nilai_angka`.
5. Submit: Buat tombol "Simpan Nilai Kelas". Saat di-klik, eksekusi `form.post(route('nilai.storeBatch'))`. Tambahkan indikator loading dan toast alert sukses setelah berhasil disimpan.

=== PHASE 5: PDF GENERATION ===
1. Instalasi: Gunakan `barryvdh/laravel-dompdf`.
2. Controller: Buat method `cetakPdf(Request $request)` di `NilaiController` yang menerima `kelas_id`. Ambil data rapor kelas pada tahun ajaran aktif.
3. Tampilan Cetak: Buat file `resources/views/pdf/rapor-asts.blade.php`. Desain HTML sederhana, buat kop surat (header) dengan teks "KEMENTERIAN AGAMA", "MTs AL - HASANAH CIOMAS", dan alamat lengkapnya. Tampilkan perulangan tabel nilai untuk masing-masing siswa (tiap siswa dipisah dengan `page-break-after: always;`).
4. Konfigurasi PDF: Set ukuran kertas menjadi F4 (215.9 mm x 330.2 mm / folio) di dalam script PHP DomPDF. Gunakan `->stream('Rapor-ASTS.pdf')` agar bisa di-preview di browser.
5. Tombol Cetak: Di UI Vue `Index.vue`, tambahkan link eksternal "Cetak Rapor Kelas" yang akan membuka tab baru ke route `cetakPdf`.