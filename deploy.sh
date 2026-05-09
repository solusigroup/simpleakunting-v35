#!/bin/bash

# SimpleAkunting v3.5 Deployment Script
# Usage: sh deploy.sh

echo "🚀 Starting deployment..."

# 1. Pull the latest code from GitHub
echo "📥 Pulling latest changes from GitHub..."
git pull origin main

# 2. Update dependencies (optional, uncomment if needed)
# echo "📦 Updating composer dependencies..."
# composer install --no-dev --optimize-autoloader

# 3. Run database migrations (optional, uncomment if needed)
# echo "🗄️ Running migrations..."
# php migrate_v3_5.php

# 4. Set permissions (Adjust according to your server environment)
echo "🔐 Setting permissions..."
# chmod -R 755 .
# chmod -R 775 app/storage # If there's a storage folder

# 5. Clear cache (If applicable)
echo "🧹 Clearing cache..."
# php clear_cache.php

echo "✅ Deployment completed successfully!"
