# 🤝 Contributing Guide

Thank you for your interest in contributing to the AI-Powered Inventory Management System! This guide will help you get started with contributing to the project.

## 📋 Table of Contents

- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Code Standards](#code-standards)
- [Workflow](#workflow)
- [Testing](#testing)
- [Documentation](#documentation)
- [Issue Reporting](#issue-reporting)

## 🚀 Getting Started

### Prerequisites

Before contributing, ensure you have:
- **Git** knowledge and GitHub account
- **PHP 8.2+** with required extensions
- **Python 3.8+** for AI components
- **Composer** for PHP dependencies
- **Node.js 16+** for frontend assets
- **MySQL** for database

### First-Time Setup

1. **Fork the Repository**
   ```bash
   # Fork the repo on GitHub, then clone your fork
   git clone https://github.com/YOUR-USERNAME/Inventory-management-system.git
   cd Inventory-management-system
   ```

2. **Add Upstream Remote**
   ```bash
   git remote add upstream https://github.com/Alysoffar/Inventory-management-system.git
   ```

3. **Follow Installation Guide**
   - Complete the setup instructions in README.md
   - Ensure both AI API and Laravel app are running

## 🛠️ Development Setup

### Branch Strategy

- **main**: Production-ready code
- **develop**: Integration branch for features
- **feature/**: New features (`feature/ai-improvements`)
- **bugfix/**: Bug fixes (`bugfix/dashboard-spacing`)
- **hotfix/**: Critical production fixes

### Creating Feature Branch

```bash
# Update your fork
git checkout main
git pull upstream main

# Create feature branch
git checkout -b feature/your-feature-name

# Make your changes
# ... work on your feature ...

# Commit and push
git add .
git commit -m "Add: description of your feature"
git push origin feature/your-feature-name
```

## 📝 Code Standards

### PHP Standards (Laravel)

Follow **PSR-12** coding standards:

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
    public function index(): View
    {
        $products = Product::with('category')
            ->orderBy('name')
            ->paginate(15);

        return view('products.index', compact('products'));
    }
}
```

### Python Standards (AI Engine)

Follow **PEP 8** guidelines:

```python
import numpy as np
import pandas as pd
from typing import Dict, List, Optional


class InventoryPredictor:
    """LSTM-based inventory prediction model."""
    
    def __init__(self, model_path: str) -> None:
        """Initialize the predictor with model path."""
        self.model_path = model_path
        self.model = None
    
    def predict(self, features: Dict[str, float]) -> Dict[str, float]:
        """Generate prediction for given features."""
        # Implementation here
        pass
```

### JavaScript/Blade Standards

```javascript
// Use ES6+ features
document.addEventListener('DOMContentLoaded', function() {
    const predictionForm = document.getElementById('predictionForm');
    
    predictionForm.addEventListener('submit', async (event) => {
        event.preventDefault();
        
        try {
            const response = await fetch('/api/predict', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            });
            
            const result = await response.json();
            // Handle response
        } catch (error) {
            console.error('Prediction failed:', error);
        }
    });
});
```

### CSS Standards

```css
/* Use consistent naming and spacing */
.dashboard-card {
    background: #ffffff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    margin-bottom: 1rem;
}

.dashboard-card__header {
    font-size: 1.1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.dashboard-card__content {
    color: #6c757d;
    font-size: 0.9rem;
}
```

## 🔄 Workflow

### Feature Development

1. **Create Issue** (if doesn't exist)
   - Describe the feature/bug clearly
   - Add appropriate labels
   - Assign to yourself

2. **Create Branch**
   ```bash
   git checkout -b feature/issue-123-new-dashboard
   ```

3. **Develop Feature**
   - Write clean, documented code
   - Follow coding standards
   - Add/update tests
   - Update documentation

4. **Test Thoroughly**
   ```bash
   # PHP tests
   php artisan test
   
   # Python tests
   python -m pytest
   
   # Manual testing
   # Test both AI API and web interface
   ```

5. **Commit Changes**
   ```bash
   git add .
   git commit -m "Add: new dashboard analytics feature
   
   - Implement real-time metrics display
   - Add Chart.js integration for visualizations
   - Update responsive design for mobile
   - Add unit tests for controller methods
   
   Closes #123"
   ```

6. **Create Pull Request**
   - Use descriptive title and description
   - Link related issues
   - Add screenshots for UI changes
   - Request review from maintainers

### Commit Message Format

Use conventional commits:

```
type(scope): description

- Detail 1
- Detail 2

Closes #issue-number
```

Types:
- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation changes
- **style**: Code style changes
- **refactor**: Code refactoring
- **test**: Adding tests
- **chore**: Maintenance tasks

## 🧪 Testing

### PHP Testing (Laravel)

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage

# Run specific test
php artisan test tests/Feature/AIPredictionTest.php
```

### Python Testing

```bash
# Run all tests
python -m pytest

# Run with coverage
python -m pytest --cov=.

# Run specific test file
python -m pytest tests/test_prediction_api.py
```

### Creating Tests

#### Laravel Feature Test Example
```php
<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AIPredictionTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_can_create_prediction()
    {
        $product = Product::factory()->create();
        
        $response = $this->post('/api/ai/predict', [
            'product_id' => $product->id,
            'current_stock' => 100,
            'expected_demand' => 25,
            'price' => 15.99
        ]);
        
        $response->assertStatus(201)
                ->assertJsonStructure([
                    'success',
                    'prediction' => [
                        'recommended_stock',
                        'reorder_point',
                        'recommendation'
                    ]
                ]);
    }
}
```

#### Python Unit Test Example
```python
import unittest
from unittest.mock import patch, MagicMock
from ai_prediction_api import predict_inventory


class TestInventoryPrediction(unittest.TestCase):
    
    def setUp(self):
        self.sample_data = {
            'current_stock': 100.0,
            'expected_demand': 25.0,
            'price': 15.99,
            'lead_time': 7
        }
    
    @patch('ai_prediction_api.model')
    def test_predict_inventory_success(self, mock_model):
        mock_model.predict.return_value = [[150.0]]
        
        result = predict_inventory(self.sample_data)
        
        self.assertIsInstance(result, dict)
        self.assertIn('recommended_stock', result)
        self.assertGreater(result['recommended_stock'], 0)


if __name__ == '__main__':
    unittest.main()
```

## 📚 Documentation

### Code Documentation

#### PHP (Laravel)
```php
/**
 * Generate AI-powered inventory prediction.
 *
 * @param \Illuminate\Http\Request $request
 * @return \Illuminate\Http\JsonResponse
 * 
 * @throws \Exception When AI API is unavailable
 */
public function predict(Request $request): JsonResponse
{
    // Implementation
}
```

#### Python
```python
def predict_inventory(features: Dict[str, float]) -> Dict[str, float]:
    """
    Generate inventory prediction using LSTM model.
    
    Args:
        features (Dict[str, float]): Input features including:
            - current_stock: Current inventory level
            - expected_demand: Forecasted demand
            - price: Product price
            - lead_time: Supplier lead time in days
    
    Returns:
        Dict[str, float]: Prediction results including:
            - recommended_stock: Optimal stock level
            - reorder_point: When to reorder
            - confidence: Prediction confidence score
    
    Raises:
        ValueError: If required features are missing
        ModelError: If prediction model fails
    """
```

### Updating Documentation

When adding features:
1. Update README.md if needed
2. Add/update API documentation
3. Update INTEGRATION_GUIDE.md for technical changes
4. Add inline code comments
5. Update user guides if UI changes

## 🐛 Issue Reporting

### Bug Reports

Use this template for bug reports:

```markdown
## Bug Description
Clear description of the bug

## Steps to Reproduce
1. Go to '...'
2. Click on '...'
3. See error

## Expected Behavior
What should happen

## Actual Behavior
What actually happens

## Environment
- OS: [e.g., Windows 10]
- PHP Version: [e.g., 8.2.1]
- Python Version: [e.g., 3.9.7]
- Browser: [e.g., Chrome 96]

## Screenshots
If applicable, add screenshots

## Additional Context
Any other context about the problem
```

### Feature Requests

```markdown
## Feature Description
Clear description of the proposed feature

## Problem Statement
What problem does this solve?

## Proposed Solution
How should this feature work?

## Alternatives Considered
Other solutions you've considered

## Additional Context
Any other context or mockups
```

## 🔍 Code Review

### Review Checklist

Before requesting review, ensure:

- [ ] Code follows project standards
- [ ] All tests pass
- [ ] Documentation is updated
- [ ] No console errors or warnings
- [ ] Performance impact considered
- [ ] Security implications reviewed
- [ ] Backwards compatibility maintained

### Review Process

1. **Self Review**: Review your own changes first
2. **Automated Checks**: Ensure CI passes
3. **Peer Review**: Request review from team members
4. **Address Feedback**: Make requested changes
5. **Final Approval**: Maintainer approval required

## 🎯 Getting Help

### Communication Channels

- **GitHub Issues**: For bugs and feature requests
- **GitHub Discussions**: For questions and general discussion
- **Pull Request Comments**: For code-specific discussions

### Resources

- **README.md**: Project overview and setup
- **INTEGRATION_GUIDE.md**: Technical implementation details
- **API Documentation**: Available endpoints and usage
- **Laravel Docs**: https://laravel.com/docs
- **TensorFlow Docs**: https://www.tensorflow.org/

---

## 📞 Questions?

If you have questions not covered in this guide:

1. Check existing GitHub issues
2. Search GitHub discussions
3. Create a new discussion
4. Contact maintainers: [@Alysoffar](https://github.com/Alysoffar)

Thank you for contributing to make this project better! 🚀
