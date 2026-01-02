// Afficher/masquer le champ timeLimitMode selon le mode choisi
document.addEventListener('DOMContentLoaded', function() {
    const modeRadios = document.querySelectorAll('input[name="hunt[mode]"]');
    const timeLimitField = document.getElementById('timeLimitModeField');
    
    if (!timeLimitField) return; // Si le champ n'existe pas, on sort
    
    function toggleTimeLimitField() {
        const selectedMode = document.querySelector('input[name="hunt[mode]"]:checked')?.value;
        if (selectedMode === 'qr_with_questions') {
            timeLimitField.style.display = 'block';
        } else {
            timeLimitField.style.display = 'none';
        }
    }
    
    // Vérifier l'état initial
    toggleTimeLimitField();
    
    // Écouter les changements
    modeRadios.forEach(radio => {
        radio.addEventListener('change', toggleTimeLimitField);
    });
});
