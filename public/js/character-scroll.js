// Character scroll visibility functionality
document.addEventListener('DOMContentLoaded', function() {
    const characterElement = document.getElementById('character-element');
    const featuresSection = document.getElementById('features');
    
    if (!characterElement || !featuresSection) {
        return;
    }
    
    // Create intersection observer for the features section
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Features section is in view - show character
                characterElement.classList.remove('opacity-0');
                characterElement.classList.add('opacity-100');
            } else {
                // Features section is out of view - hide character
                characterElement.classList.remove('opacity-100');
                characterElement.classList.add('opacity-0');
            }
        });
    }, {
        // Trigger when 50% of the features section is visible
        threshold: 0.5,
        // Add margin to delay trigger until deeper into the section
        rootMargin: '-100px 0px -100px 0px'
    });
    
    // Start observing the features section
    observer.observe(featuresSection);
});