<script setup>
/**
 * AppInput Component
 * Accessible input component for Parkly Design System.
 *
 * @prop {string|number} modelValue - Input value bound via v-model
 * @prop {string} id - Unique identifier (auto-fallback to generated id)
 * @prop {string} label - Optional label text
 * @prop {string} type - 'text' | 'email' | 'password' | 'number' | 'search' | etc.
 * @prop {string} placeholder - Placeholder text
 * @prop {string} hint - Helper text below input
 * @prop {string} error - Error message (shows invalid style & error text)
 * @prop {boolean} disabled - Disable input
 * @prop {boolean} required - Mark input as required
 */
import { computed } from 'vue'

const props = defineProps({
  modelValue: {
    type: [String, Number],
    default: '',
  },
  id: {
    type: String,
    default: () => `parkly-input-${Math.random().toString(36).slice(2, 9)}`,
  },
  label: {
    type: String,
    default: '',
  },
  type: {
    type: String,
    default: 'text',
  },
  placeholder: {
    type: String,
    default: '',
  },
  hint: {
    type: String,
    default: '',
  },
  error: {
    type: String,
    default: '',
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  required: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['update:modelValue', 'focus', 'blur'])

const hasError = computed(() => Boolean(props.error))

function onInput(event) {
  emit('update:modelValue', event.target.value)
}
</script>

<template>
  <div class="parkly-input-group" :class="{ 'has-error': hasError, 'is-disabled': disabled }">
    <label v-if="label" :for="id" class="input-label">
      {{ label }}
      <span v-if="required" class="required-star" aria-hidden="true">*</span>
    </label>

    <div class="input-wrapper">
      <span v-if="$slots.prefix" class="input-affix prefix">
        <slot name="prefix" />
      </span>

      <input
        :id="id"
        :type="type"
        :value="modelValue"
        :placeholder="placeholder"
        :disabled="disabled"
        :required="required"
        :aria-invalid="hasError"
        :aria-describedby="hasError ? `${id}-error` : hint ? `${id}-hint` : undefined"
        class="input-control"
        @input="onInput"
        @focus="$emit('focus', $event)"
        @blur="$emit('blur', $event)"
      />

      <span v-if="$slots.suffix" class="input-affix suffix">
        <slot name="suffix" />
      </span>
    </div>

    <p v-if="hasError" :id="`${id}-error`" class="feedback-text error-text" role="alert">
      {{ error }}
    </p>
    <p v-else-if="hint" :id="`${id}-hint`" class="feedback-text hint-text">
      {{ hint }}
    </p>
  </div>
</template>

<style scoped>
.parkly-input-group {
  display: flex;
  flex-direction: column;
  gap: var(--space-1);
  text-align: left;
  width: 100%;
}

.input-label {
  font-size: var(--font-size-sm);
  font-weight: var(--font-weight-medium);
  color: var(--color-text);
  display: flex;
  align-items: center;
  gap: var(--space-1);
}

.required-star {
  color: var(--color-danger);
}

.input-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  background-color: var(--color-surface);
  border: 1px solid var(--color-border);
  border-radius: var(--radius-md);
  transition:
    border-color var(--transition-fast),
    box-shadow var(--transition-fast),
    background-color var(--transition-fast);
}

.input-wrapper:hover:not(.is-disabled) {
  border-color: var(--color-border-hover);
}

.input-wrapper:focus-within {
  border-color: var(--color-border-focus);
  box-shadow: 0 0 0 3px var(--color-ring);
}

.input-control {
  flex: 1;
  width: 100%;
  height: 40px;
  padding: var(--space-2) var(--space-3);
  background: transparent;
  border: none;
  color: var(--color-text);
  font-family: var(--font-sans);
  font-size: var(--font-size-sm);
  outline: none;
}

.input-control::placeholder {
  color: var(--color-text-subtle);
}

.input-affix {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  color: var(--color-text-muted);
}

.input-affix.prefix {
  padding-left: var(--space-3);
}

.input-affix.suffix {
  padding-right: var(--space-3);
}

/* --- Error state --- */
.has-error .input-wrapper {
  border-color: var(--color-danger);
}

.has-error .input-wrapper:focus-within {
  box-shadow: 0 0 0 3px rgba(220, 38, 38, 0.2);
}

/* --- Disabled state --- */
.is-disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.is-disabled .input-wrapper {
  background-color: var(--color-surface-subtle);
  cursor: not-allowed;
}

.is-disabled .input-control {
  cursor: not-allowed;
}

.feedback-text {
  font-size: var(--font-size-xs);
  line-height: var(--line-height-normal);
  margin-top: 2px;
}

.hint-text {
  color: var(--color-text-muted);
}

.error-text {
  color: var(--color-danger);
}
</style>

