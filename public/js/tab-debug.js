// Debug script for tab switching functionality
console.log('Tab debug script loaded');

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM fully loaded');
    
    // Log all tab links
    const tabLinks = document.querySelectorAll('.tab-link');
    console.log('Tab links found:', tabLinks.length);
    tabLinks.forEach((link, index) => {
        console.log(`Tab link ${index}:`, link.textContent.trim(), 'Target:', link.getAttribute('data-target'));
    });
    
    // Log all tab panels
    const tabPanels = document.querySelectorAll('.tab-panel');
    console.log('Tab panels found:', tabPanels.length);
    tabPanels.forEach((panel, index) => {
        console.log(`Tab panel ${index}:`, panel.id, 'Visible:', !panel.classList.contains('hidden'));
    });
    
    // Add click event listeners with debug logging
    tabLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Tab clicked:', this.textContent.trim());
            
            const targetId = this.getAttribute('data-target');
            console.log('Target panel ID:', targetId);
            
            const targetPanel = document.getElementById(targetId);
            console.log('Target panel found:', targetPanel ? 'Yes' : 'No');
            
            if (targetPanel) {
                // Hide all panels
                tabPanels.forEach(panel => {
                    panel.classList.add('hidden');
                    console.log(`Panel ${panel.id} hidden`);
                });
                
                // Show target panel
                targetPanel.classList.remove('hidden');
                console.log(`Panel ${targetId} shown`);
                
                // Update active tab styling
                tabLinks.forEach(tab => {
                    tab.classList.remove('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                    tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
                });
                
                this.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300', 'dark:text-gray-400', 'dark:hover:text-gray-300');
                this.classList.add('border-indigo-500', 'text-indigo-600', 'dark:text-indigo-400');
                console.log('Tab styling updated');
            } else {
                console.error(`Target panel ${targetId} not found!`);
            }
        });
    });
    
    // Test add specification button
    const addSpecificationBtn = document.getElementById('add-specification');
    if (addSpecificationBtn) {
        console.log('Add specification button found');
    } else {
        console.error('Add specification button not found!');
    }
    
    // Test specifications container
    const specificationsContainer = document.getElementById('specifications-container');
    if (specificationsContainer) {
        console.log('Specifications container found');
    } else {
        console.error('Specifications container not found!');
    }
});
