# 🎯 Booty QR - Guide de Démarrage Rapide

## 📦 1. Installation des Bundles (PREMIÈRE ÉTAPE)

```bash
# Rendre le script exécutable
chmod +x install-bundles.sh

# Lancer l'installation
./install-bundles.sh
```

⏱️ **Durée**: 5-10 minutes

---

## 🔐 2. Configuration JWT pour API Mobile

```bash
# Générer les clés JWT
docker compose exec php bin/console lexik:jwt:generate-keypair
```

Ajouter dans `.env` :
```env
JWT_SECRET_KEY=%kernel.project_dir%/config/jwt/private.pem
JWT_PUBLIC_KEY=%kernel.project_dir%/config/jwt/public.pem
JWT_PASSPHRASE=votre_passphrase_ici
```

---

## 🗄️ 3. Création des Entités (DANS L'ORDRE)

### User
```bash
docker compose exec php bin/console make:entity User
```
- email: string, 180
- firstName: string, 100
- lastName: string, 100
- roles: json

### Hunt
```bash
docker compose exec php bin/console make:entity Hunt
```
- organizer: relation ManyToOne → User
- title: string, 255
- description: text, nullable
- maxDuration: integer, nullable
- status: string, 50
- startedAt: datetime_immutable, nullable
- endedAt: datetime_immutable, nullable

### QrCode
```bash
docker compose exec php bin/console make:entity QrCode
```
- hunt: relation ManyToOne → Hunt
- code: string, 255
- orderPosition: integer
- latitude: float, nullable
- longitude: float, nullable
- isPlaced: boolean
- placedAt: datetime_immutable, nullable

### Question
```bash
docker compose exec php bin/console make:entity Question
```
- hunt: relation ManyToOne → Hunt
- type: string, 50
- questionText: text
- points: integer
- timeLimit: integer, nullable
- image: string, 255, nullable

### QuestionChoice
```bash
docker compose exec php bin/console make:entity QuestionChoice
```
- question: relation ManyToOne → Question
- choiceText: string, 500
- isCorrect: boolean
- orderPosition: integer

### Participant
```bash
docker compose exec php bin/console make:entity Participant
```
- email: string, 180, nullable
- firstName: string, 100
- lastName: string, 100
- phoneNumber: string, 20, nullable

### Session
```bash
docker compose exec php bin/console make:entity Session
```
- hunt: relation ManyToOne → Hunt
- participant: relation ManyToOne → Participant
- status: string, 50
- startedAt: datetime_immutable
- completedAt: datetime_immutable, nullable
- totalDuration: integer
- totalScore: integer
- distanceTraveled: float
- averageSpeed: float

### SessionAnswer
```bash
docker compose exec php bin/console make:entity SessionAnswer
```
- session: relation ManyToOne → Session
- qrCode: relation ManyToOne → QrCode
- question: relation ManyToOne → Question
- answerText: text, nullable
- selectedChoices: json, nullable
- isCorrect: boolean, nullable
- pointsAwarded: integer
- timeSpent: integer
- answeredAt: datetime_immutable
- scannedAt: datetime_immutable

### SessionLocation
```bash
docker compose exec php bin/console make:entity SessionLocation
```
- session: relation ManyToOne → Session
- latitude: float
- longitude: float
- accuracy: float
- recordedAt: datetime_immutable

---

## 🔄 4. Migrations

```bash
# Créer la migration
docker compose exec php bin/console make:migration

# Vérifier le SQL généré
cat migrations/*.php

# Exécuter la migration
docker compose exec php bin/console doctrine:migrations:migrate
```

---

## 👤 5. Configuration Sécurité

```bash
# Créer User comme entité d'authentification
docker compose exec php bin/console make:user

# Répondre aux questions :
# - The name of the security user class: User
# - Do you want to store user data in the database (via Doctrine)? yes
# - Enter a property name that will be the unique "display" name: email
# - Will this app need to hash/check user passwords? yes
```

---

## 🌱 6. Fixtures de Test

```bash
# Créer fixtures
docker compose exec php bin/console make:fixtures AppFixtures

# Charger les fixtures (ATTENTION: vide la DB)
docker compose exec php bin/console doctrine:fixtures:load
```

---

## 🧪 7. Premiers Tests

