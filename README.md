# 🚀 Complete Inventory Management & Forecasting System

A comprehensive solution combining advanced AI forecasting with a modern web-based inventory management interface. This project features both machine learning predictions and a full-featured Laravel web application for complete inventory control.

## 📋 Project Overview

This integrated system provides:

1. **🤖 LSTM Forecasting Engine**: Advanced deep learning for inventory prediction using Long Short-Term Memory neural networks with attention mechanisms
2. **� Web-Based Management Interface**: Modern Laravel application for real-time inventory tracking, automated restock, and comprehensive management

The LSTM engine uses historical sales data, weather conditions, pricing information, and other relevant features to generate accurate inventory forecasts, while the web interface provides intuitive management tools for day-to-day operations.

### System Components

#### 🤖 AI Forecasting Engine
- **Advanced LSTM Architecture**: Multi-layer LSTM with attention mechanism
- **Feature Engineering**: Comprehensive feature creation including rolling averages, lag features, and seasonal components
- **Custom Loss Function**: Asymmetric loss that penalizes underforecasting more than overforecasting
- **Real-time API**: Flask-based REST API for real-time predictions
- **Comprehensive Evaluation**: Multiple metrics including RMSE, MAE, MAPE, and R²
- **Visualization**: Training history and prediction visualization

#### 💻 Web Management Interface
- **Complete Inventory Tracking**: Real-time stock monitoring with low-stock alerts
- **Automated Restock System**: Automatic purchase orders when stock falls below thresholds
- **Email Notifications**: Instant alerts for low stock situations
- **GPS Location Tracking**: Track inventory locations with interactive maps
- **Modern Responsive UI**: Clean, gradient-based interface built with Bootstrap 5
- **Dashboard Analytics**: Visual insights into inventory levels and trends
- **Multi-location Support**: Track inventory across different warehouses/locations

## 🏗️ Project Architecture

```
├── 📁 AI Forecasting Engine
│   ├── data/                          # Dataset storage
│   │   └── retail_store_inventory.csv # Main dataset
│   ├── models/                        # Saved models and preprocessing objects
│   │   ├── lstm_inventory_model.h5    # Trained LSTM model
│   │   ├── feature_scaler.pkl         # Feature scaler
│   │   ├── target_scaler.pkl          # Target scaler
│   │   └── label_encoders.pkl         # Categorical encoders
│   ├── results/                       # Training results and visualizations
│   │   └── training_results.json     # Performance metrics
│   ├── TRYING LSTM/                   # Development and testing scripts
│   ├── Fixed_model.py                 # Main LSTM model implementation
│   ├── API_.py                       # REST API for predictions
│   └── dignosis.py                   # Model diagnostic tools
│
├── 📁 Web Management Interface (inventory management system UI/)
│   ├── app/                          # Laravel application core
│   │   ├── Controllers/              # Request controllers
│   │   ├── Models/                   # Database models
│   │   └── Jobs/                     # Background jobs
│   ├── resources/                    # Views and frontend assets
│   │   └── views/                    # Blade templates
│   ├── database/                     # Database migrations and seeders
│   │   └── migrations/               # Database schema
│   ├── routes/                       # Web and API routes
│   ├── config/                       # Configuration files
│   ├── public/                       # Public web assets
│   └── storage/                      # File storage and logs
│
└── 📄 Project Documentation
    ├── README.md                     # This comprehensive guide
    ├── .gitignore                    # Git ignore rules
    └── Sample_PHP_Connection.php     # PHP integration example
```

## 🚀 Quick Start

### Prerequisites

- **For AI Engine**: Python 3.8+ with virtual environment
- **For Web Interface**: PHP 8.2+, Composer, Node.js 16+, MySQL 8.0+, XAMPP (for local development)

### Setup - AI Forecasting Engine

1. **Clone the repository**
   ```bash
   git clone https://github.com/Alysoffar/Inventory-management-system.git
   cd Inventory-management-system
   ```

2. **Create and activate virtual environment**
   ```bash
   python -m venv .venv
   .venv\Scripts\activate  # Windows
   source .venv/bin/activate  # Linux/Mac
   ```

