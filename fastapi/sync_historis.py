import pandas as pd
import pymysql
from datetime import datetime

# ==========================================================
# KONFIGURASI
# ==========================================================

DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "db11"
}

FILE_LAPORAN = "DATA LAPORAN KERUSAKAN 2021-2026.xlsx"
FILE_STATUS = "UPT-TP3A  Unit Pelaksana Teknis - Teknologi Permesinan dan Peralatan Penunjang Akademik (1).xlsx"


# ==========================================================
# KONEKSI DATABASE
# ==========================================================

def koneksi_db():

    return pymysql.connect(
        host=DB_CONFIG["host"],
        user=DB_CONFIG["user"],
        password=DB_CONFIG["password"],
        database=DB_CONFIG["database"],
        charset="utf8mb4",
        autocommit=False,
        cursorclass=pymysql.cursors.DictCursor
    )

# ==========================================================
# BACA EXCEL
# ==========================================================

def baca_excel():

    laporan = pd.read_excel(
        FILE_LAPORAN,
        header=2
    )

    status = pd.read_excel(
        FILE_STATUS,
        header=1
    )

    laporan.columns = [
        "nomor_laporan",
        "pelapor",
        "media",
        "tanggal",
        "nama_alat",
        "lokasi",
        "unit",
        "kerusakan",
        "teknisi",      # <-- bukan status
        "status",       # <-- status laporan
        "keterangan"
    ]

    status.columns = [
        "nomor_laporan",
        "tanggal",
        "nama_alat",
        "nomor_inventaris",
        "lokasi",
        "unit",
        "status_kerusakan",
        "pelaksana"
    ]

    laporan["nomor_laporan"] = laporan["nomor_laporan"].apply(bersihkan_nomor)
    status["nomor_laporan"] = status["nomor_laporan"].apply(bersihkan_nomor)

    laporan = laporan.dropna(subset=["nomor_laporan"])
    status = status.dropna(subset=["nomor_laporan"])
    
    laporan = laporan[
    laporan["nomor_laporan"].str.isnumeric()]

    status = status[
    status["nomor_laporan"].str.isnumeric()]
    
    laporan = laporan.drop_duplicates(
        subset="nomor_laporan"
    )

    status = status.drop_duplicates(
        subset="nomor_laporan"
    )

    return laporan, status

# ==========================================================
# NORMALISASI NOMOR LAPORAN
# ==========================================================

def bersihkan_nomor(value):

    if pd.isna(value):
        return None

    try:
        return str(int(float(value)))
    except:
        return str(value).strip()

# ==========================================================
# MERGE DATA
# ==========================================================

def merge_excel(laporan, status):

    hasil = laporan.merge(
    status[
        [
            "nomor_laporan",
            "nomor_inventaris",
            "nama_alat",
            "lokasi",
            "unit",
            "status_kerusakan"
        ]
    ],
    on="nomor_laporan",
    how="left",
    suffixes=("_laporan", "_status")
)

    print("="*60)
    print("HASIL MERGE")
    print("="*60)

    print("Data laporan :", len(laporan))
    print("Data status  :", len(status))
    print("Hasil merge  :", len(hasil))

    return hasil

# ==========================================================
# LOOKUP DATABASE
# ==========================================================

def load_laporan(cursor):

    cursor.execute("""

        SELECT

            id_laporan,
            nomor_laporan,
            id_teknisi,
            tanggal_laporan

        FROM tb_laporan

    """)

    rows = cursor.fetchall()

    hasil = {}

    for row in rows:

        hasil[str(row["nomor_laporan"]).strip()] = row

    return hasil

# ==========================================================
# MAPPING STATUS
# ==========================================================
def mapping_status(status):

    status = str(status).strip().upper()

    if status == "OK":
        return "SELESAI"

    elif status == "BELUM SELESAI DIPERBAIKI":
        return "PENDING"

    elif status == "TIDAK DAPAT DIPERBAIKI":
        return "RUSAK"

    return "SELESAI"

# ==========================================================
# INSERT HISTORIS
# ==========================================================

