// Gestion du formulaire dynamique de questions
let questionCounter = 0;

// Récupérer les constantes depuis les data attributes
const scriptTag = document.querySelector('script[data-min-questions]');
const MIN_QUESTIONS = parseInt(scriptTag?.dataset.minQuestions || 1);
const DEFAULT_TIME_LIMIT = scriptTag?.dataset.defaultTime !== 'null' ? parseInt(scriptTag.dataset.defaultTime) : null;
const TIME_LIMIT_MODE = scriptTag?.dataset.timeMode || null;

document.addEventListener('DOMContentLoaded', function() {
    const questionsContainer = document.getElementById('questions-container');
    const addQuestionBtn = document.getElementById('add-question-btn');
    const validateBtn = document.getElementById('validate-questions-btn');
    
    // Ajouter une question
    addQuestionBtn.addEventListener('click', function() {
        questionCounter++;
        const questionHtml = createQuestionBlock(questionCounter);
        questionsContainer.insertAdjacentHTML('beforeend', questionHtml);
        initializeQuestionEvents(questionCounter);
        checkValidation();
    });
    
    function createQuestionBlock(id) {
        return `
            <div class="question-block card mb-4" id="question-${id}">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5>Question #${id}</h5>
                    <button type="button" class="btn btn-sm btn-danger delete-question" data-id="${id}">
                        🗑️ Supprimer
                    </button>
                </div>
                <div class="card-body">
                    <!-- Type de question -->
                    <div class="mb-3">
                        <label class="form-label">Type de question</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="question-${id}-type" id="type-${id}-qcm" value="qcm" checked>
                            <label class="btn btn-outline-primary" for="type-${id}-qcm">QCM</label>
                            
                            <input type="radio" class="btn-check" name="question-${id}-type" id="type-${id}-truefalse" value="true_false">
                            <label class="btn btn-outline-primary" for="type-${id}-truefalse">Vrai/Faux</label>
                            
                            <input type="radio" class="btn-check" name="question-${id}-type" id="type-${id}-text" value="text">
                            <label class="btn btn-outline-primary" for="type-${id}-text">Texte libre</label>
                        </div>
                    </div>
                    
                    <!-- Texte de la question -->
                    <div class="mb-3">
                        <label class="form-label">Texte de la question</label>
                        <textarea class="form-control" name="question-${id}-text" rows="3" required></textarea>
                    </div>
                    
                    <!-- Points -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Points</label>
                            <input type="number" class="form-control" name="question-${id}-points" value="10" min="1">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pénalité (si temps dépassé)</label>
                            <input type="number" class="form-control" name="question-${id}-penalty" value="5" min="0">
                        </div>
                    </div>
                    
                    <!-- Image optionnelle -->
                    <div class="mb-3">
                        <label class="form-label">Image (optionnel)</label>
                        <input type="file" class="form-control" name="question-${id}-image" accept="image/*">
                    </div>
                    
                    <!-- Choix de réponses (QCM et Vrai/Faux) -->
                    <div class="choices-container" id="choices-${id}">
                        ${createChoiceHtml(id, 1)}
                        ${createChoiceHtml(id, 2)}
                    </div>
                    
                    <button type="button" class="btn btn-sm btn-secondary add-choice" data-question-id="${id}">
                        + Ajouter une réponse
                    </button>
                    
                    <!-- Réponse attendue (Texte libre) -->
                    <div class="text-answer-container" id="text-answer-${id}" style="display: none;">
                        <div class="mb-3">
                            <label class="form-label">Réponse attendue (optionnel)</label>
                            <input type="text" class="form-control" name="question-${id}-expected-answer" placeholder="Laissez vide pour validation manuelle">
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    function createChoiceHtml(questionId, choiceId) {
        return `
            <div class="choice-item d-flex align-items-center mb-2" data-choice-id="${choiceId}">
                <input type="text" class="form-control" name="question-${questionId}-choice-${choiceId}" placeholder="Réponse ${choiceId}">
                <button type="button" class="btn choice-toggle correct" data-correct="true">
                    ✓
                </button>
            </div>
        `;
    }
    
    function initializeQuestionEvents(id) {
        // Gérer le changement de type
        const typeRadios = document.querySelectorAll(`input[name="question-${id}-type"]`);
        typeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                toggleQuestionType(id, this.value);
            });
        });
        
        // Supprimer question
        const deleteBtn = document.querySelector(`#question-${id} .delete-question`);
        deleteBtn.addEventListener('click', function() {
            document.getElementById(`question-${id}`).remove();
            checkValidation();
        });
        
        // Ajouter choix
        const addChoiceBtn = document.querySelector(`#question-${id} .add-choice`);
        addChoiceBtn.addEventListener('click', function() {
            addChoice(id);
        });
        
        // Toggle correct/incorrect sur choix
        initializeChoiceToggles(id);
    }
    
    function initializeChoiceToggles(questionId) {
        // Supprimer tous les anciens listeners pour éviter les doublons
        const choiceToggles = document.querySelectorAll(`#question-${questionId} .choice-toggle`);
        const questionType = document.querySelector(`input[name="question-${questionId}-type"]:checked`)?.value;
        
        choiceToggles.forEach((btn, index) => {
            // Cloner le bouton pour supprimer tous les event listeners
            const newBtn = btn.cloneNode(true);
            btn.parentNode.replaceChild(newBtn, btn);
            
            // Ajouter le nouveau listener
            newBtn.addEventListener('click', function() {
                // Pour Vrai/Faux : comportement radio inversé (toujours inverser les deux)
                if (questionType === 'true_false') {
                    const visibleToggles = Array.from(document.querySelectorAll(`#question-${questionId} .choice-item`))
                        .filter(item => item.style.display !== 'none' && item.style.visibility !== 'hidden')
                        .map(item => item.querySelector('.choice-toggle'));
                    
                    // Inverser l'état des deux boutons à chaque clic
                    visibleToggles.forEach(toggle => {
                        const currentState = toggle.dataset.correct === 'true';
                        if (currentState) {
                            // Était vert → devient rouge
                            toggle.classList.remove('correct');
                            toggle.classList.add('incorrect');
                            toggle.dataset.correct = 'false';
                            toggle.textContent = '✗';
                        } else {
                            // Était rouge → devient vert
                            toggle.classList.remove('incorrect');
                            toggle.classList.add('correct');
                            toggle.dataset.correct = 'true';
                            toggle.textContent = '✓';
                        }
                    });
                } else {
                    // Pour QCM : comportement checkbox (plusieurs bonnes réponses possibles)
                    const isCorrect = this.dataset.correct === 'true';
                    if (isCorrect) {
                        this.classList.remove('correct');
                        this.classList.add('incorrect');
                        this.dataset.correct = 'false';
                        this.textContent = '✗';
                    } else {
                        this.classList.remove('incorrect');
                        this.classList.add('correct');
                        this.dataset.correct = 'true';
                        this.textContent = '✓';
                    }
                }
            });
        });
    }
    
    function toggleQuestionType(id, type) {
        const choicesContainer = document.getElementById(`choices-${id}`);
        const addChoiceBtn = document.querySelector(`#question-${id} .add-choice`);
        const textAnswerContainer = document.getElementById(`text-answer-${id}`);
        const allChoices = choicesContainer.querySelectorAll('.choice-item');
        
        switch(type) {
            case 'text':
                // Mode Texte libre
                choicesContainer.style.display = 'none';
                addChoiceBtn.style.display = 'none';
                textAnswerContainer.style.display = 'block';
                break;
                
            case 'true_false':
                // Mode Vrai/Faux
                choicesContainer.style.display = 'block';
                textAnswerContainer.style.display = 'none';
                addChoiceBtn.style.display = 'none';
                
                // Ne garder que les 2 premiers choix
                allChoices.forEach((choice, index) => {
                    if (index < 2) {
                        choice.style.display = 'flex';
                        choice.style.visibility = 'visible';
                        const input = choice.querySelector('input[type="text"]');
                        if (input) {
                            input.value = index === 0 ? 'Vrai' : 'Faux';
                        }
                    } else {
                        choice.style.display = 'none';
                        choice.style.visibility = 'hidden';
                        choice.style.height = '0';
                        choice.style.overflow = 'hidden';
                    }
                });
                
                // Marquer "Vrai" vert et "Faux" rouge par défaut
                const visibleToggles = Array.from(allChoices)
                    .slice(0, 2)
                    .map(choice => choice.querySelector('.choice-toggle'));
                
                if (visibleToggles[0]) {
                    visibleToggles[0].classList.add('correct');
                    visibleToggles[0].classList.remove('incorrect');
                    visibleToggles[0].dataset.correct = 'true';
                    visibleToggles[0].textContent = '✓';
                }
                if (visibleToggles[1]) {
                    visibleToggles[1].classList.add('incorrect');
                    visibleToggles[1].classList.remove('correct');
                    visibleToggles[1].dataset.correct = 'false';
                    visibleToggles[1].textContent = '✗';
                }
                
                initializeChoiceToggles(id);
                break;
                
            case 'qcm':
            default:
                // Mode QCM (défaut)
                choicesContainer.style.display = 'block';
                textAnswerContainer.style.display = 'none';
                addChoiceBtn.style.display = 'block';
                
                // Réafficher tous les choix et vider les valeurs
                allChoices.forEach(choice => {
                    choice.style.display = 'flex';
                    choice.style.visibility = 'visible';
                    choice.style.height = 'auto';
                    choice.style.overflow = 'visible';
                    const input = choice.querySelector('input[type="text"]');
                    if (input) input.value = '';
                });
                break;
        }
    }
    
    function addChoice(questionId) {
        const choicesContainer = document.getElementById(`choices-${questionId}`);
        const choiceCount = choicesContainer.querySelectorAll('.choice-item').length;
        
        if (choiceCount >= 8) {
            alert('Maximum 8 réponses par question');
            return;
        }
        
        const newChoiceId = choiceCount + 1;
        const choiceHtml = createChoiceHtml(questionId, newChoiceId);
        choicesContainer.insertAdjacentHTML('beforeend', choiceHtml);
        
        // Ré-initialiser TOUS les toggles après ajout
        initializeChoiceToggles(questionId);
    }
    
    function checkValidation() {
        const questionBlocks = document.querySelectorAll('.question-block');
        const minQuestions = MIN_QUESTIONS || 1;
        
        if (questionBlocks.length >= minQuestions) {
            validateBtn.disabled = false;
        } else {
            validateBtn.disabled = true;
        }
    }
    
    // Validation finale
    validateBtn.addEventListener('click', function() {
        // TODO: Envoyer toutes les questions au serveur
        alert('Validation en cours...');
    });
});
