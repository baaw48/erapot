/**
 * Aplikasi Rapor ASTS (Asesmen Sumatif Tengah Semester)
 * Backend Google Apps Script (Code.gs)
 * Semua tampilan digabung dalam satu file Index.html
 */

function doGet(e) {
  return HtmlService.createHtmlOutputFromFile('Index')
      .setTitle('E-Rapor ASTS - Asesmen Sumatif Tengah Semester')
      .addMetaTag('viewport', 'width=device-width, initial-scale=1')
      .setXFrameOptionsMode(HtmlService.XFrameOptionsMode.ALLOWALL);
}

/**
 * Mendapatkan Secret Key aplikasi untuk signing session token.
 * Disimpan di Script Properties agar persisten dan aman dari kebocoran kode.
 */
function getAppSecret() {
  var scriptProperties = PropertiesService.getScriptProperties();
  var secret = scriptProperties.getProperty('APP_SECRET');
  if (!secret) {
    secret = Utilities.getUuid();
    scriptProperties.setProperty('APP_SECRET', secret);
  }
  return secret;
}

/**
 * Hashing password dengan SHA-256 untuk keamanan data.
 */
function hashPassword(password) {
  var digest = Utilities.computeDigest(Utilities.DigestAlgorithm.SHA_256, password, Utilities.Charset.UTF_8);
  var hexString = '';
  for (var i = 0; i < digest.length; i++) {
    var byteVal = digest[i];
    if (byteVal < 0) byteVal += 256;
    var byteString = byteVal.toString(16);
    if (byteString.length == 1) byteString = '0' + byteString;
    hexString += byteString;
  }
  return hexString;
}

/**
 * Menghasilkan token sesi bertanda tangan digital.
 */
function generateToken(username, role, mapelDiampu, kelasDiampu) {
  var secret = getAppSecret();
  return hashPassword(username + '|' + role + '|' + (mapelDiampu || '') + '|' + (kelasDiampu || '') + '|' + secret);
}

/**
 * Validasi token sesi di sisi server.
 */
function verifySession(session) {
  if (!session || !session.username || !session.role || !session.token) {
    return false;
  }
  var expectedToken = generateToken(session.username, session.role, session.mapelDiampu, session.kelasDiampu);
  return session.token === expectedToken;
}

/**
 * Memastikan lembar sheet dan data awal (seeding) terbentuk dengan benar.
 */
