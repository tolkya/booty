# 🔍 Analyse de Configuration - Booty QR

## ✅ Configuration Actuelle (Bien configurée)

### Docker & Infrastructure
- ✅ **FrankenPHP** avec PHP 8.4.16 (dernière version)
- ✅ **PostgreSQL** 16-alpine comme base de données
- ✅ **Adminer** sur port 8080 pour gérer la DB
- ✅ **Mercure** intégré (pour notifications temps réel futures)
- ✅ **Node.js LTS** installé dans le container
- ✅ Alias `sf` configuré pour `php bin/console`

### Symfony
- ✅ **Symfony 8.0** (dernière version stable)
- ✅ **Doctrine ORM** + Migrations configurés
- ✅ Structure de base en place (Controller, Entity, Repository)

### Ports
- Port 80 (HTTP)
- Port 443 (HTTPS)
- Port 8080 (Adminer)

---

## 📋 Étapes à Suivre Maintenant

### 1️⃣ Rendre le script exécutable et installer les bundles
```bash
chmod +x install-bundles.sh
./install-bundles.sh
```

**Durée estimée**: 5-10 minutes

### 2️⃣ Configurer JWT pour l'API mobile
```bash
docker compose exec php bin/console lexik:jwt:generate-keypair
```

### 3️⃣ Créer les entités de base
Commencer par les entités principales dans cet ordre :

#### a) User (Organizer)
```bash
docker compose exec php bin/console make:entity User
```
Champs à ajouter :
- email (string, 180)
- firstName (string, 100)
- lastName (string, 100)
- roles (json)

#### b) Hunt (Chasse)
```bash
docker compose exec php bin/console make:entity Hunt
```
Champs à ajouter :
- organizer (relation ManyToOne vers User)
- title (string, 255)
- description (text)
- maxDuration (integer, nullable)
- status (string, 50)
- startedAt (datetime_immutable, nullable)
- endedAt (datetime_immutable, nullable)

#### c) QrCode
```bash
docker compose exec php bin/console make:entity QrCode
```
Champs à ajouter :
- hunt (relation ManyToOne vers Hunt)
- code (string, 255, unique)
- orderPosition (integer)
- latitude (float, nullable)
- longitude (float, nullable)
- isPlaced (boolean)
- placedAt (datetime_immutable, nullable)

#### d) Question
```bash
docker compose exec php bin/console make:entity Question
```
Champs à ajouter :
- hunt (relation ManyToOne vers Hunt)
- type (string, 50)
- questionText (text)
- points (integer)
- timeLimit (integer, nullable)
- image (string, 255, nullable)

#### e) QuestionChoice
```bash
docker compose exec php bin/console make:entity QuestionChoice
```
Champs à ajouter :
- question (relation ManyToOne vers Question)
- choiceText (string, 500)
- isCorrect (boolean)
- orderPosition (integer)

#### f) Participant
```bash
docker compose exec php bin/console make:entity Participant
```
Champs à ajouter :
- email (string, 180, nullable)
- firstName (string, 100)
- lastName (string, 100)
- phoneNumber (string, 20, nullable)

#### g) Session
```bash
docker compose exec php bin/console make:entity Session
```
Champs à ajouter :
- hunt (relation ManyToOne vers Hunt)
- participant (relation ManyToOne vers Participant)
- status (string, 50)
- startedAt (datetime_immutable)
- completedAt (datetime_immutable, nullable)
- totalDuration (integer)
- totalScore (integer)
- distanceTraveled (float)
- averageSpeed (float)

#### h) SessionAnswer
```bash
docker compose exec php bin/console make:entity SessionAnswer
```
Champs à ajouer :
- session (relation ManyToOne vers Session)
- qrCode (relation ManyToOne vers QrCode)
- question (relation ManyToOne vers Question)
- answerText (text, nullable)
- selectedChoices (json, nullable)
- isCorrect (boolean, nullable)
- pointsAwarded (integer)
- timeSpent (integer)
- answeredAt (datetime_immutable)
- scannedAt (datetime_immutable)

#### i) SessionLocation
```bash
docker compose exec php bin/console make:entity SessionLocation
```
Champs à ajouter :
- session (relation ManyToOne vers Session)
- latitude (float)
- longitude (float)
- accuracy (float)
- recordedAt (datetime_immutable)

### 4️⃣ Générer la migration
```bash
docker compose exec php bin/console make:migration
docker compose exec php bin/console doctrine:migrations:migrate
```

### 5️⃣ Créer des fixtures de test
```bash
docker compose exec php bin/console make:fixtures
```

