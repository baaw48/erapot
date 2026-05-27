Tabel,Kolom & Aturan,Relasi & Deskripsi
users,"id (PK, bigint), name (string), username (string, unique), password (string), role (enum: 'admin', 'guru')",Untuk autentikasi. Di- seed minimal 1 admin dan 1 guru.
tahun_ajarans,"id (PK, bigint), tahun (string, cth: ""2025/2026""), semester (enum: 'Ganjil', 'Genap'), is_active (boolean, default: false)",Data Master. Filter utama sistem agar data yang diproses hanya pada tahun aktif.
kelas,"id (PK, bigint), nama_kelas (string), tingkat (integer), wali_kelas_id (FK, bigint, nullable)",wali_kelas_id merujuk ke users.id. On Delete Set Null.
siswas,"id (PK, bigint), nis (string, unique), nisn (string, nullable), nama_siswa (string), kelas_id (FK, bigint)",kelas_id merujuk ke kelas.id. On Delete Cascade.
mapels,"id (PK, bigint), nama_mapel (string), kelompok (string, nullable)",Data Master Mata Pelajaran.
nilais,"id (PK, bigint), siswa_id (FK, bigint), mapel_id (FK, bigint), tahun_ajaran_id (FK, bigint), nilai_angka (integer, max: 100)","siswa_id, mapel_id, dan tahun_ajaran_id membentuk komposit unique agar tidak ada data ganda."