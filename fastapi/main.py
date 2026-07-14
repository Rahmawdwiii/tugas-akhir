from sqlalchemy import create_engine, text
from fastapi import FastAPI
from pydantic import BaseModel
import pandas as pd
import pymysql
from sqlalchemy import create_engine
from fastapi.middleware.cors import CORSMiddleware

from train_fp_growth import proses_fp_growth

app = FastAPI()

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

engine = create_engine(
    "mysql+pymysql://root:@localhost/db11"
)

class FPRequest(BaseModel):

    jenis_analisis: str
    metode_grouping: str
    jenis_item: str
    jenis_filter: str

    min_support: float
    min_confidence: float
    
    tahun_awal: int | None = None
    tahun_akhir: int | None = None
    
    tanggal_awal: str | None = None
    tanggal_akhir: str | None = None
    
class PrioritasRequest(BaseModel):

    nama_alat: str
    lokasi: str
    unit: str

@app.get("/")
def home():
    return {
        "status": "FP-Growth API Aktif"
    }

@app.get("/test-db")
def test_db():

    query = """
    SELECT
        nama_alat,
        lokasi,
        unit,
        status_laporan
    FROM tb_laporan
    LIMIT 5
    """

    df = pd.read_sql(query, engine)

    return df.to_dict(orient="records")

@app.get("/jumlah-data")
def jumlah_data():

    query = """
    SELECT COUNT(*) AS total
    FROM tb_laporan
    """

    df = pd.read_sql(query, engine)

    return df.to_dict(orient="records")

@app.post("/generate-fp-growth")
def generate_fp_growth(req: FPRequest):

    try:
        print("===== DATA DARI FASTAPI =====")

        print("Tahun Awal :", req.tahun_awal)
        print("Tahun Akhir :", req.tahun_akhir)

        print("Tanggal Awal :", req.tanggal_awal)
        print("Tanggal Akhir :", req.tanggal_akhir)

        print("=============================")

        hasil = proses_fp_growth(

            req.jenis_analisis,
            req.metode_grouping,
            req.jenis_item,
            req.jenis_filter,
            req.min_support,
            req.min_confidence,
            req.tahun_awal,
            req.tahun_akhir,
            req.tanggal_awal,
            req.tanggal_akhir

        )
        
        print("Jumlah Rule :", hasil["jumlah_rule"])

        for i, rule in enumerate(hasil["rules"][:5]):
            print(i + 1, rule["antecedent"], "->", rule["consequent"])
        
        if hasil["jumlah_data"] == 0:

            return {
                "success": False,
                "message": "Tidak ada data pada rentang tanggal yang dipilih."
            }
        
        with engine.begin() as conn:

            conn.execute(
            text("""
            INSERT INTO tb_eksperimen_fp_growth
            (
                jenis_analisis,
                metode_grouping,
                jenis_item,
                jenis_filter,

                min_support,
                min_confidence,

                jumlah_data,
                jumlah_transaksi,
                jumlah_rule,

                waktu_proses
            )
            VALUES
            (
                :jenis_analisis,
                :metode_grouping,
                :jenis_item,
                :jenis_filter,

                :min_support,
                :min_confidence,

                :jumlah_data,
                :jumlah_transaksi,
                :jumlah_rule,

                :waktu_proses
            )
            """),
            {
                "jenis_analisis": req.jenis_analisis,
                "metode_grouping": req.metode_grouping,
                "jenis_item": req.jenis_item,
                "jenis_filter": req.jenis_filter,

                "min_support": req.min_support,
                "min_confidence": req.min_confidence,

                "jumlah_data": hasil["jumlah_data"],
                "jumlah_transaksi": hasil["jumlah_transaksi"],
                "jumlah_rule": hasil["jumlah_rule"],

                "waktu_proses": hasil["waktu_proses"]
            }
        )

        # ==========================
        # SIMPAN HASIL FP-GROWTH
        # ==========================

        with engine.begin() as conn:

            conn.execute(
                text("DELETE FROM tb_hasil_fp_growth")
            )

            for rule in hasil["rules"]:

                conn.execute(
                    text("""
                    INSERT INTO tb_hasil_fp_growth
                    (
                        jenis_analisis,
                        antecedent,
                        consequent,
                        support,
                        confidence,
                        lift,
                        jumlah_transaksi,
                        sumber_file,
                        jenis_item,
                        jenis_filter
                    )
                    VALUES
                    (
                        :jenis_analisis,
                        :antecedent,
                        :consequent,
                        :support,
                        :confidence,
                        :lift,
                        :jumlah_transaksi,
                        :sumber_file,
                        :jenis_item,
                        :jenis_filter
                    )
                    """),
                    {
                        "jenis_analisis": req.jenis_analisis,
                        "antecedent": rule["antecedent"],
                        "consequent": rule["consequent"],
                        "support": rule["support"],
                        "confidence": rule["confidence"],
                        "lift": rule["lift"],
                        "jumlah_transaksi": hasil["jumlah_transaksi"],
                        "sumber_file": "tb_laporan",
                        "jenis_item": req.jenis_item,
                        "jenis_filter": req.jenis_filter
                    }
                )

        return {

            "success": True,

            "jumlah_data": hasil["jumlah_data"],

            "jumlah_transaksi": hasil["jumlah_transaksi"],

            "jumlah_rule": hasil["jumlah_rule"],

            "waktu_proses": hasil["waktu_proses"],

            "min_support": req.min_support,

            "min_confidence": req.min_confidence,

            "data": hasil["rules"]
        }

    except Exception as e:

        return {

            "success": False,

            "message": str(e)
        }

