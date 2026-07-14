# Implementasi Algoritma Frequent Pattern Growth untuk Analisis Pola Alat yang Rusak di Unit Penunjang Akademik Perawatan dan Perbaikan Politeknik Negeri Sriwijaya

## Deskripsi

Repository ini berisi source code aplikasi Tugas Akhir yang mengimplementasikan algoritma **Frequent Pattern Growth (FP-Growth)** untuk menganalisis pola kerusakan alat pada Unit Penunjang Akademik Perawatan dan Perbaikan (UPT TP3A) Politeknik Negeri Sriwijaya.

Aplikasi dikembangkan menggunakan **CodeIgniter 4** sebagai backend utama dan **FastAPI** sebagai layanan pemrosesan algoritma FP-Growth.

---

## Tujuan

Penelitian ini bertujuan untuk:

- Mengelola data laporan kerusakan alat.
- Menganalisis pola kerusakan berdasarkan data historis.
- Menghasilkan association rules menggunakan algoritma FP-Growth.
- Membantu pengambilan keputusan dalam proses pemeliharaan dan perbaikan alat.

---

## Teknologi yang Digunakan

### Backend
- PHP 8
- CodeIgniter 4
- MySQL

### Data Mining
- Python 3
- FastAPI
- Pandas
- mlxtend

### Frontend
- Bootstrap 5
- JavaScript
- jQuery
- DataTables

---

## Struktur Repository

```
tugas-akhir/
│
├── backend-ci4/
│   ├── app/
│   ├── public/
│   ├── writable/
│   └── ...
│
├── fastapi/
│   ├── main.py
│   ├── fp_growth_service.py
│   ├── requirements.txt
│   └── ...
│
├── .gitignore
└── README.md
```

---

## Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/Rahmawdwiii/tugas-akhir.git
```

---

### 2. Menjalankan Backend (CodeIgniter 4)

Masuk ke folder backend.

```bash
cd backend-ci4
```

Install dependency.

```bash
composer install
```

Salin file environment.

```bash
cp env .env
```

Sesuaikan konfigurasi database pada file `.env`.

Jalankan aplikasi.

```bash
php spark serve
```

---

### 3. Menjalankan FastAPI

Masuk ke folder FastAPI.

```bash
cd ../fastapi
```

Install dependency.

```bash
pip install -r requirements.txt
```

Jalankan API.

```bash
uvicorn main:app --reload
```

---

## Fitur

- Manajemen laporan kerusakan.
- Manajemen data barang rusak.
- Manajemen jadwal perbaikan.
- Riwayat perbaikan.
- Dashboard monitoring.
- Analisis pola kerusakan menggunakan algoritma FP-Growth.
- Pembentukan association rules.
- Filter berdasarkan tanggal, bulan, tahun, dan unit.
- Export data.

---

## Catatan

Repository ini hanya berisi **source code** aplikasi.

Beberapa komponen tidak disertakan dalam repository ini, antara lain:

- Dataset penelitian.
- Database.
- File konfigurasi `.env`.
- File hasil pelatihan atau data internal.

Untuk menjalankan aplikasi, silakan menggunakan dataset dan database sesuai kebutuhan masing-masing.

---

## Penulis

**Rahmawati Dwi Lestari**

Program Studi Sarjana Terapan Manajemen Informatika

Politeknik Negeri Sriwijaya
