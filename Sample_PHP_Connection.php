<?php
/**
 * Inventory Prediction Class for PHP
 * User: Alysoffar
 * Date: 2025-09-03 19:18:54 UTC
 */

class InventoryPredictor {
    private $api_url = 'http://localhost:5000';
    
    public function predictSales($data) {
        $postData = json_encode($data);
        
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/json\r\n",
                'content' => $postData
            ]
        ]);
        
        $response = file_get_contents($this->api_url . '/predict', false, $context);
        
        if ($response === false) {
            return ['success' => false, 'error' => 'API request failed'];
        }
        
        return json_decode($response, true);
    }
    
    public function formatRecommendation($prediction) {
        if (!$prediction['success']) {
            return "❌ Error: " . $prediction['error'];
        }
        
        $html = "<div class='inventory-prediction'>";
        $html .= "<h3>🤖 AI Inventory Forecast</h3>";
        $html .= "<p><strong>Predicted Sales:</strong> {$prediction['predicted_sales']} units</p>";
        $html .= "<p><strong>Current Stock:</strong> {$prediction['current_inventory']} units</p>";
        $html .= "<p><strong>Days of Stock:</strong> {$prediction['days_of_stock']} days</p>";
        
        if ($prediction['should_reorder']) {
            $html .= "<div class='alert alert-warning'>";
            $html .= "⚠️ <strong>Reorder Recommended!</strong><br>";
            $html .= "Suggested Order: {$prediction['recommended_order_qty']} units";
            $html .= "</div>";
        } else {
            $html .= "<div class='alert alert-success'>";
            $html .= "✅ Stock levels adequate";
            $html .= "</div>";
        }
        
        $html .= "<small>Model Performance: {$prediction['model_performance']['mape']} MAPE, R² {$prediction['model_performance']['r2']}</small>";
        $html .= "</div>";
        
        return $html;
    }
}

// Example usage
$predictor = new InventoryPredictor();

$inventoryData = [
    'Demand Forecast' => 150,
    'Inventory Level' => 200,
    'Price' => 25.50,
    'Store ID' => 'S001',
    'Product ID' => 'P001',
    'Date' => '2025-09-04'
];

$result = $predictor->predictSales($inventoryData);
echo $predictor->formatRecommendation($result);
?>