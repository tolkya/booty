# 📋 ANALYSE DES CAS - FORMULAIRES HUNT & QUESTIONS

## 🎯 Vue d'ensemble

Le flux de création Hunt comporte **2 formulaires successifs** avec plusieurs scénarios possibles selon les choix de l'organisateur.

---

## 🔀 ARBRE DE DÉCISION COMPLET

```
FORMULAIRE 1 : Création Hunt
│
├─ Mode de jeu : "qr_only" (QR codes uniquement)
│  └─> PAS DE FORMULAIRE QUESTIONS
│      └─> Redirection : Page détails hunt OU liste hunts
│      └─> Actions possibles : Télécharger PDF QR codes, placer QR géographiquement
│
└─ Mode de jeu : "qr_with_questions" (QR codes + Questions)
   └─> FORMULAIRE QUESTIONS (obligatoire)
       │
       ├─ Temps limite : NON (timeLimitEnabled = false)
       │  └─> Formulaire questions SIMPLE
       │      ├─ Champs visibles : Type, Texte, Points, Image, Choix
       │      ├─ Champs masqués : Pénalité, Temps par question
       │      └─> Questions sans contrainte de temps
       │
       └─ Temps limite : OUI (timeLimitEnabled = true)
          ├─ defaultTimeLimit : durée définie (ex: 2min 30s = 150 secondes)
          │
          ├─ Mode temps dépassé : "strict" (timeLimitMode = 'strict')
          │  └─> Formulaire questions STRICT
          │      ├─ Champs visibles : Type, Texte, Points, Image, Choix
          │      ├─ Champs masqués : Pénalité (pas de pénalité en mode strict)
          │      ├─ Info affichée : "Temps par défaut: 2min 30s (blocage après)"
          │      └─> Après temps : impossible de répondre (0 points)
          │
          └─ Mode temps dépassé : "penalty" (timeLimitMode = 'penalty')
             └─> Formulaire questions AVEC PÉNALITÉ
                 ├─ Champs visibles : Type, Texte, Points, Pénalité, Image, Choix
                 ├─ Info affichée : "Temps par défaut: 2min 30s (pénalité si dépassé)"
                 ├─ Champ pénalité : défaut basé sur points (ex: points/2)
                 └─> Après temps : réponse possible mais -X points
```

---

## 📊 MATRICE DES CAS

| Cas | Mode | Temps limite | Mode dépassé | Champs formulaire questions | Validation |
|-----|------|--------------|--------------|----------------------------|------------|
| **A** | qr_only | N/A | N/A | **Aucun** (pas de formulaire) | N/A |
| **B.1** | qr_with_questions | NON | N/A | Type, Texte, Points, Image, Choix | Required: type, texte |
| **B.2.a** | qr_with_questions | OUI | strict | Type, Texte, Points, Image, Choix | Required: type, texte |
| **B.2.b** | qr_with_questions | OUI | penalty | Type, Texte, Points, **Pénalité**, Image, Choix | Required: type, texte, pénalité |

---

## 🗂️ DONNÉES STOCKÉES

### Dans Hunt (BDD)
```php
- mode: string ('qr_only' | 'qr_with_questions')
- timeLimitEnabled: bool (true si temps limite activé)
- defaultTimeLimit: int|null (en secondes, ex: 150)
- timeLimitMode: string|null ('strict' | 'penalty')
```

### Dans Session (après formulaire 1)
```php
$session->set('default_question_time_limit', $defaultTimeLimit);
$session->set('time_limit_mode', $timeLimitMode);
```

### Dans Question (BDD)
```php
- timeLimit: int|null (hérité de defaultTimeLimit ou personnalisé)
- penalty: decimal|null (uniquement si timeLimitMode = 'penalty')
```

---

## 🎯 IMPACTS PAR FICHIER

### 1. HuntController.php

#### Méthode `new()` (après validation formulaire 1)
**Redirection conditionnelle** :
```php
if ($hunt->getMode() === 'qr_only') {
    // Cas A : Pas de questions
    $this->addFlash('success', 'Chasse créée ! Vous pouvez maintenant placer vos QR codes.');
    return $this->redirectToRoute('hunt_show', ['id' => $hunt->getId()]);
} else {
    // Cas B : Redirection vers formulaire questions
    return $this->redirectToRoute('hunt_questions', ['id' => $hunt->getId()]);
}
```

#### Méthode `questions()` (affichage formulaire 2)
**Vérifications préalables** :
```php
// Vérifier que la hunt nécessite des questions
if ($hunt->getMode() === 'qr_only') {
    throw $this->createAccessDeniedException('Cette chasse ne nécessite pas de questions.');
}

// Récupérer paramètres depuis hunt (pas session)
$timeLimitEnabled = $hunt->getTimeLimitEnabled();
$defaultTimeLimit = $hunt->getDefaultTimeLimit();
$timeLimitMode = $hunt->getTimeLimitMode();

// Passer au template pour affichage conditionnel
```

### 2. QuestionType.php

**Modifications nécessaires** :
- Rendre champ `penalty` optionnel par défaut
- Possibilité de masquer pénalité selon contexte

