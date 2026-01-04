// JavaScript simplifié avec CollectionType Symfony
document.addEventListener('DOMContentLoaded', function() {
    const questionsContainer = document.getElementById('questions-container');
    const addQuestionBtn = document.getElementById('add-question-btn');
    const form = document.getElementById('questions-form');
    
    // Index pour nouveaux formulaires
    let questionIndex = parseInt(questionsContainer.dataset.index) || 0;
    
    // === AJOUTER UNE QUESTION ===
    addQuestionBtn.addEventListener('click', function() {
        // Calculer le bon index basé sur le nombre de questions existantes
        const currentQuestions = questionsContainer.querySelectorAll('.question-block');
        questionIndex = currentQuestions.length;
        
        // Cloner le template HTML
        const template = document.getElementById('question-template');
        const clone = template.content.cloneNode(true);
        const questionBlock = clone.querySelector('.question-block');
        
        // Remplacer les placeholders
        questionBlock.dataset.questionIndex = questionIndex;
        questionBlock.querySelector('.question-number').textContent = questionIndex + 1;
        
        // Récupérer le prototype Symfony et remplacer __question_name__
        let prototype = questionsContainer.dataset.prototype;
        let formFields = prototype.replace(/__question_name__/g, questionIndex);
        
        // Parser les champs générés par Symfony avec createContextualFragment (plus sûr)
        const range = document.createRange();
        const fragment = range.createContextualFragment(formFields);
        
        // Symfony génère un seul div parent contenant tous les champs
        // On extrait les enfants de ce div principal
        let allFields = [];
        if (fragment.children.length === 1) {
            // Un seul div parent : extraire ses enfants
            allFields = Array.from(fragment.children[0].children);
        } else {
            // Plusieurs divs : utiliser directement
            allFields = Array.from(fragment.children);
        }
        
        // Extraire les radio buttons pour les types
        const typeRadios = fragment.querySelectorAll('input[type="radio"][name*="[type]"]');
        const typeButtonsContainer = questionBlock.querySelector('.type-buttons-container');
        
        // Ajouter les radio buttons stylés Bootstrap
        typeRadios.forEach(radio => {
            radio.classList.add('btn-check');
            const label = document.createElement('label');
            label.className = 'btn btn-outline-primary';
            label.setAttribute('for', radio.id);
            label.textContent = radio.value === 'qcm' ? 'QCM' : 
                               radio.value === 'true_false' ? 'Vrai/Faux' : 'Texte libre';
            
            typeButtonsContainer.appendChild(radio);
            typeButtonsContainer.appendChild(label);
        });
        
        // Placer les autres champs dans form-fields-container
        const fieldsContainer = questionBlock.querySelector('.form-fields-container');
        allFields.forEach(field => {
            // Ne pas ajouter le div qui contient les types (déjà traité)
            const hasTypeRadios = field.querySelector('.question-type-radios');
            if (!hasTypeRadios) {
                fieldsContainer.appendChild(field);
            }
        });
        
        // Extraire le prototype des choix
        const choicePrototypeContainer = fieldsContainer.querySelector('[data-prototype]');
        if (choicePrototypeContainer) {
            const choicesContainer = questionBlock.querySelector('.choices-container');
            choicesContainer.dataset.prototype = choicePrototypeContainer.dataset.prototype;
            
            // Ajouter 2 choix par défaut
            addInitialChoices(choicesContainer, 2);
        }
        
        // Déplacer le champ expectedAnswer dans text-answer-container
        const expectedAnswerField = fieldsContainer.querySelector('[name*="[expectedAnswer]"]');
        if (expectedAnswerField) {
            const textAnswerContainer = questionBlock.querySelector('.text-answer-container');
            const fieldWrapper = expectedAnswerField.closest('.mb-3') || expectedAnswerField.parentElement;
            if (fieldWrapper && textAnswerContainer) {
                textAnswerContainer.appendChild(fieldWrapper);
            }
        }
        
        // Ajouter au container
        questionsContainer.appendChild(questionBlock);
        
        // Mettre à jour l'index global
        questionIndex++;
        questionsContainer.dataset.index = questionIndex;
        
        // Initialiser les événements
        initializeQuestionEvents(questionBlock);
        
        // Mettre à jour compteur
        updateQuestionCounter();
    });
    
    // Initialiser événements sur questions existantes
    document.querySelectorAll('.question-block').forEach(block => {
        initializeQuestionEvents(block);
    });
    
    // Mettre à jour compteur initial
    updateQuestionCounter();
    
    // === INITIALISER LES ÉVÉNEMENTS D'UNE QUESTION ===
    function initializeQuestionEvents(questionBlock) {
        // Switch type question
        const typeRadios = questionBlock.querySelectorAll('input[type="radio"][name*="[type]"]');
        typeRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                toggleQuestionType(questionBlock, this.value);
            });
        });
        
        // Détecter type actuel et appliquer
        const checkedType = questionBlock.querySelector('input[type="radio"][name*="[type]"]:checked');
        if (checkedType) {
            toggleQuestionType(questionBlock, checkedType.value);
        }
        
        // Supprimer question
        const deleteBtn = questionBlock.querySelector('.delete-question');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                questionBlock.remove();
                renumberQuestions();
                updateQuestionCounter();
            });
        }
        
        // Ajouter choix
        const addChoiceBtn = questionBlock.querySelector('.add-choice');
        const choicesContainer = questionBlock.querySelector('.choices-container');
        
        if (addChoiceBtn && choicesContainer) {
            addChoiceBtn.addEventListener('click', function() {
                const currentChoices = choicesContainer.querySelectorAll('.choice-item').length;
                
                if (currentChoices >= 8) {
                    alert('Maximum 8 réponses par question');
                    return;
                }
                
                addChoiceToContainer(choicesContainer);
            });
        }
        
        // Initialiser toggles et delete sur choix existants
        questionBlock.querySelectorAll('.choice-item').forEach(item => {
            initializeChoiceEvents(item, questionBlock);
        });
    }
    
    // === AJOUTER UN CHOIX À UN CONTAINER ===
    function addChoiceToContainer(choicesContainer) {
        const prototype = choicesContainer.dataset.prototype;
        const currentChoices = choicesContainer.querySelectorAll('.choice-item').length;
        
        // Remplacer __choice_name__ par index
        let newChoice = prototype.replace(/__choice_name__/g, currentChoices);
        
        // Créer la structure complète avec bouton toggle
        const choiceWrapper = document.createElement('div');
        choiceWrapper.className = 'choice-item d-flex align-items-center mb-2';
        choiceWrapper.innerHTML = newChoice;
        
        // Ajouter le bouton toggle vert/rouge
        const hiddenInput = choiceWrapper.querySelector('input[type="hidden"]');
        const toggleBtn = document.createElement('button');
        toggleBtn.type = 'button';
        toggleBtn.className = 'btn choice-toggle incorrect';
        toggleBtn.dataset.correct = 'false';
        toggleBtn.textContent = '✗';
        
        choiceWrapper.appendChild(toggleBtn);
        
        // Ajouter bouton supprimer
        const deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'btn btn-sm btn-outline-danger ms-2 delete-choice';
        deleteBtn.textContent = '×';
        
        choiceWrapper.appendChild(deleteBtn);
        
        choicesContainer.appendChild(choiceWrapper);
        
        // Initialiser événements
        const questionBlock = choicesContainer.closest('.question-block');
        initializeChoiceEvents(choiceWrapper, questionBlock);
    }
    
    // === AJOUTER CHOIX INITIAUX POUR NOUVELLE QUESTION ===
    function addInitialChoices(choicesContainer, count) {
        for (let i = 0; i < count; i++) {
            addChoiceToContainer(choicesContainer);
        }
    }
    
    // === INITIALISER ÉVÉNEMENTS D'UN CHOIX ===
    function initializeChoiceEvents(choiceItem, questionBlock) {
        const toggleBtn = choiceItem.querySelector('.choice-toggle');
        const hiddenInput = choiceItem.querySelector('input[type="hidden"]');
        const deleteBtn = choiceItem.querySelector('.delete-choice');
        
        // Toggle vert/rouge
        if (toggleBtn && hiddenInput) {
            // Supprimer ancien listener (clonage)
            const newToggleBtn = toggleBtn.cloneNode(true);
            toggleBtn.parentNode.replaceChild(newToggleBtn, toggleBtn);
            
            newToggleBtn.addEventListener('click', function() {
                const questionType = getQuestionType(questionBlock);
                
                if (questionType === 'true_false') {
                    // Comportement radio : inverser les deux
                    toggleTrueFalse(questionBlock);
                } else {
                    // Comportement checkbox : toggle individuel
                    toggleSingleChoice(hiddenInput, newToggleBtn);
                }
            });
        }
        
        // Supprimer choix
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                const choicesContainer = choiceItem.closest('.choices-container');
                const remainingChoices = choicesContainer.querySelectorAll('.choice-item').length;
                
                if (remainingChoices > 2) {
                    choiceItem.remove();
                } else {
                    alert('Minimum 2 réponses requises');
                }
            });
        }
    }
    
    // === TOGGLE SIMPLE (QCM) ===
    function toggleSingleChoice(hiddenInput, toggleBtn) {
        const isCorrect = hiddenInput.value === 'true';
        
        if (isCorrect) {
            hiddenInput.value = 'false';
            toggleBtn.classList.remove('correct');
            toggleBtn.classList.add('incorrect');
            toggleBtn.textContent = '✗';
            toggleBtn.dataset.correct = 'false';
        } else {
            hiddenInput.value = 'true';
            toggleBtn.classList.remove('incorrect');
            toggleBtn.classList.add('correct');
            toggleBtn.textContent = '✓';
            toggleBtn.dataset.correct = 'true';
        }
    }
    
    // === TOGGLE VRAI/FAUX (inverse les deux) ===
    function toggleTrueFalse(questionBlock) {
        const choicesContainer = questionBlock.querySelector('.choices-container');
        const visibleChoices = Array.from(choicesContainer.querySelectorAll('.choice-item'))
            .filter(item => item.style.display !== 'none');
        
        visibleChoices.forEach(item => {
            const hiddenInput = item.querySelector('input[type="hidden"]');
            const toggleBtn = item.querySelector('.choice-toggle');
            const currentValue = hiddenInput.value === 'true';
            
            if (currentValue) {
                // Vert → Rouge
                hiddenInput.value = 'false';
                toggleBtn.classList.remove('correct');
                toggleBtn.classList.add('incorrect');
                toggleBtn.textContent = '✗';
                toggleBtn.dataset.correct = 'false';
            } else {
                // Rouge → Vert
                hiddenInput.value = 'true';
                toggleBtn.classList.remove('incorrect');
                toggleBtn.classList.add('correct');
                toggleBtn.textContent = '✓';
                toggleBtn.dataset.correct = 'true';
            }
        });
    }
    
    // === SWITCH TYPE QUESTION ===
    function toggleQuestionType(questionBlock, type) {
        const choicesContainer = questionBlock.querySelector('.choices-container');
        const addChoiceBtn = questionBlock.querySelector('.add-choice');
        const textAnswerContainer = questionBlock.querySelector('.text-answer-container');
        
        // Vérifier que les éléments existent
        if (!choicesContainer || !addChoiceBtn || !textAnswerContainer) {
            console.warn('Éléments manquants dans toggleQuestionType', {
                choicesContainer: !!choicesContainer,
                addChoiceBtn: !!addChoiceBtn,
                textAnswerContainer: !!textAnswerContainer
            });
            return;
        }
        
        const allChoices = choicesContainer.querySelectorAll('.choice-item');
        
        switch(type) {
            case 'text':
                // Texte libre
                choicesContainer.style.display = 'none';
                addChoiceBtn.style.display = 'none';
                textAnswerContainer.style.display = 'block';
                break;
                
            case 'true_false':
                // Vrai/Faux
                choicesContainer.style.display = 'block';
                addChoiceBtn.style.display = 'none';
                textAnswerContainer.style.display = 'none';
                
                // S'assurer qu'il y a exactement 2 choix
                const currentCount = allChoices.length;
                if (currentCount < 2) {
                    // Ajouter les choix manquants
                    for (let i = currentCount; i < 2; i++) {
                        addChoiceToContainer(choicesContainer);
                    }
                    // Recharger la liste
                    const updatedChoices = choicesContainer.querySelectorAll('.choice-item');
                    updatedChoices.forEach((choice, index) => {
                        if (index < 2) {
                            choice.style.display = 'flex';
                            const input = choice.querySelector('input[type="text"]');
                            if (input) {
                                input.value = index === 0 ? 'Vrai' : 'Faux';
                            }
                        }
                    });
                } else {
                    // Ne garder que 2 choix
                    allChoices.forEach((choice, index) => {
                        if (index < 2) {
                            choice.style.display = 'flex';
                            choice.style.visibility = 'visible';
                            choice.style.height = 'auto';
                            choice.style.overflow = 'visible';
                            
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
                }
                
                // Marquer Vrai=vert, Faux=rouge
                const visibleChoices = choicesContainer.querySelectorAll('.choice-item');
                if (visibleChoices[0]) {
                    const toggle0 = visibleChoices[0].querySelector('.choice-toggle');
                    const hidden0 = visibleChoices[0].querySelector('input[type="hidden"]');
                    if (toggle0 && hidden0) {
                        hidden0.value = 'true';
                        toggle0.classList.add('correct');
                        toggle0.classList.remove('incorrect');
                        toggle0.textContent = '✓';
                        toggle0.dataset.correct = 'true';
                    }
                }
                if (visibleChoices[1]) {
                    const toggle1 = visibleChoices[1].querySelector('.choice-toggle');
                    const hidden1 = visibleChoices[1].querySelector('input[type="hidden"]');
                    if (toggle1 && hidden1) {
                        hidden1.value = 'false';
                        toggle1.classList.add('incorrect');
                        toggle1.classList.remove('correct');
                        toggle1.textContent = '✗';
                        toggle1.dataset.correct = 'false';
                    }
                }
                break;
                
            case 'qcm':
            default:
                // QCM
                choicesContainer.style.display = 'block';
                addChoiceBtn.style.display = 'block';
                textAnswerContainer.style.display = 'none';
                
                // Réafficher tous les choix et vider
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
    
    // === UTILITAIRES ===
    function getQuestionType(questionBlock) {
        const checkedType = questionBlock.querySelector('input[type="radio"][name*="[type]"]:checked');
        return checkedType ? checkedType.value : 'qcm';
    }
    
    function renumberQuestions() {
        document.querySelectorAll('.question-block').forEach((block, index) => {
            const numberSpan = block.querySelector('.question-number');
            if (numberSpan) {
                numberSpan.textContent = index + 1;
            }
        });
    }
    
    function updateQuestionCounter() {
        const count = document.querySelectorAll('.question-block').length;
        const counter = document.getElementById('question-counter');
        if (counter) {
            counter.textContent = count;
        }
    }
});
