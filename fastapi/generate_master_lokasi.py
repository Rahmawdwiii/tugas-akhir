import pandas as pd
import pymysql

DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "db11"
}

OUTPUT_FILE = "master_lokasi_review.xlsx"

def koneksi_db():
    return pymysql.connect(
        host=DB_CONFIG["host"],
        user=DB_CONFIG["user"],
        password=DB_CONFIG["password"],
        database=DB_CONFIG["db11"],
        charset="utf8mb4",
        cursorclass=pymysql.cursors.DictCursor
    )
    
def ambil_data():

    conn = koneksi_db()
    cursor = conn.cursor()

    cursor.execute("""
        SELECT DISTINCT
            l.unit,
            l.lokasi,
            u.id_unit
        FROM tb_laporan l
        LEFT JOIN tb_master_unit u
            ON UPPER(TRIM(u.nama_unit))
            =
            UPPER(TRIM(l.unit))
        ORDER BY l.unit,l.lokasi
    """)

    data = cursor.fetchall()

    cursor.close()
    conn.close()

    return pd.DataFrame(data)

def normalisasi(df):

    df["kampus"] = "Kampus Utama"

    df["gedung"] = ""
    df["lantai"] = ""
    df["ruangan"] = ""

    return df

def export_excel(df):

    df.to_excel(
        OUTPUT_FILE,
        index=False
    )

    print("="*60)
    print("MASTER LOKASI BERHASIL DIBUAT")
    print("="*60)
    print(OUTPUT_FILE)
    
if __name__ == "__main__":

    df = ambil_data()

    print(df.head())

    df = normalisasi(df)

    export_excel(df)