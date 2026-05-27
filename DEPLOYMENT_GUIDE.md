# Setup Deployment Otomatis ke Hosting via SSH

Panduan ini akan membantu Anda mengkonfigurasi deployment otomatis setiap kali Anda push ke GitHub.

---

## 📋 Langkah 1: Buat Repository di GitHub

1. Buka [GitHub.com](https://github.com) dan login
2. Klik **New Repository** → beri nama `erapot`
3. **Jangan** centang "Add a README file"
4. Copy repository URL (HTTPS atau SSH)

---

## 📋 Langkah 2: Initialize Git di Project

```bash
cd "c:\laragon\www\E RAPOT"

# Initialize git
git init

# Add remote repository
git remote add origin https://github.com/USERNAME/erapot.git
# Atau gunakan SSH:
# git remote add origin git@github.com:USERNAME/erapot.git
```

---

## 📋 Langkah 3: Generate SSH Key untuk Deployment

Di komputer Anda (PowerShell/CMD):

```bash
# Generate SSH key (tanpa passphrase)
ssh-keygen -t rsa -b 4096 -C "deployment@erapot" -f ~/.ssh/erapot_deploy

# Tampilkan public key
cat ~/.ssh/erapot_deploy.pub
```

---

## 📋 Langkah 4: Tambahkan SSH Public Key ke Hosting

### Untuk cPanel/Hostinger/Niagahoster:
1. Login ke **cPanel** hosting Anda
2. Buka **SSH Access** atau **Terminal**
3. Atau buka **File Manager** → `public_html/.ssh/authorized_keys`
4. Paste public key ke file tersebut

### Atau via command line hosting:
```bash
mkdir -p ~/.ssh
echo "PASTE_PUBLIC_KEY_ANDA_DISINI" >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

---

## 📋 Langkah 5: Tambahkan Secrets di GitHub Repository

1. Buka repository GitHub Anda → **Settings** → **Secrets and variables** → **Actions**
2. Klik **New repository secret** dan tambahkan:

| Secret Name | Value |
|-------------|-------|
| `APP_KEY` | `${{ secrets.APP_KEY }}` ← Generate dulu |
| `SSH_PRIVATE_KEY` | Isi dengan **private key** dari `erapot_deploy` |
| `SSH_HOST` | Domain hosting Anda (contoh: `niagahoster.com` atau `IP server`) |
| `SSH_USER` | Username SSH hosting |
| `DEPLOY_PATH` | `/home/username/public_html/erapot` |
| `DB_HOST` | `localhost` atau host MySQL |
| `DB_PORT` | `3306` |
| `DB_DATABASE` | `erapot` |
| `DB_USERNAME` | Username database |
| `DB_PASSWORD` | Password database |
| `APP_URL` | `https://erapot.domain.com` |

### Generate APP_KEY baru:
```bash
php artisan key:generate --show
```
Copy output-nya ke GitHub Secrets sebagai `APP_KEY`.

---

## 📋 Langkah 6: Generate APP_KEY untuk Production

```bash
# Generate APP_KEY baru
php artisan key:generate
```

---

## 📋 Langkah 7: Push Code ke GitHub

```bash
# Add semua file (kecuali yang di .gitignore)
git add .

# Commit
git commit -m "Initial commit - Setup E-RAPOT"

# Push ke GitHub
git branch -M main
git push -u origin main
```

---

## 📋 Langkah 8: Setup Deployment Script di Hosting

SSH ke hosting Anda, lalu jalankan:

```bash
cd ~/public_html/erapot

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Set permissions
chmod -R 775 storage bootstrap/cache
chmod -R 777 storage/framework/cache storage/framework/views storage/framework/sessions storage/logs

# Clear cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## ✅ Testing Deployment

1. Edit file `app/Models/Siswa.php` (tambahkan komentar)
2. Commit dan push:
   ```bash
   git add .
   git commit -m "Test deployment"
   git push
   ```
3. Buka **Actions** tab di GitHub repository
4. Lihat progress deployment
5. Cek website Anda - harusnya sudah ter-update!

---

## 🔧 Troubleshooting

### Error: Permission denied (publickey)
- Pastikan SSH public key sudah ditambahkan ke hosting
- Pastikan `authorized_keys` memiliki permissions `600`

### Error: Connection refused
- Pastikan SSH credentials benar
- Pastikan SSH port adalah 22 (bukan 2222 untuk some hosting)

### Error: Database connection failed
- Pastikan secrets `DB_*` di GitHub sudah benar
- Pastikan database sudah dibuat di hosting

---

## 📁 Struktur File Deployment

```
.github/workflows/deploy.yml  ← GitHub Actions workflow
public_html/erapot/          ← Lokasi project di hosting
├── app/
├── bootstrap/
├── config/
├── public/
├── resources/
├── storage/
├── vendor/
└── .env                     ← Tidak di-commit (dari secrets)
```

---

## 🎉 Selamat!

Setiap kali Anda push ke branch `main`, GitHub Actions akan:
1. ✅ Install dependencies (NPM + Composer)
2. ✅ Build assets
3. ✅ Upload via RSYNC ke hosting
4. ✅ Install dependencies di hosting
5. ✅ Clear & rebuild Laravel caches
6. ✅ Set permissions yang benar