# 📊 BOOTY QR - Suivi de Progression

**Dernière mise à jour**: 29 Décembre 2025  
**Statut Global**: 🟡 Phase 1 - Configuration initiale

---

## 🎯 PHASE 1: CONFIGURATION INITIALE & GIT

### ✅ Étapes Complétées
- [x] Git initialisé avec structure branches (main, pre-prod, dev)
- [x] .gitignore configuré
- [x] .env.local configuré (non committé)
- [x] compose.override.yaml configuré (credentials DB)
- [x] Docker Compose opérationnel (postgres + adminer + php)
- [x] Symfony 8.0 installé
- [x] Doctrine ORM configuré
- [x] Connexion DB résolue
- [x] Premier commit effectué
- [x] Repository GitHub créé et code poussé
- [x] Bundles essentiels installés (maker, security, validator, fixtures)
- [x] User créé (avec make:user)
- [x] Hunt créé avec relation User

### 🔄 En Cours
- [ ] Créer QrCode (avec isStartCode pour QR départ)
- [ ] Créer Question et QuestionChoice
- [ ] Créer Hunter, Session, SessionParticipant
- [ ] Créer SessionAnswer et SessionLocation
- [ ] Créer les migrations

### ⏳ À Venir
- [ ] Exécuter migrations et vérifier DB
- [ ] Configurer JWT
- [ ] Créer repositories custom
- [ ] Créer fixtures

---

## 📦 PHASE 2: INSTALLATION BUNDLES

###x] symfony/maker-bundle
- [ ] symfony/debug-bundle  
- [ ] symfony/web-profiler-bundle
- [x] doctrine/doctrine-fixtures-bundle
- [ ] zenstruck/foundry

### Bundles Sécurité & API
- [x] symfony/security-bundle
- [ ] lexik/jwt-authentication-bundle
- [ ] api-platform/core (via `composer require api`)
- [ ] nelmio/cors-bundle

### Bundles Interface & Outils
- [ ] symfony/twig-bundle
- [ ] symfony/form
- [ ] symfony/webpack-encore-bundle
- [ ] endroid/qr-code-bundle
- [ ] vich/uploader-bundle

### Bundles Validation
- [x] symfony/validator
- [ ] symfony/serializer

**Statut**: 🟢 En cours (bundles essentiels installés)
**Statut**: ⏸️ Pas commencé

---

## 🗄️ PHASE 3: CRÉATION ENTITÉS

### Entités Principales
- [x] User (Organizer) - email, password, roles, firstName, lastName, createdAt, updatedAt
- [x] Hunt (Chasse) - organizer (ManyToOne User), title, description, maxDuration, status, createdAt, updatedAt
- [ ] QrCode - hunt (ManyToOne Hunt), code, orderPosition, latitude, longitude, isPlaced, placedAt, createdAt, isStartCode
- [ ] Question - hunt (ManyToOne Hunt), type, questionText, points, timeLimit, image, createdAt, updatedAt
- [ ] QuestionChoice - question (ManyToOne Question), choiceText, isCorrect, orderPosition
- [ ] Hunter - pseudo, groupName, createdAt
- [ ] Session - hunt (ManyToOne Hunt), maxDuration, startedAt, endedAt, status
- [ ] SessionParticipant - session (ManyToOne Session), hunter (ManyToOne Hunter), score, startedAt, finishedAt, status
- [ ] SessionAnswer - sessionParticipant (ManyToOne SessionParticipant), qrCode (ManyToOne QrCode), question (ManyToOne Question), answerText, isCorrect, points, answeredAt, timeSpent
- [ ] SessionLocation - sessionParticipant (ManyToOne SessionParticipant), latitude, longitude, recordedAt

**Statut**: 🟢 En cours (2/10 entités créées)

---

## 🔄 PHASE 4: MIGRATIONS & BASE DE DONNÉES

- [ ] Première migration créée
- [ ] Migration exécutée
- [ ] Tables vérifiées dans Adminer
- [ ] Relations testées

**Statut**: ⏸️ Pas commencé

---

## 🔐 PHASE 5: SÉCURITÉ & AUTHENTIFICATION

- [ ] Security bundle configuré
- [ ] User = UserInterface implémenté
- [ ] JWT généré (clés publique/privée)
- [ ] JWT configuré dans security.yaml
- [ ] Test connexion/authentification

**Statut**: ⏸️ Pas commencé

---

## 🌐 PHASE 6: API REST & ENDPOINTS

### Configuration API Platform
- [ ] API Platform configuré
- [ ] CORS configuré
- [ ] Documentation Swagger accessible

### Endpoints Organisateur
- [ ] POST /api/hunts (créer chasse)
- [ ] GET /api/hunts (lister chasses)
- [ ] PUT /api/hunts/{id} (modifier chasse)
- [ ] DELETE /api/hunts/{id}
- [ ] POST /api/hunts/{id}/qrcodes (ajouter QR code)
- [ ] POST /api/hunts/{id}/questions (ajouter question)

### Endpoints Mobile Organisateur
- [ ] POST /api/qrcodes/{id}/place (placer QR géographiquement)
- [ ] PATCH /api/hunts/{id}/activate (activer chasse)