**Option 1** : Passer options au FormType
```php
$builder->add('penalty', IntegerType::class, [
    'required' => $options['show_penalty'], // Conditionnel
    // ...
]);

// Dans configureOptions
$resolver->setDefaults([
    'show_penalty' => true, // Par défaut visible
]);
```

**Option 2** : Gérer en JavaScript (masquer si pas nécessaire)

### 3. questions.html.twig

**Ajout infos contextuelles** :
```twig
{% if timeLimitEnabled %}
    <div class="alert alert-warning">
        <strong>⏱️ Temps limite activé</strong><br>
        Durée par défaut : {{ defaultTimeLimit // 60 }}min {{ defaultTimeLimit % 60 }}s
        
        {% if timeLimitMode == 'strict' %}
            <br>Mode : <strong>Strict</strong> - Après le temps, impossible de répondre (0 points)
        {% else %}
            <br>Mode : <strong>Pénalité</strong> - Après le temps, pénalité appliquée sur les points
        {% endif %}
    </div>
{% endif %}
```

**Masquage conditionnel pénalité** :
```twig
{% if not timeLimitEnabled or timeLimitMode == 'strict' %}
    <style>
        .penalty-field { display: none !important; }
    </style>
{% endif %}
```

### 4. _question_block.html.twig

**Ajout classe pénalité** :
```twig
<div class="col-md-6 mb-3 penalty-field">
    {{ form_row(questionForm.penalty) }}
</div>
```

### 5. questions-form-symfony.js

**Gestion conditionnelle pénalité** :
```javascript
// Récupérer paramètres depuis variables globales
const SHOW_PENALTY = window.SHOW_PENALTY || false;

// Lors création nouvelle question
if (!SHOW_PENALTY) {
    const penaltyField = questionBlock.querySelector('.penalty-field');
    if (penaltyField) {
        penaltyField.remove(); // Ou masquer
    }
}
```

### 6. Hunt.php (Entity)

**Vérifier champs existants** :
- mode
- timeLimitEnabled
- defaultTimeLimit
- timeLimitMode

**Si manquants** : Migration nécessaire

### 7. Question.php (Entity)

**Vérifier champs** :
- timeLimit (existe déjà)
- penalty (existe déjà selon PROGRESS.md)

---

## ✅ PLAN D'ACTION PAR ÉTAPES

### ÉTAPE 1 : Vérification BDD/Entities
- [ ] Vérifier champs Hunt (mode, timeLimitEnabled, defaultTimeLimit, timeLimitMode)
- [ ] Vérifier champs Question (timeLimit, penalty)
- [ ] Migration si nécessaire

### ÉTAPE 2 : Redirection conditionnelle
- [ ] Modifier HuntController::new() → redirection selon mode
- [ ] Modifier HuntController::questions() → vérification mode + récupération params

### ÉTAPE 3 : Adaptation formulaire questions
- [ ] Passer paramètres au template (timeLimitEnabled, defaultTimeLimit, timeLimitMode)
- [ ] Afficher info contextuelle (alerte temps/mode)
- [ ] Masquer pénalité si strict ou pas de temps

### ÉTAPE 4 : JavaScript conditionnel
- [ ] Variables globales pour paramètres
- [ ] Gérer affichage pénalité dans nouvelles questions

### ÉTAPE 5 : Validation controller
- [ ] Gérer timeLimit par défaut lors création Question
- [ ] Ignorer penalty si mode strict ou pas de temps

### ÉTAPE 6 : Tests cas par cas
- [ ] Test cas A (qr_only)
- [ ] Test cas B.1 (questions sans temps)
- [ ] Test cas B.2.a (questions temps strict)
- [ ] Test cas B.2.b (questions temps pénalité)

---

## ⚠️ POINTS D'ATTENTION

1. **Pas de temps limite** ≠ **Mode strict** :
   - Sans temps : pas de timeLimit du tout
   - Mode strict : timeLimit existe, mais pas de penalty

2. **Validation cohérente** :
   - Si timeLimitMode = 'penalty' → penalty REQUIRED
   - Si timeLimitMode = 'strict' → penalty NULL
   - Si pas de temps → timeLimit NULL, penalty NULL

3. **Session vs BDD** :
   - Actuellement paramètres en session (temporaire)
   - Mieux : lire depuis Hunt entity (persistant)

4. **Affichage questions existantes** :
   - Si organisateur revient éditer : afficher params cohérents
   - Gérer cas où hunt existe avec questions déjà créées

---

## 🔄 FLUX COMPLET

```
1. Organisateur crée Hunt (formulaire 1)
   ↓
2. Hunt sauvegardé en BDD avec mode/temps/pénalité
   ↓
3. Redirection conditionnelle selon mode
   ├─ qr_only → Page détails hunt
   └─ qr_with_questions → Formulaire questions (adapté selon params)
   ↓
4. Organisateur crée questions
   ↓
5. Questions sauvegardées avec timeLimit/penalty selon contexte
   ↓
6. Hunt prêt pour activation
```

---

**Document de référence - Ne pas supprimer**
**Mise à jour : 4 Janvier 2026**
