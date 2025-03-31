/*
* Optimized text animation system with improved performance
* Key improvements:
* - Efficient text processing
* - DOM fragment usage
* - Performance-based animation handling
* - Debounced animations
* - Reduced DOM operations
*/

class TextAnimator {
    constructor() {
        this.animationFrameId = null;
        this.initialized = false;
        this.performanceMode = false;
    }

    init() {
        if (this.initialized) return;
        
        // Check device performance
        this.checkPerformance();
        
        // Initialize animations with RAF
        requestAnimationFrame(() => {
            this.initBouncingText();
            this.initWaveText();
        });

        this.initialized = true;
    }

    checkPerformance() {
        // Check device capabilities
        const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
        const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        const isLowPowerMode = navigator.deviceMemory && navigator.deviceMemory < 4;

        this.performanceMode = isMobile || prefersReducedMotion || isLowPowerMode;
    }

    createTextSpan(char, delay, className = '') {
        if (char === ' ') return document.createTextNode(' ');

        const span = document.createElement('span');
        if (this.performanceMode) {
            span.textContent = char;
            return span;
        }

        span.style.animationDelay = `${delay}s`;
        span.textContent = char;
        if (className) span.className = className;
        
        return span;
    }

    processTextWithHighlights(text) {
        const fragment = document.createDocumentFragment();
        const highlightRegex = /<span class="([^"]*)">([^<]*)<\/span>/g;
        const matches = [...text.matchAll(highlightRegex)];

        if (matches.length === 0) {
            this.processSimpleText(text, fragment);
            return fragment;
        }

        let lastIndex = 0;
        let charCount = 0;

        matches.forEach(match => {
            // Process text before highlight
            const beforeText = text.substring(lastIndex, match.index);
            this.processSimpleText(beforeText, fragment, charCount);
            charCount += beforeText.replace(/\s/g, '').length;

            // Process highlighted text
            const highlightSpan = document.createElement('span');
            highlightSpan.className = match[1];
            this.processSimpleText(match[2], highlightSpan, charCount);
            fragment.appendChild(highlightSpan);
            charCount += match[2].replace(/\s/g, '').length;

            lastIndex = match.index + match[0].length;
        });

        // Process remaining text
        const remainingText = text.substring(lastIndex);
        this.processSimpleText(remainingText, fragment, charCount);

        return fragment;
    }

    processSimpleText(text, container, startIndex = 0) {
        const chars = text.split('');
        const batchSize = 10;
        let currentBatch = document.createDocumentFragment();
        let batchCount = 0;

        chars.forEach((char, index) => {
            const span = this.createTextSpan(char, (startIndex + index) * 0.05);
            currentBatch.appendChild(span);
            batchCount++;

            if (batchCount === batchSize) {
                container.appendChild(currentBatch);
                currentBatch = document.createDocumentFragment();
                batchCount = 0;
            }
        });

        if (batchCount > 0) {
            container.appendChild(currentBatch);
        }
    }

    initBouncingText() {
        const elements = document.querySelectorAll('.bouncing-text');
        if (!elements.length) return;

        elements.forEach(element => {
            const originalText = element.innerHTML;
            const processedContent = this.processTextWithHighlights(originalText);
            
            // Use RAF for smooth DOM updates
            requestAnimationFrame(() => {
                element.innerHTML = '';
                element.appendChild(processedContent);
            });
        });
    }

    initWaveText() {
        const elements = document.querySelectorAll('.wave-text');
        if (!elements.length) return;

        elements.forEach(element => {
            const text = element.textContent;
            const fragment = document.createDocumentFragment();
            
            // Process text in batches for better performance
            const batchSize = 10;
            for (let i = 0; i < text.length; i += batchSize) {
                const batch = text.slice(i, i + batchSize);
                requestAnimationFrame(() => {
                    batch.split('').forEach((char, index) => {
                        const span = this.createTextSpan(char, (i + index) * 0.1);
                        fragment.appendChild(span);
                    });
                });
            }

            // Final update
            requestAnimationFrame(() => {
                element.innerHTML = '';
                element.appendChild(fragment);
            });
        });
    }

    // Utility method for handling animation frames
    animate(timestamp) {
        if (!this.lastFrameTime) this.lastFrameTime = timestamp;
        const elapsed = timestamp - this.lastFrameTime;

        if (elapsed > 16) { // Cap at ~60fps
            this.lastFrameTime = timestamp;
            // Animation logic here
        }

        this.animationFrameId = requestAnimationFrame(this.animate.bind(this));
    }

    // Clean up method
    destroy() {
        if (this.animationFrameId) {
            cancelAnimationFrame(this.animationFrameId);
        }
        this.initialized = false;
    }
}

// Initialize text animations when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    const textAnimator = new TextAnimator();
    textAnimator.init();

    // Handle visibility changes for performance
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            textAnimator.destroy();
        } else {
            textAnimator.init();
        }
    });

    // Handle resize events with debounce
    let resizeTimeout;
    window.addEventListener('resize', () => {
        if (resizeTimeout) clearTimeout(resizeTimeout);
        resizeTimeout = setTimeout(() => {
            textAnimator.destroy();
            textAnimator.init();
        }, 250);
    });
});