import numpy as np

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

# Contoh penggunaan
if __name__ == "__main__":
    # Data dari tabel
    sample_data = {
        'mq7': 4833.98,
        'mq135': 80.91,
        'sharp': 19.39,
        'mq131': 0
    }

    fuzzy_system = AQIFuzzification()
    results = fuzzy_system.process_data(
        sample_data['mq7'],
        sample_data['mq135'],
        sample_data['sharp'],
        sample_data['mq131']
    )

    # Menampilkan hasil
    for sensor, memberships in results.items():
        print(f"\n{sensor} Fuzzification Results:")
        for category, value in memberships.items():
            print(f"{category}: {value:.2f}")