function setupDatabase() {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  if (!ss) {
    throw new Error('Spreadsheet aktif tidak ditemukan. Pastikan script ini dijalankan dari dalam Google Sheets (container-bound script).');
  }

  // Pastikan Mapel_Config terbentuk terlebih dahulu untuk registri mapel
  var mapelConfigSheet = ss.getSheetByName("Mapel_Config");
  if (!mapelConfigSheet) {
    mapelConfigSheet = ss.insertSheet("Mapel_Config");
    var headers = ["Kode_Mapel", "Nama_Mapel", "KKTP"];
    mapelConfigSheet.getRange(1, 1, 1, headers.length).setValues([headers]);
    mapelConfigSheet.getRange(1, 1, 1, headers.length).setFontWeight("bold").setBackground("#EEF2F6");
  }

  // Seeding default mata pelajaran jika kosong
  if (mapelConfigSheet.getLastRow() <= 1) {
    var defaultMapels = [
      ["PAI", "Pendidikan Agama dan Budi Pekerti", 75],
      ["PKN", "Pendidikan Pancasila & Kewarganegaraan", 75],
      ["B_IND", "Bahasa Indonesia", 75],
      ["MTK", "Matematika", 75],
      ["IPA", "Ilmu Pengetahuan Alam", 75],
      ["IPS", "Ilmu Pengetahuan Sosial", 75],
      ["B_ING", "Bahasa Inggris", 75],
      ["PJOK", "Pendidikan Jasmani, OR & Kesehatan", 75],
      ["INFORMATIKA", "Informatika", 75],
      ["SENIBUDAYA", "Seni Budaya", 75],
      ["MULOK", "Muatan Lokal", 75]
    ];
    for (var i = 0; i < defaultMapels.length; i++) {
      mapelConfigSheet.appendRow(defaultMapels[i]);
    }
  }

  // Baca kode mapel yang aktif
  var mapelCodes = [];
  var mapelRows = mapelConfigSheet.getRange(2, 1, mapelConfigSheet.getLastRow() - 1, 1).getValues();
  for (var i = 0; i < mapelRows.length; i++) {
    mapelCodes.push(mapelRows[i][0]);
  }

  var sheetsConfig = {
    "Users": ["Username", "Password", "Role", "Nama_Lengkap", "Mapel_Diampu", "Kelas_Diampu"],
    "Data_Sekolah": ["Nama_Sekolah", "Alamat", "Kepala_Sekolah", "NIP_Kepsek", "Semester", "Tahun_Pelajaran", "Logo_Sekolah", "Jenis_Asesmen", "Yayasan", "Akreditasi", "NSM", "NPSN", "NPWP", "Akta_Notaris", "Email"],
    "Guru_Mapel": ["ID_Guru", "Nama_Guru", "NIP", "Mapel", "Kelas"],
    "Data_Siswa": ["NIS", "NISN", "Nama_Siswa", "Kelas", "Jenis_Kelamin"],
    "Leger_Nilai": ["NISN", "Nama_Siswa"].concat(mapelCodes).concat(["Jumlah", "Rata_rata"]),
    "Kehadiran_Ekstra": ["NISN", "Sakit", "Izin", "Alpa", "Ekstrakurikuler_1", "Nilai_Eks_1", "Ekstrakurikuler_2", "Nilai_Eks_2", "Ekstrakurikuler_3", "Nilai_Eks_3", "Catatan_Wali_Kelas"],
    "Mapel_Config": ["Kode_Mapel", "Nama_Mapel", "KKTP"]
  };

  for (var name in sheetsConfig) {
    var sheet = ss.getSheetByName(name);
    if (!sheet) {
      sheet = ss.insertSheet(name);
    }
    
    // Periksa atau atur header
    var headers = sheetsConfig[name];
    var lastCol = sheet.getLastColumn();
    if (lastCol === 0) {
      sheet.getRange(1, 1, 1, headers.length).setValues([headers]);
      sheet.getRange(1, 1, 1, headers.length).setFontWeight("bold").setBackground("#EEF2F6");
    } else {
      // Fitur self-healing: jika kolom terdaftar bertambah, perbarui header baris 1
      var existingHeaders = sheet.getRange(1, 1, 1, lastCol).getValues()[0];
      if (existingHeaders.length < headers.length) {
        sheet.getRange(1, 1, 1, headers.length).setValues([headers]);
        sheet.getRange(1, 1, 1, headers.length).setFontWeight("bold").setBackground("#EEF2F6");
      }
    }
  }

  // Seeding default admin user jika kosong
  var usersSheet = ss.getSheetByName("Users");
  if (usersSheet.getLastRow() <= 1) {
    var adminPasswordHash = hashPassword("admin123");
    usersSheet.appendRow(["admin", adminPasswordHash, "Admin", "Administrator Rapor", "", ""]);
    
    // Tambah sample guru
    var guruMtkHash = hashPassword("guru123");
    var guruIpaHash = hashPassword("guru123");
    usersSheet.appendRow(["guru_mtk", guruMtkHash, "Guru", "Budi Prasetyo, S.Pd.", "MTK", "VIII-A"]);
    usersSheet.appendRow(["guru_ipa", guruIpaHash, "Guru", "Siti Aminah, S.Pd.", "IPA", ""]);
  }

  // Seeding default data sekolah jika kosong
  var sekolahSheet = ss.getSheetByName("Data_Sekolah");
  if (sekolahSheet.getLastRow() <= 1) {
    sekolahSheet.appendRow([
      "MTs. AL HASANAH", 
      "Jln. Ciapus Sukamakmur No.05 Ciomas Bogor 16610", 
      "Drs. H. Ahmad Sunarya, M.Pd.", 
      "197001011995031002", 
      "Ganjil", 
      "2026/2027",
      "https://upload.wikimedia.org/wikipedia/commons/9/9c/Logo_Kementerian_Pendidikan_dan_Kebudayaan.png",
      "Asesmen Sumatif Tengah Semester (ASTS)",
      "YAYASAN AL HASANAH CIOMAS",
      "STATUS TERAKREDITASI A",
      "121.2.32.01.0188",
      "20277480",
      "02.548.702.6-434.002",
      "Akta Notaris No 09 tanggal 25 Mei 2016",
      "mtsalhasanah.ciomas@gmail.com"
    ]);
  }

  // Seeding default guru mapel jika kosong
  var guruSheet = ss.getSheetByName("Guru_Mapel");
  if (guruSheet.getLastRow() <= 1) {
    guruSheet.appendRow(["G001", "Budi Prasetyo, S.Pd.", "198512122010121003", "MTK", "VIII-A"]);
    guruSheet.appendRow(["G002", "Siti Aminah, S.Pd.", "199003152015042005", "IPA", ""]);
  }

  // Seeding default data siswa jika kosong
  var siswaSheet = ss.getSheetByName("Data_Siswa");
  if (siswaSheet.getLastRow() <= 1) {
    var sampleSiswa = [
      ["26001", "0112345678", "Andi Hermawan", "VIII-A", "L"],
      ["26002", "0112345679", "Budi Santoso", "VIII-A", "L"],
      ["26003", "0112345680", "Citra Lestari", "VIII-A", "P"],
      ["26004", "0112345681", "Dewi Sartika", "VIII-A", "P"]
    ];
    for (var i = 0; i < sampleSiswa.length; i++) {
      siswaSheet.appendRow(sampleSiswa[i]);
    }
  }

  // Sync data Leger_Nilai dan Kehadiran_Ekstra jika kosong
  var studentValues = [];
  if (siswaSheet.getLastRow() > 1) {
    studentValues = siswaSheet.getRange(2, 1, siswaSheet.getLastRow() - 1, 5).getValues();
  }
  var legerSheet = ss.getSheetByName("Leger_Nilai");
  if (legerSheet.getLastRow() <= 1) {
    for (var i = 0; i < studentValues.length; i++) {
      var nisn = studentValues[i][1];
      var nama = studentValues[i][2];
      
      // Nilai default mapel = 0, Jumlah = 0, Rata-rata = 0
      var row = [nisn, nama];
      for (var j = 0; j < mapelCodes.length; j++) {
        row.push(0);
      }
      row.push(0, 0); // Jumlah, Rata_rata
      legerSheet.appendRow(row);
    }
  }

  var kehadiranSheet = ss.getSheetByName("Kehadiran_Ekstra");
  if (kehadiranSheet.getLastRow() <= 1) {
    for (var i = 0; i < studentValues.length; i++) {
      var nisn = studentValues[i][1];
      // Headers: [NISN, Sakit, Izin, Alpa, Ekstrakurikuler_1, Nilai_Eks_1, Ekstrakurikuler_2, Nilai_Eks_2, Ekstrakurikuler_3, Nilai_Eks_3, Catatan_Wali_Kelas]
      kehadiranSheet.appendRow([nisn, 0, 0, 0, "Pramuka", "A", "-", "-", "-", "-", "Pertahankan prestasimu dan teruslah belajar dengan giat."]);
    }
  }

  return "Database berhasil diinisialisasi dengan data default!";
}

/**
 * Validasi Login Pengguna.
 */
