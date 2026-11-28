<script setup>
import { ref, computed, onMounted, onBeforeUnmount, nextTick } from 'vue'

const props = defineProps({
  modelValue: {
    type: Boolean,
    default: false,
  },
  phone: {
    type: String,
    default: '',
  },
  cooldown: {
    type: Number,
    default: 60,
  },
  devOtp: {
    type: String,
    default: '',
  },
})

const emit = defineEmits(['update:modelValue', 'verify', 'resend', 'change-number'])

const digits = ref(['', '', '', '', '', ''])
const inputRefs = ref([])
const isVerifying = ref(false)
const isResending = ref(false)
const errorMsg = ref('')
const cooldownSeconds = ref(props.cooldown)
let timer = null

const isOtpComplete = computed(() => {
  return digits.value.every((d) => d !== '' && !isNaN(d))
})

const fullCode = computed(() => digits.value.join(''))

function startTimer(seconds) {
  cooldownSeconds.value = seconds
  clearInterval(timer)
  timer = setInterval(() => {
    if (cooldownSeconds.value > 0) {
      cooldownSeconds.value--
    } else {
      clearInterval(timer)
    }
  }, 1000)
}

onMounted(() => {
  startTimer(props.cooldown)
  nextTick(() => {
    inputRefs.value[0]?.focus()
  })
})

onBeforeUnmount(() => {
  clearInterval(timer)
})

function handleInput(index, event) {
  const val = event.target.value
  errorMsg.value = ''

  // If pasting multiple digits
  if (val.length > 1) {
    const pasted = val.replace(/\D/g, '').slice(0, 6)
    for (let i = 0; i < 6; i++) {
      digits.value[i] = pasted[i] || ''
    }
    const nextIdx = Math.min(pasted.length, 5)
    inputRefs.value[nextIdx]?.focus()
    return
  }

  // Single digit
  const singleDigit = val.replace(/\D/g, '')
  digits.value[index] = singleDigit

  if (singleDigit && index < 5) {
    inputRefs.value[index + 1]?.focus()
  }
}

function handleKeyDown(index, event) {
  if (event.key === 'Backspace') {
    if (!digits.value[index] && index > 0) {
      digits.value[index - 1] = ''
      inputRefs.value[index - 1]?.focus()
    }
  } else if (event.key === 'ArrowLeft' && index > 0) {
    inputRefs.value[index - 1]?.focus()
  } else if (event.key === 'ArrowRight' && index < 5) {
    inputRefs.value[index + 1]?.focus()
  }
}

function handlePaste(event) {
  event.preventDefault()
  const text = event.clipboardData.getData('text').replace(/\D/g, '').slice(0, 6)
  if (!text) return

  for (let i = 0; i < 6; i++) {
    digits.value[i] = text[i] || ''
  }
  const nextIdx = Math.min(text.length, 5)
  inputRefs.value[nextIdx]?.focus()
}

async function submitOtp() {
  if (!isOtpComplete.value || isVerifying.value) return
  isVerifying.value = true
  errorMsg.value = ''

  try {
    await emit('verify', fullCode.value, (err) => {
      errorMsg.value = err || 'Verification failed. Please check the code and try again.'
    })
  } finally {
    isVerifying.value = false
  }
}

async function handleResend() {
  if (cooldownSeconds.value > 0 || isResending.value) return
  isResending.value = true
  errorMsg.value = ''

  try {
    await emit('resend', (newCooldown = 60, err) => {
      if (err) {
        errorMsg.value = err
      } else {
        digits.value = ['', '', '', '', '', '']
        startTimer(newCooldown)
        inputRefs.value[0]?.focus()
      }
    })
  } finally {
    isResending.value = false
  }
}

function closeModal() {
  emit('update:modelValue', false)
}
</script>

