"""
Improved Training Pipeline for LSTM Inventory Forecasting
User: Alysoffar
Current Date: 2025-09-03 19:02:25 UTC

This is a COMPLETE REPLACEMENT for the old training_pipeline.py
"""

import numpy as np
import matplotlib.pyplot as plt
from sklearn.preprocessing import RobustScaler
from sklearn.metrics import mean_squared_error, mean_absolute_error, r2_score
import tensorflow as tf
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import LSTM, Dense, Dropout, BatchNormalization, GRU
from tensorflow.keras.optimizers import Adam
from tensorflow.keras.callbacks import EarlyStopping, ReduceLROnPlateau, ModelCheckpoint
from tensorflow.keras.regularizers import l2
import joblib
from datetime import datetime

class ImprovedLSTMTrainer:
    def __init__(self, sequence_length=21):
        self.sequence_length = sequence_length
        self.model = None
        self.scaler_X = None
        self.scaler_y = None
        self.history = None
        
    def prepare_sequences(self, df, preprocessor, test_size=0.2, val_size=0.1):
        """
        Prepare sequences with improved validation and scaling
        """
        print("🔄 Preparing sequences for training...")
        
        # Create sequences for each store-product combination
        all_sequences = []
        all_targets = []
        
        successful_combinations = 0
        total_combinations = 0
        
        for (store, product), group in df.groupby(['Store ID', 'Product ID']):
            total_combinations += 1
            sequences, targets = preprocessor.create_sequences_for_store_product(
                group, self.sequence_length
            )
            
            if len(sequences) > 0:
                successful_combinations += 1
                all_sequences.extend(sequences)
                all_targets.extend(targets)
        
        print(f"   • Processed {total_combinations} store-product combinations")
        print(f"   • Successfully created sequences for {successful_combinations} combinations")
        
        if len(all_sequences) < 100:
            raise ValueError(f"Insufficient sequences created: {len(all_sequences)}")
        
        # Convert to numpy arrays
        X = np.array(all_sequences)
        y = np.array(all_targets)
        
        print(f"   • Total sequences: {len(X):,}")
        print(f"   • Sequence shape: {X.shape}")
        print(f"   • Target range: {y.min():.1f} - {y.max():.1f}")
        
        # Use RobustScaler for better outlier handling
        self.scaler_X = RobustScaler()
        self.scaler_y = RobustScaler()
        
        # Scale features
        n_samples, n_timesteps, n_features = X.shape
        X_reshaped = X.reshape(-1, n_features)
        X_scaled = self.scaler_X.fit_transform(X_reshaped)
        X_scaled = X_scaled.reshape(n_samples, n_timesteps, n_features)
        
        # Scale targets
        y_scaled = self.scaler_y.fit_transform(y.reshape(-1, 1)).flatten()
        
        # Time-series split (chronological)
        total_samples = len(X_scaled)
        train_size = int(total_samples * (1 - test_size - val_size))
        val_size_abs = int(total_samples * val_size)
        
        X_train = X_scaled[:train_size]
        X_val = X_scaled[train_size:train_size + val_size_abs]  
        X_test = X_scaled[train_size + val_size_abs:]
        
        y_train = y_scaled[:train_size]
        y_val = y_scaled[train_size:train_size + val_size_abs]
        y_test = y_scaled[train_size + val_size_abs:]
        
        print(f"   • Training: {len(X_train):,} sequences")
        print(f"   • Validation: {len(X_val):,} sequences")
        print(f"   • Test: {len(X_test):,} sequences")
        
        return X_train, X_val, X_test, y_train, y_val, y_test
    
    def build_optimized_lstm(self, n_features):
        """
        Build an optimized LSTM model with proper regularization
        """
        model = Sequential([
            # First LSTM layer with more conservative parameters
            LSTM(48, 
                 return_sequences=True,
                 input_shape=(self.sequence_length, n_features),
                 dropout=0.2,
                 recurrent_dropout=0.1,
                 kernel_regularizer=l2(0.001)),
            BatchNormalization(),
            
            # Second LSTM layer
            LSTM(24,
                 return_sequences=False,
                 dropout=0.2, 
                 recurrent_dropout=0.1,
                 kernel_regularizer=l2(0.001)),
            BatchNormalization(),
            
            # Dense layers
            Dense(16, 
                  activation='relu',
                  kernel_regularizer=l2(0.001)),
            Dropout(0.3),
            
            Dense(8, activation='relu'),
            Dropout(0.2),
            
            # Output layer
            Dense(1, activation='linear')
        ])
        
        return model
    
    def build_gru_alternative(self, n_features):
        """
        Build GRU alternative (sometimes works better than LSTM)
        """
        model = Sequential([
            GRU(48,
                return_sequences=True,
                input_shape=(self.sequence_length, n_features),
                dropout=0.2,
                recurrent_dropout=0.1),
            BatchNormalization(),
            
            GRU(24,
                return_sequences=False,
                dropout=0.2,
                recurrent_dropout=0.1),
            BatchNormalization(),
            
            Dense(16, activation='relu'),
            Dropout(0.3),
            
            Dense(8, activation='relu'),
            Dropout(0.2),
            
            Dense(1, activation='linear')
        ])
        
        return model
    
    def train_with_model_selection(self, X_train, X_val, X_test, y_train, y_val, y_test):
        """
        Train multiple model architectures and select the best one
        """
        print("🤖 Training with model selection...")
        
        models_to_try = [
            ("Optimized LSTM", self.build_optimized_lstm),
            ("GRU Alternative", self.build_gru_alternative)
        ]
        
        best_model = None
        best_val_loss = float('inf')
        best_name = None
        best_history = None
        
        for name, model_builder in models_to_try:
            print(f"\n🔄 Training {name}...")
            
            try:
                # Build model
                model = model_builder(X_train.shape[2])
                
                # Compile with optimal settings
                model.compile(
                    optimizer=Adam(learning_rate=0.001),
                    loss='huber',  # More robust than MSE for outliers
                    metrics=['mae']
                )
                
                print(f"   • Model parameters: {model.count_params():,}")
                
                # Callbacks for optimal training
                callbacks = [
                    EarlyStopping(
                        monitor='val_loss',
                        patience=25,  # Generous patience
                        restore_best_weights=True,
                        verbose=0
                    ),
                    ReduceLROnPlateau(
                        monitor='val_loss',
                        factor=0.5,
                        patience=12,
                        min_lr=0.00001,
                        verbose=0
                    )
                ]
                
                # Train model
                history = model.fit(
                    X_train, y_train,
                    validation_data=(X_val, y_val),
                    epochs=100,
                    batch_size=32,
                    callbacks=callbacks,
                    verbose=1
                )
                
                # Evaluate on validation set
                final_val_loss = min(history.history['val_loss'])
                print(f"   • Best validation loss: {final_val_loss:.4f}")
                
                # Keep track of best model
                if final_val_loss < best_val_loss:
                    best_val_loss = final_val_loss
                    best_model = model
                    best_name = name
                    best_history = history
                    
            except Exception as e:
                print(f"   ❌ {name} failed: {str(e)}")
                continue
        
        if best_model is None:
            raise ValueError("All model architectures failed!")
        
        print(f"\n✅ Best model: {best_name}")
        print(f"   • Best validation loss: {best_val_loss:.4f}")
        
        self.model = best_model
        self.history = best_history
        
        return best_model, best_history
    
    def evaluate_model_performance(self, X_test, y_test):
        """
        Comprehensive model evaluation
        """
        print("\n📈 Evaluating model performance...")
        
        # Make predictions
        y_pred_scaled = self.model.predict(X_test, verbose=0)
        
        # Inverse transform predictions
        y_pred = self.scaler_y.inverse_transform(y_pred_scaled.reshape(-1, 1)).flatten()
        y_actual = self.scaler_y.inverse_transform(y_test.reshape(-1, 1)).flatten()
        
        # Ensure non-negative predictions
        y_pred = np.maximum(y_pred, 0)
        
        # Calculate comprehensive metrics
        mae = mean_absolute_error(y_actual, y_pred)
        mse = mean_squared_error(y_actual, y_pred)
        rmse = np.sqrt(mse)
        
        # MAPE with safe division
        mape = np.mean(np.abs((y_actual - y_pred) / np.maximum(y_actual, 1))) * 100
        
        # R-squared
        r2 = r2_score(y_actual, y_pred)
        
        # Additional business metrics
        median_ae = np.median(np.abs(y_actual - y_pred))
        max_error = np.max(np.abs(y_actual - y_pred))
        
        # Prediction accuracy within ranges
        within_10pct = np.mean(np.abs((y_actual - y_pred) / np.maximum(y_actual, 1)) <= 0.1) * 100
        within_20pct = np.mean(np.abs((y_actual - y_pred) / np.maximum(y_actual, 1)) <= 0.2) * 100
        
        # Print comprehensive results
        print(f"\n{'='*60}")
        print(f"🎯 MODEL PERFORMANCE EVALUATION")
        print(f"👤 User: Alysoffar")
        print(f"📅 Evaluated: 2025-09-03 19:02:25 UTC")
        print(f"{'='*60}")
        
        print(f"📊 ACCURACY METRICS:")
        print(f"   • Mean Absolute Error (MAE): {mae:.2f} units")
        print(f"   • Root Mean Square Error (RMSE): {rmse:.2f} units")
        print(f"   • Mean Absolute Percentage Error (MAPE): {mape:.2f}%")
        print(f"   • R-squared (R²): {r2:.4f}")
        print(f"   • Median Absolute Error: {median_ae:.2f} units")
        print(f"   • Maximum Error: {max_error:.2f} units")
        
        print(f"\n📈 PREDICTION ACCURACY:")
        print(f"   • Within 10%: {within_10pct:.1f}% of predictions")
        print(f"   • Within 20%: {within_20pct:.1f}% of predictions")
        
        # Model quality assessment
        print(f"\n🏆 OVERALL ASSESSMENT:")
        if mape < 15 and r2 > 0.6:
            quality = "🟢 EXCELLENT - Production Ready"
        elif mape < 25 and r2 > 0.4:
            quality = "🟡 GOOD - Usable with Monitoring"
        elif mape < 40 and r2 > 0.2:
            quality = "🟠 FAIR - Needs Improvement"
        else:
            quality = "🔴 POOR - Requires Major Rework"
        
        print(f"   {quality}")
        
        # Business recommendations
        print(f"\n💼 BUSINESS RECOMMENDATIONS:")
        if mape < 30:
            print("   ✅ Model suitable for inventory planning")
            print("   ✅ Can be integrated into production systems")
            print("   ✅ Provides reliable demand forecasting")
        else:
            print("   ❌ Model accuracy insufficient for business use")
            print("   ❌ Requires data quality improvement")
            print("   ❌ Consider alternative forecasting methods")
        
        # Store metrics
        metrics = {
            'mae': mae,
            'rmse': rmse, 
            'mape': mape,
            'r2': r2,
            'median_ae': median_ae,
            'max_error': max_error,
            'within_10pct': within_10pct,
            'within_20pct': within_20pct,
            'is_production_ready': mape < 30 and r2 > 0.3
        }
        
        return y_pred, y_actual, metrics
    
    def create_performance_visualizations(self, y_actual, y_pred, metrics):
        """
        Create comprehensive performance visualizations
        """
        plt.style.use('default')
        fig, axes = plt.subplots(2, 3, figsize=(18, 12))
        fig.suptitle('LSTM Model Performance Analysis - Alysoffar (2025-09-03)', fontsize=16, fontweight='bold')
        
        # 1. Training history
        if self.history:
            axes[0, 0].plot(self.history.history['loss'], label='Training Loss', alpha=0.8)
            axes[0, 0].plot(self.history.history['val_loss'], label='Validation Loss', alpha=0.8)
            axes[0, 0].set_title('Training History')
            axes[0, 0].set_xlabel('Epoch')
            axes[0, 0].set_ylabel('Loss')
            axes[0, 0].legend()
            axes[0, 0].grid(True, alpha=0.3)
        
        # 2. Actual vs Predicted scatter
        axes[0, 1].scatter(y_actual, y_pred, alpha=0.6, s=20)
        min_val, max_val = min(y_actual.min(), y_pred.min()), max(y_actual.max(), y_pred.max())
        axes[0, 1].plot([min_val, max_val], [min_val, max_val], 'r--', lw=2, alpha=0.8)
        axes[0, 1].set_xlabel('Actual Sales')
        axes[0, 1].set_ylabel('Predicted Sales')
        axes[0, 1].set_title(f'Actual vs Predicted (R² = {metrics["r2"]:.3f})')
        axes[0, 1].grid(True, alpha=0.3)
        
        # 3. Residuals plot
        residuals = y_actual - y_pred
        axes[0, 2].scatter(y_pred, residuals, alpha=0.6, s=20)
        axes[0, 2].axhline(y=0, color='r', linestyle='--', alpha=0.8)
        axes[0, 2].set_xlabel('Predicted Sales')
        axes[0, 2].set_ylabel('Residuals')
        axes[0, 2].set_title('Residuals Plot')
        axes[0, 2].grid(True, alpha=0.3)
        
        # 4. Error distribution
        axes[1, 0].hist(residuals, bins=50, alpha=0.7, edgecolor='black')
        axes[1, 0].axvline(0, color='r', linestyle='--', alpha=0.8)
        axes[1, 0].set_xlabel('Prediction Errors')
        axes[1, 0].set_ylabel('Frequency')
        axes[1, 0].set_title('Error Distribution')
        axes[1, 0].grid(True, alpha=0.3)
        
        # 5. Time series comparison (sample)
        sample_size = min(100, len(y_actual))
        sample_indices = np.random.choice(len(y_actual), sample_size, replace=False)
        sample_indices = sorted(sample_indices)
        
        axes[1, 1].plot(y_actual[sample_indices], label='Actual', alpha=0.8)
        axes[1, 1].plot(y_pred[sample_indices], label='Predicted', alpha=0.8)
        axes[1, 1].set_xlabel('Time Steps (Sample)')
        axes[1, 1].set_ylabel('Units Sold')
        axes[1, 1].set_title('Time Series Comparison (Random Sample)')
        axes[1, 1].legend()
        axes[1, 1].grid(True, alpha=0.3)
        
        # 6. Metrics summary
        axes[1, 2].text(0.1, 0.8, f'MAPE: {metrics["mape"]:.1f}%', fontsize=14, fontweight='bold')
        axes[1, 2].text(0.1, 0.7, f'MAE: {metrics["mae"]:.2f}', fontsize=12)
        axes[1, 2].text(0.1, 0.6, f'RMSE: {metrics["rmse"]:.2f}', fontsize=12)
        axes[1, 2].text(0.1, 0.5, f'R²: {metrics["r2"]:.3f}', fontsize=12)
        axes[1, 2].text(0.1, 0.4, f'Within 20%: {metrics["within_20pct"]:.1f}%', fontsize=12)
        
        # Color code based on performance
        if metrics['mape'] < 30:
            axes[1, 2].text(0.1, 0.2, '✅ Production Ready', fontsize=14, color='green', fontweight='bold')
        else:
            axes[1, 2].text(0.1, 0.2, '❌ Needs Improvement', fontsize=14, color='red', fontweight='bold')
        
        axes[1, 2].set_xlim(0, 1)
        axes[1, 2].set_ylim(0, 1)
        axes[1, 2].axis('off')
        axes[1, 2].set_title('Performance Summary')
        
        plt.tight_layout()
        plt.savefig('model_performance_analysis.png', dpi=300, bbox_inches='tight')
        plt.show()
        
        return fig
    
    def generate_sample_forecasts(self, X_test, steps=7):
        """
        Generate sample forecasts for demonstration
        """
        print(f"\n🔮 Generating {steps}-day forecasts...")
        
        if len(X_test) < 5:
            return [0] * steps
        
        # Use last 5 sequences for forecasting
        sample_sequences = X_test[-5:]
        forecasts = []
        
        for seq in sample_sequences:
            current_seq = seq.copy()
            sequence_forecasts = []
            
            for day in range(steps):
                try:
                    # Predict next day
                    pred_scaled = self.model.predict(
                        current_seq.reshape(1, *current_seq.shape), 
                        verbose=0
                    )
                    
                    # Convert back to original scale
                    pred = self.scaler_y.inverse_transform(pred_scaled)[0, 0]
                    pred = max(0, pred)  # Ensure non-negative
                    
                    sequence_forecasts.append(pred)
                    
                    # Update sequence (simplified approach)
                    # In production, you'd properly update all features
                    new_seq = np.roll(current_seq, -1, axis=0)
                    new_seq[-1] = new_seq[-2]  # Copy last values
                    current_seq = new_seq
                    
                except Exception as e:
                    print(f"      Warning: Forecasting error at day {day}: {e}")
                    break
            
            if sequence_forecasts:
                forecasts.append(sequence_forecasts)
        
        if not forecasts:
            return [0] * steps
        
        # Average across all sample sequences
        min_length = min(len(f) for f in forecasts)
        avg_forecasts = []
        
        for day in range(min_length):
            day_avg = np.mean([f[day] for f in forecasts])
            avg_forecasts.append(day_avg)
        
        print(f"   📊 Sample forecasts (average across {len(forecasts)} sequences):")
        for i, forecast in enumerate(avg_forecasts, 1):
            print(f"      Day {i}: {forecast:.1f} units")
        
        return avg_forecasts
    
    def save_model_and_artifacts(self, feature_columns, metrics):
        """
        Save the trained model and all necessary artifacts
        """
        print("\n💾 Saving model and artifacts...")
        
        # Create directories
        import os
        os.makedirs('models', exist_ok=True)
        os.makedirs('results', exist_ok=True)
        
        # Save model
        model_path = 'models/improved_lstm_model.h5'
        self.model.save(model_path)
        print(f"   ✅ Model saved: {model_path}")
        
        # Save scalers
        joblib.dump(self.scaler_X, 'models/improved_feature_scaler.pkl')
        joblib.dump(self.scaler_y, 'models/improved_target_scaler.pkl')
        print("   ✅ Scalers saved")
        
        # Save feature columns
        joblib.dump(feature_columns, 'models/improved_feature_columns.pkl')
        print("   ✅ Feature columns saved")
        
        # Save comprehensive results
        results = {
            'timestamp': '2025-09-03 19:02:25 UTC',
            'user': 'Alysoffar',
            'model_type': 'Improved LSTM',
            'sequence_length': self.sequence_length,
            'n_features': len(feature_columns),
            'metrics': {k: float(v) if isinstance(v, (int, float, np.number)) else v 
                       for k, v in metrics.items()},
            'feature_columns': feature_columns,
            'is_production_ready': metrics.get('is_production_ready', False)
        }
        
        import json
        with open('results/improved_training_results.json', 'w') as f:
            json.dump(results, f, indent=2, default=str)
        
        print("   ✅ Results saved: results/improved_training_results.json")
        
        return results

if __name__ == "__main__":
    print("This module should be imported, not run directly.")
    print("Use main_improved.py to run the complete training pipeline.")