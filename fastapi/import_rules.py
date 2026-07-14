import pandas as pd
from sqlalchemy import create_engine

# ==========================
# KONEKSI DATABASE
# ==========================
engine = create_engine(
    "mysql+pymysql://root:@localhost/db11"
)

# ==========================
# HASIL UTAMA
# ==========================
files_utama = [
    ("rules_ruangan_ok.csv", "RUANGAN_OK"),
    ("rules_ruangan_seluruh.csv", "RUANGAN_SELURUH"),
    ("rules_tgl_ok.csv", "TANGGAL_OK"),
    ("rules_tgl_seluruh.csv", "TANGGAL_SELURUH")
]

for file_name, jenis in files_utama:

    print(f"Import {file_name}")

    df = pd.read_csv(file_name)

    df = df[[
        "antecedents",
        "consequents",
        "support",
        "confidence",
        "lift"
    ]]

    df.rename(columns={
        "antecedents": "antecedent",
        "consequents": "consequent"
    }, inplace=True)

    df["jenis_analisis"] = jenis
    df["jumlah_transaksi"] = None
    df["sumber_file"] = file_name

    df = df[
        [
            "jenis_analisis",
            "antecedent",
            "consequent",
            "support",
            "confidence",
            "lift",
            "jumlah_transaksi",
            "sumber_file"
        ]
    ]

    df.to_sql(
        "tb_hasil_fp_growth",
        con=engine,
        if_exists="append",
        index=False
    )

# ==========================
# EKSPERIMEN NAMA ALAT
# ==========================
files_alat = [
    ("rules_ruangan_ok_all.csv", "RUANGAN_OK_ALAT"),
    ("rules_ruangan_seluruh_all.csv", "RUANGAN_SELURUH_ALAT"),
    ("rules_tgl_ok_all.csv", "TANGGAL_OK_ALAT"),
    ("rules_tgl_seluruh_all.csv", "TANGGAL_SELURUH_ALAT")
]

for file_name, jenis in files_alat:

    print(f"Import {file_name}")

    df = pd.read_csv(file_name)

    df = df[[
        "antecedents",
        "consequents",
        "support",
        "confidence",
        "lift"
    ]]

    df.rename(columns={
        "antecedents": "antecedent",
        "consequents": "consequent"
    }, inplace=True)

    df["jenis_analisis"] = jenis
    df["jumlah_transaksi"] = None
    df["sumber_file"] = file_name

    df = df[
        [
            "jenis_analisis",
            "antecedent",
            "consequent",
            "support",
            "confidence",
            "lift",
            "jumlah_transaksi",
            "sumber_file"
        ]
    ]

    df.to_sql(
        "tb_hasil_fp_growth_alat",
        con=engine,
        if_exists="append",
        index=False
    )

print("================================")
print("IMPORT SEMUA FILE BERHASIL")
print("================================")