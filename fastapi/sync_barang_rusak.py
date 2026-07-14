import pandas as pd
import pymysql
from datetime import datetime

DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "db11"
}

FILE_RUSAK = r"D:\FP_IMPORT\UPT-TP3A (BERAT).xlsx"

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
    
def bersihkan_nomor(value):

    if pd.isna(value):
        return None

    try:
        return str(int(float(value)))
    except:
        return str(value).strip()
    
def baca_excel():

    df = pd.read_excel(
        FILE_RUSAK,
        header=1
    )

    df.columns = [
        "nomor_laporan",
        "tanggal",
        "nama_alat",
        "nomor_inventaris",
        "lokasi",
        "unit",
        "status_kerusakan",
        "pelaksana",
        "validasi_kepala",
        "validasi_pelaksana"
    ]
    
    df["nomor_laporan"] = df["nomor_laporan"].apply(bersihkan_nomor)
    df = df.dropna(subset=["nomor_laporan"])
    
    df = df[
        df["nomor_laporan"].str.isnumeric()
    ]
    
    df = df.drop_duplicates(
        subset="nomor_laporan"
    )
    
    print("=" * 60)
    print("DATA BARANG RUSAK")
    print("=" * 60)
    print("Jumlah data :", len(df))
    print(df.head())

    return df

def sync_barang_rusak(df):

    conn = koneksi_db()
    cursor = conn.cursor()
    conn.begin()

    berhasil = 0
    gagal = 0

    print("\n" + "=" * 60)
    print("MULAI SINKRONISASI")
    print("=" * 60)

    try:

        for _, row in df.iterrows():

            nomor = row["nomor_laporan"]

            cursor.execute("""
                SELECT id_laporan
                FROM tb_laporan
                WHERE nomor_laporan = %s
            """, (nomor,))

            laporan = cursor.fetchone()

            if not laporan:
                print("=" * 60)
                print("TIDAK DITEMUKAN")
                print("=" * 60)
                print("Nomor Laporan :", nomor)
                print("Tanggal       :", row["tanggal"])
                print("Nama Alat     :", row["nama_alat"])
                print("Lokasi        :", row["lokasi"])
                print("Unit          :", row["unit"])
                print()

                gagal += 1
                continue

            id_laporan = laporan["id_laporan"]
            
            # =====================================================
            # CARI ID JADWAL
            # =====================================================

            cursor.execute("""
                SELECT
                    id_jadwal
                FROM tb_jadwal_perbaikan
                WHERE id_laporan = %s
            """, (id_laporan,))

            jadwal = cursor.fetchone()

            if not jadwal:
                print(f"BELUM ADA JADWAL : {nomor}")
                gagal += 1
                continue

            id_jadwal = jadwal["id_jadwal"]
            
            # =====================================================
            # UPDATE JADWAL
            # =====================================================

            cursor.execute("""
                UPDATE tb_jadwal_perbaikan
                SET
                    status_perbaikan = %s
                WHERE id_jadwal = %s
            """, (
                "RUSAK",
                id_jadwal
            ))
            catatan = "Tidak Ekonomis Lagi Untuk Diperbaiki"
            # =====================================================
            # UPDATE tb_perbaikan
            # ====================================================

            status_kerusakan = str(row["status_kerusakan"]).strip().upper()
            if status_kerusakan not in ["RINGAN", "SEDANG", "BERAT"]:
                status_kerusakan = "BERAT"

            cursor.execute("""
                UPDATE tb_perbaikan
                SET
                    status_kerusakan=%s,
                    hasil_perbaikan=%s,
                    catatan_teknisi=%s
                WHERE id_jadwal=%s
            """, (
                status_kerusakan,
                "RUSAK TOTAL",
                catatan,
                id_jadwal
            ))
            
            # =====================================================
            # UPDATE tb_laporan
            # =====================================================

            cursor.execute("""
                UPDATE tb_laporan
                SET
                    status_laporan=%s,
                    validasi_kepala=%s
                WHERE id_laporan=%s
            """, (
                "TIDAK DAPAT DIPERBAIKI",
                "Disetujui",
                id_laporan
            ))
            
            # =====================================================
            # CEK BARANG RUSAK
            # =====================================================

            cursor.execute("""
            SELECT id_barang_rusak
            FROM tb_barang_rusak
            WHERE id_laporan=%s
            """, (id_laporan,))

            barang = cursor.fetchone()
            
            nomor_inventaris = row["nomor_inventaris"]

            if pd.isna(nomor_inventaris) or str(nomor_inventaris).strip() == "":
                nomor_inventaris = "-"

            nama_alat = row["nama_alat"]
            lokasi = row["lokasi"]
            unit = row["unit"]
            tanggal_rusak = pd.to_datetime(row["tanggal"]).date()
            
            if barang:

                cursor.execute("""
                    UPDATE tb_barang_rusak
                    SET
                        nomor_inventaris=%s,
                        nama_alat=%s,
                        lokasi=%s,
                        unit=%s,
                        alasan_rusak=%s,
                        tanggal_rusak=%s
                    WHERE id_laporan=%s
                    """, (
                        nomor_inventaris,
                        nama_alat,
                        lokasi,
                        unit,
                        catatan,
                        tanggal_rusak,
                        id_laporan
                    ))
                
            else:

                cursor.execute("""
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
                """, (
                    id_laporan,
                    nomor,
                    nomor_inventaris,
                    nama_alat,
                    lokasi,
                    unit,
                    catatan,
                    tanggal_rusak
                ))

            print(f"{berhasil + 1}. {nomor}")

            berhasil += 1

        print("\nSemua data berhasil diproses, melakukan commit...")
        conn.commit()

    except Exception as e:
        
        conn.rollback()

        print("=" * 60)
        print("ERROR")
        print("=" * 60)
        print(e)

        raise
    finally:

        cursor.close()
        conn.close()

    print("\n" + "=" * 60)
    print("SELESAI")
    print("=" * 60)
    print("Berhasil :", berhasil)
    print("Gagal    :", gagal)
    
if __name__ == "__main__":

    df = baca_excel()

    sync_barang_rusak(df)