function loginUser(username, password) {
  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName("Users");
  if (!sheet) {
    // Jika sheet belum ada, jalankan setupDatabase terlebih dahulu
    setupDatabase();
    sheet = ss.getSheetByName("Users");
  }

  var lastCol = sheet.getLastColumn();
  if (lastCol < 6) {
    setupDatabase();
    lastCol = sheet.getLastColumn();
  }

  var data = sheet.getRange(2, 1, sheet.getLastRow() - 1, 6).getValues();
  var inputHash = hashPassword(password);

  for (var i = 0; i < data.length; i++) {
    var dbUser = data[i][0];
    var dbHash = data[i][1];
    var dbRole = data[i][2];
    var dbNama = data[i][3];
    var dbMapel = data[i][4];
    var dbKelas = data[i][5] || "";

    if (String(dbUser).toLowerCase() === String(username).toLowerCase() && dbHash === inputHash) {
      var token = generateToken(dbUser, dbRole, dbMapel, dbKelas);
      return {
        success: true,
        user: {
          username: dbUser,
          role: dbRole,
          namaLengkap: dbNama,
          mapelDiampu: dbMapel,
          kelasDiampu: dbKelas,
          token: token
        }
      };
    }
  }

  return {
    success: false,
    message: "Username atau Password salah!"
  };
}

/**
 * Mengambil seluruh data untuk Dashboard (memerlukan sesi valid).
 */
function getDashboardData(session) {
  if (!verifySession(session)) {
    throw new Error("Sesi tidak valid atau telah berakhir. Silakan login kembali.");
  }

  var ss = SpreadsheetApp.getActiveSpreadsheet();
  
  // Ambil Data Sekolah
  var sekolahSheet = ss.getSheetByName("Data_Sekolah");
  var sekolahData = {};
  if (sekolahSheet && sekolahSheet.getLastRow() > 1) {
    var lastCol = sekolahSheet.getLastColumn();
    var row = [];
    if (lastCol > 0) {
      row = sekolahSheet.getRange(2, 1, 1, lastCol).getValues()[0];
    }
    sekolahData = {
      namaSekolah: row[0] || "",
      alamat: row[1] || "",
      kepalaSekolah: row[2] || "",
      nipKepsek: row[3] || "",
      semester: row[4] || "",
      tahunPelajaran: row[5] || "",
      logoSekolah: row[6] || "https://upload.wikimedia.org/wikipedia/commons/9/9c/Logo_Kementerian_Pendidikan_dan_Kebudayaan.png",
      jenisAsesmen: row[7] || "Asesmen Sumatif Tengah Semester (ASTS)",
      yayasan: row[8] || "",
      akreditasi: row[9] || "",
      nsm: row[10] || "",
      npsn: row[11] || "",
      npwp: row[12] || "",
      aktaNotaris: row[13] || "",
      email: row[14] || ""
    };
  }

  // Ambil Data Siswa
  var siswaSheet = ss.getSheetByName("Data_Siswa");
  var siswaList = [];
  if (siswaSheet && siswaSheet.getLastRow() > 1) {
    var rows = siswaSheet.getRange(2, 1, siswaSheet.getLastRow() - 1, 5).getValues();
    for (var i = 0; i < rows.length; i++) {
      siswaList.push({
        nis: rows[i][0],
        nisn: rows[i][1],
        namaSiswa: rows[i][2],
        kelas: rows[i][3],
        jenisKelamin: rows[i][4]
      });
    }
  }

  // Ambil Data Guru & Users (guruList bisa diakses Admin & Guru untuk cetak tanda tangan)
  var guruList = [];
  var userList = [];
  
  if (session.role === 'Admin' || session.role === 'Guru') {
    var guruSheet = ss.getSheetByName("Guru_Mapel");
    if (guruSheet && guruSheet.getLastRow() > 1) {
      var lastColGuru = guruSheet.getLastColumn();
      if (lastColGuru < 5) {
        setupDatabase();
        lastColGuru = 5;
      }
      var rows = guruSheet.getRange(2, 1, guruSheet.getLastRow() - 1, 5).getValues();
      for (var i = 0; i < rows.length; i++) {
        guruList.push({
          idGuru: rows[i][0],
          namaGuru: rows[i][1],
          nip: rows[i][2],
          mapel: rows[i][3],
          kelas: rows[i][4] || ""
        });
      }
    }
  }

  if (session.role === 'Admin') {
    var usersSheet = ss.getSheetByName("Users");
    if (usersSheet && usersSheet.getLastRow() > 1) {
      var lastColUsers = usersSheet.getLastColumn();
      if (lastColUsers < 6) {
        setupDatabase();
        lastColUsers = 6;
      }
      var rows = usersSheet.getRange(2, 1, usersSheet.getLastRow() - 1, 6).getValues();
      for (var i = 0; i < rows.length; i++) {
        userList.push({
          username: rows[i][0],
          role: rows[i][2],
          namaLengkap: rows[i][3],
          mapelDiampu: rows[i][4],
          kelasDiampu: rows[i][5] || ""
        });
      }
    }
  }

  // Ambil Leger Nilai
  var legerSheet = ss.getSheetByName("Leger_Nilai");
  var legerList = [];
  var mapelHeaders = [];
  
  if (legerSheet && legerSheet.getLastColumn() > 4) {
    var headersRow = legerSheet.getRange(1, 1, 1, legerSheet.getLastColumn()).getValues()[0];
    for (var col = 2; col < headersRow.length - 2; col++) {
      mapelHeaders.push(headersRow[col]);
    }
  } else {
    mapelHeaders = ["PAI", "PKN", "B_IND", "MTK", "IPA", "IPS", "B_ING", "PJOK", "INFORMATIKA", "SENIBUDAYA", "MULOK"];
  }

  if (legerSheet && legerSheet.getLastRow() > 1) {
    var rows = legerSheet.getRange(2, 1, legerSheet.getLastRow() - 1, legerSheet.getLastColumn()).getValues();
    for (var i = 0; i < rows.length; i++) {
      var gradesObj = {};
      for (var m = 0; m < mapelHeaders.length; m++) {
        gradesObj[mapelHeaders[m]] = Number(rows[i][2 + m]) || 0;
      }
      legerList.push({
        nisn: rows[i][0],
        namaSiswa: rows[i][1],
        nilai: gradesObj,
        jumlah: Number(rows[i][2 + mapelHeaders.length]) || 0,
        rataRata: Number(rows[i][2 + mapelHeaders.length + 1]) || 0
      });
    }
  }

  // Ambil Kehadiran & Ekskul
  var kehadiranSheet = ss.getSheetByName("Kehadiran_Ekstra");
  var kehadiranList = [];
  if (kehadiranSheet && kehadiranSheet.getLastRow() > 1) {
    var lastColKeh = kehadiranSheet.getLastColumn();
    var rows = kehadiranSheet.getRange(2, 1, kehadiranSheet.getLastRow() - 1, lastColKeh).getValues();
    for (var i = 0; i < rows.length; i++) {
      var row = rows[i];
      if (lastColKeh >= 11) {
        kehadiranList.push({
          nisn: row[0],
          sakit: Number(row[1]) || 0,
          izin: Number(row[2]) || 0,
          alpa: Number(row[3]) || 0,
          ekskul1: row[4] || '-',
          nilaiEks1: row[5] || '-',
          ekskul2: row[6] || '-',
          nilaiEks2: row[7] || '-',
          ekskul3: row[8] || '-',
          nilaiEks3: row[9] || '-',
          catatanWali: row[10] || ''
        });
      } else {
        // Old 7-column layout fallback
        kehadiranList.push({
          nisn: row[0],
          sakit: Number(row[1]) || 0,
          izin: Number(row[2]) || 0,
          alpa: Number(row[3]) || 0,
          ekskul1: row[4] || '-',
          nilaiEks1: row[5] || '-',
          ekskul2: '-',
          nilaiEks2: '-',
          ekskul3: '-',
          nilaiEks3: '-',
          catatanWali: row[6] || ''
        });
      }
    }
  }

  // Ambil Mapel Config
  var mapelConfigSheet = ss.getSheetByName("Mapel_Config");
  var mapelNamesObj = {};
  var mapelKktpObj = {};
  if (mapelConfigSheet && mapelConfigSheet.getLastRow() > 1) {
    var lastCol = mapelConfigSheet.getLastColumn();
    var rows = mapelConfigSheet.getRange(2, 1, mapelConfigSheet.getLastRow() - 1, Math.max(lastCol, 3)).getValues();
    for (var i = 0; i < rows.length; i++) {
      var code = rows[i][0];
      mapelNamesObj[code] = rows[i][1];
      mapelKktpObj[code] = Number(rows[i][2]) || 75;
    }
  }
  
  var defaultNames = {
    "PAI": "Pendidikan Agama dan Budi Pekerti",
    "PKN": "Pendidikan Pancasila & Kewarganegaraan",
    "B_IND": "Bahasa Indonesia",
    "MTK": "Matematika",
    "IPA": "Ilmu Pengetahuan Alam",
    "IPS": "Ilmu Pengetahuan Sosial",
    "B_ING": "Bahasa Inggris",
    "PJOK": "Pendidikan Jasmani, OR & Kesehatan",
    "INFORMATIKA": "Informatika",
    "SENIBUDAYA": "Seni Budaya",
    "MULOK": "Muatan Lokal"
  };
  for (var key in defaultNames) {
    if (!mapelNamesObj[key]) mapelNamesObj[key] = defaultNames[key];
    if (!mapelKktpObj[key]) mapelKktpObj[key] = 75;
  }
  for (var i = 0; i < mapelHeaders.length; i++) {
    var code = mapelHeaders[i];
    if (!mapelNamesObj[code]) mapelNamesObj[code] = code;
    if (!mapelKktpObj[code]) mapelKktpObj[code] = 75;
  }

  return {
    sekolah: sekolahData,
    siswa: siswaList,
    guru: guruList,
    users: userList,
    leger: legerList,
    kehadiran: kehadiranList,
    mapelHeaders: mapelHeaders,
    mapelNames: mapelNamesObj,
    mapelKktp: mapelKktpObj
  };
}

