import pandas as pd
from sqlalchemy import create_engine, text
import os

# ==========================================
# KONEKSI DATABASE
# ==========================================

engine = create_engine(
    "mysql+pymysql://root:@localhost/db11"
)

# ==========================================
# MAPPING USERNAME
# ==========================================

USERNAME_MAPPING = {

    # Username yang sudah sesuai
    "cipto": "cipto",
    "riput": "riput",
    "edial": "edial",
    "icon": "icon",
    "sukri": "sukri",
    "harba": "harba",
    "sairespen": "sairespen",

    # Alias
    "rian": "rian",
    "eko": "eko"

}

# ==========================================
# MEMBACA FILE EXCEL
# ==========================================

def baca_excel(file_excel):

    print("=" * 60)
    print("MEMBACA FILE EXCEL...")
    print("=" * 60)

    # Tidak menggunakan header
    df = pd.read_excel(file_excel, header=None)

    # Data sebenarnya mulai dari baris ke-4
    df = df.iloc[4:].reset_index(drop=True)

    print(f"Jumlah Data : {len(df)}")

    return df

# ==========================================
# AMBIL DATA USER
# ==========================================

# ==========================================
# AMBIL DATA USER
# ==========================================

def ambil_user():

    query = """
    SELECT
        id_user,
        username
    FROM tb_user
    """

    df = pd.read_sql(query, engine)

    users = {}

    for _, row in df.iterrows():

        username = str(row["username"]).strip().lower()

        users[username] = row["id_user"]

    return users

# ==========================================
# PREVIEW SINKRONISASI
# ==========================================

def preview_sinkronisasi(df_excel, users):

    berhasil = 0
    gagal = 0

    username_tidak_ada = set()

    hasil = []

    for _, row in df_excel.iterrows():

        # Lewati baris kosong
        if pd.isna(row.iloc[0]):
            continue

        nomor = str(row.iloc[0]).strip()
        
        if nomor == "" or nomor.lower() == "nan":
            continue

        # Jika nama teknisi kosong
        if pd.isna(row.iloc[8]):
            continue

        username_excel = str(row.iloc[8]).strip().lower()

        username = USERNAME_MAPPING.get(
            username_excel,
            username_excel
        )

        if username in users:

            berhasil += 1

            hasil.append({

                "nomor_laporan": nomor,
                "id_teknisi": users[username],
                "username": username

            })

        else:

            gagal += 1
            username_tidak_ada.add(username_excel)

    print("=" * 60)
    print("HASIL PREVIEW")
    print("=" * 60)

    print(f"Total Excel             : {len(df_excel)}")
    print(f"Berhasil Dimapping      : {berhasil}")
    print(f"Gagal Username          : {gagal}")

    if username_tidak_ada:

        print("\nUsername tidak ditemukan:")

        for u in sorted(username_tidak_ada):

            print(" -", u)

    return hasil

# ==========================================
# BULK UPDATE ID TEKNISI
# ==========================================

def bulk_update(hasil):

    query = text("""
        UPDATE tb_laporan
        SET id_teknisi = :id_teknisi
        WHERE nomor_laporan = :nomor_laporan
          AND id_teknisi IS NULL
    """)

    with engine.begin() as conn:

        result = conn.execute(query, hasil)

    print("\n" + "=" * 60)
    print("HASIL UPDATE DATABASE")
    print("=" * 60)

    print(f"Total Mapping      : {len(hasil)}")
    print(f"Berhasil Update    : {result.rowcount}")

    if len(hasil) != result.rowcount:
        print(f"Gagal Update       : {len(hasil) - result.rowcount}")

    print("=" * 60)

# ==========================================
# MAIN PROGRAM
# ==========================================

if __name__ == "__main__":

    file_excel = input("Masukkan lokasi file Excel : ").strip()

    if not os.path.exists(file_excel):

        print("\nFile tidak ditemukan.")
        exit()

    df = baca_excel(file_excel)

    print("\nPreview Data:\n")

    users = ambil_user()

    hasil = preview_sinkronisasi(
        df,
        users
    )

    print("\nContoh hasil mapping:\n")

    for item in hasil[:10]:
        print(item)

    print("\n")

    jawaban = input("Lanjut update database? (y/n) : ").strip().lower()

    if jawaban == "y":

        bulk_update(hasil)

    else:

        print("\nUpdate dibatalkan.")
