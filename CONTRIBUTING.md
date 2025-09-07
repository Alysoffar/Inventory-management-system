# 🤝 Contributing to Smart Inventory Management System

Thank you for your interest in contributing to the Smart Inventory Management System! This document provides guidelines and information for contributors.

## 📋 Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [Development Setup](#development-setup)
- [Contributing Process](#contributing-process)
- [Coding Standards](#coding-standards)
- [Testing Guidelines](#testing-guidelines)
- [Pull Request Process](#pull-request-process)
- [Issue Reporting](#issue-reporting)
- [Feature Requests](#feature-requests)
- [Documentation](#documentation)

## 📜 Code of Conduct

This project adheres to a code of conduct to foster an inclusive and welcoming community:

### Our Pledge
- Be respectful and inclusive
- Welcome newcomers and help them learn
- Focus on constructive feedback
- Respect different viewpoints and experiences

### Unacceptable Behavior
- Harassment or discriminatory language
- Personal attacks or trolling
- Publishing private information
- Any conduct that could be considered inappropriate

## 🚀 Getting Started

### Prerequisites
- PHP 8.2+ with required extensions
- Composer 2.5+
- Node.js 16+ and NPM 8+
- MySQL 8.0+ or SQLite 3.8+
- Git 2.30+
- Basic knowledge of Laravel, Bootstrap, and JavaScript

### Development Environment
1. Fork the repository
2. Clone your fork: `git clone https://github.com/YOUR_USERNAME/Inventory-management-system.git`
3. Follow the [Installation Guide](README.md#installation-guide) in README.md

## 🛠️ Development Setup

### Local Development
```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/Inventory-management-system.git
cd inventory-management-system

# Install dependencies
composer install
npm install

# Setup environment
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
php artisan storage:link

# Start development servers
php artisan serve
npm run dev
```

### Database Setup
```bash
# For SQLite (development)
touch database/database.sqlite
php artisan migrate --seed

# For MySQL (production)
# Configure .env with your database credentials
php artisan migrate --seed
```

## 🔄 Contributing Process

### 1. Find or Create an Issue
- Browse [existing issues](https://github.com/Alysoffar/Inventory-management-system/issues)
- Create a new issue if your contribution addresses something new
- Comment on the issue to let others know you're working on it

### 2. Create a Feature Branch
```bash
# Update your main branch
git checkout main
git pull upstream main

# Create a new branch
git checkout -b feature/your-feature-name
# or
git checkout -b fix/issue-number-description
```

### 3. Make Your Changes
- Write clear, readable code
- Follow our coding standards
- Add tests for new functionality
- Update documentation as needed

### 4. Test Your Changes
```bash
# Run PHP tests
php artisan test

# Run JavaScript tests (if applicable)
npm test

# Check code style
./vendor/bin/pint --test
npm run lint
```

### 5. Commit Your Changes
```bash
git add .
git commit -m "feat: add inventory prediction algorithm

- Implement LSTM-based demand forecasting
- Add prediction accuracy metrics
- Update AI controller with new endpoints
- Add tests for prediction functionality

Fixes #123"
```

### 6. Push and Create Pull Request
```bash
git push origin feature/your-feature-name
```

Then create a pull request through GitHub's interface.

## 📝 Coding Standards

### PHP Standards
- Follow PSR-12 coding standard
- Use Laravel best practices
- Write descriptive variable and method names
- Add PHPDoc comments for classes and methods

```php
<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

/**
 * Inventory prediction service using AI algorithms
 */
class InventoryPredictionService
{
    /**
     * Predict demand for a specific product
     *
     * @param Product $product The product to predict demand for
     * @param int $days Number of days to predict
     * @return array Prediction results with confidence scores
     */
    public function predictDemand(Product $product, int $days = 30): array
    {
        // Implementation here
        return [
            'predicted_demand' => 150,
            'confidence' => 0.87,
            'trend' => 'increasing'
        ];
    }
}
```

### JavaScript Standards
- Use ES6+ features
- Follow consistent indentation (4 spaces)
- Use meaningful variable names
- Add JSDoc comments for functions

```javascript
/**
 * Initialize the inventory map with product locations
 * @param {Array} locations - Array of location objects
 * @param {string} containerId - ID of the map container
 */
function initializeInventoryMap(locations, containerId) {
    const map = L.map(containerId).setView([40.7128, -74.0060], 13);
    
    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
    // Add markers for each location
    locations.forEach(location => {
        L.marker([location.lat, location.lng])
            .bindPopup(location.name)
            .addTo(map);
    });
}
```

### CSS/SCSS Standards
- Use BEM methodology for class names
- Follow consistent indentation
- Group related properties together

```scss
// Good
.inventory-dashboard {
    &__header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
    }
    
    &__stats-card {
        background: $card-bg;
        border-radius: $border-radius;
        padding: 1.5rem;
        box-shadow: $card-shadow;
    }
}
```

## 🧪 Testing Guidelines

### PHP Tests
- Write feature tests for new endpoints
- Write unit tests for services and models
- Maintain test coverage above 80%

```php
<?php

namespace Tests\Feature;

use App\Models\Product;
use Tests\TestCase;

class InventoryTest extends TestCase
{
    /** @test */
    public function it_can_display_inventory_dashboard()
    {
        Product::factory()->count(5)->create();
        
        $response = $this->get('/inventory/dashboard');
        
        $response->assertStatus(200)
                ->assertViewIs('inventory.dashboard')
                ->assertViewHas('stats');
    }
    
    /** @test */
    public function it_can_predict_inventory_demand()
    {
        $product = Product::factory()->create();
        
        $response = $this->post('/api/ai/predict', [
            'product_id' => $product->id,
            'days' => 30
        ]);
        
        $response->assertStatus(200)
                ->assertJsonStructure([
                    'predicted_demand',
                    'confidence',
                    'trend'
                ]);
    }
}
```

### JavaScript Tests (if applicable)
```javascript
describe('Inventory Map', () => {
    test('should initialize map with correct center', () => {
        const map = initializeInventoryMap([], 'test-map');
        expect(map.getCenter()).toEqual({ lat: 40.7128, lng: -74.0060 });
    });
    
    test('should add markers for each location', () => {
        const locations = [
            { lat: 40.7128, lng: -74.0060, name: 'Warehouse 1' },
            { lat: 40.7580, lng: -73.9855, name: 'Warehouse 2' }
        ];
        
        const map = initializeInventoryMap(locations, 'test-map');
        expect(map._layers).toHaveLength(3); // 1 tile layer + 2 markers
    });
});
```

## 📤 Pull Request Process

### Before Submitting
- [ ] Code follows project standards
- [ ] Tests pass locally
- [ ] Documentation is updated
- [ ] Commit messages are clear and descriptive
- [ ] Branch is up to date with main

### Pull Request Template
```markdown
## Description
Brief description of the changes made.

## Type of Change
- [ ] Bug fix (non-breaking change which fixes an issue)
- [ ] New feature (non-breaking change which adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to not work as expected)
- [ ] Documentation update

## How Has This Been Tested?
- [ ] Unit tests
- [ ] Integration tests
- [ ] Manual testing

## Screenshots (if applicable)

## Checklist
- [ ] My code follows the style guidelines of this project
- [ ] I have performed a self-review of my code
- [ ] I have commented my code, particularly in hard-to-understand areas
- [ ] I have made corresponding changes to the documentation
- [ ] My changes generate no new warnings
- [ ] I have added tests that prove my fix is effective or that my feature works
- [ ] New and existing unit tests pass locally with my changes
```

## 🐛 Issue Reporting

### Bug Reports
When reporting bugs, please include:

```markdown
## Bug Description
A clear and concise description of what the bug is.

## To Reproduce
Steps to reproduce the behavior:
1. Go to '...'
2. Click on '....'
3. Scroll down to '....'
4. See error

## Expected Behavior
What you expected to happen.

## Screenshots
If applicable, add screenshots.

## Environment
- OS: [e.g. Windows 10, macOS 12.0, Ubuntu 20.04]
- Browser: [e.g. Chrome 96, Firefox 94]
- PHP Version: [e.g. 8.2.1]
- Laravel Version: [e.g. 12.28.0]

## Additional Context
Any other context about the problem.
```

### Security Issues
For security vulnerabilities:
- **DO NOT** create a public issue
- Email: alysoffar06@gmail.com
- Include detailed reproduction steps
- Allow time for assessment and fix before disclosure

## 💡 Feature Requests

### Feature Request Template
```markdown
## Feature Description
A clear and concise description of what you want to happen.

## Problem Statement
What problem does this feature solve?

## Proposed Solution
Detailed description of how you envision this feature working.

## Alternatives Considered
Any alternative solutions or features you've considered.

## Additional Context
Any other context, mockups, or screenshots about the feature request.

## Implementation Ideas
If you have ideas about how this could be implemented.
```

## 📚 Documentation

### Documentation Standards
- Use clear, concise language
- Include code examples where applicable
- Keep documentation up to date with code changes
- Use proper Markdown formatting

### Areas Needing Documentation
- New features and APIs
- Configuration options
- Deployment procedures
- Troubleshooting guides

## 🏷️ Commit Message Guidelines

### Format
```
type(scope): subject

body

footer
```

### Types
- **feat**: New feature
- **fix**: Bug fix
- **docs**: Documentation only changes
- **style**: Changes that do not affect the meaning of the code
- **refactor**: Code change that neither fixes a bug nor adds a feature
- **test**: Adding missing tests
- **chore**: Changes to the build process or auxiliary tools

### Examples
```bash
feat(inventory): add AI-powered demand prediction

Implement LSTM neural network for predicting inventory demand
- Add prediction service with TensorFlow integration
- Create API endpoints for generating predictions
- Add database tables for storing prediction results
- Include confidence scores and trend analysis

Closes #123

fix(dashboard): resolve memory leak in real-time updates

The dashboard was not properly cleaning up WebSocket connections
when navigating away from the page, causing memory leaks.

- Add proper cleanup in beforeUnmount lifecycle hook
- Implement connection pooling for WebSocket connections
- Add error handling for failed connections

Fixes #456
```

## 🎯 Development Workflow

### Branch Naming Convention
- `feature/description` - For new features
- `fix/issue-number-description` - For bug fixes
- `hotfix/description` - For urgent fixes
- `docs/description` - For documentation updates
- `refactor/description` - For code refactoring

### Release Process
1. Features are merged into `develop` branch
2. Release candidates are created from `develop`
3. After testing, releases are merged into `main`
4. Tags are created for each release

## 🆘 Getting Help

### Communication Channels
- **Issues**: For bug reports and feature requests
- **Discussions**: For questions and general discussion
- **Email**: alysoffar06@gmail.com for direct contact

### Before Asking for Help
- Check existing issues and documentation
- Search previous discussions
- Try to isolate the problem
- Provide minimal reproduction case

## 🏆 Recognition

Contributors will be recognized in:
- CONTRIBUTORS.md file
- Release notes for significant contributions
- GitHub repository insights

### Types of Contributions
- Code contributions
- Documentation improvements
- Bug reports and testing
- Feature suggestions
- Community support

## 📄 License

By contributing to this project, you agree that your contributions will be licensed under the same MIT License that covers the project.

---

**Thank you for contributing to the Smart Inventory Management System! 🚀**

*Last Updated: September 8, 2025*