### Endpoints Participants
- [ ] POST /api/sessions (démarrer session)
- [ ] POST /api/sessions/{id}/scan (scanner QR)
- [ ] GET /api/sessions/{id}/question (récupérer question)
- [ ] POST /api/sessions/{id}/answer (soumettre réponse)
- [ ] POST /api/sessions/{id}/locations (envoyer position GPS)
- [ ] POST /api/sessions/{id}/complete (terminer session)

**Statut**: ⏸️ Pas commencé

---

## 🎨 PHASE 7: INTERFACE WEB ORGANISATEUR

- [ ] Webpack Encore configuré
- [ ] Dashboard principal
- [ ] Page liste chasses
- [ ] Page création/édition chasse
- [ ] Page configuration QR codes
- [ ] Page gestion questions
- [ ] Page génération/impression QR codes
- [ ] Page statistiques
- [ ] Page détail session participant

**Statut**: ⏸️ Pas commencé

---

## 🧮 PHASE 8: LOGIQUE MÉTIER COMPLEXE

- [ ] Service d'attribution aléatoire questions
- [ ] Service calcul distance (Haversine)
- [ ] Service calcul vitesse moyenne
- [ ] Service gestion expiration session
- [ ] Service calcul score total
- [ ] Service classement participants
- [ ] Service validation réponses

**Statut**: ⏸️ Pas commencé

---

## 🌱 PHASE 9: FIXTURES & DONNÉES DE TEST

- [ ] UserFixtures (organisateurs)
- [ ] HuntFixtures (chasses)
- [ ] QrCodeFixtures
- [ ] QuestionFixtures
- [ ] ParticipantFixtures
- [ ] SessionFixtures (sessions complètes)
- [ ] Charger toutes les fixtures

**Statut**: ⏸️ Pas commencé

---

## 🧪 PHASE 10: TESTS

- [ ] Tests unitaires entités
- [ ] Tests fonctionnels API
- [ ] Tests services métier
- [ ] Tests calculs distance/vitesse
- [ ] Tests attribution aléatoire questions

**Statut**: ⏸️ Pas commencé

---

## 📱 PHASE 11: INTÉGRATION FLUTTER

- [ ] Application Organisateur Mobile
- [ ] Application Participant Mobile
- [ ] Connexion API
- [ ] Gestion GPS
- [ ] Scan QR codes

**Statut**: ⏸️ Pas commencé

---

## 🚀 PHASE 12: DÉPLOIEMENT

- [ ] Configuration production
- [ ] Optimisations
- [ ] Documentation API
- [ ] Tests production

**Statut**: ⏸️ Pas commencé

---

## 📝 NOTES DE SESSION
 - Partie 1
**Objectif**: Configuration initiale et structure Git

**Actions réalisées**:
- ✅ Analyse configuration Docker existante
- ✅ Création PROJECT_ROADMAP.md (plan général)
- ✅ Création SETUP_GUIDE.md (guide configuration)
- ✅ Création QUICKSTART.md (guide démarrage rapide)
- ✅ Création database_schema.dbml (schéma DB)
- ✅ Création install-bundles.sh (script installation)
- ✅ Création PROGRESS.md (ce fichier de suivi)

### Session du 29 Décembre 2025 - Partie 2
**Objectif**: Configuration Git, résolution problème DB, installation bundles, clarification modèle

**Actions réalisées**:
- ✅ Structure Git créée : main (prod) → pre-prod (tests) → dev (développement)
- ✅ Premier commit effectué et poussé sur GitHub
- ✅ Résolution problème authentification PostgreSQL
  - Ajout credentials dans compose.override.yaml (non committé)
  - Configuration DATABASE_URL dans service PHP
  - Correction healthcheck PostgreSQL
- ✅ Installation bundles essentiels :
  - symfony/maker-bundle (dev)
  - symfony/security-bundle
  - symfony/validator
  - doctrine/doctrine-fixtures-bundle (dev)
- ✅ Clarification complète du modèle de données

**Compréhension du projet validée**:
- **Hunt vs Session** : Hunt = template/modèle (QR + questions), Session = instance lancée avec startedAt/endedAt

**Actions entités** :
- ✅ User créé avec make:user (email, password hashed, roles, firstName, lastName, createdAt, updatedAt)
- ✅ Hunt créé (title, description, maxDuration, status, createdAt, updatedAt) avec relation ManyToOne vers User

**Prochaine action**: Continuer création entités (QrCode avec isStartCode, Question, QuestionChoice, Hunter, Session, SessionParticipant, SessionAnswer, SessionLocation)

**Blocages**: Aucun

**Questions en suspens**: Aucune
- **Attribution questions** : Aléatoire parmi celles non posées au hunter, pas de répétition
- **QR codes** : Ordre libre, hunter ne peut pas rescanner un QR déjà fait
- **Tracking GPS** : Position du hunter enregistrée pendant toute la session (anti-triche)
- **Temps** : Limite globale sur la session (définie par organisateur) + optionnel par question
- **Calcul scores** : Après la session, statistiques web (organisateur) et mobile (hunters)

