from flask import Flask, jsonify
from flask_cors import CORS
import json
from air_quality_fuzzy import evaluate_water_quality

app = Flask(__name__)
CORS(app)

@app.route("/", methods=["GET"])
def run_fuzzy():
    try:
        df = evaluate_water_quality()
        # Add 'suhu' to the list of columns to be included in the JSON response
        result = df[['id', 'TDS_ppm', 'Turbidity_NTU', 'pH', 'suhu', 'quality_score', 'kategori',
                     'TDS_baik', 'TDS_buruk', 'Turbidity_baik', 'Turbidity_buruk',
                     'pH_ideal', 'pH_asam', 'pH_basa']].to_dict(orient='records')
        
        return app.response_class(
            response=json.dumps({
                "status": "success",
                "message": "Fuzzy evaluation complete.",
                "data": result
            }, indent=2),
            mimetype='application/json'
        )
    except Exception as e:
        return jsonify({"status": "error", "message": str(e)}), 500

if __name__ == "__main__":
    app.run(host="0.0.0.0", port=5001)

