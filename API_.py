"""
Inventory Forecasting API for PHP Integration
User: Alysoffar
Current Date: 2025-09-03 19:18:54 UTC
"""

from flask import Flask, request, jsonify
import joblib
import pandas as pd
import numpy as np
from datetime import datetime
import json

app = Flask(__name__)

# Load your trained model
print("Loading trained model...")
model = joblib.load('models/fixed_simple_model.pkl')
scaler = joblib.load('models/fixed_scaler.pkl')
feature_columns = joblib.load('models/fixed_feature_columns.pkl')

with open('models/fixed_model_metadata.json', 'r') as f:
    metadata = json.load(f)

print(f"✅ Model loaded! Performance: MAPE {metadata['mape']:.1f}%, R² {metadata['r2']:.3f}")

@app.route('/predict', methods=['POST'])
def predict_sales():
    """
    Predict sales for inventory management
    """
    try:
        data = request.get_json()
        
        # Convert input data to DataFrame
        df = pd.DataFrame([data])
        
        # Add required features if missing
        if 'Date' in data:
            df['Date'] = pd.to_datetime(df['Date'])
            df['Month'] = df['Date'].dt.month
            df['DayOfWeek'] = df['Date'].dt.dayofweek
            df['Quarter'] = df['Date'].dt.quarter
            df['IsWeekend'] = (df['DayOfWeek'] >= 5).astype(int)
        
        # Create derived features
        df['Demand_Forecast'] = df.get('Demand Forecast', df.get('demand_forecast', 0))
        df['Inventory_Level'] = df.get('Inventory Level', df.get('inventory_level', 0))
        df['Demand_to_Inventory_Ratio'] = df['Demand_Forecast'] / (df['Inventory_Level'] + 1)
        df['Price_per_Demand'] = df.get('Price', 0) / (df['Demand_Forecast'] + 1)
        
        # Fill missing features with defaults
        for col in feature_columns:
            if col not in df.columns:
                df[col] = 0
        
        # Select and scale features
        X = df[feature_columns]
        X_scaled = scaler.transform(X)
        
        # Make prediction
        prediction = model.predict(X_scaled)[0]
        prediction = max(0, prediction)  # Ensure non-negative
        
        # Generate recommendations
        current_inventory = float(df['Inventory_Level'].iloc[0])
        days_of_stock = current_inventory / (prediction + 1)
        should_reorder = days_of_stock < 5
        
        result = {
            'success': True,
            'predicted_sales': round(prediction, 1),
            'current_inventory': current_inventory,
            'days_of_stock': round(days_of_stock, 1),
            'should_reorder': should_reorder,
            'recommended_order_qty': round(prediction * 7 + 50, 0) if should_reorder else 0,
            'confidence': 'HIGH' if metadata['mape'] < 25 else 'MEDIUM',
            'model_performance': {
                'mape': f"{metadata['mape']:.1f}%",
                'r2': f"{metadata['r2']:.3f}"
            },
            'timestamp': datetime.utcnow().strftime('%Y-%m-%d %H:%M:%S UTC')
        }
        
        return jsonify(result)
        
    except Exception as e:
        return jsonify({
            'success': False,
            'error': str(e)
        }), 400

@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'model_performance': f"MAPE: {metadata['mape']:.1f}%, R²: {metadata['r2']:.3f}",
        'user': 'Alysoffar',
        'timestamp': datetime.utcnow().strftime('%Y-%m-%d %H:%M:%S UTC')
    })

if __name__ == '__main__':
    print("🚀 Starting Inventory Forecasting API...")
    print(f"👤 User: Alysoffar")
    print(f"📅 Started: 2025-09-03 19:18:54 UTC")
    print(f"📊 Model Performance: MAPE {metadata['mape']:.1f}%, R² {metadata['r2']:.3f}")
    app.run(debug=True, host='0.0.0.0', port=5000)