<template>
  <div v-if="modelValue" class="otp-modal-backdrop" role="dialog" aria-modal="true" @click.self="closeModal">
    <div class="otp-modal-card">
      <!-- Modal Header -->
      <div class="otp-modal-header">
        <div class="otp-header-icon">
          <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
          </svg>
        </div>
        <button type="button" class="btn-close" aria-label="Close modal" @click="closeModal">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <line x1="18" y1="6" x2="6" y2="18" />
            <line x1="6" y1="6" x2="18" y2="18" />
          </svg>
        </button>
      </div>

      <!-- Title & Subtitle -->
      <div class="otp-modal-body">
        <h3 class="otp-title">Verify Your Phone Number</h3>
        <p class="otp-desc">
          Enter the six-digit verification code sent to <strong class="phone-highlight">{{ phone }}</strong>.
        </p>

        <!-- Dev environment OTP banner -->
        <div v-if="devOtp" class="dev-otp-pill">
          <span>Dev OTP: <strong>{{ devOtp }}</strong></span>
        </div>

        <!-- 6-Digit OTP Inputs -->
        <div class="otp-inputs-grid" @paste="handlePaste">
          <input
            v-for="(digit, idx) in digits"
            :key="idx"
            :ref="(el) => (inputRefs[idx] = el)"
            v-model="digits[idx]"
            type="text"
            inputmode="numeric"
            maxlength="1"
            class="otp-digit-box"
            :class="{ active: digit !== '', error: Boolean(errorMsg) }"
            @input="handleInput(idx, $event)"
            @keydown="handleKeyDown(idx, $event)"
          />
        </div>

        <!-- Error Feedback -->
        <div v-if="errorMsg" class="otp-error-msg">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10" />
            <line x1="12" y1="8" x2="12" y2="12" />
            <line x1="12" y1="16" x2="12.01" y2="16" />
          </svg>
          <span>{{ errorMsg }}</span>
        </div>

        <!-- Verify Button -->
        <button
          type="button"
          class="btn-verify-submit"
          :disabled="!isOtpComplete || isVerifying"
          @click="submitOtp"
        >
          <span v-if="isVerifying" class="spinner-sm"></span>
          <span>{{ isVerifying ? 'Verifying Code...' : 'Verify Phone Number' }}</span>
        </button>

        <!-- Resend & Change Number Actions -->
        <div class="otp-footer-actions">
          <div class="resend-wrap">
            <span v-if="cooldownSeconds > 0" class="cooldown-text">
              Resend code in <strong>{{ cooldownSeconds }}s</strong>
            </span>
            <button
              v-else
              type="button"
              class="btn-resend"
              :disabled="isResending"
              @click="handleResend"
            >
              {{ isResending ? 'Sending...' : 'Resend Code' }}
            </button>
          </div>

          <button type="button" class="btn-change-number" @click="emit('change-number')">
            Change Number
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
.otp-modal-backdrop {
  position: fixed;
  inset: 0;
  background: rgba(11, 18, 32, 0.6);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
  padding: 16px;
}

.otp-modal-card {
  background: #ffffff;
  border: 1px solid #e5eaf0;
  border-radius: 20px;
  width: min(440px, 100%);
  padding: 24px 28px;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
  display: flex;
  flex-direction: column;
  gap: 18px;
  animation: modalPop 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

@keyframes modalPop {
  from {
    opacity: 0;
    transform: scale(0.96) translateY(8px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.otp-modal-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.otp-header-icon {
  width: 44px;
  height: 44px;
  border-radius: 12px;
  background: rgba(13, 148, 136, 0.1);
  color: #0d9488;
  display: flex;
  align-items: center;
  justify-content: center;
}

.btn-close {
  background: none;
  border: none;
  color: #94a3b8;
  cursor: pointer;
  padding: 6px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.15s ease;
}

.btn-close:hover {
  background: #f1f5f9;
  color: #111827;
}

.otp-modal-body {
  display: flex;
  flex-direction: column;
  gap: 14px;
  text-align: center;
}

.otp-title {
  font-size: 18px;
  font-weight: 700;
  color: #111827;
  letter-spacing: -0.01em;
  margin: 0;
}

.otp-desc {
  font-size: 13px;
  color: #64748b;
  margin: 0;
  line-height: 1.5;
}

.phone-highlight {
  color: #111827;
}

.dev-otp-pill {
  background: rgba(13, 148, 136, 0.1);
  color: #0d9488;
  border: 1px dashed #0d9488;
  padding: 6px 12px;
  border-radius: 8px;
  font-size: 12px;
  display: inline-block;
  margin: 0 auto;
}

.otp-inputs-grid {
  display: flex;
  justify-content: center;
  gap: 10px;
  margin: 10px 0 6px;
}

.otp-digit-box {
  width: 46px;
  height: 52px;
  border-radius: 12px;
  border: 1.5px solid #e5eaf0;
  background: #f8fafc;
  text-align: center;
  font-size: 22px;
  font-weight: 700;
  color: #111827;
  outline: none;
  transition: all 0.15s ease;
}

.otp-digit-box:focus {
  border-color: #0d9488;
  background: #ffffff;
  box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.15);
}

.otp-digit-box.active {
  border-color: #0d9488;
  background: #ffffff;
}

.otp-digit-box.error {
  border-color: #ef4444;
  background: #fef2f2;
}

.otp-error-msg {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
  font-size: 12px;
  color: #ef4444;
  background: rgba(239, 68, 68, 0.08);
  padding: 8px 12px;
  border-radius: 8px;
}

.btn-verify-submit {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  padding: 12px 20px;
  background: #0d9488;
  color: #ffffff;
  border: none;
  border-radius: 12px;
  font-size: 14px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.15s ease;
  margin-top: 6px;
  box-shadow: 0 4px 12px rgba(13, 148, 136, 0.2);
}

.btn-verify-submit:hover:not(:disabled) {
  background: #0f766e;
  transform: translateY(-1px);
}

.btn-verify-submit:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.spinner-sm {
  width: 16px;
  height: 16px;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-top-color: #ffffff;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.otp-footer-actions {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding-top: 10px;
  border-top: 1px solid #e5eaf0;
  font-size: 13px;
}

.cooldown-text {
  color: #94a3b8;
}

.btn-resend {
  background: none;
  border: none;
  color: #0d9488;
  font-weight: 600;
  cursor: pointer;
  padding: 0;
}

.btn-resend:hover {
  text-decoration: underline;
}

.btn-change-number {
  background: none;
  border: none;
  color: #64748b;
  cursor: pointer;
  padding: 0;
}

.btn-change-number:hover {
  color: #111827;
  text-decoration: underline;
}
</style>
