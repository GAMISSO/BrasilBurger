#!/bin/bash
set -e

echo "🚀 Starting BrasilBurger Symfony Application..."

# Attendre que la base de données soit prête
echo "⏳ Waiting for database..."
sleep 5

# Vérifier les variables d'environnement
if [ -z "$DATABASE_URL" ]; then
    echo "❌ ERROR: DATABASE_URL is not set!"
    exit 1
fi

# Vérifier que DATABASE_URL ne pointe pas vers localhost
if [[ "$DATABASE_URL" == *"localhost"* ]] || [[ "$DATABASE_URL" == *"127.0.0.1"* ]]; then
    echo "❌ ERROR: DATABASE_URL points to localhost!"
    echo "   You need to set the DATABASE_URL to your Render PostgreSQL Internal URL"
    echo "   Current value: $DATABASE_URL"
    exit 1
fi

if [ -z "$APP_SECRET" ]; then
    echo "❌ ERROR: APP_SECRET is not set!"
    exit 1
fi

echo "✅ Environment variables validated"
echo "📦 Database URL: ${DATABASE_URL%%@*}@..." # Show partial URL for security

# Clear cache
echo "🧹 Clearing cache..."
php bin/console cache:clear --no-warmup --env=prod

# Warm up cache
echo "🔥 Warming up cache..."
php bin/console cache:warmup --env=prod

# Run database migrations
echo "📦 Running database migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --allow-no-migration --env=prod || echo "⚠️  No migrations to run or migration failed"

echo "✅ Application ready!"

# Démarrer le serveur PHP
echo "🌐 Starting web server on port 8080..."
exec php -S 0.0.0.0:8080 -t public
