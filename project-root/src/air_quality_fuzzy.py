import pandas as pd
import numpy as np
import skfuzzy as fuzz
from skfuzzy import control as ctrl
import mysql.connector
from datetime import datetime, timedelta
from config import DB_CONFIG

def evaluate_water_quality():
    try:
        conn = mysql.connector.connect(**DB_CONFIG)
        print("[SUCCESS] Terhubung ke database MySQL")
    except mysql.connector.Error as err:
        print(f"[ERROR] Koneksi database gagal: {err}")
        return

    try:
        current_time = datetime.now()
        # Changed from 30 minutes to 1 hour
        time_1_hour_ago = current_time - timedelta(hours=1)
        time_1_hour_ago_str = time_1_hour_ago.strftime('%Y-%m-%d %H:%M:%S')
        current_time_str = current_time.strftime('%Y-%m-%d %H:%M:%S')

        query = f"""
            SELECT id, TDS_ppm, Turbidity_NTU, pH, suhu, timestamp
            FROM air
            WHERE timestamp BETWEEN '{time_1_hour_ago_str}' AND '{current_time_str}'
        """
        raw_df = pd.read_sql(query, conn)

        if raw_df.empty:
            print("[INFO] Tidak ada data dalam 1 jam terakhir, mengambil data terbaru.")
            query = "SELECT id, TDS_ppm, Turbidity_NTU, pH, suhu, timestamp FROM air ORDER BY timestamp DESC LIMIT 1"
            raw_df = pd.read_sql(query, conn)

        df = raw_df.copy()
        df['TDS_ppm'] = pd.to_numeric(df['TDS_ppm'], errors='coerce')
        df['Turbidity_NTU'] = pd.to_numeric(df['Turbidity_NTU'], errors='coerce')
        df['pH'] = pd.to_numeric(df['pH'], errors='coerce')
        df['suhu'] = pd.to_numeric(df['suhu'], errors='coerce') # Added suhu
        df = df.dropna(subset=['TDS_ppm', 'Turbidity_NTU', 'pH', 'suhu']) # Added suhu
        print(f"[SUCCESS] Data valid: {len(df)}/{len(raw_df)} baris")

    except Exception as e:
        print(f"[ERROR] Gagal membaca data: {e}")
        conn.close()
        return

    # Setup fuzzy variables (no change here as suhu is not part of fuzzy logic)
    tds = ctrl.Antecedent(np.arange(0, 501, 1), 'TDS')
    turbidity = ctrl.Antecedent(np.arange(0, 10.1, 0.1), 'turbidity')
    ph = ctrl.Antecedent(np.arange(0, 14.1, 0.1), 'pH')

    tds['baik'] = fuzz.trimf(tds.universe, [0, 0, 300])
    tds['buruk'] = fuzz.trimf(tds.universe, [300, 500, 500])

    turbidity['baik'] = fuzz.trimf(turbidity.universe, [0, 0, 3])
    turbidity['buruk'] = fuzz.trimf(turbidity.universe, [3, 10, 10])

    ph['ideal'] = fuzz.trimf(ph.universe, [6.5, 7.5, 8.5])
    ph['asam'] = fuzz.trimf(ph.universe, [0, 0, 6.5])
    ph['basa'] = fuzz.trimf(ph.universe, [8.5, 14, 14])

    quality = ctrl.Consequent(np.arange(0, 11, 1), 'quality')
    quality['buruk'] = fuzz.trimf(quality.universe, [0, 0, 5])
    quality['baik'] = fuzz.trimf(quality.universe, [5, 10, 10])

    rules = [
        ctrl.Rule(tds['buruk'] | turbidity['buruk'] | ph['asam'] | ph['basa'], quality['buruk']),
        ctrl.Rule(tds['baik'] & turbidity['baik'] & ph['ideal'], quality['baik'])
    ]

    quality_ctrl = ctrl.ControlSystem(rules)
    quality_sim = ctrl.ControlSystemSimulation(quality_ctrl)

    def fuzzy_evaluation(row):
        try:
            quality_sim.input['TDS'] = float(row['TDS_ppm'])
            quality_sim.input['turbidity'] = float(row['Turbidity_NTU'])
            quality_sim.input['pH'] = float(row['pH'])
            quality_sim.compute()

            tds_baik = fuzz.interp_membership(tds.universe, tds['baik'].mf, row['TDS_ppm'])
            tds_buruk = fuzz.interp_membership(tds.universe, tds['buruk'].mf, row['TDS_ppm'])

            turbidity_baik = fuzz.interp_membership(turbidity.universe, turbidity['baik'].mf, row['Turbidity_NTU'])
            turbidity_buruk = fuzz.interp_membership(turbidity.universe, turbidity['buruk'].mf, row['Turbidity_NTU'])

            ph_ideal = fuzz.interp_membership(ph.universe, ph['ideal'].mf, row['pH'])
            ph_asam = fuzz.interp_membership(ph.universe, ph['asam'].mf, row['pH'])
            ph_basa = fuzz.interp_membership(ph.universe, ph['basa'].mf, row['pH'])

            return {
                'quality_score': round(quality_sim.output['quality'], 2),
                'fuzzy_values': {
                    'TDS_baik': round(tds_baik, 2),
                    'TDS_buruk': round(tds_buruk, 2),
                    'Turbidity_baik': round(turbidity_baik, 2),
                    'Turbidity_buruk': round(turbidity_buruk, 2),
                    'pH_ideal': round(ph_ideal, 2),
                    'pH_asam': round(ph_asam, 2),
                    'pH_basa': round(ph_basa, 2),
                }
            }
        except Exception as e:
            print(f"Error ID {row['id']}: {str(e)}")
            return None

    result = df.apply(fuzzy_evaluation, axis=1)
    df['quality_score'] = [r['quality_score'] if r else None for r in result]
    fuzzy_data = [r['fuzzy_values'] if r else None for r in result]

    for col in ['TDS_baik', 'TDS_buruk', 'Turbidity_baik', 'Turbidity_buruk', 'pH_ideal', 'pH_asam', 'pH_basa']:
        df[col] = [r.get(col, None) if r else None for r in fuzzy_data]

    df = df.dropna(subset=['quality_score'])
    df['kategori'] = pd.cut(df['quality_score'], bins=[-np.inf, 5, np.inf], labels=['Buruk', 'Baik'])

    try:
        cursor = conn.cursor()
        cursor.execute("""        
            CREATE TABLE IF NOT EXISTS hasil_evaluasi (
                id INT PRIMARY KEY,
                TDS_ppm FLOAT,
                Turbidity_NTU FLOAT,
                pH FLOAT,
                suhu FLOAT,  -- Added suhu column
                quality_score FLOAT,
                kategori VARCHAR(10),
                timestamp TIMESTAMP,
                TDS_baik FLOAT,
                TDS_buruk FLOAT,
                Turbidity_baik FLOAT,
                Turbidity_buruk FLOAT,
                pH_ideal FLOAT,
                pH_asam FLOAT,
                pH_basa FLOAT
            )
        """)

        for _, row in df.iterrows():
            cursor.execute("""
                INSERT INTO hasil_evaluasi 
                VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
                ON DUPLICATE KEY UPDATE
                    TDS_ppm = VALUES(TDS_ppm),
                    Turbidity_NTU = VALUES(Turbidity_NTU),
                    pH = VALUES(pH),
                    suhu = VALUES(suhu), -- Added suhu to update
                    quality_score = VALUES(quality_score),
                    kategori = VALUES(kategori),
                    TDS_baik = VALUES(TDS_baik),
                    TDS_buruk = VALUES(TDS_buruk),
                    Turbidity_baik = VALUES(Turbidity_baik),
                    Turbidity_buruk = VALUES(Turbidity_buruk),
                    pH_ideal = VALUES(pH_ideal),
                    pH_asam = VALUES(pH_asam),
                    pH_basa = VALUES(pH_basa)
            """, (
                row['id'],
                row['TDS_ppm'],
                row['Turbidity_NTU'],
                row['pH'],
                row['suhu'], # Added suhu to insert values
                row['quality_score'],
                row['kategori'],
                row['timestamp'].to_pydatetime(),  # Penting: untuk hindari error MySQL
                row['TDS_baik'],
                row['TDS_buruk'],
                row['Turbidity_baik'],
                row['Turbidity_buruk'],
                row['pH_ideal'],
                row['pH_asam'],
                row['pH_basa']
            ))

        conn.commit()
        print(f"\n[SUCCESS] Disimpan: {len(df)} data")

    except Exception as e:
        print(f"\n[ERROR] Gagal simpan: {e}")
    finally:
        conn.close()

    print("\nHasil Evaluasi:")    
    print(df[['id', 'TDS_ppm', 'Turbidity_NTU', 'pH', 'suhu', 'quality_score', 'kategori', 'TDS_baik', 'TDS_buruk', 'Turbidity_baik', 'Turbidity_buruk', 'pH_ideal', 'pH_asam', 'pH_basa']])

    return df
