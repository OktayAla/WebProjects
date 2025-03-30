// Text Animation Script
document.addEventListener('DOMContentLoaded', function() {
    // Get the bouncing text element
    const bouncingTextElement = document.querySelector('.bouncing-text');
    
    if (bouncingTextElement) {
        // Get the original text
        const originalText = bouncingTextElement.innerHTML;
        
        // Use a more robust approach to handle nested spans with multiple classes
        // First, extract the highlighted content with its classes
        const highlightRegex = /<span class="([^"]*)">([^<]*)<\/span>/g;
        const matches = [...originalText.matchAll(highlightRegex)];
        
        // If we found highlighted spans
        if (matches.length > 0) {
            // Get the text before the first highlight
            const beforeHighlight = originalText.split(matches[0][0])[0];
            // Get the text after the last highlight
            const afterHighlight = originalText.split(matches[matches.length-1][0])[1];
            
            let newHtml = '';
            
            // Process text before highlight
            for (let i = 0; i < beforeHighlight.length; i++) {
                const char = beforeHighlight[i];
                if (char === ' ') {
                    newHtml += ' ';
                } else {
                    newHtml += `<span style="animation-delay: ${i * 0.05}s">${char}</span>`;
                }
            }
            
            // Add the highlighted spans with their original classes
            matches.forEach(match => {
                newHtml += `<span class="${match[1]}">${match[2]}</span>`;
            });
            
            // Process text after highlight
            for (let i = 0; i < afterHighlight.length; i++) {
                const char = afterHighlight[i];
                if (char === ' ') {
                    newHtml += ' ';
                } else {
                    newHtml += `<span style="animation-delay: ${(beforeHighlight.length + i) * 0.05}s">${char}</span>`;
                }
            }
        
        bouncingTextElement.innerHTML = newHtml;
    }
    
    
    // Initialize wave animation for specific elements if needed
    const waveTextElements = document.querySelectorAll('.wave-text');
    waveTextElements.forEach(element => {
        const text = element.textContent;
        let newHtml = '';
        
        for (let i = 0; i < text.length; i++) {
            if (text[i] === ' ') {
                newHtml += ' ';
            } else {
                newHtml += `<span style="animation-delay: ${i * 0.1}s">${text[i]}</span>`;
            }
        }
        
        element.innerHTML = newHtml;
    });
}});