@app.get("/rekomendasi/{nama_alat}")
def rekomendasi(nama_alat):

    query = """
    SELECT
        antecedent,
        consequent,
        support,
        confidence,
        lift
    FROM tb_hasil_fp_growth_alat
    WHERE antecedent LIKE %s
    ORDER BY lift DESC, confidence DESC
    LIMIT 10
    """

    df = pd.read_sql(
        query,
        engine,
        params=(f"%{nama_alat}%",)
    )

    hasil = []

    sudah_ada = set()

    for _, row in df.iterrows():

        key = (
            row["antecedent"],
            row["consequent"]
        )

        if key in sudah_ada:
            continue

        sudah_ada.add(key)

        hasil.append({
            "alat_pemicu": row["antecedent"],
            "alat_terkait": row["consequent"],
            "support": float(row["support"]),
            "confidence": float(row["confidence"]),
            "lift": float(row["lift"])
        })
    return hasil

@app.get("/rekomendasi-prioritas/{nama_alat}")
def rekomendasi_prioritas(nama_alat):

    query = """
    SELECT
        antecedent,
        consequent,
        support,
        confidence,
        lift
    FROM tb_hasil_fp_growth_alat
    WHERE antecedent LIKE %s
    ORDER BY
        lift DESC,
        confidence DESC,
        support DESC
    LIMIT 1
    """

    df = pd.read_sql(
        query,
        engine,
        params=(f"%{nama_alat}%",)
    )

    if df.empty:

        return {
            "success": False,
            "data": None
        }

    row = df.iloc[0]

    return {

        "success": True,

        "data": {

            "alat_pemicu":
                row["antecedent"],

            "alat_terkait":
                row["consequent"],

            "support":
                float(row["support"]),

            "confidence":
                float(row["confidence"]),

            "lift":
                float(row["lift"])
        }
    }
    
@app.post("/rekomendasi-prioritas")
def rekomendasi_prioritas_teknisi(req: PrioritasRequest):

    item_lengkap = f"{req.nama_alat} ({req.lokasi}, {req.unit})"

    # ===============================
    # PRIORITAS 1
    # ===============================

    query = """
    SELECT
        antecedent,
        consequent,
        support,
        confidence,
        lift
    FROM tb_hasil_fp_growth
    WHERE
        jenis_item = 'alat_lokasi_unit'
        AND antecedent LIKE %s
    ORDER BY
        lift DESC,
        confidence DESC,
        support DESC
    LIMIT 1
    """

    df = pd.read_sql(
        query,
        engine,
        params=(f"%{item_lengkap}%",)
    )

    # ===============================
    # PRIORITAS 2
    # ===============================

    if df.empty:

        query = """
        SELECT
            antecedent,
            consequent,
            support,
            confidence,
            lift
        FROM tb_hasil_fp_growth
        WHERE
            antecedent LIKE %s
            AND jenis_item='alat'
        ORDER BY
            lift DESC,
            confidence DESC,
            support DESC
        LIMIT 1
        """

        df = pd.read_sql(
            query,
            engine,
            params=(f"%{req.nama_alat}%",)
        )

    # ===============================

    if df.empty:

        return {
            "success": False,
            "data": None
        }

    row = df.iloc[0]

    return {

        "success": True,

        "data": {

            "alat_pemicu": row["antecedent"],
            "alat_terkait": row["consequent"],
            "support": float(row["support"]),
            "confidence": float(row["confidence"]),
            "lift": float(row["lift"])
        }
    }

@app.get("/riwayat-eksperimen")
def riwayat_eksperimen():

    query = """
    SELECT *
    FROM tb_eksperimen_fp_growth
    ORDER BY id_eksperimen DESC
    """

    df = pd.read_sql(query, engine)

    return df.to_dict(orient="records")
