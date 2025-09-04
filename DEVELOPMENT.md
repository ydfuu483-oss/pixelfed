# Pix Development Guide

This guide provides comprehensive instructions for setting up and developing the Pix application with the new Lees video feature.

## 🚀 Quick Start

### Prerequisites

- Docker & Docker Compose
- PHP 8.1+ (for local development)
- Node.js 18+ (for local development)
- Composer
- Git

### Using Docker (Recommended)

1. **Clone the repository:**
   ```bash
   git clone https://github.com/ydfuu483-oss/pixelfed.git
   cd pixelfed
   git checkout dev
   ```

2. **Start development environment:**
   ```bash
   make dev
   ```

3. **Access the application:**
   - Application: http://localhost:8080
   - MailHog (email testing): http://localhost:8025
   - Database: localhost:3307

### Local Development Setup

1. **Install dependencies:**
   ```bash
   make install
   ```

2. **Configure environment:**
   ```bash
   cp .env.example .env
   # Edit .env with your database and other settings
   ```

3. **Setup database:**
   ```bash
   make migrate
   ```

4. **Build assets:**
   ```bash
   make build-dev
   ```

5. **Start development server:**
   ```bash
   php artisan serve
   ```

## 🎬 Lees Video Feature

The Lees feature is a comprehensive short video sharing system integrated into Pix.

### Key Components

#### Backend Components
- **Models**: `LeesVideo`, `LeesVideoLike`, `LeesVideoComment`, `LeesVideoShare`, `LeesVideoView`
- **Controller**: `LeesController` with full CRUD operations
- **Migrations**: 5 database tables for video management
- **Configuration**: `config/lees.php` for feature settings

#### Frontend Components
- **Vue Components**: Located in `resources/assets/js/components/lees/`
  - `LeesFeed.vue` - Main video feed
  - `LeesVideoCard.vue` - Individual video display
  - `LeesUploadForm.vue` - Video upload interface

#### API Endpoints
```
GET    /api/lees/feed          - Get video feed
POST   /api/lees/upload        - Upload new video
POST   /api/lees/{id}/like     - Like/unlike video
POST   /api/lees/{id}/comment  - Add comment
POST   /api/lees/{id}/share    - Share video
POST   /api/lees/{id}/view     - Record view
GET    /api/lees/user/{id}     - Get user's videos
```

### Configuration

Add these environment variables to your `.env`:

```bash
# Lees Video Feature
LEES_ENABLED=true
LEES_MAX_VIDEO_SIZE=104857600  # 100MB
LEES_MAX_THUMBNAIL_SIZE=10485760  # 10MB
LEES_ALLOWED_VIDEO_FORMATS=mp4,mov,avi
LEES_ALLOWED_THUMBNAIL_FORMATS=jpeg,png,jpg
LEES_MAX_HASHTAGS=10
LEES_MAX_DESCRIPTION_LENGTH=500
LEES_DEFAULT_VISIBILITY=public

# AWS S3 for Lees (Optional)
LEES_AWS_ACCESS_KEY_ID=your_key
LEES_AWS_SECRET_ACCESS_KEY=your_secret
LEES_AWS_DEFAULT_REGION=us-east-1
LEES_AWS_BUCKET=your-bucket
```

## 🛠️ Development Commands

We provide a comprehensive Makefile for common development tasks:

### Installation & Setup
```bash
make install          # Install all dependencies
make install-prod     # Install production dependencies
make quick-start      # Complete setup for new developers
```

### Development Environment
```bash
make dev              # Start development environment
make dev-stop         # Stop development environment
make dev-logs         # View development logs
make dev-shell        # Access container shell
```

### Database Management
```bash
make migrate          # Run migrations
make migrate-fresh    # Fresh migration with seeding
make seed             # Seed database
make lees-setup       # Setup Lees feature specifically
```

### Building & Assets
```bash
make build            # Build production assets
make build-dev        # Build development assets
make watch            # Watch and rebuild assets
```

### Testing
```bash
make test             # Run all tests
make test-php         # Run PHP tests only
make test-js          # Run JavaScript tests only
make lees-test        # Test Lees functionality
```

### Code Quality
```bash
make lint             # Run code linting
make format           # Format code
```

### Cache Management
```bash
make cache-clear      # Clear all caches
make cache-optimize   # Optimize caches for production
```

### Deployment
```bash
make deploy-staging   # Deploy to staging
make deploy-prod      # Deploy to production
```

### Docker Operations
```bash
make docker-build     # Build Docker image
make docker-run       # Run Docker container
make docker-stop      # Stop Docker container
```

### Maintenance
```bash
make clean            # Clean temporary files
make update           # Update dependencies
make health           # Check application health
```

## 🧪 Testing

### Running Tests

```bash
# All tests
make test

# PHP tests only
make test-php

# Lees feature tests
make lees-test

# With coverage
make test-coverage
```

