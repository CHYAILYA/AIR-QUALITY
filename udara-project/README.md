# Udara Project

## Overview
The Udara Project is designed to process and analyze sensor data using fuzzy logic. It includes a class for fuzzifying sensor data based on predefined ranges and retrieves the latest data from a MySQL database.

## Project Structure
- `src/metode.py`: Contains the `AQIFuzzification` class for fuzzifying sensor data.
- `src/config/database.py`: Establishes a connection to the MySQL database and retrieves sensor data.
- `src/models/sensor_data.py`: Defines a model for structuring and manipulating sensor data.
- `requirements.txt`: Lists the dependencies required for the project.

## Setup Instructions
1. Clone the repository:
   ```
   git clone <repository-url>
   ```
2. Navigate to the project directory:
   ```
   cd udara-project
   ```
3. Install the required dependencies:
   ```
   pip install -r requirements.txt
   ```

## Usage
To run the application, execute the following command:
```
python src/metode.py
```

Ensure that the MySQL database is running and the connection details in `src/config/database.py` are correctly configured.

## License
This project is licensed under the MIT License.