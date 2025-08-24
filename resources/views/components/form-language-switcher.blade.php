@props(['fieldName' => 'default'])

@php
    $supportedLocales = [
        'en' => ['name' => 'English', 'native' => 'English'],
        'ar' => ['name' => 'Arabic', 'native' => 'العربية']
    ];
@endphp

<div class="form-language-switcher mb-3" data-field="{{ $fieldName }}">
    <div class="btn-group" role="group" aria-label="Language switcher for {{ $fieldName }}">
        @foreach($supportedLocales as $locale => $details)
            <button type="button" 
                    class="btn btn-outline-primary language-tab {{ $loop->first ? 'active' : '' }}" 
                    data-language="{{ $locale }}"
                    data-field="{{ $fieldName }}">
                <span class="language-name">{{ $details['native'] }}</span>
            </button>
        @endforeach
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize form language switcher functionality
    const languageSwitchers = document.querySelectorAll('.form-language-switcher');
    
    languageSwitchers.forEach(function(switcher) {
        const fieldName = switcher.dataset.field;
        const languageTabs = switcher.querySelectorAll('.language-tab');
        
        languageTabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                const selectedLanguage = this.dataset.language;
                const currentField = this.dataset.field;
                
                // Update active tab
                languageTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Show/hide corresponding form fields
                toggleLanguageFields(currentField, selectedLanguage);
                
                // Store current language selection for this field
                sessionStorage.setItem(`formLanguage_${currentField}`, selectedLanguage);
            });
        });
        
        // Restore previous language selection if exists
        const savedLanguage = sessionStorage.getItem(`formLanguage_${fieldName}`);
        if (savedLanguage) {
            const savedTab = switcher.querySelector(`[data-language="${savedLanguage}"]`);
            if (savedTab) {
                languageTabs.forEach(t => t.classList.remove('active'));
                savedTab.classList.add('active');
                toggleLanguageFields(fieldName, savedLanguage);
            }
        } else {
            // Default to English
            toggleLanguageFields(fieldName, 'en');
        }
    });
});

function toggleLanguageFields(fieldName, language) {
    // Hide all language variants for this field
    const allFields = document.querySelectorAll(`[data-language-field="${fieldName}"]`);
    allFields.forEach(field => {
        field.style.display = 'none';
        field.classList.remove('active-language-field');
    });
    
    // Show the selected language field
    const targetField = document.querySelector(`[data-language-field="${fieldName}"][data-language="${language}"]`);
    if (targetField) {
        targetField.style.display = 'block';
        targetField.classList.add('active-language-field');
        
        // Focus on the input if it's visible
        const input = targetField.querySelector('input, textarea');
        if (input) {
            setTimeout(() => input.focus(), 100);
        }
    }
}

// Function to get current language for a field
function getCurrentLanguage(fieldName) {
    const switcher = document.querySelector(`[data-field="${fieldName}"]`);
    if (switcher) {
        const activeTab = switcher.querySelector('.language-tab.active');
        return activeTab ? activeTab.dataset.language : 'en';
    }
    return 'en';
}

// Function to validate bilingual fields
function validateBilingualField(fieldName, isRequired = false) {
    const enField = document.querySelector(`[data-language-field="${fieldName}"][data-language="en"] input, [data-language-field="${fieldName}"][data-language="en"] textarea`);
    const arField = document.querySelector(`[data-language-field="${fieldName}"][data-language="ar"] input, [data-language-field="${fieldName}"][data-language="ar"] textarea`);
    
    if (!enField || !arField) return true;
    
    const enValue = enField.value.trim();
    const arValue = arField.value.trim();
    
    if (isRequired) {
        // Both languages required
        return enValue !== '' && arValue !== '';
    } else {
        // Optional, but if one is filled, both must be filled
        if (enValue === '' && arValue === '') return true; // Both empty is OK
        return enValue !== '' && arValue !== ''; // Both must be filled if one is filled
    }
}
</script>

<style>
.form-language-switcher {
    margin-bottom: 1rem;
}

.form-language-switcher .btn-group {
    display: flex;
    flex-direction: row;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    border-radius: 6px;
    overflow: hidden;
}

.form-language-switcher .language-tab {
    border: 1px solid #dee2e6;
    background: white;
    color: #495057;
    padding: 0.5rem 1rem;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    flex: 1;
    min-width: 80px;
}

.form-language-switcher .language-tab:hover {
    background: #f8f9fa;
    border-color: #007bff;
    color: #007bff;
}

.form-language-switcher .language-tab.active {
    background: #007bff;
    border-color: #007bff;
    color: white;
    font-weight: 500;
}

.form-language-switcher .flag-icon {
    font-size: 1.1em;
}

.form-language-switcher .language-name {
    font-size: 0.9em;
}

/* Ensure horizontal layout with English on left, Arabic on right */
.form-language-switcher .language-tab:first-child {
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
    border-right: none;
}

.form-language-switcher .language-tab:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}

/* RTL Support */
[dir="rtl"] .form-language-switcher .language-tab {
    flex-direction: row-reverse;
}

[dir="rtl"] .form-language-switcher .btn-group {
    flex-direction: row-reverse;
}

/* Language field containers */
[data-language-field] {
    display: none;
}

[data-language-field].active-language-field {
    display: block;
}

/* Responsive design */
@media (max-width: 768px) {
    .form-language-switcher .language-name {
        display: none;
    }
    
    .form-language-switcher .language-tab {
        padding: 0.5rem;
        min-width: 50px;
        justify-content: center;
    }
}
</style>
