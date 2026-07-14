# 🔍 DEBUG GUIDE: Rating & Ulasan Missing

## Langkah 1: Buka Browser Console
1. Buka Admin page di browser
2. Tekan **F12** untuk buka Developer Tools
3. Pergi ke tab **Console**
4. Klik tab **Validasi Akhir** untuk load data
5. Klik tombol **VALIDASI** pada salah satu kartu
6. Perhatikan log yang muncul di console (dimulai dengan "=== LAPORAN DATA VALIDATION MODAL ===")

## Langkah 2: Periksa Data yang Diterima
Di console Anda akan melihat output seperti:
```
=== LAPORAN DATA VALIDATION MODAL ===
ID: 1234
Rating Value: 4
Keluhan (Ulasan): Komputer rusak
Foto URLs: [...]
Foto Teknisi URLs: [...]
Full Laporan Object: {...}
=====================================
```

### Kemungkinan Hasil:
- **Rating Value: 0 atau undefined** → Rating belum ada di database
- **Keluhan (Ulasan): null atau empty** → Field kerusakan kosong di database
- **Foto URLs: []** → Foto pelapor tidak ada di database
- **Foto Teknisi URLs: []** → Foto teknisi tidak ada di database

## Langkah 3: Periksa Network Tab
1. Buka tab **Network** di DevTools
2. Filter "get_antrian"
3. Klik salah satu request dari tab Validasi Akhir
4. Buka tab **Response** untuk lihat JSON yang dikirim server
5. Cari field berikut:
   - `rating` (harus ada & > 0)
   - `keluhan_lengkap` (harus berisi teks)
   - `foto_urls` (array of URLs)
   - `foto_bukti_teknisi_urls` (array of URLs)

## Langkah 4: Query Database Langsung (Terakhir)
Jika data tidak ada di API response, periksa database:

```sql
-- Cek field rating_pelapor dan kerusakan
SELECT 
  nomor_laporan,
  rating_pelapor,
  kerusakan,
  path_foto_bukti,
  status_laporan
FROM tb_laporan
WHERE status_laporan IN ('MENUNGGU KONFIRMASI', 'SELESAI')
LIMIT 5;

-- Cek field foto_bukti di perbaikan
SELECT 
  id_jadwal,
  foto_bukti,
  status_kerusakan
FROM tb_perbaikan
LIMIT 5;
```

## Checklist Perbaikan:

✅ **Modal Photo Zoom** - Perbaikan: Dipindahkan ke root level, z-index ditingkatkan
- [ ] Cek: Saat klik zoom photo di validation modal, apakah foto muncul di atas?

✅ **Field Mapping** - Backend diperbaiki:
- [ ] `rating_pelapor` → dipilih sebagai `rating`
- [ ] `kerusakan` → dipilih sebagai `keluhan_lengkap` 
- [ ] `foto_bukti` → diproses sebagai `foto_bukti_teknisi_urls`

⚠️ **Data ada/tidak ada di Database** - Perlu dicek:
- [ ] Pastikan ada data di `tb_laporan.rating_pelapor` (bukan NULL/kosong)
- [ ] Pastikan ada data di `tb_laporan.kerusakan` (bukan NULL/kosong)
- [ ] Pastikan ada data di `tb_perbaikan.foto_bukti` (bukan NULL/kosong)

## Debugging Tips:

### Jika Rating tidak muncul di card:
1. Lihat console log "Rating Value"
2. Jika 0 → data tidak ada di database → insert test data dengan rating
3. Jika undefined → field `rating` tidak terkirim dari API → check Admin.php query

### Jika Ulasan tidak muncul di card:
1. Lihat console log "Keluhan (Ulasan)"
2. Jika null/empty → data tidak ada di database → check field `kerusakan`
3. Jika ada text tapi tidak muncul → check field ID di HTML modal

### Jika Foto tidak muncul:
1. Lihat console log "Foto URLs" dan "Foto Teknisi URLs"
2. Jika [] (array kosong) → tidak ada foto di database atau path salah
3. Jika ada URLs tapi tidak muncul → check HTML container IDs

## File yang Sudah Diubah:

✅ `app/Views/admin/antrian_perbaikan.php`
- Pindahkan modalFotoPreview ke root level
- Tambah z-index styling

✅ `public/admin/antrian_perbaikan.js`
- Tambah console logging di openValidateModal()
- Debug info untuk rating, ulasan, dan foto

✅ `app/Controllers/Admin.php` (sudah ada)
- SELECT include rating_pelapor, keluhan_lengkap, foto_bukti_teknisi
- Photo processing loops untuk 2 array terpisah

## Langkah Next:

1. **Buka validation modal** dan periksa console
2. **Report hasilnya** - apa yang muncul di console log?
3. **Berdasarkan hasil** - kita tahu dimana masalahnya (DB vs API vs Frontend)
