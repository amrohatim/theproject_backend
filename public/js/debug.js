document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to all links
    const links = document.querySelectorAll('a');
    links.forEach(link => {
        link.addEventListener('click', function(event) {
            console.log('Link clicked:', this.href);
            // Don't prevent default behavior, let the link work normally
        });
    });

    console.log('Debug script loaded successfully');
});
