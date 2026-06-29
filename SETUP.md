## Langkah-langkah Setup

### 1. Rename Environment File
```bash
rename env jadi .env
```

### 2. Konfigurasi Database di `.env`

Ubah bagian database sesuai dengan kredensial Anda:

```ini
database.default.hostname = 52.237.90.199
database.default.database = kantin_<NPM_Mahasiswa>
database.default.username = <NPM_Mahasiswa>
database.default.password = <Password>
database.default.DBDriver = MySQLi
database.default.DBPrefix =
database.default.port = 3306
```

### 3. Install Dependencies
```bash
composer install
```

### 4. Jalankan Migration
```bash
php spark migrate
```

### 5. Seed Database
```bash
php spark db:seed UserSeeder
```

**Sample Akun Admin Default:**
- Username: `Admin`
- Password: `Kantin123456UPB`

## Menjalankan Aplikasi

```bash
php spark serve
```

Aplikasi dapat diakses di: http://localhost:8080



## Dokumentasi Perintah - Perintah Umum

```bash
# Migration
php spark migrate
php spark migrate:rollback

# Seeder
php spark db:seed UserSeeder

# Generate Code
php spark make:model ModelName
php spark make:controller ControllerName
php spark make:migration MigrationName
```

<!-- !Semangat! -->