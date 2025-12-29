# 🎯 BOOTY QR - Planning de Développement
## Projet de Chasse aux QR Codes avec Questions Interactives

**Statut Projet**: 🟢 Phase 1 - Configuration Infrastructure  
**Date Début**: 29 Décembre 2025  
**Stack**: Symfony 8.0 + PostgreSQL + Docker + Flutter (mobile)

---

## 📋 RÉSUMÉ DU PROJET

### Concept
Application de chasse aux QR codes interactive où les organisateurs créent des parcours avec questions et les participants chassent les QR codes en répondant aux questions contre la montre.

### Acteurs
- **Organisateur (Web)**: Création/configuration des chasses, QR codes, questions, consultation statistiques
- **Organisateur (Mobile Flutter)**: Placement géographique des QR codes
- **Participant (Mobile Flutter)**: Scan QR codes, réponses questions, tracking GPS

### Fonctionnalités Clés
1. ✅ Configuration chasses avec N QR codes
2. ✅ Questions (choix multiple OU réponse libre) avec points configurables
3. ✅ Attribution aléatoire des questions aux QR codes
4. ✅ Tracking GPS complet du participant
5. ✅ Chronomètres multiples (session, temps réponse)
6. ✅ Statistiques complètes + classements
7. ✅ Visualisation parcours sur carte
8. ✅ Gestion temps maximum par session

---

## 🏗️ PHASE 1: INFRASTRUCTURE & BUNDLES (EN COURS)

### ✅ État Actuel
- [x] Symfony 8.0 installé
- [x] Doctrine ORM + Migrations
- [x] PostgreSQL configuré
- [x] Docker + FrankenPHP
- [x] Adminer (interface DB)

### 🎯 Bundles à Installer

#### Sécurité & Authentification
```bash
composer require symfony/security-bundle
composer require lexik/jwt-authentication-bundle  # Pour API mobile
```

#### API REST
```bash
composer require api  # API Platform pour REST API
composer require nelmio/cors-bundle  # CORS pour Flutter
```

#### Validation & Serialization
```bash
composer require symfony/validator
composer require symfony/serializer
```

#### Assets & Interface Web
```bash
composer require symfony/webpack-encore-bundle
composer require symfony/twig-bundle
composer require symfony/form
composer require symfony/asset
```

#### Upload & Fichiers
```bash
composer require vich/uploader-bundle  # Upload images questions
```

#### QR Code Generation
```bash
composer require endroid/qr-code-bundle
```

#### Outils Développement
```bash
composer require --dev symfony/maker-bundle
composer require --dev symfony/debug-bundle
composer require --dev symfony/web-profiler-bundle
composer require --dev doctrine/doctrine-fixtures-bundle
composer require --dev zenstruck/foundry
```

#### Tests
```bash
composer require --dev symfony/test-pack
composer require --dev phpunit/phpunit
```

---

## 🗃️ PHASE 2: MODÉLISATION BASE DE DONNÉES

### Entités à Créer

#### 1. User (Organizer)
```
- id: UUID
- email: string (unique)
- password: string (hashed)
- roles: array
- firstName: string
- lastName: string
- createdAt: datetime
- updatedAt: datetime
```

#### 2. Hunt (Chasse)
```
- id: UUID
- organizer: User (ManyToOne)
- title: string
- description: text
- maxDuration: int (minutes, nullable)
- status: enum (draft, ready, active, completed, archived)
- createdAt: datetime
- updatedAt: datetime
- startedAt: datetime (nullable)
- endedAt: datetime (nullable)
```

#### 3. QrCode
```
- id: UUID
- hunt: Hunt (ManyToOne)
- code: string (unique, généré)
- order: int
- latitude: float (nullable)
- longitude: float (nullable)
- isPlaced: boolean
- placedAt: datetime (nullable)
- createdAt: datetime
```

#### 4. Question
```
- id: UUID
- hunt: Hunt (ManyToOne)
- type: enum (multiple_choice, free_text)
- questionText: text
- points: int
- timeLimit: int (secondes, nullable)
- image: string (nullable)
- createdAt: datetime
- updatedAt: datetime
```

#### 5. QuestionChoice (Pour choix multiples)
```
- id: UUID
- question: Question (ManyToOne)
- choiceText: string
- isCorrect: boolean
- order: int
```

#### 6. Participant
```
- id: UUID
- email: string (nullable)
- firstName: string
- lastName: string
- phoneNumber: string (nullable)
- createdAt: datetime
```

#### 7. Session
```
- id: UUID
- hunt: Hunt (ManyToOne)
- participant: Participant (ManyToOne)
- status: enum (in_progress, completed, expired, abandoned)
- startedAt: datetime
- completedAt: datetime (nullable)
- totalDuration: int (secondes)
- totalScore: int
- distanceTraveled: float (km)
- averageSpeed: float (km/h)
```

#### 8. SessionAnswer
```
- id: UUID
- session: Session (ManyToOne)
- qrCode: QrCode (ManyToOne)
- question: Question (ManyToOne)
- answerText: text (nullable - pour réponse libre)
- selectedChoices: array (nullable - IDs pour choix multiples)
- isCorrect: boolean (nullable - null si réponse libre non notée)
- pointsAwarded: int
- timeSpent: int (secondes)
- answeredAt: datetime
- scannedAt: datetime
```

#### 9. SessionLocation
```
- id: UUID
- session: Session (ManyToOne)
- latitude: float
- longitude: float
- accuracy: float (mètres)
- recordedAt: datetime
```