/**
 * Menyimpan seluruh data siswa (Hanya Admin).
 * Secara otomatis mensinkronkan NISN di Leger dan Kehadiran.
 */
function saveDataSiswa(session, siswaList) {
  if (!verifySession(session)) {
    throw new Error("Sesi tidak valid.");
  }
  if (session.role !== 'Admin') {
    throw new Error("Akses ditolak. Anda bukan Administrator.");
  }

  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName("Data_Siswa");
  if (!sheet) throw new Error("Sheet Data_Siswa tidak ditemukan.");

  // Bersihkan data lama, kecuali header
  if (sheet.getLastRow() > 1) {
    sheet.getRange(2, 1, sheet.getLastRow() - 1, 5).clearContent();
  }

  // Tulis data baru
  if (siswaList.length > 0) {
    var values = [];
    for (var i = 0; i < siswaList.length; i++) {
      values.push([
        siswaList[i].nis,
        siswaList[i].nisn,
        siswaList[i].namaSiswa,
        siswaList[i].kelas,
        siswaList[i].jenisKelamin
      ]);
    }
    sheet.getRange(2, 1, values.length, 5).setValues(values);
  }

  // Sinkronisasi data di Leger_Nilai dan Kehadiran_Ekstra
  syncSiswaRelatedSheets(ss, siswaList);

  return "Data siswa berhasil diperbarui!";
}

/**
 * Fungsi pembantu untuk mensinkronkan data siswa ke Leger dan Kehadiran
 */
