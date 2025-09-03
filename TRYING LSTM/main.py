"""
Simple Stable LSTM Training Pipeline
User: Alysoffar
Current Date: 2025-09-03 19:08:11 UTC

Lightweight version that avoids getting stuck
"""

import sys
import os
import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
from sklearn.preprocessing import StandardScaler, LabelEncoder
from sklearn.metrics import mean_squared_error, mean_absolute_error, r2_score
import tensorflow as tf
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import LSTM, Dense, Dropout
from tensorflow.keras.optimizers import Adam
from tensorflow.keras.callbacks import EarlyStopping
import warnings
warnings.filterwarnings('ignore')

# Set TensorFlow to use less memory and avoid hanging
tf.config.threading.set_intra_op_parallelism_threads(2)
tf.config.threading.set_inter_op_parallelism_threads(2)

def create_simple_effective_dataset(file_path):
    """
    Create a simple but effective dataset
    """
    print("📊 Creating simple effective dataset...")
    
    # Load data
    df = pd.read_csv(file_path)
    df['Date'] = pd.to_datetime(df['Date'])
    df = df.sort_values(['Store ID', 'Product ID', 'Date']).reset_index(drop=True)
    
    print(f"   • Loaded {len(df):,} records")
    
    # Simple data cleaning
    df = df[(df['Units Sold'] >= 0) & (df['Price'] > 0) & (df['Inventory Level'] >= 0)]
    
    # Remove extreme outliers (keep only middle 95% of data)
    q_low = df['Units Sold'].quantile(0.025)
    q_high = df['Units Sold'].quantile(0.975)
    df = df[(df['Units Sold'] >= q_low) & (df['Units Sold'] <= q_high)]
    
    print(f"   • After cleaning: {len(df):,} records")
    
    # Filter to store-product combinations with sufficient data and variance
    combo_stats = df.groupby(['Store ID', 'Product ID'])['Units Sold'].agg(['count', 'std']).reset_index()
    good_combos = combo_stats[(combo_stats['count'] >= 30) & (combo_stats['std'] > 1.0)]
    
    # Keep only good combinations
    good_pairs = [(row['Store ID'], row['Product ID']) for _, row in good_combos.iterrows()]
    df_filtered = df[df.apply(lambda x: (x['Store ID'], x['Product ID']) in good_pairs, axis=1)]
    
    print(f"   • Valid combinations: {len(good_combos)}")
    print(f"   • Final dataset: {len(df_filtered):,} records")
    
    if len(df_filtered) < 1000:
        raise ValueError("Insufficient quality data after filtering")
    
    return df_filtered

def create_minimal_features(df):
    """
    Create only essential features to avoid complexity
    """
    print("⚙️ Creating minimal essential features...")
    
    df = df.copy()
    
    # Time features
    df['DayOfWeek'] = df['Date'].dt.dayofweek
    df['Month'] = df['Date'].dt.month
    df['IsWeekend'] = (df['DayOfWeek'] >= 5).astype(int)
    
    # Simple lag features
    df['Sales_Yesterday'] = df.groupby(['Store ID', 'Product ID'])['Units Sold'].shift(1)
    df['Sales_LastWeek'] = df.groupby(['Store ID', 'Product ID'])['Units Sold'].shift(7)
    
    # Simple rolling average
    df['Sales_Avg_7Days'] = df.groupby(['Store ID', 'Product ID'])['Units Sold'].transform(
        lambda x: x.rolling(7, min_periods=3).mean()
    )
    
    # Price features
    df['Has_Discount'] = (df['Discount'] > 0).astype(int)
    
    # Simple category encoding
    le_store = LabelEncoder()
    le_product = LabelEncoder()
    
    df['Store_Encoded'] = le_store.fit_transform(df['Store ID'])
    df['Product_Encoded'] = le_product.fit_transform(df['Product ID'])
    
    # Fill missing values
    df = df.fillna(method='ffill').fillna(method='bfill').fillna(0)
    
    # Select final features (keep it simple!)
    feature_columns = [
        'Inventory Level', 'Price', 'Demand Forecast',
        'DayOfWeek', 'Month', 'IsWeekend', 'Has_Discount',
        'Sales_Yesterday', 'Sales_LastWeek', 'Sales_Avg_7Days',
        'Store_Encoded', 'Product_Encoded'
    ]
    
    print(f"   • Selected {len(feature_columns)} features")
    
    return df, feature_columns

