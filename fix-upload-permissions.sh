#!/bin/bash

echo "🔧 Fixing upload permissions and directories..."

# Criar diretórios necessários
mkdir -p storage/app/public/payment_proofs
mkdir -p storage/app/livewire-tmp
mkdir -p storage/logs

# Corrigir permissões
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/

# Criar link simbólico se não existir
if [ ! -L public/storage ]; then
    php artisan storage:link
    echo "✓ Storage link created"
fi

# Limpar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear

echo "✅ Upload system fixed!"