function syncSiswaRelatedSheets(ss, siswaList) {
  var legerSheet = ss.getSheetByName("Leger_Nilai");
  var kehadiranSheet = ss.getSheetByName("Kehadiran_Ekstra");

  // Deteksi mapelHeaders dari kolom leger dinamis
  var mapelHeaders = [];
  var lastCol = legerSheet.getLastColumn();
  if (lastCol > 4) {
    var headersRow = legerSheet.getRange(1, 1, 1, lastCol).getValues()[0];
    for (var col = 2; col < headersRow.length - 2; col++) {
      mapelHeaders.push(headersRow[col]);
    }
  }

  // Dapatkan map nilai & kehadiran lama berdasarkan NISN untuk menyimpan data lama
  var oldLegerMap = {};
  if (legerSheet && legerSheet.getLastRow() > 1) {
    var rows = legerSheet.getRange(2, 1, legerSheet.getLastRow() - 1, lastCol).getValues();
    for (var i = 0; i < rows.length; i++) {
      var nisn = rows[i][0];
      var rowData = [];
      for (var j = 2; j < lastCol; j++) {
        rowData.push(rows[i][j]); // Nilai mapel, jumlah, rata
      }
      oldLegerMap[nisn] = rowData;
    }
  }

  var oldKehadiranMap = {};
  var kehColCount = 7;
  if (kehadiranSheet) {
    kehColCount = kehadiranSheet.getLastColumn();
  }
  if (kehadiranSheet && kehadiranSheet.getLastRow() > 1) {
    var rows = kehadiranSheet.getRange(2, 1, kehadiranSheet.getLastRow() - 1, kehColCount).getValues();
    for (var i = 0; i < rows.length; i++) {
      var nisn = rows[i][0];
      var row = rows[i];
      if (kehColCount >= 11) {
        oldKehadiranMap[nisn] = [
          row[1], // Sakit
          row[2], // Izin
          row[3], // Alpa
          row[4], // Ekskul 1
          row[5], // Nilai Ekskul 1
          row[6], // Ekskul 2
          row[7], // Nilai Ekskul 2
          row[8], // Ekskul 3
          row[9], // Nilai Ekskul 3
          row[10] // Catatan
        ];
      } else {
        // Old 7-column layout fallback
        oldKehadiranMap[nisn] = [
          row[1], // Sakit
          row[2], // Izin
          row[3], // Alpa
          row[4], // Ekskul 1
          row[5], // Nilai Ekskul 1
          "-",    // Ekskul 2
          "-",    // Nilai Ekskul 2
          "-",    // Ekskul 3
          "-",    // Nilai Ekskul 3
          row[6]  // Catatan
        ];
      }
    }
  }

  // Tulis ulang leger secara dinamis
  if (legerSheet) {
    if (legerSheet.getLastRow() > 1) {
      legerSheet.getRange(2, 1, legerSheet.getLastRow() - 1, lastCol).clearContent();
    }
    if (siswaList.length > 0) {
      var newLegerValues = [];
      for (var i = 0; i < siswaList.length; i++) {
        var nisn = siswaList[i].nisn;
        var nama = siswaList[i].namaSiswa;
        
        var defaultGrades = [];
        for (var j = 0; j < mapelHeaders.length + 2; j++) {
          defaultGrades.push(0); // mapel + jumlah + rata
        }
        var oldGrades = oldLegerMap[nisn] || defaultGrades;
        
        var row = [nisn, nama];
        for (var j = 0; j < oldGrades.length; j++) {
          row.push(oldGrades[j]);
        }
        newLegerValues.push(row);
      }
      legerSheet.getRange(2, 1, newLegerValues.length, lastCol).setValues(newLegerValues);
    }
  }

  // Tulis ulang kehadiran
  if (kehadiranSheet) {
    var lastColKehadiran = kehadiranSheet.getLastColumn();
    if (lastColKehadiran < 11) {
      setupDatabase();
      lastColKehadiran = 11;
    }
    if (kehadiranSheet.getLastRow() > 1) {
      kehadiranSheet.getRange(2, 1, kehadiranSheet.getLastRow() - 1, lastColKehadiran).clearContent();
    }
    if (siswaList.length > 0) {
      var newKehadiranValues = [];
      for (var i = 0; i < siswaList.length; i++) {
        var nisn = siswaList[i].nisn;
        var defaultKehadiran = [0, 0, 0, "Pramuka", "A", "-", "-", "-", "-", "Pertahankan prestasimu dan teruslah belajar dengan giat."];
        var oldKehadiran = oldKehadiranMap[nisn] || defaultKehadiran;

        var row = [nisn];
        for (var j = 0; j < oldKehadiran.length; j++) {
          row.push(oldKehadiran[j]);
        }
        newKehadiranValues.push(row);
      }
      kehadiranSheet.getRange(2, 1, newKehadiranValues.length, 11).setValues(newKehadiranValues);
    }
  }
}

/**
 * Menyimpan Guru & Kredensial Pengguna (Hanya Admin).
 */
function saveDataGuru(session, guruList, userList) {
  if (!verifySession(session)) {
    throw new Error("Sesi tidak valid.");
  }
  if (session.role !== 'Admin') {
    throw new Error("Akses ditolak.");
  }

  var ss = SpreadsheetApp.getActiveSpreadsheet();
  
  // Simpan Guru_Mapel
  var guruSheet = ss.getSheetByName("Guru_Mapel");
  if (guruSheet) {
    var lastColGuru = guruSheet.getLastColumn();
    if (lastColGuru < 5) {
      setupDatabase();
      lastColGuru = 5;
    }
    if (guruSheet.getLastRow() > 1) {
      guruSheet.getRange(2, 1, guruSheet.getLastRow() - 1, 5).clearContent();
    }
    if (guruList.length > 0) {
      var values = [];
      for (var i = 0; i < guruList.length; i++) {
        values.push([
          guruList[i].idGuru,
          guruList[i].namaGuru,
          guruList[i].nip,
          guruList[i].mapel,
          guruList[i].kelas || ""
        ]);
      }
      guruSheet.getRange(2, 1, values.length, 5).setValues(values);
    }
  }

  // Simpan Users (tanpa menimpa password jika tidak berubah)
  var usersSheet = ss.getSheetByName("Users");
  if (usersSheet) {
    var lastColUsers = usersSheet.getLastColumn();
    if (lastColUsers < 6) {
      setupDatabase();
      lastColUsers = 6;
    }
    var oldUsersMap = {};
    if (usersSheet.getLastRow() > 1) {
      var rows = usersSheet.getRange(2, 1, usersSheet.getLastRow() - 1, 6).getValues();
      for (var i = 0; i < rows.length; i++) {
        oldUsersMap[rows[i][0]] = rows[i][1];
      }
    }

    if (usersSheet.getLastRow() > 1) {
      usersSheet.getRange(2, 1, usersSheet.getLastRow() - 1, 6).clearContent();
    }

    if (userList.length > 0) {
      var values = [];
      for (var i = 0; i < userList.length; i++) {
        var username = userList[i].username;
        var role = userList[i].role;
        var namaLengkap = userList[i].namaLengkap;
        var mapel = userList[i].mapelDiampu;
        var kelasDiampu = userList[i].kelasDiampu || "";
        
        var passwordHash = "";
        if (userList[i].password && userList[i].password.trim() !== "") {
          passwordHash = hashPassword(userList[i].password.trim());
        } else {
          passwordHash = oldUsersMap[username] || hashPassword("guru123");
        }
        values.push([username, passwordHash, role, namaLengkap, mapel, kelasDiampu]);
      }
      usersSheet.getRange(2, 1, values.length, 6).setValues(values);
    }
  }

  return "Data guru dan pengguna berhasil diperbarui!";
}