def insert_historis(df):

    conn = koneksi_db()
    cursor = conn.cursor()

    try:

        laporan_db = load_laporan(cursor)

        berhasil = 0
        gagal = 0

        print("\n")
        print("=" * 60)
        print("MULAI MIGRASI")
        print("=" * 60)

        for _, row in df.iterrows():

            nomor = row["nomor_laporan"]

            if nomor not in laporan_db:
                print(f"TIDAK DITEMUKAN : {nomor}")
                gagal += 1
                continue

            data_laporan = laporan_db[nomor]

            id_laporan = data_laporan["id_laporan"]
            id_teknisi = data_laporan["id_teknisi"]
            tanggal = data_laporan["tanggal_laporan"]
            
            if id_teknisi is None:
                print(f"SKIP (Belum ada teknisi): {nomor}")
                gagal += 1
                continue

            if isinstance(tanggal, str):
                tanggal = datetime.strptime(
                    tanggal,
                    "%Y-%m-%d %H:%M:%S"
                )

            status_jadwal = mapping_status(row["status"])
            
            cursor.execute("""
            SELECT id_jadwal
            FROM tb_jadwal_perbaikan
            WHERE id_laporan = %s
            """, (id_laporan,))

            if cursor.fetchone():
                print(f"SKIP (Sudah dimigrasikan): {nomor}")
                continue

            # ==================================================
            # INSERT tb_jadwal_perbaikan
            # ==================================================

            sql_jadwal = """
            INSERT INTO tb_jadwal_perbaikan
            (
                id_laporan,
                id_teknisi,
                tanggal_perbaikan,
                jenis_penugasan,
                status_perbaikan,
                created_at,
                waktu_dijadwalkan
            )
            VALUES
            (%s,%s,%s,%s,%s,%s,%s)
            """

            now = datetime.now()

            cursor.execute(
                sql_jadwal,
                (
                    id_laporan,
                    id_teknisi,
                    tanggal.date() if hasattr(tanggal, "date") else tanggal,
                    "MANUAL",
                    status_jadwal,
                    now,
                    tanggal
                )
            )

            id_jadwal = cursor.lastrowid
            
            # ==========================================
            # HASIL PERBAIKAN
            # ==========================================

            if status_jadwal == "SELESAI":
                hasil_perbaikan = "SELESAI"

            elif status_jadwal == "RUSAK":
                hasil_perbaikan = "RUSAK TOTAL"

            elif status_jadwal == "PENDING":
                hasil_perbaikan = "BELUM SELESAI"

            else:
                hasil_perbaikan = None

            # ==================================================
            # INSERT tb_perbaikan
            # ==================================================

            sql_perbaikan = """
            INSERT INTO tb_perbaikan
            (
                id_jadwal,
                status_kerusakan,
                hasil_perbaikan,
                catatan_teknisi,
                created_at
            )
            VALUES
            (%s,%s,%s,%s,%s)
            """

            status_kerusakan = row["status_kerusakan"]

            if pd.isna(status_kerusakan):

                if row["status"] == "TIDAK DAPAT DIPERBAIKI":
                    status_kerusakan = "BERAT"
                else:
                    status_kerusakan = None

            catatan = row["keterangan"]

            if pd.isna(catatan):

                if status_jadwal == "RUSAK":
                    catatan = "Tidak Ekonomis Lagi Untuk Diperbaiki"
                else:
                    catatan = None

            cursor.execute(
                sql_perbaikan,
                (
                    id_jadwal,
                    status_kerusakan,
                    hasil_perbaikan,
                    catatan,
                    now
                )
            )
            
            # ==================================================
            # INSERT tb_barang_rusak
            # ==================================================

            if status_jadwal == "RUSAK":
                
                print(f"INSERT BARANG RUSAK -> {nomor}")

                cursor.execute("""
                    SELECT id_barang_rusak
                    FROM tb_barang_rusak
                    WHERE id_laporan=%s
                """, (id_laporan,))

                if cursor.fetchone():

                    print(f"Barang rusak sudah ada : {nomor}")

                else:

                    sql_barang = """
                    INSERT INTO tb_barang_rusak
                    (
                        id_laporan,
                        nomor_laporan,
                        nomor_inventaris,
                        nama_alat,
                        lokasi,
                        unit,
                        alasan_rusak,
                        tanggal_rusak
                    )
                    VALUES
                    (%s,%s,%s,%s,%s,%s,%s,%s)
                    """

                    nomor_inventaris = row["nomor_inventaris"]

                    if pd.isna(nomor_inventaris):
                        nomor_inventaris = "-"

                    cursor.execute(
                        sql_barang,
                        (
                            id_laporan,
                            nomor,
                            nomor_inventaris,
                            row["nama_alat_laporan"],
                            row["lokasi_laporan"],
                            row["unit_laporan"],
                            catatan,
                            tanggal
                        )
                    )

            # ==========================================
            # UPDATE VALIDASI KEPALA
            # ==========================================

            if row["status"] == "BELUM SELESAI DIPERBAIKI":
                validasi = "Menunggu"
            else:
                validasi = "Disetujui"

            cursor.execute("""
                UPDATE tb_laporan
                SET validasi_kepala = %s
                WHERE id_laporan = %s
            """, (
                validasi,
                id_laporan
            ))
            
            berhasil += 1

            if berhasil <= 10:

                print(
                    f"{berhasil}. {nomor} -> OK"
                )

        conn.commit()

        print("\n")
        print("=" * 60)
        print("SELESAI")
        print("=" * 60)
        print("Berhasil :", berhasil)
        print("Gagal    :", gagal)

    except Exception as e:

        conn.rollback()

        print("\nERROR :")
        print(e)

    finally:

        cursor.close()
        conn.close()
        
# ==========================================================
# MAIN
# ==========================================================

if __name__ == "__main__":

    laporan, status = baca_excel()

    print("\n===== STATUS DARI EXCEL =====")
    print(laporan["status"].value_counts(dropna=False))

    hasil = merge_excel(laporan, status)

    print("\n===== CEK DATA RUSAK =====")

    cek = hasil[
        hasil["status"] == "TIDAK DAPAT DIPERBAIKI"
    ][[
        "nomor_laporan",
        "status",
        "status_kerusakan"
    ]]

    print(cek.head(30))

    print(
        hasil[
            [
                "nomor_laporan",
                "status",
                "status_kerusakan",
                "keterangan"
            ]
        ].head(10)
    )

    insert_historis(hasil)