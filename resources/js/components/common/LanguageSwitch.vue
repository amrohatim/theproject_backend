<template>
  <div :class="containerClass">
    <div :class="switchClass">
      <button
        type="button"
        :class="buttonClass('en')"
        @click="switchLanguage('en')"
      >
        <span class="language-text">{{ useServiceStyle ? 'EN' : 'English' }}</span>
      </button>

      <div v-if="!useServiceStyle" class="language-icon">
        <svg width="28" height="28" viewBox="0 0 28 28" fill="none" xmlns="http://www.w3.org/2000/svg">
          <rect width="28" height="28" fill="url(#pattern0_620_15)"/>
          <defs>
            <pattern id="pattern0_620_15" patternContentUnits="objectBoundingBox" width="1" height="1">
              <use href="#image0_620_15" transform="scale(0.01)"/>
            </pattern>
            <image id="image0_620_15" width="100" height="100" preserveAspectRatio="none" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAAAXNSR0IArs4c6QAAAERlWElmTU0AKgAAAAgAAYdpAAQAAAABAAAAGgAAAAAAA6ABAAMAAAABAAEAAKACAAQAAAABAAAAZKADAAQAAAABAAAAZAAAAAAvu95BAAADTUlEQVR4Ae2dXWrbQBCAY7u3KISSE/QKuUKN7PYh0Bv1Dv2JcY17hZCnvPQMhYZcw3ZnQgTCKNqd3VmhSp9e5Ghn/74vO2iFlVxccEAAAhCAAAQgAAEIQAACEIAABCAAAQhAAAKjI7DZbN6VntS8dAdjan+xWNzv9/urknNCiI3u5eFwuCspBSE2IRpdVApC7EKKSkFImpBiUhCSLqSIFITkCXGXgpB8Ia5SEOIjxE0KQvyEuEhBiK+QbCkI8ReSJWUWGs9POSSmCsVR3krgUZ5/XS+Xyz+tpS0XWSEtUBwvmR+zIMSR/itNmaQg5BWKzpejpSDEmXxHc2+Px+P7jvLnIoSECPmUH06n0+eqqn6FmkNIiFB++bOM9Xr9PaYphMRQSo8xydBu3kT09WU2m+0i4kYfImlH92Sxh8q4kZWxia2gccGNoaWxscfKHvkUOUfzyqjbJWXVJPzOyTJ0CAjxE6EtZcnQBhCiFHyObBk6DIQMSAZCBiYDIflCXNJUcxikrCYN22d3Gdo9QmwS6ugiMhBS47Wdi8lAiE2ERheVYR/OxGvsdruPE0fA9CEAAQhAAAIQgAAEIAABCNgJsJmyMSv6cFFkVPJtvW+2IU07upiQFxm38hWimK8aTdtCY/ZFhCCjQdj40V0IMowGzsJdhSDjjG7Cj25CkJFAv6WKixBktJBNvJQtZLvdruTWlrupRAHn1bKEqAxp8Ae3tudY039OFoKMdOhdNZOEIKMLaV6Z+f0QkfFJUpQ+DlnEdi3xmtomf8gLPE+r1eqhC4TpscbLyvgqDUbL0M6Nbx51jfd/L9M30TqFRKcs0lQ/vwtRQpDRjwztJZiyZNP3QVLOrcSa0lR/UxhXT8EVIjJ+y8bv77imPdzZBIXIXcHjfD6/FinRf2JouNMd/siCQnQKSOlPZJQQpPQnxLwxlJfnLyV93Ukau4odpqwwcz+xbY8tLnqF1BMnfdUkypzNQnQYSCkjQ1tNEqIVkaIU/I9kIToUpAxMCFIGKAQpvlKyUlZzKKSvJo30z25CdAhISRdR13QVgpQaa/rZXQhS0mVozSJCkJInpWjtPv5dadEJ0DgEIAABCEAAAhCAAAQgAAEIQAACEIAABCDQSuAfhAiELkOuc2YAAAAASUVORK5CYII="/>
          </defs>
        </svg>
      </div>

      <button
        type="button"
        :class="buttonClass('ar')"
        @click="switchLanguage('ar')"
      >
        <span class="language-text">{{ useServiceStyle ? 'AR' : 'Arabic' }}</span>
      </button>
    </div>
  </div>
