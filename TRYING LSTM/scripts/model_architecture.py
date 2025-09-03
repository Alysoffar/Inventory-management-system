"""
Improved LSTM Model Architecture
User: Alysoffar  
Date: 2025-09-03 19:00:01 UTC
"""

import tensorflow as tf
from tensorflow.keras.models import Sequential
from tensorflow.keras.layers import LSTM, Dense, Dropout, BatchNormalization, GRU
from tensorflow.keras.optimizers import Adam
from tensorflow.keras.regularizers import l2

def build_simple_effective_lstm(sequence_length, n_features, 
                               lstm_units=[32, 16], dense_units=[8], 
                               dropout_rate=0.2, l2_reg=0.001):
    """
    Build a simple but effective LSTM model
    Reduced complexity to prevent overfitting on small datasets
    """
    model = Sequential([
        # First LSTM layer
        LSTM(lstm_units[0], 
             return_sequences=True, 
             input_shape=(sequence_length, n_features),
             dropout=dropout_rate,
             recurrent_dropout=dropout_rate,
             kernel_regularizer=l2(l2_reg)),
        BatchNormalization(),
        
        # Second LSTM layer  
        LSTM(lstm_units[1], 
             return_sequences=False,
             dropout=dropout_rate,
             recurrent_dropout=dropout_rate,
             kernel_regularizer=l2(l2_reg)),
        BatchNormalization(),
        
        # Dense layer
        Dense(dense_units[0], 
              activation='relu',
              kernel_regularizer=l2(l2_reg)),
        Dropout(dropout_rate),
        
        # Output layer
        Dense(1, activation='linear')
    ])
    
    return model

def build_gru_model(sequence_length, n_features,
                   gru_units=[32, 16], dense_units=[8],
                   dropout_rate=0.2):
    """
    Alternative GRU model (sometimes works better than LSTM)
    """
    model = Sequential([
        GRU(gru_units[0], 
            return_sequences=True,
            input_shape=(sequence_length, n_features),
            dropout=dropout_rate,
            recurrent_dropout=dropout_rate),
        BatchNormalization(),
        
        GRU(gru_units[1],
            return_sequences=False, 
            dropout=dropout_rate,
            recurrent_dropout=dropout_rate),
        BatchNormalization(),
        
        Dense(dense_units[0], activation='relu'),
        Dropout(dropout_rate),
        
        Dense(1, activation='linear')
    ])
    
    return model

def get_model_compiler_config():
    """
    Return optimal compiler configuration
    """
    return {
        'optimizer': Adam(learning_rate=0.001),
        'loss': 'huber',  # More robust to outliers than MSE
        'metrics': ['mae']
    }

def get_training_callbacks():
    """
    Return optimized callbacks for training
    """
    from tensorflow.keras.callbacks import EarlyStopping, ReduceLROnPlateau
    
    return [
        EarlyStopping(
            monitor='val_loss',
            patience=25,  # Increased patience for better convergence
            restore_best_weights=True,
            verbose=1
        ),
        ReduceLROnPlateau(
            monitor='val_loss',
            factor=0.7,
            patience=15,
            min_lr=0.00001,
            verbose=1
        )
    ]