**Structure finale 10 entités**:
   - Champs: email, password (hashed), roles, firstName, lastName, createdAt, updatedAt
   - Relation: OneToMany → Hunt
2. Hunt (chasse créée par User)
   - Champs: title, description, maxDuration (minutes), status (draft/ready/active/archived), createdAt, updatedAt
   - Relations: ManyToOne → User, OneToMany → QrCode, OneToMany → Question, OneToMany → Session
3. QrCode (appartient à Hunt, dont QR "départ")
   - Champs: code (unique), orderPosition, latitude, longitude, isPlaced, placedAt, createdAt, isStartCode
   - Relation: ManyToOne → Hunt
4. Question (appartient à Hunt, choix multiple OU texte libre, points, temps limite optionnel)
   - Champs: type (multiple_choice/free_text), questionText, points, timeLimit (secondes), image, createdAt, updatedAt
   - Relations: ManyToOne → Hunt, OneToMany → QuestionChoice
5. QuestionChoice (choix d'une Question)
   - Champs: choiceText, isCorrect, orderPosition
   - Relation: ManyToOne → Question
6. Hunter (juste pseudo/nom, pas de compte)
   - Champs: pseudo, groupName, createdAt
   - Relation: OneToMany → SessionParticipant
7. Session (lancée par organisateur sur une Hunt)
   - Champs: maxDuration (minutes), startedAt, endedAt, status (pending/active/finished)
   - Relations: ManyToOne → Hunt, OneToMany → SessionParticipant
8. SessionParticipant (entité intermédiaire: Hunter dans Session avec score/temps/statut)
   - Champs: score, startedAt, finishedAt, status (in_progress/completed/abandoned)
   - Relations: ManyToOne → Session, ManyToOne → Hunter, OneToMany → SessionAnswer, OneToMany → SessionLocation
9. SessionAnswer (réponse: SessionParticipant + QrCode + Question + réponse + points + temps)
   - Champs: answerText, isCorrect, points, answeredAt, timeSpent (secondes)
   - Relations: ManyToOne → SessionParticipant, ManyToOne → QrCode, ManyToOne → Question
10. SessionLocation (tracking GPS: SessionParticipant + lat/lng + timestamp)
    - Champs: latitude, longitude, recordedAt
    - Relation: ManyToOne → SessionParticipant

**Prochaine action**: Créer les entités restantes (QrCode, Question, QuestionChoice, Hunter, Session, SessionParticipant, SessionAnswer, SessionLocation
**Prochaine action**: Créer les entités de base (User, Hunt, QrCode, Question, QuestionChoice)

**Blocages**: Aucun

**Questions en suspens**: Aucune

**⚠️ RAPPEL IMPORTA██ 100% (Configuration initiale - TERMINÉE)
Phase 2  : ████░░░░░░  40% (Bundles essentiels installés)
Phase 3  : ░░░░░░░░░░   0% (Entités)
Phase 4  : ░░░░░░░░░░   0% (Migrations)
Phase 5  : ░░░░░░░░░░   0% (Sécurité)
Phase 6  : ░░░░░░░░░░   0% (API)
Phase 7  : ░░░░░░░░░░   0% (Interface Web)
Phase 8  : ░░░░░░░░░░   0% (Logique Métier)
Phase 9  : ░░░░░░░░░░   0% (Fixtures)
Phase 10 : ░░░░░░░░░░   0% (Tests)
Phase 11 : ░░░░░░░░░░   0% (Flutter)
Phase 12 : ░░░░░░░░░░   0% (Déploiement)

TOTAL    : ██░░░░░░░░  15 IMMÉDIATES

1. **Améliorer .gitignore** (ajouter règles Docker, IDE, etc.)
2. **Créer .env.local** (avec vos paramètres personnels)
3. **Faire premier commit** (état initial propre)
4. **Créer repo GitHub** et pousser le code
5. **Installer premier bundle** (maker-bundle) pour tester

---

## 📊 PROGRESSION GLOBALE

```
Phase 1  : ████████░░ 80%  (Configuration initiale)
Phase 2  : ░░░░░░░░░░  0%  (Bundles)
Phase 3  : ░░░░░░░░░░  0%  (Entités)
Phase 4  : ░░░░░░░░░░  0%  (Migrations)
Phase 5  : ░░░░░░░░░░  0%  (Sécurité)
Phase 6  : ░░░░░░░░░░  0%  (API)
Phase 7  : ░░░░░░░░░░  0%  (Interface Web)
Phase 8  : ░░░░░░░░░░  0%  (Logique Métier)
Phase 9  : ░░░░░░░░░░  0%  (Fixtures)
Phase 10 : ░░░░░░░░░░  0%  (Tests)
Phase 11 : ░░░░░░░░░░  0%  (Flutter)
Phase 12 : ░░░░░░░░░░  0%  (Déploiement)

TOTAL    : █░░░░░░░░░  7%
```

---

*Ce fichier sera mis à jour après chaque étape complétée*
*N'hésitez pas à ajouter vos propres notes dans la section "Notes de Session"*
