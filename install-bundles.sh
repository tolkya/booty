#!/bin/bash
# Script d'installation des bundles Symfony pour Booty QR
# Phase 1: Infrastructure

echo "🚀 Installation des bundles Symfony pour Booty QR"
echo "=================================================="

# Bundles de développement essentiels
echo ""
echo "📦 Installation des bundles de développement..."
docker compose exec php composer require --dev symfony/maker-bundle
docker compose exec php composer require --dev symfony/debug-bundle
docker compose exec php composer require --dev symfony/web-profiler-bundle
docker compose exec php composer require --dev doctrine/doctrine-fixtures-bundle
docker compose exec php composer require --dev zenstruck/foundry

# Sécurité
echo ""
echo "🔐 Installation de la sécurité..."
docker compose exec php composer require symfony/security-bundle
docker compose exec php composer require lexik/jwt-authentication-bundle

# API REST
echo ""
echo "🌐 Installation de l'API REST..."
docker compose exec php composer require api
docker compose exec php composer require nelmio/cors-bundle

# Validation & Serialization
echo ""
echo "✅ Installation validation & serialization..."
docker compose exec php composer require symfony/validator
docker compose exec php composer require symfony/serializer

# Interface Web
echo ""
echo "🎨 Installation interface web..."
docker compose exec php composer require symfony/webpack-encore-bundle
docker compose exec php composer require symfony/twig-bundle
docker compose exec php composer require symfony/form
docker compose exec php composer require symfony/asset

# Upload & QR Code
echo ""
echo "📸 Installation upload & QR code..."
docker compose exec php composer require vich/uploader-bundle
docker compose exec php composer require endroid/qr-code-bundle

# Tests
echo ""
echo "🧪 Installation tests..."
docker compose exec php composer require --dev symfony/test-pack

echo ""
echo "✅ Installation terminée!"
echo ""
echo "Prochaines étapes:"
echo "1. Configurer JWT: docker compose exec php bin/console lexik:jwt:generate-keypair"
echo "2. Créer les entités avec: docker compose exec php bin/console make:entity"
echo "3. Voir le roadmap complet dans PROJECT_ROADMAP.md"
