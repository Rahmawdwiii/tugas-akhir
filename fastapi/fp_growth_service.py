import sys
import pandas as pd

from sqlalchemy import create_engine

from mlxtend.preprocessing import TransactionEncoder
from mlxtend.frequent_patterns import fpgrowth
from mlxtend.frequent_patterns import association_rules


engine = create_engine(
    "mysql+pymysql://root:@localhost/db11"
)


jenis_analisis = sys.argv[1]
mjenis_analisis = sys.argv[1]
metode_grouping = sys.argv[2]
jenis_item = sys.argv[3]
jenis_filter = sys.argv[4]

min_support = float(sys.argv[5])
min_confidence = float(sys.argv[6])


def load_data():

    query = """
    SELECT
        tanggal_laporan,
        nama_alat,
        lokasi,
        unit,
        status_laporan
    FROM tb_laporan
    """

    return pd.read_sql(query, engine)

def buat_item_utama(df):

    if jenis_item == "alat":

        return (
            df["nama_alat"]
            .astype(str)
        )

    return (
        df["nama_alat"].astype(str)
        + " ("
        + df["lokasi"].astype(str)
        + ", "
        + df["unit"].astype(str)
        + ")"
    )


def buat_transaksi_per_ruangan(df):

    if df.empty:
        return pd.DataFrame()

    df = df.copy()

    df["tanggal_laporan"] = pd.to_datetime(
    df["tanggal_laporan"],
    errors="coerce"
)
    
    df["ITEM_UTAMA"] = buat_item_utama(df)
    
    print("\nJENIS ITEM :", jenis_item)

    print("\nCONTOH ITEM_UTAMA")
    print(df["ITEM_UTAMA"].head(10))

    keranjang = (
        df.groupby(
            [
                "tanggal_laporan",
                "lokasi",
                "unit"
            ]
        )
        .agg({
            "ITEM_UTAMA": lambda x: list(x)
        })
        .reset_index()
    )

    transaksi = []

    for _, row in keranjang.iterrows():

        item = list(set(row["ITEM_UTAMA"]))

        proses_filter_transaksi(
            item,
            transaksi
        )

    if len(transaksi) == 0:
        return pd.DataFrame()

    te = TransactionEncoder()

    te_ary = te.fit(transaksi).transform(
        transaksi
    )

    return pd.DataFrame(
        te_ary,
        columns=te.columns_
    )


def buat_transaksi_per_tanggal(df):

    if df.empty:
        return pd.DataFrame()

    df = df.copy()

    df["tanggal_laporan"] = pd.to_datetime(
    df["tanggal_laporan"],
    errors="coerce"
)
    df = df.dropna(
    subset=["tanggal_laporan"]
)
    
    df["ITEM_UTAMA"] = buat_item_utama(df)

    keranjang = (
        df.groupby(
            [
                "tanggal_laporan"
            ]
        )
        .agg({
            "ITEM_UTAMA": lambda x: list(x)
        })
        .reset_index()
    )

    transaksi = []

    for _, row in keranjang.iterrows():

        item = list(set(row["ITEM_UTAMA"]))

        proses_filter_transaksi(
            item,
            transaksi
        )

    if len(transaksi) == 0:
        return pd.DataFrame()

    print("\nJENIS ITEM :", jenis_item)

    for i, t in enumerate(transaksi[:3]):
        print(f"Transaksi {i+1}:")
        print(t)
        print()
    
    te = TransactionEncoder()

    te_ary = te.fit(transaksi).transform(
        transaksi
    )

    return pd.DataFrame(
        te_ary,
        columns=te.columns_
    )

def proses_filter_transaksi(item, transaksi):

    if jenis_filter == "filter":

        if len(item) > 1:
            transaksi.append(item)

    else:

        transaksi.append(item)
        

