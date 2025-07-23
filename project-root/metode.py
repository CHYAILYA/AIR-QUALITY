import numpy as np
from src.models.sensor_data import SensorData

class AQIFuzzification:
    def __init__(self):
        # Batasan untuk setiap parameter
        self.mq7_ranges = {'baik': (0, 5000), 'sedang': (4000, 8000), 'buruk': (7000, 10000)}
        self.mq135_ranges = {'baik': (0, 100), 'sedang': (80, 150), 'buruk': (130, 200)}
        self.sharp_ranges = {'baik': (0, 20), 'sedang': (15, 35), 'buruk': (30, 50)}
        self.mq131_ranges = {'baik': (0, 30), 'sedang': (25, 60), 'buruk': (55, 100)}

    def calculate_membership(self, value, range_low, range_high):
        if value <= range_low:
            return 0
        elif value >= range_high:
            return 1
        else:
            return (value - range_low) / (range_high - range_low)

    def fuzzify_sensor(self, value, ranges):
        result = {}
        for category, (low, high) in ranges.items():
            if category == 'baik':
                result[category] = max(0, 1 - self.calculate_membership(value, low, high))
            elif category == 'sedang':
                mid = (low + high) / 2
                if value <= mid:
                    result[category] = self.calculate_membership(value, low, mid)
                else:
                    result[category] = 1 - self.calculate_membership(value, mid, high)
            else:  # buruk
                result[category] = self.calculate_membership(value, low, high)
        return result

    def process_data(self, mq7, mq135, sharp, mq131):
        results = {
            'MQ7 (CO)': self.fuzzify_sensor(mq7, self.mq7_ranges),
            'MQ135 (CO2)': self.fuzzify_sensor(mq135, self.mq135_ranges),
            'Sharp (Debu)': self.fuzzify_sensor(sharp, self.sharp_ranges),
            'MQ131 (O3)': self.fuzzify_sensor(mq131, self.mq131_ranges)
        }
        return results

    def calculate_overall_aqi(self, results):
        """Calculate overall AQI based on all sensor readings"""
        # Calculate average membership values for each category
        overall = {'baik': 0, 'sedang': 0, 'buruk': 0}
        sensor_count = len(results)
        
        for sensor_results in results.values():
            for category, value in sensor_results.items():
                overall[category] += value
        
        # Get average for each category
        for category in overall:
            overall[category] /= sensor_count
            
        # Determine final AQI status
        max_category = max(overall.items(), key=lambda x: x[1])
        
        return {
            'status': max_category[0],
            'confidence': max_category[1],
            'details': overall
        }

# Penggunaan dengan data dari database
if __name__ == "__main__":
    try:
        # Mengambil data terbaru dari database
        sensor_model = SensorData()
        latest_data = sensor_model.get_latest_data()

        if latest_data:
            fuzzy_system = AQIFuzzification()
            results = fuzzy_system.process_data(
                latest_data['mq7'],
                latest_data['mq135'],
                latest_data['sharp'],
                latest_data['mq131']
            )

            # Menampilkan hasil
            print("\nData sensor terbaru dari database:")
            print(f"MQ7: {latest_data['mq7']:.2f}")
            print(f"MQ135: {latest_data['mq135']:.2f}")
            print(f"Sharp: {latest_data['sharp']:.2f}")
            print(f"MQ131: {latest_data['mq131']:.2f}")
            
            print("\nHasil Fuzzifikasi:")
            for sensor, memberships in results.items():
                print(f"\n{sensor} Fuzzification Results:")
                for category, value in memberships.items():
                    print(f"{category}: {value:.2f}")
            
            # Calculate and display overall AQI
            overall_aqi = fuzzy_system.calculate_overall_aqi(results)
            print("\n=== Hasil Analisis Kualitas Udara ===")
            print(f"Status: {overall_aqi['status'].upper()}")
            print(f"Tingkat Keyakinan: {overall_aqi['confidence']:.2%}")
            print("\nDetail Tingkat Kualitas:")
            print(f"Baik: {overall_aqi['details']['baik']:.2%}")
            print(f"Sedang: {overall_aqi['details']['sedang']:.2%}")
            print(f"Buruk: {overall_aqi['details']['buruk']:.2%}")
            
            # Rekomendasi berdasarkan status
            print("\nRekomendasi:")
            if overall_aqi['status'] == 'baik':
                print("✅ Kualitas udara aman untuk beraktivitas")
            elif overall_aqi['status'] == 'sedang':
                print("⚠️ Kualitas udara cukup baik, tapi perlu waspada")
            else:
                print("❌ Kualitas udara buruk, disarankan menggunakan masker")
    
        else:
            print("Error: Tidak dapat mengambil data dari database")
            
    finally:
        # Ensure proper cleanup
        if 'sensor_model' in locals():
            del sensor_model