---

## 🔧 PHASE 3: CONFIGURATION SÉCURITÉ & API

### Sous-phases
- [ ] 3.1: Configuration Security (Users)
- [ ] 3.2: Configuration JWT pour API mobile
- [ ] 3.3: Configuration CORS
- [ ] 3.4: Création endpoints API Platform
- [ ] 3.5: Groupes de sérialisation

---

## 🎨 PHASE 4: INTERFACE WEB ORGANISATEUR

### Sous-phases
- [ ] 4.1: Setup Webpack Encore + Tailwind/Bootstrap
- [ ] 4.2: Dashboard principal
- [ ] 4.3: CRUD Hunts (chasses)
- [ ] 4.4: Configuration QR Codes par chasse
- [ ] 4.5: CRUD Questions + gestion réponses
- [ ] 4.6: Génération & impression QR codes
- [ ] 4.7: Interface liste des chasses actives

---

## 🔥 PHASE 5: API ENDPOINTS MOBILE

### Endpoints Organisateur Mobile
- [ ] 5.1: POST /api/qrcodes/{id}/place - Placer un QR code
- [ ] 5.2: GET /api/hunts/{id}/qrcodes - Liste QR codes à placer
- [ ] 5.3: PATCH /api/hunts/{id}/activate - Activer une chasse

### Endpoints Participant Mobile
- [ ] 5.4: POST /api/sessions - Démarrer une session
- [ ] 5.5: POST /api/sessions/{id}/scan - Scanner un QR code
- [ ] 5.6: GET /api/sessions/{id}/question - Obtenir question
- [ ] 5.7: POST /api/sessions/{id}/answer - Soumettre réponse
- [ ] 5.8: POST /api/sessions/{id}/locations - Enregistrer positions GPS
- [ ] 5.9: POST /api/sessions/{id}/complete - Terminer session
- [ ] 5.10: GET /api/sessions/{id}/progress - Progression session

---

## 📊 PHASE 6: STATISTIQUES & CLASSEMENTS

### Sous-phases
- [ ] 6.1: Page liste des sessions terminées
- [ ] 6.2: Détail session avec infos participant
- [ ] 6.3: Classement participants (score, temps, distance)
- [ ] 6.4: Visualisation parcours sur carte (Google Maps / Leaflet)
- [ ] 6.5: Détail réponses question par question
- [ ] 6.6: Interface notation réponses libres
- [ ] 6.7: Export données (PDF/Excel)

---

## 🧮 PHASE 7: LOGIQUE MÉTIER COMPLEXE

### Algorithmes à implémenter
- [ ] 7.1: Attribution aléatoire questions aux QR codes
- [ ] 7.2: Calcul distance parcourue (Haversine)
- [ ] 7.3: Calcul vitesse moyenne
- [ ] 7.4: Gestion expiration session (temps max)
- [ ] 7.5: Calcul score total (avec bonus temps?)
- [ ] 7.6: Validation unicité questions par participant

---

## 📱 PHASE 8: INTÉGRATION FLUTTER

### Applications Mobile
- [ ] 8.1: App Organisateur - Connexion
- [ ] 8.2: App Organisateur - Scanner & placer QR codes
- [ ] 8.3: App Participant - Inscription/Connexion session
- [ ] 8.4: App Participant - Scanner QR codes
- [ ] 8.5: App Participant - Interface réponse questions
- [ ] 8.6: App Participant - Tracking GPS
- [ ] 8.7: App Participant - Affichage progression

---

## 🧪 PHASE 9: TESTS & QUALITÉ

### Sous-phases
- [ ] 9.1: Tests unitaires entités
- [ ] 9.2: Tests fonctionnels contrôleurs
- [ ] 9.3: Tests API endpoints
- [ ] 9.4: Tests algorithmes métier
- [ ] 9.5: Fixtures pour données de test

---

## 🚀 PHASE 10: DÉPLOIEMENT & PRODUCTION

### Sous-phases
- [ ] 10.1: Configuration environnement production
- [ ] 10.2: Optimisation performances
- [ ] 10.3: Backup automatique DB
- [ ] 10.4: Monitoring & logs
- [ ] 10.5: Documentation API

---

## 📌 PROCHAINES ÉTAPES IMMÉDIATES

### 🎯 À Faire Maintenant
1. **Installer les bundles essentiels** (Maker, Security, API Platform)
2. **Créer les entités de base** (User, Hunt, QrCode, Question)
3. **Configurer l'authentification** (Security bundle)
4. **Générer la première migration**
5. **Créer fixtures de test**

---

## 📝 NOTES TECHNIQUES

### Considérations Architecture
- **API REST** pour communication Symfony ↔ Flutter
- **JWT** pour authentification mobile sécurisée
- **WebSocket** (Mercure) pour notifications temps réel (optionnel)
- **PostGIS** pour requêtes géospatiales avancées (optionnel)
- **Redis** pour cache sessions actives (optionnel)

### Optimisations Futures
- Index sur colonnes de recherche fréquente
- Cache Redis pour classements
- Queue système pour calculs lourds
- CDN pour QR codes générés

---

## 🏁 PROGRESSION GLOBALE

**Phase 1**: ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ 40% (Configuration en cours)  
**Projet Global**: ⬜⬜⬜⬜⬜⬜⬜⬜⬜⬜ 5%

---

*Dernière mise à jour: 29 Décembre 2025*
*Ce document sera mis à jour à chaque étape complétée*
