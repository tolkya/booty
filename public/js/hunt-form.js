// Gestion de l'affichage dynamique des champs du formulaire Hunt
document.addEventListener('DOMContentLoaded', function() {
    const modeRadios = document.querySelectorAll('input[name="hunt[mode]"]');
    const questionSettingsBlock = document.getElementById('questionSettingsBlock');
    const hasTimeLimitRadios = document.querySelectorAll('input[name="hunt[hasTimeLimit]"]');
    const timeLimitFields = document.getElementById('timeLimitFields');
    
    // Afficher/masquer le bloc "Paramètres des questions"
    function toggleQuestionSettings() {
        const selectedMode = document.querySelector('input[name="hunt[mode]"]:checked')?.value;
        if (selectedMode === 'qr_with_questions') {
            questionSettingsBlock.style.display = 'block';
            toggleTimeLimitFields(); // Vérifier aussi l'état du temps limite
        } else {
            questionSettingsBlock.style.display = 'none';
        }
    }
    
    // Afficher/masquer les champs de temps limite
    function toggleTimeLimitFields() {
        const hasTimeLimit = document.querySelector('input[name="hunt[hasTimeLimit]"]:checked')?.value;
        if (hasTimeLimit === '1') { // '1' = true en string
            timeLimitFields.style.display = 'block';
        } else {
            timeLimitFields.style.display = 'none';
        }
    }
    
    // État initial
    toggleQuestionSettings();
    
    // Écouter les changements
    modeRadios.forEach(radio => {
        radio.addEventListener('change', toggleQuestionSettings);
    });
    
    if (hasTimeLimitRadios) {
        hasTimeLimitRadios.forEach(radio => {
            radio.addEventListener('change', toggleTimeLimitFields);
        });
    }
});
