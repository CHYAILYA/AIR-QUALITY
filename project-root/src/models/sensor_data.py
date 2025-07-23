import mysql.connector
import logging
from datetime import datetime, timedelta

# Setup logging
logging.basicConfig(
    level=logging.INFO,
    format="%(asctime)s - %(levelname)s - %(message)s"
)
logger = logging.getLogger(__name__)

class SensorData:
    def __init__(self):
        self.db_config = {
            'host': 'localhost',
            'user': 'udara',
            'password': '@UdaraUnis2024',
            'database': 'udara',
            'port': 3306
        }
        self.last_saved_time = None  # Track last time data was saved

    def connect(self):
        try:
            self.connection = mysql.connector.connect(**self.db_config)
            self.cursor = self.connection.cursor(dictionary=True)
            logger.info("Database connected successfully.")
        except mysql.connector.Error as err:
            logger.error(f"Error connecting to MySQL: {err}")
            raise

    def get_latest_valid_data(self):
        try:
            self.connect()
            query = """
                SELECT id, timestamp, mq7, mq135, sharp, mq131 
                FROM data_udara 
                WHERE timestamp >= NOW() - INTERVAL 30 MINUTE
                ORDER BY timestamp DESC
            """
            self.cursor.execute(query)
            results = self.cursor.fetchall()
            logger.info(f"Data retrieved from DB: {results}")

            if results:
                data_list = []
                for result in results:
                    logger.info(f"Raw sensor values for ID {result['id']}: {result}")
                    
                    # Validasi dan konversi nilai sensor
                    mq7_value = self.validate_sensor_value(result['mq7'], 'mq7', result['id'])
                    mq135_value = self.validate_sensor_value(result['mq135'], 'mq135', result['id'])
                    sharp_value = self.validate_sensor_value(result['sharp'], 'sharp', result['id'])
                    mq131_value = self.validate_sensor_value(result['mq131'], 'mq131', result['id'])

                    data_list.append({
                        'id': result['id'],
                        'timestamp': result['timestamp'],
                        'mq7': mq7_value,
                        'mq135': mq135_value,
                        'sharp': sharp_value,
                        'mq131': mq131_value
                    })

                return data_list
            else:
                logger.warning("Tidak ada data valid ditemukan dalam 30 menit terakhir.")
                return None
        except Exception as e:
            logger.error(f"Database error: {str(e)}")
            return None
        finally:
            if hasattr(self, 'connection') and self.connection:
                self.connection.close()

    def validate_sensor_value(self, value, sensor_name, record_id):
        if value is None or str(value).strip() == '' or str(value).lower() == 'nan':
            logger.warning(f"[ID {record_id}] Sensor '{sensor_name}' has invalid value: {value}. Returning 0.0.")
            return 0.0
        try:
            return float(value)
        except (ValueError, TypeError):
            logger.warning(f"[ID {record_id}] Sensor '{sensor_name}' failed to convert value: {value}. Returning 0.0.")
            return 0.0

    def save_hourly_data(self, data, aqi):
        current_time = datetime.now()
        if self.last_saved_time is None or current_time - self.last_saved_time >= timedelta(hours=1):
            try:
                self.connect()
                insert_query = """
                    INSERT INTO histori_Aqi (tgl, aqi_udara, conc_pm25, conc_co, conc_no2, conc_o3)
                    VALUES (%s, %s, %s, %s, %s, %s)
                """
                values = (
                    data['timestamp'],
                    aqi,
                    data['mq7'],
                    data['mq135'],
                    data['sharp'],
                    data['mq131']
                )
                logger.info(f"Inserting data: {values}")
                
                if None not in values and '' not in values:
                    self.cursor.execute(insert_query, values)
                    logger.info(f"Data inserted for timestamp {data['timestamp']}.")
                else:
                    logger.warning(f"Skipping invalid values: {values}")

                self.connection.commit()
                self.last_saved_time = current_time
                logger.info("Hourly data saved successfully.")
            except mysql.connector.Error as err:
                logger.error(f"MySQL error: {err}")
            except Exception as e:
                logger.error(f"Error: {e}")
            finally:
                if hasattr(self, 'connection') and self.connection:
                    self.connection.close()

    def calculate_aqi(self, record):
        # Replace with your actual AQI calculation logic
        aqi = (record['mq7'] + record['mq135'] + record['sharp'] + record['mq131']) / 4
        logger.info(f"Calculated AQI: {aqi}")
        return aqi
from flask import Flask, jsonify
app = Flask(__name__)

@app.route('/', methods=['GET'])
def get_aqi_data():
    try:
        sensor_model = SensorData()
        data_list = sensor_model.get_latest_valid_data()

        if data_list is None:
            return jsonify({'error': 'Tidak ada data valid dalam 30 menit terakhir'}), 404

        logger.info(f"Data for AQI calculation: {data_list}")

        # Save data if an hour has passed
        sensor_model.save_hourly_data(data_list)

        return jsonify({
            'timestamp': datetime.now().isoformat(),
            'data_count': len(data_list),
            'sensors': data_list,
            'aqi': 'To be calculated'  # Placeholder for AQI calculation
        }), 200
    except Exception as e:
        logger.exception("Error while processing AQI data.")
        return jsonify({'error': 'Terjadi kesalahan pada server'}), 500

if __name__ == '__main__':
    app.run(debug=True, host='0.0.0.0', port=5000)