def create_simple_sequences(df, feature_columns, sequence_length=14):
    """
    Create sequences with simple logic
    """
    print(f"🔄 Creating sequences (length={sequence_length})...")
    
    sequences = []
    targets = []
    
    for (store, product), group in df.groupby(['Store ID', 'Product ID']):
        group = group.sort_values('Date').reset_index(drop=True)
        
        if len(group) < sequence_length + 3:
            continue
        
        # Skip if no variance
        if group['Units Sold'].std() < 1.0:
            continue
        
        # Create sequences
        for i in range(len(group) - sequence_length):
            seq = group[feature_columns].iloc[i:i+sequence_length].values
            target = group['Units Sold'].iloc[i + sequence_length]
            
            # Basic validation
            if not np.isnan(seq).any() and not np.isnan(target) and target > 0:
                sequences.append(seq)
                targets.append(target)
    
    X = np.array(sequences)
    y = np.array(targets)
    
    print(f"   • Created {len(X):,} sequences")
    print(f"   • Shape: {X.shape}")
    
    return X, y

def build_ultra_simple_lstm(sequence_length, n_features):
    """
    Build the simplest possible LSTM that works
    """
    print("🤖 Building ultra-simple LSTM...")
    
    model = Sequential([
        LSTM(16, input_shape=(sequence_length, n_features)),  # Very small
        Dropout(0.2),
        Dense(8, activation='relu'),
        Dense(1, activation='linear')
    ])
    
    model.compile(
        optimizer=Adam(learning_rate=0.01),  # Higher learning rate for faster convergence
        loss='mse',
        metrics=['mae']
    )
    
    print(f"   • Model parameters: {model.count_params():,}")
    
    return model

def train_simple_model(X_train, X_test, y_train, y_test):
    """
    Train with minimal complexity
    """
    print("🎯 Training simple model...")
    
    # Build model
    model = build_ultra_simple_lstm(X_train.shape[1], X_train.shape[2])
    
    # Simple callback - stop if not improving
    callbacks = [
        EarlyStopping(
            monitor='val_loss',
            patience=10,  # Short patience
            restore_best_weights=True,
            verbose=1
        )
    ]
    
    # Train with small epochs to avoid hanging
    print("   • Starting training (max 30 epochs)...")
    
    try:
        history = model.fit(
            X_train, y_train,
            validation_data=(X_test, y_test),
            epochs=30,  # Small number to avoid hanging
            batch_size=64,
            callbacks=callbacks,
            verbose=2  # Less verbose output
        )
        
        print("   ✅ Training completed!")
        return model, history
        
    except Exception as e:
        print(f"   ❌ Training failed: {e}")
        return None, None

