function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const text = element.textContent;
    
    navigator.clipboard.writeText(text).then(function() {
        const button = element.nextElementSibling;
        const originalText = button.textContent;
        button.textContent = 'Tersalin!';
        button.style.background = '#4CAF50';
        button.style.color = 'white';
        
        setTimeout(function() {
            button.textContent = originalText;
            button.style.background = '#fbfada';
            button.style.color = '#133d2f';
        }, 2000);
    }).catch(function(err) {
        console.error('Gagal menyalin: ', err);
    });
} 