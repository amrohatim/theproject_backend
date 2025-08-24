@props(['fieldName' => 'language'])

@php
    $uniqueId = uniqid('lang_switch_');
@endphp

<div class="form-language-switch mb-3" id="{{ $uniqueId }}">
    <div class="btn-group btn-group-sm" role="group" aria-label="Language Selection">
        <input type="radio" class="btn-check" name="{{ $fieldName }}_switch" id="{{ $uniqueId }}_en" value="en" checked>
        <label class="btn btn-outline-primary" for="{{ $uniqueId }}_en">
            <i class="fas fa-globe"></i> {{ __('provider.english') }}
        </label>

        <input type="radio" class="btn-check" name="{{ $fieldName }}_switch" id="{{ $uniqueId }}_ar" value="ar">
        <label class="btn btn-outline-primary" for="{{ $uniqueId }}_ar">
            <i class="fas fa-globe"></i> {{ __('provider.arabic') }}
        </label>
    </div>
</div>

<style>
.form-language-switch .btn-check:checked + .btn {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

.form-language-switch .btn {
    font-size: 0.875rem;
    padding: 0.375rem 0.75rem;
}

.form-language-switch .btn i {
    margin-right: 0.25rem;
}

/* RTL Support */
[dir="rtl"] .form-language-switch .btn i {
    margin-right: 0;
    margin-left: 0.25rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const switchContainer = document.getElementById('{{ $uniqueId }}');
    const radioButtons = switchContainer.querySelectorAll('input[type="radio"]');
    
    radioButtons.forEach(function(radio) {
        radio.addEventListener('change', function() {
            const selectedLanguage = this.value;
            const fieldName = '{{ $fieldName }}';
            
            // Hide all language-specific fields for this field name
            const allFields = document.querySelectorAll(`[data-lang-field="${fieldName}"]`);
            allFields.forEach(function(field) {
                field.style.display = 'none';
            });
            
            // Show the selected language field
            const selectedField = document.querySelector(`[data-lang-field="${fieldName}"][data-lang="${selectedLanguage}"]`);
            if (selectedField) {
                selectedField.style.display = 'block';
            }
        });
    });
    
    // Initialize - show English by default
    const defaultField = document.querySelector(`[data-lang-field="{{ $fieldName }}"][data-lang="en"]`);
    if (defaultField) {
        defaultField.style.display = 'block';
    }
    
    // Hide Arabic field by default
    const arabicField = document.querySelector(`[data-lang-field="{{ $fieldName }}"][data-lang="ar"]`);
    if (arabicField) {
        arabicField.style.display = 'none';
    }
});
</script>