/**
 * Menyimpan pengaturan Data Sekolah (Hanya Admin).
 */
function saveDataSekolah(session, sekolahData) {
  if (!verifySession(session)) {
    throw new Error("Sesi tidak valid.");
  }
  if (session.role !== 'Admin') {
    throw new Error("Akses ditolak.");
  }

  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName("Data_Sekolah");
  if (!sheet) throw new Error("Sheet Data_Sekolah tidak ditemukan.");

  var lastCol = sheet.getLastColumn();
  if (lastCol < 15) {
    setupDatabase();
    lastCol = sheet.getLastColumn();
  }

  if (sheet.getLastRow() > 1) {
    sheet.getRange(2, 1, sheet.getLastRow() - 1, 15).clearContent();
  }

  sheet.getRange(2, 1, 1, 15).setValues([[
    sekolahData.namaSekolah || "",
    sekolahData.alamat || "",
    sekolahData.kepalaSekolah || "",
    sekolahData.nipKepsek || "",
    sekolahData.semester || "",
    sekolahData.tahunPelajaran || "",
    sekolahData.logoSekolah || "",
    sekolahData.jenisAsesmen || "Asesmen Sumatif Tengah Semester (ASTS)",
    sekolahData.yayasan || "",
    sekolahData.akreditasi || "",
    sekolahData.nsm || "",
    sekolahData.npsn || "",
    sekolahData.npwp || "",
    sekolahData.aktaNotaris || "",
    sekolahData.email || ""
  ]]);

  return "Pengaturan sekolah berhasil disimpan!";
}

/**
 * Menyimpan Leger Nilai secara dinamis.
 */
function saveLegerNilai(session, legerList) {
  if (!verifySession(session)) {
    throw new Error("Sesi tidak valid.");
  }

  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName("Leger_Nilai");
  if (!sheet) throw new Error("Sheet Leger_Nilai tidak ditemukan.");

  var lastRow = sheet.getLastRow();
  if (lastRow <= 1) {
    throw new Error("Tidak ada data siswa untuk diberi nilai. Silakan tambahkan siswa terlebih dahulu.");
  }

  // Ambil mapelHeaders secara dinamis dari kolom
  var lastCol = sheet.getLastColumn();
  var mapelHeaders = [];
  if (lastCol > 4) {
    var headersRow = sheet.getRange(1, 1, 1, lastCol).getValues()[0];
    for (var col = 2; col < headersRow.length - 2; col++) {
      mapelHeaders.push(headersRow[col]);
    }
  }

  var range = sheet.getRange(2, 1, lastRow - 1, lastCol);
  var dbValues = range.getValues();

  var dbMap = {};
  for (var i = 0; i < dbValues.length; i++) {
    dbMap[dbValues[i][0]] = {
      index: i,
      row: dbValues[i]
    };
  }

  for (var k = 0; k < legerList.length; k++) {
    var item = legerList[k];
    var dbItem = dbMap[item.nisn];
    if (dbItem) {
      var rowData = dbItem.row;
      
      var sum = 0;
      var count = 0;

      for (var m = 0; m < mapelHeaders.length; m++) {
        var mapel = mapelHeaders[m];
        var colIndex = 2 + m;

        if (session.role === 'Admin') {
          var val = Number(item.nilai[mapel]);
          rowData[colIndex] = isNaN(val) ? 0 : val;
        } else if (session.role === 'Guru') {
          if (session.mapelDiampu === mapel) {
            var val = Number(item.nilai[mapel]);
            rowData[colIndex] = isNaN(val) ? 0 : val;
          }
        }
        
        sum += Number(rowData[colIndex]) || 0;
        count++;
      }

      var rata = count > 0 ? (sum / count) : 0;
      rowData[lastCol - 2] = sum; // Jumlah
      rowData[lastCol - 1] = Math.round(rata * 100) / 100; // Rata-rata
    }
  }

  var finalValues = [];
  for (var i = 0; i < dbValues.length; i++) {
    finalValues.push(dbValues[i]);
  }
  range.setValues(finalValues);

  return "Nilai berhasil disimpan!";
}

/**
 * Menyimpan Kehadiran & Ekskul (Hanya Admin).
 */
