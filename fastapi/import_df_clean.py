import pandas as pd
from sqlalchemy import create_engine

engine = create_engine(
    "mysql+pymysql://root:@localhost/db11"
)

df = pd.read_csv(
    "df_clean.csv"
)

# Rename kolom CSV -> Database
df = df.rename(columns={
    "Nomor Laporan": "nomor_laporan",
    "Tanggal Laporan": "tanggal_laporan",
    "Nama": "nama_pelapor",
    "Nama Alat": "nama_alat",
    "Lokasi Alat": "lokasi",
    "Jurusan/Unit": "unit",
    "Kerusakan/Keluhan": "kerusakan",
    "Media": "media_laporan",
    "Status": "status_laporan"
})

df["tanggal_laporan"] = pd.to_datetime(
    df["tanggal_laporan"],
    format="%d/%m/%Y",
    errors="coerce"
)

# Kolom tambahan
df["id_pelapor"] = None
df["nomor_inventaris"] = None
df["komplain"] = None
df["uraian_pekerjaan"] = None
df["nama_barang"] = None
df["jumlah_barang"] = None
df["cetak_identitas_alat"] = "Tidak"
df["path_foto_bukti"] = None
df["link_pendukung"] = None
df["rating_pelapor"] = None
df["validasi_kepala"] = "Disetujui"
df["created_at"] = pd.Timestamp.now()
df["updated_at"] = pd.Timestamp.now()

# Ambil hanya kolom yang ada di tb_laporan
df = df[
    [
        "nomor_laporan",
        "tanggal_laporan",
        "id_pelapor",
        "nama_pelapor",
        "nama_alat",
        "nomor_inventaris",
        "lokasi",
        "unit",
        "kerusakan",
        "komplain",
        "media_laporan",
        "uraian_pekerjaan",
        "nama_barang",
        "jumlah_barang",
        "cetak_identitas_alat",
        "path_foto_bukti",
        "link_pendukung",
        "status_laporan",
        "rating_pelapor",
        "created_at",
        "updated_at",
        "validasi_kepala"
    ]
]

df["tanggal_laporan"] = pd.to_datetime(
    df["tanggal_laporan"],
    dayfirst=True,
    errors="coerce"
)

print(df["tanggal_laporan"].head(20))
print(df["tanggal_laporan"].dtype)

df.to_sql(
    "tb_laporan",
    con=engine,
    if_exists="append",
    index=False
)

print("Berhasil import")
print("Jumlah data:", len(df))