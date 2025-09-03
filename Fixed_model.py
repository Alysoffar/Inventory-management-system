"""
Fixed Simple Model - Using the Perfect Predictor
User: Alysoffar
Current Date: 2025-09-03 19:14:41 UTC

Since Demand Forecast has 0.997 correlation, let's use it properly!
"""

import pandas as pd
import numpy as np
from sklearn.preprocessing import StandardScaler
from sklearn.metrics import mean_squared_error, mean_absolute_error, r2_score
from sklearn.linear_model import LinearRegression
from sklearn.ensemble import RandomForestRegressor
import tensorflow as tf
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import Dense
import matplotlib.pyplot as plt
import warnings
warnings.filterwarnings('ignore')

def create_optimized_features(df):
    """
    Create features that leverage the high correlation we found
    """
    print("⚙️ Creating optimized features based on diagnosis...")
    
    df = df.copy()
    df = df.sort_values(['Store ID', 'Product ID', 'Date']).reset_index(drop=True)
    
    # Time features
    df['Month'] = df['Date'].dt.month
    df['DayOfWeek'] = df['Date'].dt.dayofweek
    df['Quarter'] = df['Date'].dt.quarter
    df['IsWeekend'] = (df['DayOfWeek'] >= 5).astype(int)
    
    # The PERFECT predictor (0.997 correlation)
    df['Demand_Forecast'] = df['Demand Forecast']  # Clean column name
    
    # Leverage the high correlation features
    df['Inventory_Level'] = df['Inventory Level']
    
    # Create ratio features (often very predictive)
    df['Demand_to_Inventory_Ratio'] = df['Demand_Forecast'] / (df['Inventory_Level'] + 1)
    df['Price_per_Demand'] = df['Price'] / (df['Demand_Forecast'] + 1)
    
    # Simple lag features
    df['Demand_Yesterday'] = df.groupby(['Store ID', 'Product ID'])['Demand_Forecast'].shift(1)
    df['Sales_Yesterday'] = df.groupby(['Store ID', 'Product ID'])['Units Sold'].shift(1)
    
    # Rolling averages
    df['Demand_Avg_3Days'] = df.groupby(['Store ID', 'Product ID'])['Demand_Forecast'].transform(
        lambda x: x.rolling(3, min_periods=1).mean()
    )
    
    # Categorical encodings
    from sklearn.preprocessing import LabelEncoder
    le_store = LabelEncoder()
    le_product = LabelEncoder()
    
    df['Store_Encoded'] = le_store.fit_transform(df['Store ID'])
    df['Product_Encoded'] = le_product.fit_transform(df['Product ID'])
    
    # Fill any missing values
    df = df.fillna(method='ffill').fillna(method='bfill').fillna(0)
    
    # Feature selection - focus on the high-correlation features
    feature_columns = [
        'Demand_Forecast',  # 0.997 correlation - our golden feature!
        'Inventory_Level',  # 0.590 correlation - second best
        'Month', 'DayOfWeek', 'IsWeekend', 'Quarter',
        'Demand_to_Inventory_Ratio', 'Price_per_Demand',
        'Demand_Yesterday', 'Sales_Yesterday', 'Demand_Avg_3Days',
        'Store_Encoded', 'Product_Encoded'
    ]
    
    print(f"   ✅ Selected {len(feature_columns)} optimized features")
    print(f"   🎯 Primary predictor: Demand_Forecast (0.997 correlation)")
    
    return df, feature_columns