### Test Structure

- **Unit Tests**: `tests/Unit/`
- **Feature Tests**: `tests/Feature/`
- **Lees Tests**: `tests/Feature/LeesTest.php`

## 🔧 GitHub Actions

The project includes comprehensive CI/CD workflows:

### Workflows

1. **CI Pipeline** (`.github/workflows/ci.yml`)
   - Runs on push/PR to dev/main
   - Tests multiple PHP/Node versions
   - Runs database migrations
   - Builds assets
   - Performs security scans

2. **Deployment** (`.github/workflows/deploy.yml`)
   - Deploys to staging on main branch
   - Deploys to production on tags
   - Manual deployment trigger

3. **Code Quality** (`.github/workflows/quality.yml`)
   - Code quality checks
   - Security scanning
   - Dependency auditing
   - Lees feature verification

### Triggering Workflows

```bash
# Push to dev branch (triggers CI)
git push origin dev

# Push to main branch (triggers CI + staging deployment)
git push origin main

# Create release tag (triggers production deployment)
git tag v1.0.0
git push origin v1.0.0
```

## 🐳 Docker Development

### Development Environment

```bash
# Start all services
docker-compose -f docker-compose.dev.yml up -d

# View logs
docker-compose -f docker-compose.dev.yml logs -f

# Access application container
docker-compose -f docker-compose.dev.yml exec pix-app sh

# Stop all services
docker-compose -f docker-compose.dev.yml down
```

### Services

- **pix-app**: Main application (port 8080)
- **pix-db**: MySQL database (port 3307)
- **pix-redis**: Redis cache (port 6380)
- **pix-mailhog**: Email testing (port 8025)
- **pix-worker**: Queue worker
- **pix-scheduler**: Task scheduler

## 📁 Project Structure

```
pixelfed/
├── app/
│   ├── Http/Controllers/
│   │   └── LeesController.php
│   ├── Models/
│   │   ├── LeesVideo.php
│   │   ├── LeesVideoLike.php
│   │   ├── LeesVideoComment.php
│   │   ├── LeesVideoShare.php
│   │   └── LeesVideoView.php
│   └── Util/Site/Config.php
├── config/
│   └── lees.php
├── database/
│   └── migrations/
│       ├── *_create_lees_videos_table.php
│       ├── *_create_lees_video_likes_table.php
│       ├── *_create_lees_video_comments_table.php
│       ├── *_create_lees_video_shares_table.php
│       └── *_create_lees_video_views_table.php
├── resources/
│   ├── assets/js/components/lees/
│   │   ├── LeesFeed.vue
│   │   ├── LeesVideoCard.vue
│   │   └── LeesUploadForm.vue
│   └── views/lees/
│       └── user.blade.php
├── routes/
│   ├── api.php
│   └── web.php
├── .github/workflows/
│   ├── ci.yml
│   ├── deploy.yml
│   └── quality.yml
├── docker-compose.dev.yml
├── Makefile
└── DEVELOPMENT.md
```

## 🔍 Debugging

### Common Issues

1. **Database Connection Issues**
   ```bash
   # Check database status
   make health
   
   # Reset database
   make migrate-fresh
   ```

2. **Asset Build Issues**
   ```bash
   # Clear node modules
   rm -rf node_modules
   npm install
   
   # Rebuild assets
   make build-dev
   ```

3. **Permission Issues**
   ```bash
   # Fix storage permissions
   chmod -R 775 storage bootstrap/cache
   chown -R www-data:www-data storage bootstrap/cache
   ```

### Logs

```bash
# Application logs
tail -f storage/logs/laravel.log

# Development environment logs
make dev-logs

# Specific service logs
docker-compose -f docker-compose.dev.yml logs pix-app
```

## 🚀 Deployment

### Staging Deployment

1. Push to dev branch:
   ```bash
   git push origin dev
   ```

2. GitHub Actions will automatically:
   - Run tests
   - Build assets
   - Deploy to staging environment

### Production Deployment

1. Create a release tag:
   ```bash
   git tag v1.0.0
   git push origin v1.0.0
   ```

2. GitHub Actions will:
   - Run full test suite
   - Build production assets
   - Deploy to production environment

### Manual Deployment

Use the workflow dispatch feature in GitHub Actions to manually trigger deployments.

## 📚 Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [Vue.js Documentation](https://vuejs.org/guide/)
- [Docker Documentation](https://docs.docker.com/)
- [GitHub Actions Documentation](https://docs.github.com/en/actions)

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/amazing-feature`
3. Make your changes
4. Run tests: `make test`
5. Commit changes: `git commit -m 'Add amazing feature'`
6. Push to branch: `git push origin feature/amazing-feature`
7. Open a Pull Request

## 📄 License

This project is licensed under the AGPL License - see the LICENSE file for details.