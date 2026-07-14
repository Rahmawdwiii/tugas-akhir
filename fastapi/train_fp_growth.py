import sys
import pandas as pd
import time

from sqlalchemy import create_engine

from mlxtend.preprocessing import TransactionEncoder
from mlxtend.frequent_patterns import fpgrowth
from mlxtend.frequent_patterns import association_rules


engine = create_engine(
    "mysql+pymysql://root:@localhost/db11"
)

jenis_item = ""
jenis_filter = ""
min_support = 0.01
min_confidence = 0.5

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

def proses_fp_growth(

    jenis_analisis,
    metode_grouping,
    jenis_item_param,
    jenis_filter_param,
    min_support_param,
    min_confidence_param,

    tahun_awal=None,
    tahun_akhir=None,

    tanggal_awal=None,
    tanggal_akhir=None
):
    start_time = time.time()

    global jenis_item
    global jenis_filter
    global min_support
    global min_confidence

    jenis_item = jenis_item_param
    jenis_filter = jenis_filter_param
    min_support = float(min_support_param)
    min_confidence = float(min_confidence_param)

    df = load_data()

    df["tanggal_laporan"] = pd.to_datetime(
        df["tanggal_laporan"],
        errors="coerce"
    )

# ==========================
# FILTER RENTANG TAHUN
# ==========================

    if (
        tahun_awal is not None
        and tahun_awal != ""
        and tahun_akhir is not None
        and tahun_akhir != ""
    ):

        print(
            "FILTER TAHUN :",
            tahun_awal,
            "-",
            tahun_akhir
        )

        df = df[
            (
                df["tanggal_laporan"].dt.year >= int(tahun_awal)
            )
            &
            (
                df["tanggal_laporan"].dt.year <= int(tahun_akhir)
            )
        ]

# ==========================
# FILTER TANGGAL
# DIGUNAKAN JIKA RENTANG TAHUN TIDAK DIPILIH
# ==========================

    elif (
        tanggal_awal is not None
        and tanggal_awal != ""
        and tanggal_akhir is not None
        and tanggal_akhir != ""
    ):

        print(
            "FILTER TANGGAL :",
            tanggal_awal,
            "-",
            tanggal_akhir
        )

        tanggal_awal = pd.to_datetime(
            tanggal_awal
        )

        tanggal_akhir = pd.to_datetime(
            tanggal_akhir
        )

        df = df[
            (
                df["tanggal_laporan"] >= tanggal_awal
            )
            &
            (
                df["tanggal_laporan"] <= tanggal_akhir
            )
        ]

    else:

        print(
            "MENGGUNAKAN SELURUH DATA"
        )

    # ==========================
    # FILTER STATUS
    # ==========================

    df_status_ok = df[
        df["status_laporan"] == "OK"
    ].copy()

    df_status_belum_diperbaiki = df[
        df["status_laporan"] == "BELUM DIPERBAIKI"
    ].copy()

    df_status_tidak_dapat_diperbaiki = df[
        df["status_laporan"]
        == "TIDAK DAPAT DIPERBAIKI"
    ].copy()

    df_seluruh_status = df.copy()

    if jenis_analisis == "STATUS_OK":

        df_analisis = df_status_ok

    elif jenis_analisis == "BELUM_DIPERBAIKI":

        df_analisis = df_status_belum_diperbaiki

    elif jenis_analisis == "TIDAK_DAPAT_DIPERBAIKI":

        df_analisis = (
            df_status_tidak_dapat_diperbaiki
        )

    else:

        df_analisis = df_seluruh_status
        
    print("TOTAL DATA :", len(df))
    
    # ==========================
    # GROUPING TRANSAKSI
    # ==========================

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

    rules = generate_rules(df_trans)

    if rules.empty:

        return {

            "jumlah_data":
                len(df_analisis),

            "jumlah_transaksi":
                len(df_trans),

            "jumlah_rule":
                0,
                
            "waktu_proses":
                round(time.time() - start_time, 2),

            "rules":
                []
        }

    hasil = []

    for _, row in rules.iterrows():

        antecedent = ", ".join(list(row["antecedents"]))
        consequent = ", ".join(list(row["consequents"]))

        knowledge = (
            f"Kerusakan pada {antecedent} cenderung diikuti "
            f"oleh kerusakan pada {consequent}. "
            f"Pola ini diperoleh dari data historis sehingga "
            f"dapat menjadi dasar pemeliharaan preventif."
        )

        hasil.append({

            "antecedent": antecedent,

            "consequent": consequent,

            "support":
                round(float(row["support"]), 4),

            "confidence":
                round(float(row["confidence"]), 4),

            "lift":
                round(float(row["lift"]), 4),

            "knowledge":
                knowledge

        })

    waktu_proses = round(
    time.time() - start_time,
    2
)

    return {

        "jumlah_data":
            len(df_analisis),

        "jumlah_transaksi":
            len(df_trans),

        "jumlah_rule":
            len(hasil),

        "waktu_proses":
            waktu_proses,

        "rules":
            hasil
    }

if __name__ == "__main__":

    hasil = proses_fp_growth(
        "SELURUH_STATUS",
        "ruangan",
        "alat",
        "filter",
        0.01,
        0.5,
        None,
        None,
        None,
        None
    )

    print(hasil)