function saveKehadiran(session, kehadiranList) {
  if (!verifySession(session)) {
    throw new Error("Sesi tidak valid.");
  }
  
  var isWaliKelas = session.role === 'Guru' && session.kelasDiampu && session.kelasDiampu.trim() !== '';
  if (session.role !== 'Admin' && !isWaliKelas) {
    throw new Error("Akses ditolak. Hanya Wali Kelas atau Admin yang dapat mengisi kehadiran & catatan.");
  }

  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName("Kehadiran_Ekstra");
  if (!sheet) throw new Error("Sheet Kehadiran_Ekstra tidak ditemukan.");

  var lastRow = sheet.getLastRow();
  if (lastRow <= 1) {
    throw new Error("Tidak ada data siswa untuk di-update.");
  }

  var lastCol = sheet.getLastColumn();
  if (lastCol < 11) {
    setupDatabase();
    lastCol = sheet.getLastColumn();
  }

  var range = sheet.getRange(2, 1, lastRow - 1, 11);
  var dbValues = range.getValues();

  var dbMap = {};
  for (var i = 0; i < dbValues.length; i++) {
    dbMap[dbValues[i][0]] = i;
  }

  // Proteksi Sisi Server: Dapatkan NISN siswa di kelas yang diampu Wali Kelas
  var allowedNisns = null;
  if (session.role !== 'Admin' && isWaliKelas) {
    var siswaSheet = ss.getSheetByName("Data_Siswa");
    if (siswaSheet && siswaSheet.getLastRow() > 1) {
      var siswaRows = siswaSheet.getRange(2, 1, siswaSheet.getLastRow() - 1, 5).getValues();
      allowedNisns = {};
      for (var i = 0; i < siswaRows.length; i++) {
        if (String(siswaRows[i][3]) === session.kelasDiampu) {
          allowedNisns[siswaRows[i][1]] = true;
        }
      }
    }
  }

  for (var k = 0; k < kehadiranList.length; k++) {
    var item = kehadiranList[k];
    
    // Jika Wali Kelas, pastikan NISN termasuk dalam kelasnya
    if (allowedNisns && !allowedNisns[item.nisn]) {
      continue;
    }
    
    var idx = dbMap[item.nisn];
    if (idx !== undefined) {
      dbValues[idx][1] = Number(item.sakit) || 0;
      dbValues[idx][2] = Number(item.izin) || 0;
      dbValues[idx][3] = Number(item.alpa) || 0;
      dbValues[idx][4] = item.ekskul1 || '-';
      dbValues[idx][5] = item.nilaiEks1 || '-';
      dbValues[idx][6] = item.ekskul2 || '-';
      dbValues[idx][7] = item.nilaiEks2 || '-';
      dbValues[idx][8] = item.ekskul3 || '-';
      dbValues[idx][9] = item.nilaiEks3 || '-';
      dbValues[idx][10] = item.catatanWali || '';
    }
  }

  range.setValues(dbValues);
  return "Data kehadiran dan ekstrakurikuler berhasil disimpan!";
}

/**
 * Menyimpan nama kustom mapel & KKTP secara dinamis.
 */
function saveMapelConfig(session, mapelNamesObj, mapelKktpObj) {
  if (!verifySession(session)) {
    throw new Error("Sesi tidak valid.");
  }
  if (session.role !== 'Admin') {
    throw new Error("Akses ditolak.");
  }

  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var sheet = ss.getSheetByName("Mapel_Config");
  if (!sheet) {
    setupDatabase();
    sheet = ss.getSheetByName("Mapel_Config");
  }

  if (sheet.getLastRow() > 1) {
    sheet.getRange(2, 1, sheet.getLastRow() - 1, 3).clearContent();
  }

  var values = [];
  for (var code in mapelNamesObj) {
    var name = mapelNamesObj[code] || code;
    var kktp = Number(mapelKktpObj ? mapelKktpObj[code] : 75) || 75;
    values.push([code, name, kktp]);
  }
  if (values.length > 0) {
    sheet.getRange(2, 1, values.length, 3).setValues(values);
  }

  return "Pengaturan mata pelajaran & KKTP berhasil disimpan!";
}

/**
 * Menambahkan Mata Pelajaran Baru (Hanya Admin).
 * Menyisipkan kolom baru di Leger_Nilai sebelum kolom Jumlah.
 */
function addMataPelajaran(session, code, name, kktp) {
  if (!verifySession(session)) {
    throw new Error("Sesi tidak valid.");
  }
  if (session.role !== 'Admin') {
    throw new Error("Akses ditolak.");
  }

  var cleanCode = code.trim().toUpperCase().replace(/[^A-Z0-9_]/g, '_');
  if (cleanCode === "") {
    throw new Error("Kode mapel tidak valid.");
  }

  var ss = SpreadsheetApp.getActiveSpreadsheet();
  var configSheet = ss.getSheetByName("Mapel_Config");
  if (!configSheet) throw new Error("Sheet Mapel_Config tidak ditemukan.");

  // Periksa duplikasi
  if (configSheet.getLastRow() > 1) {
    var existingCodes = configSheet.getRange(2, 1, configSheet.getLastRow() - 1, 1).getValues();
    for (var i = 0; i < existingCodes.length; i++) {
      if (String(existingCodes[i][0]).toUpperCase() === cleanCode) {
        throw new Error("Kode mapel '" + cleanCode + "' sudah terdaftar!");
      }
    }
  }

  // 1. Tambah baris di Mapel_Config
  configSheet.appendRow([cleanCode, name.trim(), Number(kktp) || 75]);

  // 2. Sisipkan kolom di Leger_Nilai
  var legerSheet = ss.getSheetByName("Leger_Nilai");
  if (legerSheet) {
    var lastCol = legerSheet.getLastColumn();
    // Kolom 'Jumlah' berada di lastCol - 1, kita sisipkan di sana
    legerSheet.insertColumnBefore(lastCol - 1);
    
    // Set Header
    legerSheet.getRange(1, lastCol - 1).setValue(cleanCode).setFontWeight("bold").setBackground("#EEF2F6");
    
    // Set nilai default 0 untuk baris siswa
    var lastRow = legerSheet.getLastRow();
    if (lastRow > 1) {
      var range = legerSheet.getRange(2, lastCol - 1, lastRow - 1, 1);
      var vals = [];
      for (var r = 0; r < lastRow - 1; r++) {
        vals.push([0]);
      }
      range.setValues(vals);
      
      // Rekalkulasi Jumlah & Rata-rata agar tetap konsisten dengan mapel baru
      recalculateLeger(ss);
    }
  }

  return "Mata pelajaran '" + cleanCode + "' berhasil ditambahkan!";
}

/**
 * Menghapus Mata Pelajaran (Hanya Admin).
 * Menghapus kolom mapel di Leger_Nilai secara aman dan membersihkan penugasan guru.
 */
