# Local Setup Guide

This guide runs the app locally using Docker and Docker Compose from the app/ folder.

## Prerequisites
- Docker Desktop (includes Docker Compose v2)

## First-time setup

1) Copy the env file

```
cp app/.env.example app/.env
```

Optional: edit app/.env if you want to change APP_NAME or APP_URL.

2) Build and start containers (includes Vite dev server)

```
cd app
docker compose --profile dev up -d --build
```

This starts:
- app (PHP-FPM)
- web (Nginx on port 8080)
- db (MySQL 8 on port 3307)
- node (Vite dev server on port 5173)

3) Run migrations and seed data

```
docker compose exec app php artisan migrate --seed
```

4) Open the app

- http://localhost:8080
- Vite dev server: http://localhost:5173

## Common tasks

- View logs:

```
docker compose logs -f app
```

- Stop containers:

```
docker compose down
```

- Reset database (destructive):

```
docker compose down -v
docker compose --profile dev up -d --build
docker compose exec app php artisan migrate --seed
```

## Notes
- The container entrypoint installs Composer dependencies and creates .env if missing.
- The default database credentials are:
  - Host (inside containers): db
  - Host (from your laptop): 127.0.0.1:3307
  - Database: ojt_application
  - User: root
  - Password: secret
- A seeded test user is created:
  - Email: test@example.com
  - Password: password

## Troubleshooting

### Ports already in use
If you see errors about ports 8080, 3307, or 5173 already in use, stop other services using those ports or edit the port mappings in app/docker-compose.yml, then restart:

```
cd app
docker compose down
docker compose --profile dev up -d --build
```

### Database connection errors
If migrations fail with a database connection error, wait a few seconds for MySQL to start, then retry:

```
cd app
docker compose exec app php artisan migrate --seed
```

Check DB logs if it persists:

```
cd app
docker compose logs -f db
```

### APP_KEY missing or invalid
If you see an APP_KEY error, generate it inside the container:

```
cd app
docker compose exec app php artisan key:generate
```

### 502 Bad Gateway from Nginx
This usually means PHP-FPM is not running yet or failed. Restart the app service:

```
cd app
docker compose restart app
```

### Vite assets not loading
If the UI has missing styles or scripts, make sure the node service is running with the dev profile:

```
cd app
docker compose --profile dev up -d
```

Then reload the page.

### Storage or cache permission errors
If you see write permission errors for storage or bootstrap/cache:

```
cd app
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```
