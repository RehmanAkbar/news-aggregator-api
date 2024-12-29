# News Aggregator API

A RESTful API built with Laravel for aggregating and managing news articles from multiple sources. The API supports user preferences, article categorization, and personalized news feeds.

## Table of Contents
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Docker Setup](#docker-setup)
- [API Documentation](#api-documentation)
- [Development Guidelines](#development-guidelines)
- [Testing](#testing)
- [Maintenance](#maintenance)

## Features

- User authentication and authorization
- Article management with filtering and search
- User preferences for personalized news feeds
- Multiple news source integration (NewsAPI, Guardian, NYTimes)
- Article categorization
- Background job processing for article syncing
- Docker containerization
- API response formatting and error handling

## Requirements

- Docker and Docker Compose
- PHP 8.2+
- Composer
- MySQL 8.0
- Git

## Installation

1. Clone the repository:
```bash
git clone https://your-repository-url.git
cd news-api
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Configure your .env file with the following required variables:
```env
# Database
DB_CONNECTION=mysql
DB_HOST=db
DB_PORT=3306
DB_DATABASE=news_db
DB_USERNAME=news_user
DB_PASSWORD=secret

# News API Keys
NEWSAPI_KEY=your_newsapi_key
GUARDIAN_API_KEY=your_guardian_api_key
NYTIMES_API_KEY=your_nytimes_api_key
```

## Docker Setup

1. Build and start the containers:
```bash
docker-compose up -d --build
```

2. Install dependencies:
```bash
docker-compose exec app composer install
```

3. Generate application key:
```bash
docker-compose exec app php artisan key:generate
```

4. Run migrations and seeders:
```bash
docker-compose exec app php artisan migrate --seed
```

The application will be available at: http://localhost:8000

PHPMyAdmin will be available at: http://localhost:8080
- Username: news_user
- Password: secret

### Container Structure
- **app**: PHP-FPM container with the Laravel application
- **webserver**: Nginx web server
- **db**: MySQL database
- **phpmyadmin**: Database management tool

### Useful Docker Commands
```bash
# View container logs
docker-compose logs -f

# Stop containers
docker-compose down

# Rebuild containers
docker-compose up -d --build

# Access container shell
docker-compose exec app sh
```

## API Documentation

### Authentication Endpoints

#### Register User
```http
POST /api/register
Content-Type: application/json

{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password",
    "password_confirmation": "password"
}
```

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "john@example.com",
    "password": "password"
}
```

### Article Endpoints

#### List Articles
```http
GET /api/articles
Authorization: Bearer {token}
```

Query Parameters:
- `keyword`: Search in title and content
- `date_from`: Start date (YYYY-MM-DD)
- `date_to`: End date (YYYY-MM-DD)
- `category_id`: Filter by category
- `source_id`: Filter by source
- `author`: Filter by author
- `per_page`: Results per page (default: 15)

#### Get Single Article
```http
GET /api/articles/{id}
Authorization: Bearer {token}
```

### User Preferences Endpoints

#### Get Preferences
```http
GET /api/preferences
Authorization: Bearer {token}
```

#### Update Preferences
```http
PUT /api/preferences
Authorization: Bearer {token}
Content-Type: application/json

{
    "preferred_categories": [1, 2, 3],
    "preferred_sources": [1, 2],
    "preferred_authors": ["John Doe", "Jane Smith"]
}
```

#### Get Personalized Feed
```http
GET /api/feed
Authorization: Bearer {token}
```

## Development Guidelines

### Code Structure
- Controllers: `app/Http/Controllers`
- Services: `app/Services`
- Models: `app/Models`
- Resources: `app/Http/Resources`
- Requests: `app/Http/Requests`
- Jobs: `app/Jobs`

### Adding New Features
1. Create necessary database migrations
2. Create or update models
3. Create service classes for business logic
4. Create controller methods
5. Create request validation classes
6. Create API resources for response formatting
7. Add routes in `routes/api.php`
8. Add tests

### Coding Standards
- Follow PSR-12 coding standards
- Use type hints and return types
- Document classes and methods with PHPDoc
- Use dependency injection
- Keep controllers thin, put business logic in services
- Use Laravel's built-in features and best practices

## Testing

Run tests using PHPUnit:
```bash
docker-compose exec app php artisan test
```

## Maintenance

### Queue Worker
The application uses Laravel's queue system for background jobs. The worker is managed by Supervisor in the Docker container.

### Article Syncing
Articles are synced periodically using the following command:
```bash
docker-compose exec app php artisan articles:fetch
```

To force sync regardless of the last sync time:
```bash
docker-compose exec app php artisan articles:fetch --force
```

### Logs
Application logs are available in:
- Laravel Log: `storage/logs/laravel.log`
- Supervisor Logs: `storage/logs/supervisor/`

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.