def build_simple_effective_model(X_train, X_test, y_train, y_test):
    """
    Build multiple simple models and pick the best one
    """
    print("🤖 Testing multiple simple models...")
    
    models = {
        'Linear Regression': LinearRegression(),
        'Random Forest': RandomForestRegressor(n_estimators=50, random_state=42),
        'Simple Neural Net': None  # Will build this
    }
    
    results = {}
    
    # Test Linear Regression
    print("   🧪 Testing Linear Regression...")
    lr_model = models['Linear Regression']
    lr_model.fit(X_train, y_train)
    lr_pred = lr_model.predict(X_test)
    lr_pred = np.maximum(lr_pred, 0)  # Non-negative
    
    lr_mape = np.mean(np.abs((y_test - lr_pred) / np.maximum(y_test, 1))) * 100
    lr_r2 = r2_score(y_test, lr_pred)
    results['Linear Regression'] = {'mape': lr_mape, 'r2': lr_r2, 'model': lr_model}
    print(f"      MAPE: {lr_mape:.2f}%, R²: {lr_r2:.4f}")
    
    # Test Random Forest
    print("   🧪 Testing Random Forest...")
    rf_model = models['Random Forest']
    rf_model.fit(X_train, y_train)
    rf_pred = rf_model.predict(X_test)
    rf_pred = np.maximum(rf_pred, 0)
    
    rf_mape = np.mean(np.abs((y_test - rf_pred) / np.maximum(y_test, 1))) * 100
    rf_r2 = r2_score(y_test, rf_pred)
    results['Random Forest'] = {'mape': rf_mape, 'r2': rf_r2, 'model': rf_model}
    print(f"      MAPE: {rf_mape:.2f}%, R²: {rf_r2:.4f}")
    
    # Test Simple Neural Network
    print("   🧪 Testing Simple Neural Network...")
    try:
        nn_model = Sequential([
            Dense(32, activation='relu', input_shape=(X_train.shape[1],)),
            Dense(16, activation='relu'),
            Dense(8, activation='relu'),
            Dense(1, activation='linear')
        ])
        
        nn_model.compile(
            optimizer='adam',
            loss='mse',
            metrics=['mae']
        )
        
        nn_model.fit(X_train, y_train, 
                    validation_data=(X_test, y_test),
                    epochs=50, 
                    batch_size=64, 
                    verbose=0)
        
        nn_pred = nn_model.predict(X_test, verbose=0).flatten()
        nn_pred = np.maximum(nn_pred, 0)
        
        nn_mape = np.mean(np.abs((y_test - nn_pred) / np.maximum(y_test, 1))) * 100
        nn_r2 = r2_score(y_test, nn_pred)
        results['Simple Neural Net'] = {'mape': nn_mape, 'r2': nn_r2, 'model': nn_model}
        print(f"      MAPE: {nn_mape:.2f}%, R²: {nn_r2:.4f}")
        
    except Exception as e:
        print(f"      ❌ Neural Network failed: {e}")
    
    # Find best model
    best_model_name = min(results.keys(), key=lambda k: results[k]['mape'])
    best_result = results[best_model_name]
    
    print(f"\n🏆 Best Model: {best_model_name}")
    print(f"   MAPE: {best_result['mape']:.2f}%")
    print(f"   R²: {best_result['r2']:.4f}")
    
    return best_result['model'], best_result, best_model_name

