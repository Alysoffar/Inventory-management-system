# 🔗 Integration Guide: AI Engine + Web Interface

This guide explains how to integrate the LSTM AI forecasting engine with the Laravel web interface.

## 🎯 Integration Overview

The system consists of two main components:
1. **Python AI Engine**: Handles machine learning predictions
2. **Laravel Web Interface**: Manages inventory and user interactions

## 🔄 Data Flow

```
[Web Interface] → [Database] → [AI Engine] → [Predictions] → [Web Dashboard]
```

## 🛠️ Integration Steps

### 1. Database Integration

Both systems should share the same MySQL database:

**AI Engine Configuration:**
```python
# In your Python scripts, connect to the same database
import mysql.connector

config = {
    'user': 'laravel_user',
    'password': 'your_password',
    'host': 'localhost',
    'database': 'inventory_management',
    'raise_on_warnings': True
}

connection = mysql.connector.connect(**config)
```

**Laravel Configuration:**
```php
// In .env file
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_management
DB_USERNAME=laravel_user
DB_PASSWORD=your_password
```

### 2. API Integration

**Python AI API Endpoint:**
```python
# API_.py - Add prediction endpoint
@app.route('/api/predict', methods=['POST'])
def predict_inventory():
    data = request.get_json()
    
    # Process prediction
    prediction = model.predict(data['features'])
    
    return jsonify({
        'prediction': float(prediction[0]),
        'confidence': calculate_confidence(prediction),
        'timestamp': datetime.now().isoformat()
    })
```

**Laravel Integration:**
```php
// In Laravel Controller
use Illuminate\Support\Facades\Http;

class ForecastController extends Controller
{
    public function getPrediction($productId, $storeId)
    {
        $features = $this->prepareFeatures($productId, $storeId);
        
        $response = Http::post('http://localhost:5000/api/predict', [
            'features' => $features
        ]);
        
        return $response->json();
    }
    
    private function prepareFeatures($productId, $storeId)
    {
        // Fetch last 14 days of data for LSTM input
        return InventoryTransaction::where('product_id', $productId)
            ->where('store_id', $storeId)
            ->orderBy('date', 'desc')
            ->take(14)
            ->get()
            ->map(function($item) {
                return [
                    $item->units_sold,
                    $item->inventory_level,
                    $item->price,
                    // ... other features
                ];
            })->flatten()->toArray();
    }
}
```

### 3. Automated Workflow Integration

**Laravel Job for AI Predictions:**
```php
// app/Jobs/GenerateForecasts.php
class GenerateForecasts implements ShouldQueue
{
    public function handle()
    {
        $products = Product::where('active', true)->get();
        
        foreach ($products as $product) {
            $forecast = app(ForecastController::class)
                ->getPrediction($product->id, $product->store_id);
                
            // Update product with forecast
            $product->update([
                'predicted_demand' => $forecast['prediction'],
                'forecast_confidence' => $forecast['confidence'],
                'forecast_date' => now()
            ]);
            
            // Check if restock needed
            if ($product->inventory_level < $forecast['prediction']) {
                event(new RestockNeeded($product));
            }
        }
    }
}
```

**Schedule in Laravel:**
```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->job(new GenerateForecasts())
        ->dailyAt('02:00')
        ->emailOutputOnFailure('alysoffar06@gmail.com');
}
```

### 4. Dashboard Integration

**Blade Template for Forecasts:**
```html
<!-- resources/views/dashboard/forecasts.blade.php -->
<div class="card">
    <div class="card-header">
        <h5>AI Forecast Results</h5>
    </div>
    <div class="card-body">
        @foreach($products as $product)
        <div class="row mb-3">
            <div class="col-md-4">{{ $product->name }}</div>
            <div class="col-md-3">
                Current: {{ $product->inventory_level }}
            </div>
            <div class="col-md-3">
                Predicted: {{ $product->predicted_demand }}
            </div>
            <div class="col-md-2">
                @if($product->inventory_level < $product->predicted_demand)
                    <span class="badge bg-warning">Restock Needed</span>
                @else
                    <span class="badge bg-success">Stock OK</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
</div>
```

## 🔧 Configuration Files

### Environment Variables

**AI Engine (.env):**
```env
FLASK_APP=API_.py
FLASK_ENV=development
DATABASE_URL=mysql://user:password@localhost/inventory_management
MODEL_PATH=models/lstm_inventory_model.h5
```

**Laravel (.env):**
```env
AI_API_URL=http://localhost:5000
AI_API_TOKEN=your_api_token
FORECAST_SCHEDULE=daily
EMAIL_NOTIFICATIONS=true
```

## 📊 Monitoring Integration

### Health Checks
```php
// Laravel route for AI engine health
Route::get('/ai-health', function() {
    try {
        $response = Http::timeout(5)->get(config('app.ai_api_url') . '/health');
        return response()->json(['ai_engine' => 'online']);
    } catch (Exception $e) {
        return response()->json(['ai_engine' => 'offline'], 503);
    }
});
```

## 🚀 Deployment Integration

### Docker Compose
```yaml
version: '3.8'
services:
  ai-engine:
    build: .
    ports:
      - "5000:5000"
    environment:
      - FLASK_ENV=production
    
  web-interface:
    build: ./inventory\ management\ system\ UI
    ports:
      - "80:80"
    depends_on:
      - mysql
      - ai-engine
    
  mysql:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: root
      MYSQL_DATABASE: inventory_management
```

## 🔒 Security Considerations

1. **API Authentication**: Use tokens for AI API access
2. **Database Security**: Separate user permissions
3. **Environment Variables**: Never commit sensitive data
4. **HTTPS**: Use SSL in production
5. **Input Validation**: Sanitize all data inputs

## 📈 Performance Optimization

1. **Caching**: Cache AI predictions in Redis
2. **Queue System**: Use Laravel queues for heavy operations
3. **Database Indexing**: Index frequently queried columns
4. **API Rate Limiting**: Limit API calls to prevent overload

This integration ensures both systems work seamlessly together while maintaining their individual strengths.
