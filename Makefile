# Pix Development Makefile

.PHONY: help install dev build test clean deploy

# Default target
help: ## Show this help message
	@echo "Pix Development Commands:"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-20s\033[0m %s\n", $$1, $$2}'

# Installation
install: ## Install all dependencies
	@echo "🔧 Installing PHP dependencies..."
	composer install
	@echo "🔧 Installing Node.js dependencies..."
	npm install
	@echo "🔧 Generating application key..."
	php artisan key:generate
	@echo "🔧 Creating storage link..."
	php artisan storage:link
	@echo "✅ Installation completed!"

install-prod: ## Install production dependencies
	@echo "🔧 Installing PHP dependencies (production)..."
	composer install --no-dev --optimize-autoloader
	@echo "🔧 Installing Node.js dependencies..."
	npm ci --only=production
	@echo "✅ Production installation completed!"

# Development
dev: ## Start development environment
	@echo "🚀 Starting development environment..."
	docker-compose -f docker-compose.dev.yml up -d
	@echo "✅ Development environment started!"
	@echo "🌐 Application: http://localhost:8080"
	@echo "📧 MailHog: http://localhost:8025"
	@echo "🗄️ Database: localhost:3307"

dev-stop: ## Stop development environment
	@echo "🛑 Stopping development environment..."
	docker-compose -f docker-compose.dev.yml down
	@echo "✅ Development environment stopped!"

dev-logs: ## Show development logs
	docker-compose -f docker-compose.dev.yml logs -f

dev-shell: ## Access development container shell
	docker-compose -f docker-compose.dev.yml exec pix-app sh

# Database
migrate: ## Run database migrations
	@echo "🗄️ Running database migrations..."
	php artisan migrate
	@echo "✅ Migrations completed!"

migrate-fresh: ## Fresh migration with seeding
	@echo "🗄️ Running fresh migrations..."
	php artisan migrate:fresh --seed
	@echo "✅ Fresh migrations completed!"

seed: ## Seed the database
	@echo "🌱 Seeding database..."
	php artisan db:seed
	@echo "✅ Database seeded!"

# Building
build: ## Build frontend assets
	@echo "🏗️ Building frontend assets..."
	npm run production
	@echo "✅ Assets built!"

build-dev: ## Build development assets
	@echo "🏗️ Building development assets..."
	npm run development
	@echo "✅ Development assets built!"

watch: ## Watch and rebuild assets
	@echo "👀 Watching assets for changes..."
	npm run watch

# Testing
test: ## Run all tests
	@echo "🧪 Running PHP tests..."
	php artisan test
	@echo "🧪 Running Node.js tests..."
	npm test
	@echo "✅ All tests completed!"

test-php: ## Run PHP tests only
	@echo "🧪 Running PHP tests..."
	php artisan test

test-js: ## Run JavaScript tests only
	@echo "🧪 Running JavaScript tests..."
	npm test

test-coverage: ## Run tests with coverage
	@echo "🧪 Running tests with coverage..."
	php artisan test --coverage

# Code Quality
lint: ## Run code linting
	@echo "🔍 Running PHP linting..."
	find app -name "*.php" | xargs -I {} php -l {}
	@echo "🔍 Running JavaScript linting..."
	npm run lint
	@echo "✅ Linting completed!"

format: ## Format code
	@echo "🎨 Formatting PHP code..."
	./vendor/bin/php-cs-fixer fix || echo "PHP CS Fixer not available"
	@echo "🎨 Formatting JavaScript code..."
	npm run format || echo "JavaScript formatter not available"
	@echo "✅ Code formatting completed!"

# Cache Management
cache-clear: ## Clear all caches
	@echo "🧹 Clearing caches..."
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
	@echo "✅ Caches cleared!"

cache-optimize: ## Optimize caches for production
	@echo "⚡ Optimizing caches..."
	php artisan config:cache
	php artisan route:cache
	php artisan view:cache
	composer dump-autoload --optimize
	@echo "✅ Caches optimized!"

# Lees Feature
lees-setup: ## Setup Lees video feature
	@echo "🎬 Setting up Lees video feature..."
	php artisan migrate --path=database/migrations/lees
	@echo "✅ Lees feature setup completed!"

lees-test: ## Test Lees functionality
	@echo "🧪 Testing Lees functionality..."
	php artisan test --filter=Lees
	@echo "✅ Lees tests completed!"

# Deployment
deploy-staging: ## Deploy to staging
	@echo "🚀 Deploying to staging..."
	git push origin dev
	@echo "✅ Staging deployment initiated!"

deploy-prod: ## Deploy to production
	@echo "🚀 Deploying to production..."
	git push origin main
	@echo "✅ Production deployment initiated!"

# Docker
docker-build: ## Build Docker image
	@echo "🐳 Building Docker image..."
	docker build -t pix:latest .
	@echo "✅ Docker image built!"

docker-run: ## Run Docker container
	@echo "🐳 Running Docker container..."
	docker run -d -p 8080:80 --name pix-container pix:latest
	@echo "✅ Docker container started!"
	@echo "🌐 Application: http://localhost:8080"

docker-stop: ## Stop Docker container
	@echo "🐳 Stopping Docker container..."
	docker stop pix-container || true
	docker rm pix-container || true
	@echo "✅ Docker container stopped!"

# Maintenance
clean: ## Clean temporary files and caches
	@echo "🧹 Cleaning temporary files..."
	rm -rf node_modules/.cache
	rm -rf storage/framework/cache/data/*
	rm -rf storage/framework/sessions/*
	rm -rf storage/framework/views/*
	rm -rf storage/logs/*.log
	@echo "✅ Cleanup completed!"

update: ## Update dependencies
	@echo "📦 Updating PHP dependencies..."
	composer update
	@echo "📦 Updating Node.js dependencies..."
	npm update
	@echo "✅ Dependencies updated!"

# Health Check
health: ## Check application health
	@echo "🏥 Checking application health..."
	@echo "✅ PHP version: $(shell php --version | head -n 1)"
	@echo "✅ Node.js version: $(shell node --version)"
	@echo "✅ Composer version: $(shell composer --version)"
	@echo "✅ NPM version: $(shell npm --version)"
	@echo "🎬 Lees feature: $(shell [ -f config/lees.php ] && echo 'Enabled' || echo 'Disabled')"
	@echo "🗄️ Database connection: $(shell php artisan tinker --execute='DB::connection()->getPdo(); echo \"Connected\";' 2>/dev/null || echo 'Failed')"

# Quick Start
quick-start: install migrate build ## Quick start for new developers
	@echo "🚀 Quick start completed!"
	@echo "🌐 Run 'make dev' to start development environment"
	@echo "📖 Run 'make help' to see all available commands"