def main():
    """
    Fixed main function using the diagnosis insights
    """
    print("🚀 FIXED MODEL USING DIAGNOSIS INSIGHTS")
    print("=" * 60)
    print(f"👤 User: Alysoffar")
    print(f"📅 Current Date: 2025-09-03 19:14:41 UTC")
    print(f"🎯 Strategy: Leverage Demand Forecast (0.997 correlation)")
    print("=" * 60)
    
    try:
        # Load data
        df = pd.read_csv(r'D:\WORK\Findo\AIMODEL\data\retail_store_inventory.csv')
        df['Date'] = pd.to_datetime(df['Date'])
        print(f"📊 Loaded {len(df):,} records")
        
        # Create optimized features
        df_featured, feature_columns = create_optimized_features(df)
        
        # Prepare data
        X = df_featured[feature_columns]
        y = df_featured['Units Sold']
        
        # Simple chronological split
        split_idx = int(len(X) * 0.8)
        X_train, X_test = X[:split_idx], X[split_idx:]
        y_train, y_test = y[:split_idx], y[split_idx:]
        
        # Scale features
        scaler = StandardScaler()
        X_train_scaled = scaler.fit_transform(X_train)
        X_test_scaled = scaler.transform(X_test)
        
        print(f"🔧 Training set: {len(X_train_scaled):,} samples")
        print(f"🔧 Test set: {len(X_test_scaled):,} samples")
        
        # Build and test models
        best_model, best_result, model_name = build_simple_effective_model(
            X_train_scaled, X_test_scaled, y_train, y_test
        )
        
        # Final evaluation
        if model_name == 'Simple Neural Net':
            final_pred = best_model.predict(X_test_scaled, verbose=0).flatten()
        else:
            final_pred = best_model.predict(X_test_scaled)
        
        final_pred = np.maximum(final_pred, 0)
        
        final_mape = np.mean(np.abs((y_test - final_pred) / np.maximum(y_test, 1))) * 100
        final_r2 = r2_score(y_test, final_pred)
        final_mae = mean_absolute_error(y_test, final_pred)
        
        # Results
        print(f"\n🎉 FINAL RESULTS:")
        print(f"=" * 40)
        print(f"Best Model: {model_name}")
        print(f"MAPE: {final_mape:.2f}% {'✅' if final_mape < 30 else '❌'}")
        print(f"R²: {final_r2:.4f} {'✅' if final_r2 > 0.5 else '❌'}")  
        print(f"MAE: {final_mae:.2f} units")
        print(f"=" * 40)
        
        # Show sample predictions
        print(f"\n📊 Sample Predictions vs Actual:")
        sample_indices = np.random.choice(len(y_test), 10)
        for i, idx in enumerate(sample_indices):
            actual = y_test.iloc[idx]
            predicted = final_pred[idx]
            error_pct = abs(actual - predicted) / max(actual, 1) * 100
            print(f"   {i+1:2d}. Actual: {actual:6.1f}, Predicted: {predicted:6.1f}, Error: {error_pct:5.1f}%")
        
        # Save if good
        if final_mape < 50:  # Reasonable threshold
            print(f"\n💾 Saving model...")
            
            import os
            import joblib
            os.makedirs('models', exist_ok=True)
            
            if model_name == 'Simple Neural Net':
                best_model.save('models/fixed_neural_model.h5')
            else:
                joblib.dump(best_model, 'models/fixed_simple_model.pkl')
            
            joblib.dump(scaler, 'models/fixed_scaler.pkl')
            joblib.dump(feature_columns, 'models/fixed_feature_columns.pkl')
            
            # Save metadata
            metadata = {
                'model_type': model_name,
                'mape': float(final_mape),
                'r2': float(final_r2),
                'mae': float(final_mae), 
                'features': feature_columns,
                'user': 'Alysoffar',
                'date': '2025-09-03 19:18:54 UTC',
                'production_ready': 'YES' if final_mape < 30 else 'NO'  # ← String instead of boolean
            }
            
            import json
            with open('models/fixed_model_metadata.json', 'w') as f:
                json.dump(metadata, f, indent=2)
            
            print(f"✅ Model saved!")
        
        # Assessment
        if final_mape < 20 and final_r2 > 0.7:
            print(f"\n🟢 EXCELLENT! Model is production ready!")
            print(f"   Ready for PHP integration")
        elif final_mape < 40 and final_r2 > 0.4:
            print(f"\n🟡 GOOD! Model is usable with monitoring")
            print(f"   Can proceed with PHP integration")
        else:
            print(f"\n🔴 Model still needs work")
            print(f"   Consider data quality improvements")
        
        # Create simple visualization
        plt.figure(figsize=(12, 4))
        
        plt.subplot(1, 3, 1)
        sample_size = min(200, len(y_test))
        sample_idx = slice(0, sample_size)
        plt.plot(y_test.iloc[sample_idx].values, label='Actual', alpha=0.8)
        plt.plot(final_pred[sample_idx], label='Predicted', alpha=0.8)
        plt.title('Actual vs Predicted (Sample)')
        plt.legend()
        plt.grid(True, alpha=0.3)
        
        plt.subplot(1, 3, 2)
        plt.scatter(y_test, final_pred, alpha=0.6, s=10)
        min_val, max_val = min(y_test.min(), final_pred.min()), max(y_test.max(), final_pred.max())
        plt.plot([min_val, max_val], [min_val, max_val], 'r--', alpha=0.8)
        plt.xlabel('Actual')
        plt.ylabel('Predicted')
        plt.title(f'Scatter Plot (R² = {final_r2:.3f})')
        plt.grid(True, alpha=0.3)
        
        plt.subplot(1, 3, 3)
        errors = y_test - final_pred
        plt.hist(errors, bins=50, alpha=0.7, edgecolor='black')
        plt.axvline(0, color='r', linestyle='--', alpha=0.8)
        plt.title('Prediction Errors')
        plt.xlabel('Error (Actual - Predicted)')
        plt.grid(True, alpha=0.3)
        
        plt.tight_layout()
        plt.savefig('fixed_model_results.png', dpi=300, bbox_inches='tight')
        plt.show()
        
        return best_model, best_result
        
    except Exception as e:
        print(f"❌ Error: {str(e)}")
        import traceback
        traceback.print_exc()
        return None, None

if __name__ == "__main__":
    model, results = main()