def generate_rules(df_trans):

    if df_trans.empty:
        return pd.DataFrame()

    frequent_itemsets = fpgrowth(
        df_trans,
        min_support=min_support,
        use_colnames=True
    )

    if frequent_itemsets.empty:
        return pd.DataFrame()

    rules = association_rules(
        frequent_itemsets,
        metric="confidence",
        min_threshold=min_confidence
    )

    if rules.empty:
        return pd.DataFrame()

    rules = rules[
        rules["lift"] > 1
    ]

    return rules.sort_values(
        by="lift",
        ascending=False
    )
    
def simpan_hasil(rules, jenis_analisis, jumlah_transaksi):

    conn = engine.raw_connection()

    cursor = conn.cursor()

    cursor.execute(
    """
    DELETE FROM tb_hasil_fp_growth
    WHERE jenis_analisis = %s
    AND jenis_item = %s
    AND jenis_filter = %s
    """,
    (
        jenis_analisis,
        jenis_item,
        jenis_filter
    )
)

    for _, row in rules.iterrows():

        cursor.execute(
    """
    INSERT INTO tb_hasil_fp_growth
    (
        jenis_analisis,
        jenis_item,
        jenis_filter,
        antecedent,
        consequent,
        support,
        confidence,
        lift,
        jumlah_transaksi,
        sumber_file
    )
    VALUES
    (
        %s,%s,%s,%s,%s,%s,%s,%s,%s,%s
    )
    """,
    (
        jenis_analisis,
        jenis_item,
        jenis_filter,
        ", ".join(list(row["antecedents"])),
        ", ".join(list(row["consequents"])),
        float(row["support"]),
        float(row["confidence"]),
        float(row["lift"]),
        jumlah_transaksi,
        "tb_laporan"
    )
)

    conn.commit()

    cursor.close()

    conn.close()


def train_fp_growth():

    df = load_data()
    
    print("\nDAFTAR STATUS:")
    print(df["status_laporan"].value_counts())
    print(df["status_laporan"].unique())
    
    print(df.head(20))
    print(df.dtypes)

    print("\nCEK TANGGAL")
    print(df["tanggal_laporan"].head(20))
    
    print(df.head(10))
    print()
    print("TOTAL DATA :", len(df))

    df_status_ok = df[
        df["status_laporan"] == "OK"
    ].copy()

    df_status_belum_diperbaiki = df[
        df["status_laporan"] == "BELUM DIPERBAIKI"
    ].copy()

    df_status_tidak_dapat_diperbaiki = df[
        df["status_laporan"] == "TIDAK DAPAT DIPERBAIKI"
    ].copy()

    df_seluruh_status = df.copy()

    if jenis_analisis == "STATUS_OK":

        df_analisis = df_status_ok

    elif jenis_analisis == "BELUM_DIPERBAIKI":

        df_analisis = df_status_belum_diperbaiki

    elif jenis_analisis == "TIDAK_DAPAT_DIPERBAIKI":

        df_analisis = df_status_tidak_dapat_diperbaiki

    else:

        df_analisis = df_seluruh_status

    if metode_grouping == "ruangan":

        df_trans = buat_transaksi_per_ruangan(
            df_analisis
        )

    else:

        df_trans = buat_transaksi_per_tanggal(
            df_analisis
        )

    print("================================")
    print("DEBUG FP-GROWTH")
    print("================================")
    print("Jenis Analisis :", jenis_analisis)
    print("Metode Grouping :", metode_grouping)
    print("Jumlah Data Analisis :", len(df_analisis))
    print("Jumlah Transaksi :", len(df_trans))
    print("Jenis Item :", jenis_item)
    print("Jenis Filter :", jenis_filter)

    rules = generate_rules(
        df_trans
    )

    if rules.empty:

        print("Tidak ditemukan rule.")

        return

    print("\nCONTOH RULE")
    print(rules[["antecedents", "consequents"]].head(10))

    simpan_hasil(
        rules,
        jenis_analisis,
        len(df_trans)
    )

    print(
        "Berhasil simpan ke database"
    )


if __name__ == "__main__":
    train_fp_growth()