function deleteMataPelajaran(session, code) {
  if (!verifySession(session)) {
    throw new Error("Sesi tidak valid.");
  }
  if (session.role !== 'Admin') {
    throw new Error("Akses ditolak.");
  }

  var cleanCode = code.trim().toUpperCase();
  var ss = SpreadsheetApp.getActiveSpreadsheet();

  // 1. Hapus kolom di Leger_Nilai
  var legerSheet = ss.getSheetByName("Leger_Nilai");
  if (legerSheet) {
    var lastCol = legerSheet.getLastColumn();
    var headers = legerSheet.getRange(1, 1, 1, lastCol).getValues()[0];
    var colIndex = -1;
    for (var col = 2; col < headers.length - 2; col++) {
      if (String(headers[col]).toUpperCase() === cleanCode) {
        colIndex = col + 1; // 1-based index
        break;
      }
    }
    if (colIndex !== -1) {
      legerSheet.deleteColumn(colIndex);
      
      // Rekalkulasi Jumlah & Rata-rata agar data mapel yang dihapus tidak tertinggal
      recalculateLeger(ss);
    }
  }

  // 2. Hapus baris di Mapel_Config
  var configSheet = ss.getSheetByName("Mapel_Config");
  if (configSheet && configSheet.getLastRow() > 1) {
    var rows = configSheet.getRange(2, 1, configSheet.getLastRow() - 1, 2).getValues();
    for (var i = 0; i < rows.length; i++) {
      if (String(rows[i][0]).toUpperCase() === cleanCode) {
        configSheet.deleteRow(i + 2);
        break;
      }
    }
  }

  // 3. Bersihkan penugasan guru di Users
  var usersSheet = ss.getSheetByName("Users");
  if (usersSheet && usersSheet.getLastRow() > 1) {
    var userRows = usersSheet.getRange(2, 1, usersSheet.getLastRow() - 1, usersSheet.getLastColumn()).getValues();
    for (var i = 0; i < userRows.length; i++) {
      if (String(userRows[i][4]).toUpperCase() === cleanCode) {
        usersSheet.getRange(i + 2, 5).setValue("");
      }
    }
  }

  // 4. Bersihkan penugasan di Guru_Mapel
  var guruSheet = ss.getSheetByName("Guru_Mapel");
  if (guruSheet && guruSheet.getLastRow() > 1) {
    var guruRows = guruSheet.getRange(2, 1, guruSheet.getLastRow() - 1, guruSheet.getLastColumn()).getValues();
    for (var i = 0; i < guruRows.length; i++) {
      if (String(guruRows[i][3]).toUpperCase() === cleanCode) {
        guruSheet.getRange(i + 2, 4).setValue("");
      }
    }
  }

  return "Mata pelajaran '" + cleanCode + "' berhasil dihapus!";
}

/**
 * Rekalkulasi jumlah dan rata-rata leger secara baris-per-baris (dinamis).
 */
function recalculateLeger(ss) {
  var legerSheet = ss.getSheetByName("Leger_Nilai");
  if (!legerSheet) return;
  var lastRow = legerSheet.getLastRow();
  var lastCol = legerSheet.getLastColumn();
  if (lastRow <= 1 || lastCol <= 4) return;
  
  var range = legerSheet.getRange(2, 1, lastRow - 1, lastCol);
  var values = range.getValues();
  
  for (var i = 0; i < values.length; i++) {
    var row = values[i];
    var sum = 0;
    var count = 0;
    // Kolom mapel berada di kolom index 2 sampai lastCol - 3
    for (var col = 2; col < lastCol - 2; col++) {
      var val = Number(row[col]) || 0;
      sum += val;
      count++;
    }
    var rata = count > 0 ? (sum / count) : 0;
    row[lastCol - 2] = sum; // Kolom Jumlah
    row[lastCol - 1] = Math.round(rata * 100) / 100; // Kolom Rata-rata
  }
  
  range.setValues(values);
}

/**
 * Mendapatkan informasi sekolah publik secara cepat (untuk portal login ter-branding).
 */
function getPublicSchoolInfo() {
  try {
    var ss = SpreadsheetApp.getActiveSpreadsheet();
    if (!ss) return null;
    var sheet = ss.getSheetByName("Data_Sekolah");
    if (sheet && sheet.getLastRow() > 1) {
      var lastCol = sheet.getLastColumn();
      var row = sheet.getRange(2, 1, 1, Math.max(lastCol, 8)).getValues()[0];
      return {
        namaSekolah: row[0] || "E-Rapor Sekolah",
        alamat: row[1] || "",
        logoSekolah: row[6] || "https://upload.wikimedia.org/wikipedia/commons/9/9c/Logo_Kementerian_Pendidikan_dan_Kebudayaan.png",
        jenisAsesmen: row[7] || "Asesmen Sumatif Tengah Semester (ASTS)"
      };
    }
  } catch (e) {
    // Abaikan jika database belum setup
  }
  return {
    namaSekolah: "E-Rapor",
    alamat: "",
    logoSekolah: "https://upload.wikimedia.org/wikipedia/commons/9/9c/Logo_Kementerian_Pendidikan_dan_Kebudayaan.png",
    jenisAsesmen: "Asesmen Sumatif Tengah Semester (ASTS)"
  };
}

/**
 * Mengunggah logo sekolah kustom ke Google Drive pengguna dan mengembalikan URL publik.
 */
function uploadSchoolLogo(session, base64Data, fileName) {
  if (!verifySession(session)) {
    throw new Error("Sesi tidak valid.");
  }
  if (session.role !== 'Admin') {
    throw new Error("Akses ditolak.");
  }
  
  try {
    if (!base64Data || !base64Data.startsWith("data:image/")) {
      throw new Error("Format gambar tidak valid.");
    }
    
    return {
      success: true,
      url: base64Data
    };
  } catch(e) {
    throw new Error("Gagal memproses logo: " + e.toString());
  }
}
