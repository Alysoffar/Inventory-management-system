# LSTM Inventory Forecasting System

A sophisticated deep learning solution for retail inventory forecasting using Long Short-Term Memory (LSTM) neural networks with attention mechanisms.

## 📋 Project Overview

This project implements an advanced LSTM-based forecasting system designed to predict inventory levels for retail stores. The system uses historical sales data, weather conditions, pricing information, and other relevant features to generate accurate inventory forecasts.

### Key Features

- **Advanced LSTM Architecture**: Multi-layer LSTM with attention mechanism
- **Feature Engineering**: Comprehensive feature creation including rolling averages, lag features, and seasonal components
- **Custom Loss Function**: Asymmetric loss that penalizes underforecasting more than overforecasting
- **Real-time API**: Flask-based REST API for real-time predictions
- **Comprehensive Evaluation**: Multiple metrics including RMSE, MAE, MAPE, and R²
- **Visualization**: Training history and prediction visualization

## 🏗️ Architecture

```
├── data/                          # Dataset storage
│   └── retail_store_inventory.csv # Main dataset
├── models/                        # Saved models and preprocessing objects
│   ├── lstm_inventory_model.h5    # Trained LSTM model
│   ├── feature_scaler.pkl         # Feature scaler
│   ├── target_scaler.pkl          # Target scaler
│   └── label_encoders.pkl         # Categorical encoders
├── results/                       # Training results and visualizations
│   └── training_results.json     # Performance metrics
├── TRYING LSTM/                   # Development and testing scripts
├── diogonistic images/            # Diagnostic plots and visualizations
├── Fixed_model.py                 # Main LSTM model implementation
├── API_.py                       # REST API for predictions
├── dignosis.py                   # Model diagnostic tools
└── Sample_PHP_Connection.php      # PHP integration example
```

## 🚀 Quick Start

### Prerequisites

- Python 3.8 or higher
- Virtual environment (recommended)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd AIMODEL
   ```

2. **Create and activate virtual environment**
   ```bash
   python -m venv .venv
   .venv\Scripts\activate  # Windows
   source .venv/bin/activate  # Linux/Mac
   ```

3. **Install dependencies**
   ```bash
   pip install tensorflow pandas numpy scikit-learn matplotlib seaborn joblib flask
   ```

### Training the Model

1. **Prepare your data**
   - Place your CSV file in the `data/` directory
   - Ensure your data has columns: Date, Store ID, Product ID, Units Sold, etc.

2. **Run the training script**
   ```bash
   python Fixed_model.py
   ```

3. **Monitor training progress**
   - The script will display real-time training metrics
   - Visualizations will be saved in the `results/` directory

### Using the API

1. **Start the API server**
   ```bash
   python API_.py
   ```

2. **Make predictions**
   ```bash
   curl -X POST http://localhost:5000/predict \
   -H "Content-Type: application/json" \
   -d '{
     "store_id": "S001",
     "product_id": "P001",
     "features": [...]
   }'
   ```

## 📊 Dataset Requirements

Your CSV file should contain the following columns:

| Column | Description | Type |
|--------|-------------|------|
| Date | Transaction date | datetime |
| Store ID | Unique store identifier | string |
| Product ID | Unique product identifier | string |
| Category | Product category | string |
| Units Sold | Number of units sold | numeric |
| Inventory Level | Current inventory level | numeric |
| Price | Product price | numeric |
| Discount | Discount percentage | numeric |
| Weather Condition | Weather description | string |
| Region | Store region | string |
| Demand Forecast | Predicted demand | numeric |
| Competitor Pricing | Competitor prices | numeric |
| Units Ordered | Units ordered | numeric |

## 🎯 Model Performance

The LSTM model achieves the following performance metrics on the test dataset:

- **RMSE**: Root Mean Square Error for prediction accuracy
- **MAE**: Mean Absolute Error for average prediction deviation
- **MAPE**: Mean Absolute Percentage Error for relative accuracy
- **R²**: Coefficient of determination for model fit quality

Performance metrics are automatically saved to `results/training_results.json` after each training run.

## 🔧 Configuration

### Model Hyperparameters

You can adjust the following parameters in `Fixed_model.py`:

```python
SEQUENCE_LENGTH = 14    # Days of history to use
EPOCHS = 100           # Training iterations
BATCH_SIZE = 64        # Batch size
LEARNING_RATE = 0.001  # Learning rate
```

### Feature Engineering

The system automatically creates:

- **Rolling averages**: 3, 7, 14, 30-day windows
- **Lag features**: 1, 2, 3, 7, 14-day lags
- **Price features**: Price changes, discounts, competitor ratios
- **Inventory metrics**: Turnover, stockout risk, reorder points
- **Seasonal features**: Cyclical encoding of time components

## 📈 API Endpoints

### POST /predict
Generate inventory predictions for specific store-product combinations.

**Request:**
```json
{
  "store_id": "S001",
  "product_id": "P001",
  "sequence_data": [
    {
      "date": "2025-09-01",
      "units_sold": 45,
      "inventory_level": 120,
      "price": 29.99,
      ...
    }
  ]
}
```

**Response:**
```json
{
  "prediction": 42.5,
  "confidence": 0.87,
  "timestamp": "2025-09-03T15:30:00Z"
}
```

### GET /health
Check API health status.

## 🛠️ Development

### Running Diagnostics

```bash
python dignosis.py
```

This will generate diagnostic plots and model performance analysis.

### Model Architecture Details

The LSTM model features:

- **Input Layer**: Sequences of historical data
- **LSTM Layers**: Two stacked LSTM layers with dropout and batch normalization
- **Attention Mechanism**: Simplified attention for focusing on relevant time steps
- **Dense Layers**: Fully connected layers with regularization
- **Output Layer**: Single unit for inventory prediction

### Custom Loss Function

The model uses an asymmetric loss function that penalizes underforecasting (stockouts) more heavily than overforecasting (excess inventory):

```python
def asymmetric_loss(y_true, y_pred):
    error = y_true - y_pred
    return tf.reduce_mean(tf.where(error >= 0, 
                                 2.0 * tf.square(error),  # Higher penalty for underforecasting
                                 tf.square(error)))       # Normal penalty for overforecasting
```

## 📋 Troubleshooting

### Common Issues

1. **Memory errors**: Reduce batch size or sequence length
2. **Convergence issues**: Adjust learning rate or add more regularization
3. **Poor performance**: Check data quality and feature scaling
4. **API errors**: Ensure all dependencies are installed and ports are available

### Performance Optimization

- Use GPU acceleration with CUDA-enabled TensorFlow
- Implement early stopping to prevent overfitting
- Use learning rate scheduling for better convergence
- Consider ensemble methods for improved accuracy

## 📚 References

- [LSTM Networks](https://colah.github.io/posts/2015-08-Understanding-LSTMs/)
- [Attention Mechanisms](https://arxiv.org/abs/1706.03762)
- [Time Series Forecasting](https://otexts.com/fpp3/)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👨‍💻 Author

**Alysoffar**
- Project started: September 3, 2025
- GitHub: [Alysoffar](https://github.com/Alysoffar)

## 🙏 Acknowledgments

- TensorFlow team for the deep learning framework
- Scikit-learn contributors for preprocessing tools
- The open-source community for various utilities and libraries

---

*For questions, issues, or feature requests, please open an issue on GitHub.*