3. **Install AI dependencies**
   ```bash
   pip install tensorflow pandas numpy scikit-learn matplotlib seaborn joblib flask
   ```

4. **Train the LSTM model**
   ```bash
   python Fixed_model.py
   ```

5. **Start the AI prediction API**
   ```bash
   python API_.py
   ```

### Setup - Web Management Interface

1. **Navigate to web interface directory**
   ```bash
   cd "inventory management system UI"
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database setup**
   ```bash
   # Configure your database in .env file
   php artisan migrate
   php artisan db:seed
   ```

6. **Build assets**
   ```bash
   npm run dev
   ```

7. **Start the web server**
   ```bash
   php artisan serve
   ```

The web interface will be available at `http://localhost:8000`

## 🔄 Integrated Workflow

### How the Systems Work Together

1. **Data Collection**: The web interface collects real-time inventory data
2. **AI Analysis**: Historical data feeds into the LSTM model for training
3. **Predictions**: The AI engine generates forecasts via REST API
4. **Smart Decisions**: Web interface uses predictions for automated restock alerts
5. **Continuous Learning**: New data continuously improves model accuracy

### Integration Points

- **API Communication**: Web interface calls AI prediction endpoints
- **Shared Database**: Both systems can access the same inventory data
- **Automated Workflows**: Predictions trigger restock notifications
- **Dashboard Integration**: AI insights displayed in web dashboard

## 🌐 Web Interface Features

### User Interface Highlights
- **Modern Design**: Bootstrap 5 with gradient themes
- **Interactive Maps**: GPS tracking with Leaflet.js integration
- **Real-time Alerts**: Instant email notifications to `alysoffar06@gmail.com`
- **Mobile Responsive**: Works seamlessly across all devices
- **Dashboard Analytics**: Visual charts and inventory insights

### Core Web Functionalities
- **Inventory Management**: Add, edit, track all inventory items
- **Supplier Management**: Comprehensive supplier database
- **Location Tracking**: GPS coordinates for inventory locations  
- **Purchase History**: Complete audit trail of transactions
- **User Authentication**: Secure login with Laravel Sanctum
- **Automated Restock**: Smart reorder point calculations
- **Multi-warehouse**: Support for multiple locations

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

## 📱 Technology Stack

### AI Forecasting Engine
- **Backend**: Python 3.8+, TensorFlow 2.x, Flask
- **Data Processing**: Pandas, NumPy, Scikit-learn
- **Visualization**: Matplotlib, Seaborn
- **Storage**: Joblib for model persistence

### Web Management Interface  
- **Backend**: Laravel 10.x (PHP 8.2+)
- **Database**: MySQL 8.0+
- **Frontend**: Bootstrap 5, Leaflet.js for maps
- **Email**: Laravel Mail with SMTP support
- **Authentication**: Laravel Sanctum
- **Task Scheduling**: Laravel Scheduler for automated processes
- **Development**: XAMPP for local development

## 📊 Deployment Options

### Local Development
1. **AI Engine**: Python virtual environment + Flask dev server
2. **Web Interface**: XAMPP + Laravel artisan serve

### Production Deployment
1. **AI Engine**: Docker container + Gunicorn + Nginx
2. **Web Interface**: Apache/Nginx + PHP-FPM + MySQL

### Docker Integration
Both systems include Docker support:
- `Dockerfile` for containerized deployment
- `docker-compose.yml` for multi-service orchestration

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
- Repository: [Inventory-management-system](https://github.com/Alysoffar/Inventory-management-system)
- Contact: alysoffar06@gmail.com (for system notifications)

## 🏷️ Project Structure Summary

This repository contains two integrated systems:

1. **Root Directory**: AI forecasting engine with Python/TensorFlow
2. **inventory management system UI/**: Laravel web application

Both systems work together to provide a complete inventory management solution with AI-powered forecasting capabilities.

## 🙏 Acknowledgments

- TensorFlow team for the deep learning framework
- Scikit-learn contributors for preprocessing tools
- The open-source community for various utilities and libraries

---

*For questions, issues, or feature requests, please open an issue on GitHub.*
