#!/bin/bash

echo "🚀 Continuando configuração com chaves de desenvolvimento..."

# 1. Copiar chaves para .env principal
echo "📋 Adicionando chaves ao .env..."
echo "" >> .env
echo "# VAPID Keys - Development" >> .env
echo "VAPID_SUBJECT=mailto:admin@farmacia.com" >> .env
echo "VAPID_PUBLIC_KEY=BEl62iUYgUivxIkv69yViEuiBIa40HI0DzCp4CMcpW3gBC4HfcKNdXAwGZsVOMLwk77XVLJmNhvOuHd4xzipxm8" >> .env
echo "VAPID_PRIVATE_KEY=nNiRpKAQHn-5_1m7uYPTgnO-GOEXn2dOSUHuP9JgKjI" >> .env

# 2. Publicar configurações
echo "📋 Publicando configurações..."
php artisan vendor:publish --provider="NotificationChannels\WebPush\WebPushServiceProvider" --tag="config" --force

# 3. Publicar migrations
echo "📋 Publicando migrations..."
php artisan vendor:publish --provider="NotificationChannels\WebPush\WebPushServiceProvider" --tag="migrations" --force

# 4. Executar migrations
echo "🗄️ Executando migrations..."
php artisan migrate

echo "✅ Configuração básica completa!"