```bash
# Créer un contrôleur de test
docker compose exec php bin/console make:controller TestController

# Voir les routes
docker compose exec php bin/console debug:router

# Tester dans le navigateur
# http://localhost/test
```

---

## 🎨 8. Interface Web (Optionnel pour l'instant)

```bash
# Installer assets npm
docker compose exec php npm install

# Compiler en mode dev
docker compose exec php npm run dev

# Watcher (recompile automatiquement)
docker compose exec php npm run watch
```

---

## 🌐 9. Configuration API Platform

Créer `config/packages/api_platform.yaml` :
```yaml
api_platform:
    title: 'Booty QR API'
    version: '1.0.0'
    show_webby: false
    
    defaults:
        pagination_enabled: true
        pagination_items_per_page: 30
    
    formats:
        json: ['application/json']
    
    swagger:
        api_keys:
            JWT:
                name: Authorization
                type: header
```

---

## 🔗 10. Configuration CORS

Créer `config/packages/nelmio_cors.yaml` :
```yaml
nelmio_cors:
    defaults:
        origin_regex: true
        allow_origin: ['*']
        allow_methods: ['GET', 'OPTIONS', 'POST', 'PUT', 'PATCH', 'DELETE']
        allow_headers: ['Content-Type', 'Authorization']
        expose_headers: ['Link']
        max_age: 3600
    paths:
        '^/api':
            allow_origin: ['*']
            allow_headers: ['*']
            allow_methods: ['POST', 'PUT', 'GET', 'DELETE', 'PATCH', 'OPTIONS']
            max_age: 3600
```

---

## 📊 11. Accès Base de Données

**Via Adminer** (Interface graphique) :
- URL : http://localhost:8080
- Système : PostgreSQL
- Serveur : database
- Utilisateur : app
- Mot de passe : !ChangeMe!
- Base : booty

**Via CLI** :
```bash
docker compose exec database psql -U app -d booty
```

---

## 🔍 12. Commandes Utiles au Quotidien

```bash
# Clear cache
docker compose exec php bin/console cache:clear

# Voir la structure d'une entité
docker compose exec php bin/console doctrine:mapping:info

# Voir le SQL d'une entité
docker compose exec php bin/console doctrine:schema:update --dump-sql

# Créer un CRUD complet
docker compose exec php bin/console make:crud Hunt

# Logs en temps réel
docker compose logs -f php

# Accéder au bash du container
docker compose exec php bash
```

---

## 📈 Visualiser le Schéma de Base de Données

```bash
# Générer un diagramme avec dbml-renderer (déjà installé dans Docker)
docker compose exec php dbml2sql database_schema.dbml --postgres -o database_schema.sql

# Ou allez sur https://dbdiagram.io
# Et collez le contenu de database_schema.dbml
```

---

## ✅ Checklist de Validation

Après avoir suivi toutes les étapes :

- [ ] Bundles installés sans erreur
- [ ] JWT configuré (dossier `config/jwt/` existe)
- [ ] 9 entités créées (User, Hunt, QrCode, Question, QuestionChoice, Participant, Session, SessionAnswer, SessionLocation)
- [ ] Migration créée et exécutée
- [ ] Base de données visible dans Adminer avec 9 tables
- [ ] Security configuré (User = User Security)
- [ ] Routes API visibles avec `bin/console debug:router`
- [ ] Accès à http://localhost fonctionne

---

## 🚀 Prochaines Étapes

Une fois cette phase terminée, nous passerons à :

1. **Phase 2** : Créer les premiers contrôleurs API
2. **Phase 3** : Implémenter la logique d'attribution aléatoire des questions
3. **Phase 4** : Interface web pour les organisateurs
4. **Phase 5** : Calculs de distance et tracking GPS
5. **Phase 6** : Statistiques et classements
6. **Phase 7** : Intégration Flutter

---

## 📚 Documentation

- [PROJECT_ROADMAP.md](PROJECT_ROADMAP.md) - Plan complet du projet
- [SETUP_GUIDE.md](SETUP_GUIDE.md) - Guide détaillé de configuration
- [database_schema.dbml](database_schema.dbml) - Schéma de base de données
- Symfony Docs : https://symfony.com/doc/current/
- API Platform : https://api-platform.com/docs/

---

**Prêt à commencer ?** Lance `./install-bundles.sh` ! 🚀
