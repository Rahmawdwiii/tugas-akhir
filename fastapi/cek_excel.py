import pandas as pd

file = r"D:\FP_IMPORT\UPT-TP3A  Unit Pelaksana Teknis - Teknologi Permesinan dan Peralatan Penunjang Akademik (1).xlsx"

df = pd.read_excel(file)

print("=" * 60)
print("NAMA KOLOM")
print("=" * 60)
print(df.columns.tolist())

print("\n")

print("=" * 60)
print("5 BARIS PERTAMA")
print("=" * 60)
print(df.head())