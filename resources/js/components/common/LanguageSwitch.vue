<template>
  <div class="language-switch-container">
    <div class="language-switch">
      <!-- English Button -->
      <button
        type="button"
        class="language-button"
        :class="{ 'active': currentLanguage === 'en', 'inactive': currentLanguage !== 'en' }"
        @click="switchLanguage('en')"
      >
        <span class="language-text">English</span>
      </button>

      <!-- Language Icon -->
      <div class="language-icon">
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

      <!-- Arabic Button -->
      <button
        type="button"
        class="language-button"
        :class="{ 'active': currentLanguage === 'ar', 'inactive': currentLanguage !== 'ar' }"
        @click="switchLanguage('ar')"
      >
        <span class="language-text">Arabic</span>
      </button>
    </div>
  </div>
</template>

<script>
import { ref, computed } from 'vue'

export default {
  name: 'LanguageSwitch',
  props: {
    modelValue: {
      type: String,
      default: 'en'
    }
  },
  emits: ['update:modelValue', 'language-changed'],
  setup(props, { emit }) {
    const currentLanguage = computed({
      get: () => props.modelValue,
      set: (value) => emit('update:modelValue', value)
    })

    const switchLanguage = (language) => {
      if (language !== currentLanguage.value) {
        currentLanguage.value = language
        emit('language-changed', language)
      }
    }

    return {
      currentLanguage,
      switchLanguage
    }
  }
}
</script>

<style scoped>
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

.language-button.active {
  background: #369FFF;
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
</style>