def evaluate_simple_model(model, X_test, y_test, scaler_y):
    """
    Simple evaluation
    """
    print("📈 Evaluating model...")
    
    # Predictions
    y_pred = model.predict(X_test, verbose=0)
    
    # Inverse transform if needed
    if scaler_y is not None:
        y_pred = scaler_y.inverse_transform(y_pred).flatten()
        y_actual = scaler_y.inverse_transform(y_test.reshape(-1, 1)).flatten()
    else:
        y_pred = y_pred.flatten()
        y_actual = y_test
    
    # Ensure non-negative
    y_pred = np.maximum(y_pred, 0)
    
    # Calculate metrics
    mae = mean_absolute_error(y_actual, y_pred)
    rmse = np.sqrt(mean_squared_error(y_actual, y_pred))
    mape = np.mean(np.abs((y_actual - y_pred) / np.maximum(y_actual, 1))) * 100
    r2 = r2_score(y_actual, y_pred)
    
    print(f"\n📊 RESULTS:")
    print(f"   • MAE: {mae:.2f} units")
    print(f"   • RMSE: {rmse:.2f} units")
    print(f"   • MAPE: {mape:.2f}% {'✅' if mape < 50 else '❌'}")
    print(f"   • R²: {r2:.4f} {'✅' if r2 > 0.1 else '❌'}")
    
    # Quick assessment
    if mape < 30 and r2 > 0.3:
        print("🟢 GOOD: Model is usable!")
    elif mape < 50 and r2 > 0.1:
        print("🟡 FAIR: Model shows promise")
    else:
        print("🔴 POOR: Model needs work")
    
    return {'mae': mae, 'rmse': rmse, 'mape': mape, 'r2': r2}

def main():
    """
    Ultra-simple main function
    """
    print("🚀 ULTRA-SIMPLE LSTM TRAINING")
    print("=" * 50)
    print(f"👤 User: Alysoffar")
    print(f"📅 Time: 2025-09-03 19:08:11 UTC")
    print("=" * 50)
    
    try:
        # Step 1: Load and clean data
        df = create_simple_effective_dataset(r'D:\WORK\Findo\AIMODEL\data\retail_store_inventory.csv')
        
        # Step 2: Create features
        df_featured, feature_columns = create_minimal_features(df)
        
        # Step 3: Create sequences
        X, y = create_simple_sequences(df_featured, feature_columns, sequence_length=14)
        
        if len(X) < 100:
            raise ValueError("Not enough sequences created")
        
        # Step 4: Simple scaling
        print("🔧 Scaling data...")
        scaler_X = StandardScaler()
        scaler_y = StandardScaler()
        
        # Reshape for scaling
        n_samples, n_timesteps, n_features = X.shape
        X_reshaped = X.reshape(-1, n_features)
        X_scaled = scaler_X.fit_transform(X_reshaped)
        X_scaled = X_scaled.reshape(n_samples, n_timesteps, n_features)
        
        y_scaled = scaler_y.fit_transform(y.reshape(-1, 1)).flatten()
        
        # Step 5: Simple split
        split_idx = int(len(X_scaled) * 0.8)
        X_train, X_test = X_scaled[:split_idx], X_scaled[split_idx:]
        y_train, y_test = y_scaled[:split_idx], y_scaled[split_idx:]
        
        print(f"   • Training: {len(X_train):,} sequences")
        print(f"   • Testing: {len(X_test):,} sequences")
        
        # Step 6: Train
        model, history = train_simple_model(X_train, X_test, y_train, y_test)
        
        if model is None:
            raise ValueError("Model training failed")
        
        # Step 7: Evaluate
        metrics = evaluate_simple_model(model, X_test, y_test, scaler_y)
        
        # Step 8: Save if decent
        if metrics['mape'] < 80:  # Very lenient threshold
            print("\n💾 Saving model...")
            
            os.makedirs('models', exist_ok=True)
            model.save('models/simple_lstm_model.h5')
            
            import joblib
            joblib.dump(scaler_X, 'models/simple_feature_scaler.pkl')
            joblib.dump(scaler_y, 'models/simple_target_scaler.pkl')
            joblib.dump(feature_columns, 'models/simple_feature_columns.pkl')
            
            print("✅ Model saved!")
        
        print(f"\n🎉 COMPLETED!")
        print(f"   • Model MAPE: {metrics['mape']:.1f}%")
        print(f"   • Model R²: {metrics['r2']:.3f}")
        
        if metrics['mape'] < 50:
            print("   🟢 Ready to try PHP integration!")
        else:
            print("   🔴 Need to improve model first")
        
        return model, metrics
        
    except Exception as e:
        print(f"\n❌ ERROR: {str(e)}")
        import traceback
        traceback.print_exc()
        return None, None

if __name__ == "__main__":
    model, metrics = main()