### 6️⃣ Configurer la sécurité
```bash
docker compose exec php bin/console make:user
```

---

## 🎯 Optimisations Recommandées

### 1. Variables d'environnement à ajouter dans .env
```env
# JWT
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=your_passphrase_here

# CORS (pour Flutter)
CORS_ALLOW_ORIGIN=*

# Upload
MAX_UPLOAD_SIZE=10M

# QR Code
QR_CODE_SIZE=300
QR_CODE_MARGIN=10
```

### 2. Ajouter extension PostgreSQL pour géolocalisation (optionnel)
Si vous voulez des requêtes géospatiales avancées :
```sql
CREATE EXTENSION IF NOT EXISTS postgis;
```

### 3. Configuration Webpack Encore (après installation)
```bash
docker compose exec php npm install
docker compose exec php npm run dev
```

---

## 📊 Architecture Technique

### Stack Complète
```
┌─────────────────────────────────────┐
│     Flutter Mobile (iOS/Android)    │
│  - Organisateur (placement QR)      │
│  - Participant (scan + GPS)         │
└─────────────┬───────────────────────┘
              │ REST API (JWT)
              │
┌─────────────▼───────────────────────┐
│      Symfony 8 + API Platform       │
│  - Interface Web Organisateur       │
│  - API REST pour mobile             │
│  - Logique métier                   │
└─────────────┬───────────────────────┘
              │
┌─────────────▼───────────────────────┐
│         PostgreSQL 16               │
│  - Données chasses                  │
│  - Sessions participants            │
│  - Tracking GPS                     │
└─────────────────────────────────────┘
```

### Flux de Données Principal

1. **Création Chasse** (Web)
   - Organisateur crée Hunt
   - Ajoute N QrCode
   - Crée Questions avec réponses

2. **Placement QR Codes** (Mobile Organisateur)
   - Liste QR codes de la chasse
   - Scan position GPS
   - Marque QR code comme "placé"

3. **Session Participant** (Mobile Participant)
   - Démarrer session → POST /api/sessions
   - Scanner QR code → POST /api/sessions/{id}/scan
   - Recevoir question aléatoire → GET /api/sessions/{id}/question
   - Répondre → POST /api/sessions/{id}/answer
   - Envoyer GPS périodiquement → POST /api/sessions/{id}/locations
   - Terminer → POST /api/sessions/{id}/complete

4. **Statistiques** (Web)
   - Voir toutes les sessions d'une chasse
   - Classement participants
   - Carte avec parcours GPS
   - Noter réponses libres

---

## ⚡ Commandes Utiles

### Développement
```bash
# Logs en temps réel
docker compose logs -f php

# Accéder au container
docker compose exec php bash

# Console Symfony
docker compose exec php bin/console

# Clear cache
docker compose exec php bin/console cache:clear

# Liste des routes
docker compose exec php bin/console debug:router

# Créer un contrôleur
docker compose exec php bin/console make:controller
```

### Base de données
```bash
# Créer migration
docker compose exec php bin/console make:migration

# Exécuter migrations
docker compose exec php bin/console doctrine:migrations:migrate

# Charger fixtures
docker compose exec php bin/console doctrine:fixtures:load

# Accéder à la DB via Adminer
# Ouvrir http://localhost:8080
# Système: PostgreSQL
# Serveur: database
# Utilisateur: app
# Mot de passe: !ChangeMe!
# Base: booty
```

### Tests
```bash
# Lancer les tests
docker compose exec php bin/phpunit

# Tests avec couverture
docker compose exec php bin/phpunit --coverage-html var/coverage
```

---

## 🚦 Statut Actuel

**Phase 1 - Infrastructure**: 40% ✅
- [x] Docker configuré
- [x] Symfony 8 installé
- [x] PostgreSQL configuré
- [x] Adminer disponible
- [x] Roadmap créé
- [ ] Bundles installés
- [ ] Entités créées
- [ ] Migrations générées
- [ ] Fixtures créées
- [ ] Sécurité configurée

**Prochaine action**: Lancer `./install-bundles.sh`

---

## 📞 Support

Référez-vous au [PROJECT_ROADMAP.md](PROJECT_ROADMAP.md) pour le plan complet.

Pour chaque phase, nous procéderons étape par étape :
1. ✅ Validation de l'étape précédente
2. 🔧 Implémentation de la nouvelle étape
3. 🧪 Tests
4. 📝 Mise à jour du roadmap

---

*Dernière mise à jour: 29 Décembre 2025*
