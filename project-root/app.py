from flask import Flask, jsonify, request
from flask_cors import CORS
import logging
from metode import AQIFuzzification
from src.models.sensor_data import SensorData
import datetime
import traceback
import sys

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Initialize Flask app
app = Flask(__name__)

# Konfigurasi CORS
allowed_origins = [
    "https://udara.unis.ac.id",
    "http://localhost:3000",
    "https://udara.unis.ac.id/analytics"
]

CORS(app, resources={r"/*": {"origins": allowed_origins}})

# Header keamanan tambahan
security_headers = {
    'Strict-Transport-Security': 'max-age=63072000; includeSubDomains; preload',
    'X-Content-Type-Options': 'nosniff',
    'Content-Security-Policy': "default-src 'self'",
    'X-Frame-Options': 'DENY',
    'Referrer-Policy': 'strict-origin-when-cross-origin'
}

@app.after_request
def add_security_headers(response):
    response.headers.update(security_headers)
    return response

@app.route('/', methods=['GET'])
def get_aqi_data():
    try:
        logger.info("Starting AQI data request")

        # Get the latest sensor data
        sensor_model = SensorData()
        latest_data = sensor_model.get_latest_valid_data()

        if not latest_data:
            logger.error("No valid data returned from sensor model")
            return make_error_response(
                'Tidak ada data valid dalam 1 jam terakhir',
                404,
                "NO_RECENT_DATA"
            )

        logger.info(f"Latest sensor data: {latest_data}")

        # Fuzzy logic processing
        try:
            fuzzy_system = AQIFuzzification()
            results = fuzzy_system.process_data(
                float(latest_data[0]['mq7']),
                float(latest_data[0]['mq135']),
                float(latest_data[0]['sharp']),
                float(latest_data[0]['mq131'])
            )
        except Exception as e:
            logger.error(f"Error in fuzzy logic processing: {str(e)}")
            return make_error_response(
                "Kesalahan dalam pemrosesan fuzzy logic",
                500,
                "FUZZY_PROCESSING_ERROR"
            )

        # Calculate overall AQI
        try:
            overall_aqi = fuzzy_system.calculate_overall_aqi(results)
            logger.info(f"AQI Results: {overall_aqi}")

            # Dapatkan nilai AQI numeric
            status = overall_aqi.get('status', 'baik')
            confidence = overall_aqi.get('confidence', 0)

            if status == 'baik':
                aqi_numeric = round(confidence * 50)  # 0–50
            elif status == 'sedang':
                aqi_numeric = round(50 + (confidence * 50))  # 51–100
            elif status == 'buruk':
                aqi_numeric = round(100 + (confidence * 100))  # 101–200
            else:
                aqi_numeric = 0

            # Simpan data ke database
            sensor_model.save_hourly_data(latest_data[0], aqi_numeric)
            logger.info("Data saved to database successfully.")
        except Exception as e:
            logger.error(f"Error calculating overall AQI: {str(e)}")
            return make_error_response(
                "Kesalahan dalam perhitungan AQI",
                500,
                "AQI_CALCULATION_ERROR"
            )

        # Create the response data
        try:
            response_data = create_response_data(latest_data[0], results, overall_aqi)
            response_data['timestamp'] = latest_data[0]['timestamp'].isoformat()
            return make_success_response(response_data)
        except Exception as e:
            logger.error(f"Error creating response: {str(e)}")
            return make_error_response(
                "Kesalahan dalam membuat response",
                500,
                "RESPONSE_CREATION_ERROR"
            )

    except Exception as e:
        exc_type, exc_value, exc_traceback = sys.exc_info()
        error_details = traceback.format_exception(exc_type, exc_value, exc_traceback)
        logger.error(f"Kesalahan sistem: {str(e)}\nTraceback:\n{''.join(error_details)}")
        return make_error_response(
            "Terjadi kesalahan internal",
            500,
            "INTERNAL_ERROR"
        )

# Endpoint untuk mengirim data real-time
@app.route('/realtime', methods=['POST'])
def process_realtime():
    try:
        data = request.get_json()
        logger.info("Processing real-time data")

        required_fields = ['mq7', 'mq135', 'sharp', 'mq131']
        for field in required_fields:
            if field not in data:
                logger.error(f"Missing field in request data: {field}")
                return make_error_response(
                    f"Field {field} is required",
                    400,
                    "MISSING_FIELD"
                )
            try:
                float(data[field])  # Ensure valid numeric values
            except (TypeError, ValueError):
                logger.error(f"Invalid value for field {field}: {data[field]}")
                return make_error_response(
                    f"Value for field {field} must be a number",
                    400,
                    "INVALID_FIELD_VALUE"
                )

        # Fuzzy logic processing for real-time data
        try:
            fuzzy_system = AQIFuzzification()
            results = fuzzy_system.process_data(
                float(data['mq7']),
                float(data['mq135']),
                float(data['sharp']),
                float(data['mq131'])
            )
            overall_aqi = fuzzy_system.calculate_overall_aqi(results)
            logger.info(f"Real-time AQI Results: {overall_aqi}")
        except Exception as e:
            logger.error(f"Error in fuzzy logic processing: {str(e)}")
            return make_error_response(
                "Kesalahan dalam pemrosesan fuzzy logic",
                500,
                "FUZZY_PROCESSING_ERROR"
            )

        # Create and return the real-time response
        response_data = create_response_data(data, results, overall_aqi)
        response_data['timestamp'] = datetime.datetime.now().isoformat()

        # Simpan data ke database
        sensor_model = SensorData()
        sensor_model.save_hourly_data(data, overall_aqi)

        return make_success_response(response_data)

    except Exception as e:
        logger.error(f"Error processing real-time data: {str(e)}")
        return make_error_response(
            "Terjadi kesalahan dalam pemrosesan data",
            500,
            "REALTIME_PROCESSING_ERROR"
        )

# Helper functions for success and error responses
def make_success_response(data):
    return jsonify({
        'status': 'success',
        'data': data
    }), 200

def make_error_response(message, status_code, error_code):
    return jsonify({
        'status': 'error',
        'message': message,
        'error_code': error_code
    }), status_code

def create_response_data(latest_data, results, overall_aqi):
    status = overall_aqi.get('status', 'baik')
    confidence = overall_aqi.get('confidence', 0)

    # Convert status to numeric AQI value
    if status == 'baik':
        aqi_numeric = round(confidence * 50)  # 0–50
    elif status == 'sedang':
        aqi_numeric = round(50 + (confidence * 50))  # 51–100
    elif status == 'buruk':
        aqi_numeric = round(100 + (confidence * 100))  # 101–200
    else:
        aqi_numeric = 0

    return {
        'latest_data': latest_data,
        'fuzzy_results': results,
        'overall_aqi': overall_aqi,
        'aqi': {
            'label': 'AQI⁺ US',
            'value': aqi_numeric,
            'status': status
        }
    }

if __name__ == '__main__':
    app.run(
        host='0.0.0.0',
        port=5000,
        debug=False,
        threaded=True
    )
