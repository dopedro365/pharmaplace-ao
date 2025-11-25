#!/bin/bash

echo "🔧 Fixing Laravel system issues..."

# 1. Limpar cache
echo "1. Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 2. Recriar autoload
echo "2. Regenerating autoload..."
composer dump-autoload

# 3. Corrigir permissões de arquivos
echo "3. Fixing file permissions..."
php artisan fix:file-permissions

# 4. Criar link simbólico do storage
echo "4. Creating storage link..."
php artisan storage:link

# 5. Executar migrações
echo "5. Running migrations..."
php artisan migrate

# 6. Verificar configurações
echo "6. Checking configurations..."
php artisan config:cache
php artisan route:cache

echo "✅ System fixed successfully!"
echo ""
echo "🔍 Next steps:"
echo "1. Check if middleware is working: php artisan route:list"
echo "2. Test file uploads in a controlled environment"
echo "3. Monitor logs: tail -f storage/logs/laravel.log"