</template>

<script>
import { computed } from 'vue'

export default {
  name: 'LanguageSwitch',
  props: {
    modelValue: {
      type: String,
      default: 'en'
    },
    userRole: {
      type: String,
      default: 'vendor'
    },
    variant: {
      type: String,
      default: 'default'
    }
  },
  emits: ['update:modelValue', 'language-changed'],
  setup(props, { emit }) {
    const currentLanguage = computed({
      get: () => props.modelValue,
      set: (value) => emit('update:modelValue', value)
    })

    const isProductsManagerContext = computed(() => {
      return props.userRole === 'products_manager' || window.location.pathname.includes('/products-manager/')
    })

    const useServiceStyle = computed(() => props.variant === 'service-form')

    const containerClass = computed(() => {
      return useServiceStyle.value ? 'lang-toggle-wrapper' : 'language-switch-container'
    })

    const switchClass = computed(() => {
      return useServiceStyle.value ? 'lang-toggle' : 'language-switch'
    })

    const buttonClass = (language) => {
      if (useServiceStyle.value) {
        return ['lang-toggle-button', { active: currentLanguage.value === language }]
      }

      return [
        'language-button',
        currentLanguage.value === language
          ? (isProductsManagerContext.value ? 'active-pm' : 'active-vendor')
          : 'inactive'
      ]
    }

    const switchLanguage = (language) => {
      if (language !== currentLanguage.value) {
        currentLanguage.value = language
        emit('language-changed', language)
      }
    }

    return {
      currentLanguage,
      isProductsManagerContext,
      useServiceStyle,
      containerClass,
      switchClass,
      buttonClass,
      switchLanguage
    }
  }
}
</script>

<style scoped>
.lang-toggle-wrapper {
  margin-bottom: 1rem;
}

.lang-toggle {
  display: inline-flex;
  gap: 0.5rem;
  padding: 0.25rem;
  border: 1px solid #e5e7eb;
  border-radius: 9999px;
  background: #ffffff;
}

.lang-toggle-button {
  border: none;
  background: transparent;
  color: #6b7280;
  font-size: 0.75rem;
  font-weight: 600;
  padding: 0.35rem 0.9rem;
  border-radius: 9999px;
  display: inline-flex;
  align-items: center;
  gap: 0.35rem;
  cursor: pointer;
}

.lang-toggle-button.active {
  background: #f3f4f6;
  color: #111827;
}

.language-switch-container {
  display: flex;
  justify-content: center;
  margin-bottom: 1rem;
}

.language-switch {
  display: flex;
  align-items: center;
  background: #f9fafb;
  border-radius: 6px;
  padding: 2px;
  border: 1px solid #e5e7eb;
  position: relative;
}

.language-button {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 8px 16px;
  border: none;
  border-radius: 6px;
  font-size: 14px;
  font-weight: 500;
  cursor: pointer;
  transition: all 0.2s ease;
  min-width: 80px;
}

.language-button.active-vendor {
  background: #369FFF;
  color: white;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.language-button.active-pm {
  background: #f59e0b;
  color: white;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.language-button.inactive {
  background: #D9D9D9;
  color: rgba(0, 0, 0, 0.6);
}

.language-button:hover {
  transform: translateY(-1px);
}

.language-icon {
  margin: 0 4px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.language-text {
  font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

:global(.dark) .lang-toggle {
  border-color: #4b5563;
  background: #1f2937;
}

:global(.dark) .lang-toggle-button {
  color: #d1d5db;
}

:global(.dark) .lang-toggle-button.active {
  background: #374151;
  color: #f9fafb;
}

@media (max-width: 640px) {
  .lang-toggle-button {
    min-height: 2.25rem;
  }
}
</style>
