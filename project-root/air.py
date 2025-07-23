# air_quality_fuzzy.py
import pandas as pd
import numpy as np
import skfuzzy as fuzz
from skfuzzy import control as ctrl
import mysql.connector
from config import DB_CONFIG

def evaluate_water_quality():
    # ==========================================
    # 1. KONEKSI DATABASE
    # ==========================================
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        print("[SUCCESS] Terhubung ke database MySQL")
    except mysql.connector.Error as err:
        print(f"[ERROR] Koneksi database gagal: {err}")
        return

    # ==========================================
    # 2. AMBIL DATA & KONVERSI TIPE DATA
    # ==========================================
    try:
        query = """
            SELECT id, TDS_ppm, Turbidity_NTU, pH, timestamp 
            FROM air  # PASTIKAN NAMA TABEL SESUAI
        """
        raw_df = pd.read_sql(query, conn)
        
        # Konversi ke float
        df = raw_df.copy()
        df['TDS_ppm'] = pd.to_numeric(df['TDS_ppm'], errors='coerce')
        df['Turbidity_NTU'] = pd.to_numeric(df['Turbidity_NTU'], errors='coerce')
        df['pH'] = pd.to_numeric(df['pH'], errors='coerce')
        
        # Hapus data invalid
        df = df.dropna(subset=['TDS_ppm', 'Turbidity_NTU', 'pH'])
        print(f"[SUCCESS] Data valid: {len(df)}/{len(raw_df)} baris")
        
    except Exception as e:
        print(f"[ERROR] Gagal membaca data: {e}")
        conn.close()
        return

    # ==========================================
    # 3. SETUP SISTEM FUZZY
    # ==========================================
    # Variabel input
    tds = ctrl.Antecedent(np.arange(0, 501, 1), 'TDS')
    turbidity = ctrl.Antecedent(np.arange(0, 10, 0.1), 'turbidity')
    ph = ctrl.Antecedent(np.arange(0, 14, 0.1), 'pH')

    # Fungsi keanggotaan
    tds['baik'] = fuzz.trimf(tds.universe, [0, 0, 300])
    tds['buruk'] = fuzz.trimf(tds.universe, [300, 500, 500])
    
    turbidity['baik'] = fuzz.trimf(turbidity.universe, [0, 0, 3])
    turbidity['buruk'] = fuzz.trimf(turbidity.universe, [3, 10, 10])
    
    ph['ideal'] = fuzz.trimf(ph.universe, [6.5, 7.5, 8.5])
    ph['asam'] = fuzz.trimf(ph.universe, [0, 0, 6.5])
    ph['basa'] = fuzz.trimf(ph.universe, [8.5, 14, 14])

    # Variabel output
    quality = ctrl.Consequent(np.arange(0, 11, 1), 'quality')
    quality['buruk'] = fuzz.trimf(quality.universe, [0, 0, 5])
    quality['baik'] = fuzz.trimf(quality.universe, [5, 10, 10])

    # Aturan fuzzy
    rules = [
        ctrl.Rule(tds['buruk'] | turbidity['buruk'] | ph['asam'] | ph['basa'], quality['buruk']),
        ctrl.Rule(tds['baik'] & turbidity['baik'] & ph['ideal'], quality['baik'])
    ]

    # Inisialisasi sistem fuzzy
    quality_ctrl = ctrl.ControlSystem(rules)
    quality_sim = ctrl.ControlSystemSimulation(quality_ctrl)  # <-- INI HARUS ADA

    # ==========================================
    # 4. EVALUASI DATA
    # ==========================================
    def fuzzy_evaluation(row):
        try:
            quality_sim.input['TDS'] = float(row['TDS_ppm'])
            quality_sim.input['turbidity'] = float(row['Turbidity_NTU'])
            quality_sim.input['pH'] = float(row['pH'])
            quality_sim.compute()
            return round(quality_sim.output['quality'], 2)
        except Exception as e:
            print(f"Error ID {row['id']}: {str(e)}")
            return None

    df['quality_score'] = df.apply(fuzzy_evaluation, axis=1)
    df = df.dropna(subset=['quality_score'])
    df['kategori'] = pd.cut(
        df['quality_score'],
        bins=[-np.inf, 5, np.inf],
        labels=['Buruk', 'Baik']
    )

    # ==========================================
    # 5. SIMPAN HASIL
    # ==========================================
    try:
        cursor = conn.cursor()
        cursor.execute("""
            CREATE TABLE IF NOT EXISTS hasil_evaluasi (
                id INT PRIMARY KEY,
                TDS_ppm FLOAT,
                Turbidity_NTU FLOAT,
                pH FLOAT,
                quality_score FLOAT,
                kategori VARCHAR(10),
                timestamp TIMESTAMP
            )
        """)

        for _, row in df.iterrows():
            cursor.execute("""
                INSERT INTO hasil_evaluasi 
                VALUES (%s, %s, %s, %s, %s, %s, %s)
                ON DUPLICATE KEY UPDATE
                TDS_ppm = VALUES(TDS_ppm),
                Turbidity_NTU = VALUES(Turbidity_NTU),
                pH = VALUES(pH),
                quality_score = VALUES(quality_score),
                kategori = VALUES(kategori)
            """, (
                row['id'],
                row['TDS_ppm'],
                row['Turbidity_NTU'],
                row['pH'],
                row['quality_score'],
                row['kategori'],
                row['timestamp']
            ))
        
        conn.commit()
        print(f"\n[SUCCESS] Disimpan: {len(df)} data")
        
    except Exception as e:
        print(f"\n[ERROR] Gagal simpan: {e}")
    finally:
        conn.close()

    # ==========================================
    # 6. TAMPILKAN HASIL
    # ==========================================
    print("\nHasil Evaluasi:")
    print(df[['id', 'TDS_ppm', 'Turbidity_NTU', 'pH', 'quality_score', 'kategori']])

if __name__ == "__main__":
    evaluate_water_quality()